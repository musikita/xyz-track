<?php

namespace Suitcorecms\Fields\FieldTypes;

class File extends BasicField
{
    protected $attributes = [
        'orderable'  => false,
        'searchable' => false,
    ];

    public function formBuild($builder, $value = null, $newName = null)
    {
        return $this->oneTypeField($builder, 'file', $newName);
    }
}
