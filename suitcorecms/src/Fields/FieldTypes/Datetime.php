<?php

namespace Suitcorecms\Fields\FieldTypes;

use Carbon\Carbon;

class Datetime extends BasicField
{
    public function formBuild($builder, $value = null, $newName = null)
    {
        $this->attributes['attributes']['autocomplete'] = 'off';
        $this->attributes['attributes']['data-provide'] = 'datetimepicker';

        return $this->twoTypeField($builder, 'text', $value ? (new Carbon($value))->format('Y-m-d\ H:i') : null, $newName);
    }

    public function formJavascript()
    {
        return <<<'JAVASCRIPT'
        $('[data-provide="datetimepicker"]').datetimepicker({
            format: "yyyy-mm-dd hh:ii",
            autoclose: true,
            todayBtn: true,
        });
JAVASCRIPT;
    }

    public function formInput($value)
    {
        if (!$value) {
            return null;
        }

        try {
            $datetime = Carbon::createFromFormat('Y-m-d\ H:i', $value);
        } catch (\Exception $e) {
            try {
                $datetime = new Carbon($value);
            } catch (\Exception $e) {
                return null;
            }
        }

        return $datetime;
    }
}
