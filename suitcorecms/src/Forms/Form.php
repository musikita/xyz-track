<?php

namespace Suitcorecms\Forms;

use BadMethodCallException;
use Collective\Html\FormBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class Form
{
    protected $id;
    protected $resource;
    protected $method;
    protected $template;
    protected $options = [];
    protected $builder;
    protected $model;
    protected $actionUrl;
    protected $fields;
    protected $rules;
    protected $nameCreator;

    public function __construct(FormBuilder $builder)
    {
        $this->id = 'form__'.uniqid();
        $this->builder = $builder;
        $this->template = config('suitcorecms.forms.template');
    }

    public function getId()
    {
        return $this->id;
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

    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setActionUrl($url)
    {
        $this->actionUrl = $url;

        return $this;
    }

    public function getActionUrl()
    {
        if ($this->actionUrl) {
            return $this->actionUrl;
        }

        if ($this->getMethod() == 'update') {
            return $this->getResource()->routeUpdate();
        }

        return $this->getResource()->routeStore();
    }

    public function template($template)
    {
        $this->template = $template;

        return $this;
    }

    public function nameCreator(callable $callback)
    {
        $this->nameCreator = $callback;

        return $this;
    }

    public function createName($name)
    {
        if ($namer = $this->nameCreator) {
            $name = $namer($name);
        }

        return $name;
    }

    public function option($key, $default = null)
    {
        if (is_array($key)) {
            $this->options = array_replace($this->options, $key);

            return $this;
        }

        return $this->options[$key] ?? $default;
    }

    public function of(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    public function fields(array $fields = [])
    {
        $this->fields = $fields;

        return $this;
    }

    public function addField($field)
    {
        $this->fields[] = is_array($field) ? $field : $field->toForm();

        return $this;
    }

    protected function createHtml($title, $control)
    {
        if (!($this->fieldTemplate ?? false)) {
            $this->fieldTemplate = config('suitcorecms.templates.form_group');
        }
        $template = $this->fieldTemplate;

        return new HtmlString(str_replace(['{title}', '{control}'], [$title, $control], $template));
    }

    public function getModel()
    {
        return $this->model ?? ($this->resource ? $this->resource->getResourceable() : null);
    }

    protected function getRule($fieldName = null)
    {
        $rules = $this->rules ??
            ($this->resource
                ? $this->resource->rules($this->method, request())
                : (method_exists($this->model, 'rules') ? $this->model->rules($this->method) : [])
            ) ?? [];

        return $fieldName !== null ? ($rules[$fieldName] ?? null) : $rules;
    }

    protected function updateTitle($fieldName, $title)
    {
        $rules = $this->getRule($fieldName);
        $rules = is_array($rules) ? $rules : explode('|', $rules);

        $hasRequired = false;
        foreach ($rules as $rule) {
            $rule = trim($rule);
            if (strpos($rule, 'required') !== false) {
                $hasRequired = true;
                break;
            }
        }

        if ($hasRequired) {
            $title .= '*';
        }

        return $title;
    }

    protected function getValue($name, $default = null)
    {
        $names = [];
        if (strpos($name, '[') !== false) {
            $names = explode('[', str_replace(']', '', $name));
            $name = array_shift($names);
        }
        $model = $this->getModel();
        $value = $model ? $model->{$name} : null;
        if ($value !== null) {
            foreach ($names as $slice) {
                $value = $value[$slice] ?? null;
            }
        }

        return $value ?? $default;
    }

    protected function build($field)
    {
        if ($build = $field['build'] ?? false) {
            $name = $field['name'];
            $title = $this->updateTitle($name, $field['title']);
            $value = $this->getValue($name, $field['value'] ?? null);
            if ($output = $field['output'] ?? false) {
                $value = $output($this->getModel(), $name, $this);
            }
            $name = $this->createName($name);
            $built = $build($this->builder, $value, $name, $this);
            if ($field['no_template'] ?? false) {
                return $built;
            }

            return $this->createHtml($title, $built);
        }

        return '';
    }

    protected function wrap()
    {
        return view($this->template, ['form' => $this])->render();
    }

    public function show($wrapper = true)
    {
        if ($wrapper && $this->template) {
            return $this->wrap();
        }
        $this->builder->setModel($this->getModel());
        $form = '';
        foreach ($this->fields as $field) {
            $form .= $this->build($field);
        }

        return $form;
    }

    public function showAs($method)
    {
        return $this->setMethod($method)->show();
    }

    public function javascript()
    {
        $javascript = [];
        foreach ($this->fields as $field) {
            if ($build = $field['build'] ?? false) {
                $obj = $build[0];
                if (!isset($javascript[$type = $field['type']])) {
                    if (method_exists($obj, 'formJavascript')) {
                        $javascript[$type] = $obj->formJavascript();
                    }
                }
                if (is_callable($js = $field['javascript'] ?? false)) {
                    $javascript[] = call_user_func_array($js, []);
                }
            }
        }

        return implode("\n", $javascript);
    }

    public function __call($method, $args)
    {
        if (array_key_exists($method, $this->options)) {
            return $this->options[$method];
        }

        throw new BadMethodCallException();
    }
}
