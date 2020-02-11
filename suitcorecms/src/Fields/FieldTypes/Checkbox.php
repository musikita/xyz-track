<?php

namespace Suitcorecms\Fields\FieldTypes;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Checkbox extends BasicField
{
    protected $attributes = [
        'orderable'  => false,
        'searchable' => false,
    ];

    public function datatablesOutput($model)
    {
        $name = $this->attributes['name'];
        $title = $this->attributes['title'] ?? null;
        $value = $model->{$name};
        $options = $this->attributes['options'];
        if (is_callable($options)) {
            $options = call_user_func_array($options, []);
        }
        if (!$this->attributes['relation']) {
            $value = collect((array) $value)->map(function ($item) use ($options) {
                $it['name'] = $options[$item] ?? $item;

                return $it;
            });
        }
        if ($value instanceof Collection) {
            $value = $value->pluck('name')->implode(', ');
        }
        $length = config('suitcorecms.fields.richtext.checkbox.length', 50);

        return $value ? Str::limit($value, $length, '... '.$this->popoverHtml('checkbox', $title, $value)) : null;
    }

    public function datatablesJavascript()
    {
        return $this->popoverJavascript('checkbox');
    }

    public function showOutput($model)
    {
        $name = $this->attributes['name'];
        $options = $this->attributes['options'];
        if (is_callable($options)) {
            $options = call_user_func_array($options, []);
        }
        $value = $model->{$name};
        if ($this->attributes['relation']) {
            if ($value instanceof Collection) {
                $value = $value
                            ->pluck('name')
                            ->map(function ($item) {
                                return '<span class="kt-badge kt-badge--xl kt-badge--unified-dark kt-badge--inline">'.$item.'</span>';
                            })
                            ->implode(' ');
            }

            return $value;
        }

        $value = collect((array) $value);

        return $value->map(function ($item) use ($options) {
            return '<span class="kt-badge kt-badge--xl kt-badge--unified-dark kt-badge--inline">'.($options[$item] ?? $item).'</span>';
        })
        ->implode(' ');
    }

    public function formBuild($builder, $value = null, $newName = null)
    {
        $this->attributes['name'] = $newName ?? $this->getName();
        $options = $this->attributes['options'] ?? [];
        if (is_callable($options)) {
            $options = call_user_func_array($options, []);
        }
        $html = '<div class="kt-checkbox-list">';
        $value = !$this->attributes['relation'] ? collect((array) $value)->map(function ($item) {
            $it['id'] = $item;

            return $it;
        }) : $value;
        $valueIds = $value ? $value->pluck('id')->toArray() : [];
        foreach ($options as $key => $option) {
            $checked = in_array($key, $valueIds);
            $html .= '<label class="kt-checkbox">';
            $html .= $builder->checkbox($this->getName().'[]', $key, $checked)->toHtml().$option;
            $html .= '<span></span>';
            $html .= '</label>';
        }
        $html .= '</div>';

        return $html;
    }

    public function formInput($value, $request)
    {
        return $value ?? [];
    }
}
