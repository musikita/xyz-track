<?php

namespace Suitcorecms\Fields\FieldTypes;

use Carbon\Carbon;

class Datetimelocal extends BasicField
{
    public function formBuild($builder, $value = null, $newName = null)
    {
        $value = $value ?? $builder->getModel()->{$this->getName()};

        return $this->twoTypeField($builder, 'datetimeLocal', $value ? (new Carbon($value))->format('Y-m-d\TH:i') : null, $newName);
    }

    public function formInput($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d\TH:i', $value) : null;
    }
}
