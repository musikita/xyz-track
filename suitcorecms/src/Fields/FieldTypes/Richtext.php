<?php

namespace Suitcorecms\Fields\FieldTypes;

use Illuminate\Support\Str;

class Richtext extends BasicField
{
    protected $attributes = [
        'orderable' => false,
    ];

    public function datatablesOutput($model)
    {
        $name = $this->attributes['name'];
        $title = $this->attributes['title'] ?? null;
        $value = $model->{$name};
        $strips = str_replace('"', '`', $value);
        $length = config('suitcorecms.fields.richtext.index.length', 50);

        return $value ? Str::limit(strip_tags($value), $length, '... '.$this->popoverHtml('richtext', $title, $strips)) : null;
    }

    public function datatablesJavascript()
    {
        return $this->popoverJavascript('richtext');
    }

    public function formBuild($builder, $value = null, $newName = null)
    {
        $this->attributes['attributes']['richtext'] = true;

        $image = '<input name="tiny_mce_upload_image" type="file" id="tiny_mce_upload_image" class="kt-hidden" onchange="">';

        return $this->twoTypeField($builder, 'textarea', $value, $newName).$image;
    }

    public function formJavascript()
    {
        return config('suitcorecms.fields.richtext.form.javascript');
    }
}
