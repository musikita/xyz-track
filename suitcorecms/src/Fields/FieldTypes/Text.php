<?php

namespace Suitcorecms\Fields\FieldTypes;

class Text extends BasicField
{
    public function formBuild($builder, $value = null, $newName = null)
    {
        return $this->twoTypeField($builder, 'text', $value, $newName);
    }
}
