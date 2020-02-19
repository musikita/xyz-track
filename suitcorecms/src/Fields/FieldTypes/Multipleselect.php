<?php

namespace Suitcorecms\Fields\FieldTypes;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Multipleselect extends BasicField
{
    protected $attributes = [
        'orderable' => false,
        'multiple'  => true,
    ];

    protected $specificAttributes = ['multiple'];

    public function datatablesOutput($model, $name, $datatables)
    {
        $name = $this->attributes['name'];
        $title = $this->attributes['title'] ?? null;
        $value = $model->{$name};
        $options = $this->attributes['options'];
        if (is_callable($options)) {
            $options = call_user_func_array($options, []);
        }
        if (!$this->attributes['relation']) {
            $value = is_array($value) ? $value : (array) $value;
            $value = collect($value)->map(function ($item) use ($options) {
                $it['name'] = $options[$item] ?? $item;

                return $it;
            });
        }
        if ($value instanceof Collection) {
            $value = $value->pluck('name')->implode(', ');
        }
        $length = config('suitcorecms.fields.multipleselect.length', 50);

        return $value ? Str::limit($value, $length, '... '.$this->popoverHtml('multipleselect', $title, $value)) : null;
    }

    public function datatablesJavascript()
    {
        return $this->popoverJavascript('multipleselect');
    }

    public function showOutput($model)
    {
        $name = $this->attributes['name'];

        $value = $model->{$name};
        $options = $this->attributes['options'];
        if (is_callable($options)) {
            $options = call_user_func_array($options, []);
        }
        if (!$this->attributes['relation']) {
            $value = is_array($value) ? $value : (array) $value;
            $value = collect($value)->map(function ($item) use ($options) {
                $it['name'] = $options[$item] ?? $item;

                return $it;
            });
        }

        return
            $value instanceof Collection
            ? $value
                ->pluck('name')
                ->map(function ($item) {
                    return '<span class="kt-badge kt-badge--xl kt-badge--unified-dark kt-badge--inline mb-1">'.$item.'</span>';
                })
                ->implode(' ')
            : null;
    }

    public function formBuild($builder, $value = [], $newName = null)
    {
        $this->attributes['attributes'] = array_merge($this->attributes['attributes'], ['multiple' => true]);

        return $this->threeTypeField($builder, 'select', $value ?? [], $newName.'[]');
    }
}
