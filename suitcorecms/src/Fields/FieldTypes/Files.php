<?php

namespace Suitcorecms\Fields\FieldTypes;

class Files extends BasicField
{
    protected $attributes = [
        'orderable'  => false,
        'searchable' => false,
    ];

    protected $specificAttributes = ['multiple'];

    public function formBuild($builder, $value = null, $newName = null)
    {
        $this->attributes['attributes'] = array_merge($this->attributes['attributes'], ['multiple' => true]);

        return $this->oneTypeField($builder, 'file', $newName.'[]');
    }
}
