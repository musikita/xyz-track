<?php

namespace Suitcorecms\Fields\FieldTypes;

class Email extends BasicField
{
    public function formBuild($builder, $value = null, $newName = null)
    {
        return $this->twoTypeField($builder, 'email', $value, $newName);
    }
}
