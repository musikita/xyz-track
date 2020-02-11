<?php

namespace Suitcorecms\Fields\FieldTypes;

class Password extends BasicField
{
    protected $attributes = [
        'orderable'  => false,
        'searchable' => false,
    ];

    public function formBuild($builder, $value = null, $newName = null)
    {
        return $this->oneTypeField($builder, 'password', $newName);
    }

    public function showOutput($model, $value)
    {
        return '******';
    }
}
