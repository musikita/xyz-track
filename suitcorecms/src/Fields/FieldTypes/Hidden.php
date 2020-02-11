<?php

namespace Suitcorecms\Fields\FieldTypes;

class Hidden extends BasicField
{
    protected $attributes = [
        'on_index'    => false,
        'on_show'     => false,
        'orderable'   => false,
        'searchable'  => false,
        'no_template' => true,
    ];

    public function formBuild($builder, $value = null, $newName = null)
    {
        return $this->twoTypeField($builder, 'hidden', $value, $newName);
    }
}
