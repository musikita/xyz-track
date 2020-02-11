<?php

namespace Suitcorecms\Fields\FieldTypes;

class Number extends BasicField
{
    public function formBuild($builder, $value = null, $newName = null)
    {
        return $this->twoTypeField($builder, 'number', $value, $newName);
    }
}
