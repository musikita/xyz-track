<?php

namespace Suitcorecms\Fields\FieldTypes;

class Time extends BasicField
{
    public function formBuild($builder, $value = null, $newName = null)
    {
        $this->attributes['attributes']['data-provide'] = 'timepicker';
        $this->attributes['attributes']['data-show-meridian'] = 'false';

        return $this->twoTypeField($builder, 'text', $value, $newName);
    }
}
