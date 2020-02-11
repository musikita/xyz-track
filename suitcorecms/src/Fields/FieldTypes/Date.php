<?php

namespace Suitcorecms\Fields\FieldTypes;

use Carbon\Carbon;

class Date extends BasicField
{
    public function formBuild($builder, $value = null, $newName = null)
    {
        $this->attributes['attributes']['autocomplete'] = 'off';
        $this->attributes['attributes']['data-provide'] = 'datepicker';
        $this->attributes['attributes']['data-date-format'] = 'yyyy-mm-dd';

        return $this->twoTypeField($builder, 'text', $value ? (new Carbon($value))->format('Y-m-d') : null, $newName);
    }

    public function formInput($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value) : null;
    }

    public function showOutput($model, $value)
    {
        $finalValue = $value instanceof Carbon ? $value->format('Y-m-d') : (new Carbon())->format('Y-m-d');

        return $finalValue;
    }
}
