<?php

namespace Suitcorecms\Fields\FieldTypes;

class Button extends BasicField
{
    protected $attributes = [
        'orderable'  => false,
        'searchable' => false,
    ];

    protected $specificAttributes = ['buttonHtml', 'buttonJavascript'];

    public function datatablesOutput($model)
    {
        $html = $this->attributes['html'] ?? null;
        if (is_callable($html)) {
            $html = call_user_func_array($html, [$model, $this]);
        }

        return $html;
    }

    public function datatablesJavascript()
    {
        $javascript = $this->attributes['javascript'] ?? null;
        if (is_callable($javascript)) {
            $javascript = call_user_func_array($javascript, [$this]);
        }

        return $javascript;
    }
}
