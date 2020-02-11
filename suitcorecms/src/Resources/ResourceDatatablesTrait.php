<?php

namespace Suitcorecms\Resources;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Suitcorecms\Datatables\Datatables;

trait ResourceDatatablesTrait
{
    protected $datatablesAjaxUrl;
    protected $datatablesAjaxType;
    protected $datatablesAjaxData;
    protected $checkboxField;
    protected $actionField;

    protected function detectAjaxSettings()
    {
        if (!$this->datatablesAjaxUrl) {
            $this->datatablesAjaxUrl = $this->routeIndex();
        }
    }

    protected function loadDatatablesColumnSettings()
    {
        if (!$this->checkboxField && $checkbox = config('suitcorecms.datatables.checkbox', false)) {
            $this->checkboxField = $checkbox;
        }
        if (!$this->actionField && $action = config('suitcorecms.datatables.action', false)) {
            $this->actionField = $action;
        }
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

    protected function genericDatatablesField($name, $output = null, array $others = [])
    {
        $field = [
            'name'       => $name,
            'data'       => $name,
            'title'      => $name,
            'searchable' => false,
            'orderable'  => false,
        ];
        if ($output) {
            $field['output'] = $output;
        }

        $field = array_replace($field, $others);

        return $field;
    }

    public function setCheckboxField(callable $callback)
    {
        $this->checkboxField = $callback;

        return $this;
    }

    public function noCheckboxField()
    {
        $this->checkboxField = null;

        return $this;
    }

    public function setActionField(callable $callback)
    {
        $this->actionField = $callback;

        return $this;
    }

    public function noActionField()
    {
        $this->actionField = null;

        return $this;
    }

    protected function datatablesRelationColumn($relationName, $column)
    {
        return array_replace($column, [
            'data'       => $relationName,
            'title'      => $column['title'],
            'searchable' => false,
            'orderable'  => false,
        ]);
    }

    protected function getFinalRelatedModel($model, $relation)
    {
        if (!$relation) {
            return false;
        }

        $relations = is_string($relation) ? explode('.', $relation) : $relation;
        foreach ($relations as $relation) {
            if ($model instanceof Relation) {
                $model = $model->getRelated();
            }
            $model = $model->{$relation}();
        }

        return $model;
    }

    protected function datatablesColumns()
    {
        $this->loadDatatablesColumnSettings();
        $datatablesColumns = [];
        if ($this->checkboxField) {
            $datatablesColumns[] = $this->genericDatatablesField('_', $this->checkboxField);
        }
        foreach ($this->fields('index') as $key => $column) {
            $relation = $column['relation'] ?? false;
            unset($column['relation']);
            $relationship = $this->getFinalRelatedModel($this->resourceable, $relation);
            if ($relationship) {
                $related = $relationship->getRelated();
                $name = $related->getCaptionField();
                $relationName = $relation.'.'.$name;
                if (in_array(get_class($relationship), [HasOne::class, HasOneThrough::class, BelongsTo::class])) {
                    $column = $this->datatablesRelationColumn($relationName, $column);
                } elseif (in_array(get_class($relationship), [BelongsToMany::class])) {
                    $column = $this->datatablesRelationColumn($relationName, $column);
                    $column['data'] = $relation.'_items';
                }
            }

            $res = $relationship ? $relationship->getRelated() : $this->resourceable;

            $name = $relation ? $res->getCaptionField() : $column['name'];
            if (method_exists($res, 'translation') && in_array($name, $res->getTranslateableAttributes())) {
                $column['name'] = ($relation ? $relation.'.' : '').'translation.'.$name;
            } else {
                $column['name'] = ($relation ? $relation.'.' : '').$name;
            }

            $datatablesColumns[] = $column;
        }
        if ($this->actionField) {
            $datatablesColumns[] = $this->genericDatatablesField('Action', $this->actionField, ['autoHide' => false]);
        }

        return $datatablesColumns;
    }

    protected function datatablesQuery($query = null)
    {
        $relations = array_filter(Arr::pluck($this->fields('index'), 'relation'));
        $base = $this->resourceable->datatablesQuery();
        if (is_callable($query)) {
            $query = $query($base);
        }
        $query = $query ?? $base;
        if (count($relations)) {
            $query->with($relations);
        }

        return $query;
    }

    public function datatablesSetup()
    {
        return app(Datatables::class)
            ->setResource($this)
            ->columns($this->datatablesColumns());
    }

    public function jsonDatatables($query = null)
    {
        if ($this->jsonDatatables ?? false) {
            return $this->jsonDatatables;
        }

        return $this->jsonDatatables = $this->datatablesSetup()
                ->of($this->datatablesQuery($query))
                ->make(true);
    }

    public function datatables($noCache = false)
    {
        if (!$noCache && ($this->datatables ?? false)) {
            return $this->datatables;
        }
        $this->detectAjaxSettings();

        return $this->datatables = $this->datatablesSetup()
            ->setDatatablesAjaxUrl($this->datatablesAjaxUrl)
            ->setDatatablesAjaxType($this->datatablesAjaxType)
            ->setDatatablesAjaxData($this->datatablesAjaxData)
            ->show();
    }
}
