<?php

namespace Suitcorecms\Resources;

use Illuminate\Support\Str;

class Resources
{
    protected $resources = [];

    protected $fields = [];

    public static function make()
    {
        $instance = new static();

        return $instance->setResources(func_get_args());
    }

    public function setResources(array $resources = [])
    {
        $res = [];
        foreach ($resources as $resource) {
            $res[] = $resource->setPrefix($resource->getPrefix() ?? Str::snake($resource->getName()));
            $this->insertField($resource);
        }
        $this->resources = $res;

        return $this;
    }

    protected function insertField($resource)
    {
        $prefix = $resource->getPrefix();
        $method = $resource->getMethod();
        foreach ($resource->fields($method) as $field) {
            $name = $field['name'];
            $field['form_name'] = $resource->form($method)->createName($name);
            $this->fields[$prefix.'.'.$name] = $field;
        }
    }

    public function getField($name)
    {
        return $this->fields[$name] ?? null;
    }

    public function fields()
    {
        return $this->fields;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function processRequest($method)
    {
        foreach ($this->resources as $resource) {
            $resource->processRequest($method);
        }

        return request();
    }
}
