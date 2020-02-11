<?php

namespace Suitcorecms\Fields;

class Field
{
    protected $fields;

    public function __construct(array $fields = [])
    {
        $this->setFields($fields);
    }

    public function setFields(array $fields = [])
    {
        $this->fields = $fields;

        return $this;
    }

    protected function getColumns($type = 'index')
    {
        $fields = $this->fields;
        $key = 'on_'.$type;
        $columns = [];
        foreach ($fields as $title => $field) {
            if ($field['title'] ?? false) {
                $title = $field['title'];
                unset($field['title']);
            }
            $check = $field[$key] ?? true;
            if ($check != false) {
                $input = $type.'Column';
                if ($column = $this->{$input}($title, $field)) {
                    $columns[] = $column;
                }
            }
        }

        return $columns;
    }

    protected function getFieldType($title, $field)
    {
        $basicField = 'BasicField';
        $type = $field['type'] ?? $basicField;
        $namespace = '\\Suitcorecms\\Fields\\FieldTypes\\';
        if (!class_exists($class = $namespace.ucfirst($type))) {
            $class = $namespace.$basicField;
        }

        $field['title'] = $title;

        return new $class($field);
    }

    protected function indexColumn($title, $field)
    {
        $field = $this->getFieldType($title, $field);

        return $field->toIndex();
    }

    protected function createColumn($title, $field)
    {
        $field = $this->getFieldType($title, $field);

        return $field->toCreate();
    }

    protected function editColumn($title, $field)
    {
        $field = $this->getFieldType($title, $field);

        return $field->toEdit();
    }

    protected function showColumn($title, $field)
    {
        $field = $this->getFieldType($title, $field);

        return $field->toShow();
    }

    public function index()
    {
        return $this->getColumns('index');
    }

    public function create()
    {
        return $this->getColumns('create');
    }

    public function edit()
    {
        return $this->getColumns('edit');
    }

    public function update()
    {
        return $this->edit();
    }

    public function show()
    {
        return $this->getColumns('show');
    }
}
