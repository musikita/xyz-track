<?php

namespace Suitcorecms\Fields\FieldTypes;

use Illuminate\Support\Arr;

class Boolean extends Checkbox
{
    protected $attributes = [
        'orderable'  => false,
        'searchable' => false,
    ];

    public function formBuild($builder, $value = null, $newName = null)
    {
        $this->attributes['options'] = [1 => 'Yes'];

        return parent::formBuild($builder, $value, $newName);
    }

    public function datatablesOutput($model)
    {
        $this->attributes['options'] = [0 => 'No', 1 => 'Yes'];

        return parent::datatablesOutput($model);
    }

    public function showOutput($model)
    {
        $this->attributes['options'] = [0 => 'No', 1 => 'Yes'];

        return parent::showOutput($model);
    }

    public function formInput($value, $request)
    {
        return (bool) Arr::first((array) $value);
    }
}
