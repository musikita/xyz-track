<?php

namespace Suitcorecms\Fields\FieldTypes;

class Select extends BasicField
{
    protected $attributes = [
        'orderable' => false,
    ];

    public function datatablesOutput($model, $column, $datatables)
    {
        if ($this->attributes['relation']) {
            $value = $model;
            foreach (explode('.', $column) as $rel) {
                $value = $value->{$rel};
            }

            return $value;
        }
        $name = $this->attributes['name'];
        $title = $this->attributes['title'] ?? null;
        $value = $model->{$name};
        $options = $this->attributes['options'];
        if (is_callable($options)) {
            $options = call_user_func_array($options, []);
        }

        return $options[$value] ?? $value;
    }

    public function formBuild($builder, $value = null, $newName = null)
    {
        return $this->threeTypeField($builder, 'select', $value, $newName);
    }
}
