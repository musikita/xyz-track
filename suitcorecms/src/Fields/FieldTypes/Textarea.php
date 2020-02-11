<?php

namespace Suitcorecms\Fields\FieldTypes;

use Illuminate\Support\Str;

class Textarea extends BasicField
{
    protected $attributes = [
        'orderable' => false,
    ];

    public function datatablesOutput($model)
    {
        $name = $this->attributes['name'];
        $title = $this->attributes['title'] ?? null;
        $value = $model->{$name};
        $length = config('suitcorecms.fields.textarea.index.length', 20);
        $class = config('suitcorecms.fields.textarea.index.class', 'text-dark');

        return '<a href="javascript:;" title="'.$value.'" class="'.$class.'">'.Str::limit($value, $length).'</i></a>';
    }

    public function formBuild($builder, $value = null, $newName = null)
    {
        return $this->twoTypeField($builder, 'textarea', $value, $newName);
    }
}
