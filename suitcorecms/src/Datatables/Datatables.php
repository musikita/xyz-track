<?php

namespace Suitcorecms\Datatables;

use BadMethodCallException;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables as Engine;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\QueryDataTable;

class Datatables
{
    protected static $collection = [];
    protected $engine;
    protected $resource;
    protected $builder;
    protected $checkbox;
    protected $action;
    protected $columns = [];
    protected $rawColumns = [];
    protected $javascriptColumns = [];
    protected $parameters;
    protected $datatablesAjaxUrl;
    protected $datatablesAjaxType = 'GET';
    protected $datatablesAjaxData;

    protected $datatable;

    public function __construct(Engine $engine, Builder $builder)
    {
        $this->engine = $engine;
        $this->builder = $builder;
        $this->parameters = config('suitcorecms.datatables.parameters', []);
    }

    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setDatatablesAjaxUrl($url)
    {
        $this->datatablesAjaxUrl = $url;

        return $this;
    }

    public function setDatatablesAjaxType($type)
    {
        $this->datatablesAjaxType = $type;

        return $this;
    }

    public function setDatatablesAjaxData($data)
    {
        $this->datatablesAjaxData = $data;

        return $this;
    }

    public static function defaultCheckbox($model, $name, $instance)
    {
        $id = $model->getKey();

        return <<<HTML
            <label class="kt-checkbox kt-checkbox--single kt-checkbox--solid">
                        <input type="checkbox" name="checkbox[{$id}]" data-id="{$id}" class="m-checkable datatables-checkbox" onchange="\$.DatatablesSelect(this)">
                <span></span>
            </label>
HTML;
    }

    public static function defaultAction($model, $name, $instance)
    {
        $id = $model->getKey();
        $resource = $instance->getResource();
        $editButton = false;
        if ($resource->routeExist('edit')) {
            $editUrl = $resource->routeEdit($id);
            $editButton = <<<HTML
            <a class="dropdown-item btn-detail-datatables" href="{$editUrl}" data-id="{$id}" title="Edit"> <i class="la la-edit"></i> Edit Data</a>
HTML;
        }
        $showButton = false;
        if ($resource->routeExist('show')) {
            $showUrl = $resource->routeShow($id);
            $showButton = <<<HTML
            <a class="dropdown-item btn-detail-datatables" href="{$showUrl}"><i class="la la-exclamation-circle"></i> Show Details</a>
HTML;
        }

        // <a class="dropdown-item btn-delete-datatables red" href="#"><i class="la la-trash red"></i> Remove</a>
        $deleteButton = false;
        if ($resource->routeExist('destroy')) {
            $deleteUrl = $resource->routeDestroy($id);
            $csrf_field = csrf_field();
            $deleteButton = <<<HTML
            <form action="{$deleteUrl}" method="POST">
                <input type="hidden" name="_method" value="DELETE">
                {$csrf_field}
            </form>
            <a class="dropdown-item btn-delete-datatables btn-danger" href="#" onclick="return (confirm(&quot;Are you sure?&quot;) ? $(this).siblings(&quot;form&quot;).submit() : false);"><i class="la la-trash text-white"></i> Delete</a>
HTML;
        }

        $dropdown = false;
        if ($editButton || $showButton || $deleteButton) {
            $dropdown = <<<HTML
            <span class="dropdown">
                <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                  <i class="la la-ellipsis-h"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    {$showButton} {$editButton} {$deleteButton}
                </div>
            </span>
HTML;
        }

        return <<<HTML
            {$dropdown}
HTML;
    }

    public function columns(array $columns = [])
    {
        $raw = [];
        $javascript = [];
        foreach ($columns as $key => $column) {
            if ($output = $column['output'] ?? false) {
                $raw[$column['data']] = $output;
                unset($columns[$key]['output']);
            }
            if ($js = $column['javascript'] ?? false) {
                if (is_callable($js)) {
                    $javascript[$column['type']] = call_user_func_array($js, []);
                }
                unset($columns[$key]['javascript']);
            }
            unset($columns[$key]['options']);
        }
        $this->columns = $columns;
        $this->rawColumns = $raw;
        $this->javascriptColumns = $javascript;

        return $this;
    }

    public function parameters(array $parameters = [])
    {
        $this->parameters = array_merge($this->parameters, $parameters);

        return $this;
    }

    protected function loadAjaxSetting()
    {
        if ($this->datatablesAjaxUrl) {
            $url = $this->datatablesAjaxUrl;
        }
        if ($this->datatablesAjaxType) {
            $type = $this->datatablesAjaxType;
        }
        if ($this->datatablesAjaxData) {
            $data = $this->datatablesAjaxData;
        }

        return compact('url', 'type', 'data');
    }

    protected function processParameters()
    {
        $parameters = $this->parameters;
        $columnDefs = $parameters['columnDefs'] ?? [];
        if ($columnDefs !== []) {
            $parameters['columnDefs'] = $columnDefs;
        }
        $callbacks = [];
        foreach ($this->javascriptColumns as $js) {
            $callbacks[] = $js;
        }
        if ($callback = $parameters['drawCallback'] ?? false) {
            $callbacks[] = '('.$callback.')(this)';
        }
        $parameters['drawCallback'] = 'function() {'."\n".implode("\n", $callbacks)."\n".'}';

        return $parameters;
    }

    public function show($name = null)
    {
        $this->datatable = $this->builder
            ->ajax($this->loadAjaxSetting())
            ->setTableId($tableId = uniqid())
            ->columns($this->columns)
            ->parameters(
                $this->processParameters()
            );

        return static::push($name ?? $tableId, $this);
    }

    public function table()
    {
        if ($datatable = $this->datatable) {
            return $datatable->table();
        }

        throw new BadMethodCallException();
    }

    public function scripts($autoStart = true)
    {
        if ($datatable = $this->datatable) {
            $script = $datatable->generateScripts();
            $scriptName = "window.SuitcorecmsDatatables[\"{$datatable->getTableAttribute('id')}\"]";
            $scripts = new HtmlString("window.SuitcorecmsDatatables=window.SuitcorecmsDatatables||{};{$scriptName} = function() { {$script} }; \n");
            if ($autoStart) {
                $scripts .= new HtmlString("{$scriptName}()");
            }

            $scripts .= "\n".config('suitcorecms.datatables.datatables.script');

            return new HtmlString("<script type=\"text/javascript\">{$scripts}</script>\n");
        }

        throw new BadMethodCallException();
    }

    public function make($mDataSupport = true)
    {
        $engine = $this->engine;
        if (count($this->rawColumns)) {
            $columns = [];
            foreach ($this->rawColumns as $column => $callback) {
                if (is_callable($callback)) {
                    $engine->editColumn($column, function ($model) use ($column, $callback) {
                        return $callback($model, $column, $this);
                    });
                    $columns[] = $column;
                }
            }
            $engine->rawColumns($columns);
        }

        return $engine->make($mDataSupport);
    }

    public function __call($method, $args)
    {
        if (method_exists($this->engine, $method)) {
            $this->engine = call_user_func_array([$this->engine, $method], $args);

            return $this->engine instanceof QueryDataTable ? $this : $this->engine;
        }

        throw new BadMethodCallException();
    }

    public static function __callStatic($method, $args)
    {
        $instance = app(static::class);
        $class = get_class($instance->engine);
        if (method_exists($class, $method)) {
            return call_user_func_array([$class, $method], $args);
        }

        throw new BadMethodCallException();
    }

    public static function push($name, $datatables)
    {
        return static::$collection[$name] = $datatables;
    }

    public static function get($name = null)
    {
        $collection = static::$collection;

        return $name
            ? ($collection[$name] ?? null)
            : (count($collection) ? Arr::last($collection) : null);
    }

    public static function all()
    {
        return static::$collection;
    }
}
