<?php

namespace Suitcorecms\Fields\FieldTypes;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BasicField
{
    protected $attributes = [];
    protected $specificAttributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = array_replace_recursive($this->attributes, $attributes);
    }

    public function toIndex()
    {
        $attributes = $this->attributes;
        if ($attributes['on_index'] ?? true) {
            $indexAttributes = $attributes['on_index'] ?? [];
            unset($attributes['on_index']);
            $attributes = array_replace($attributes, $indexAttributes);
            $type = $this->getType();
            $title = $this->getTitle();
            $name = $this->getName();
            $data = $attributes['data'] ?? $name;
            $orderable = $attributes['orderable'] ?? true;
            $searchable = $attributes['searchable'] ?? true;
            $relation = $attributes['relation'] ?? null;
            $options = $attributes['options'] ?? null;
            if (method_exists($this, 'datatablesOutput')) {
                $output = [$this, 'datatablesOutput'];
            }
            if ($attributes['output'] ?? false) {
                $output = $attributes['output'];
            }
            if (method_exists($this, 'datatablesJavascript')) {
                $javascript = [$this, 'datatablesJavascript'];
            }
            if ($attributes['javascript'] ?? false) {
                $javascript = $attributes['javascript'];
            }
            $field = compact('name', 'type', 'data', 'title', 'orderable', 'searchable', 'relation', 'options', 'output', 'javascript');
            $field = array_replace($field, Arr::only($this->attributes, $this->specificAttributes));

            return $this->attributes = $field;
        }

        return false;
    }

    public function toShow()
    {
        $attributes = $this->attributes;
        if ($attributes['on_show'] ?? true) {
            $showAttributes = $attributes['on_show'] ?? [];
            unset($attributes['on_show']);
            $attributes = array_replace($attributes, $showAttributes);
            $type = $this->getType();
            $title = $this->getTitle();
            $name = $this->getName();
            $relation = $attributes['relation'] ?? null;
            $options = $attributes['options'] ?? null;
            if (method_exists($this, 'showOutput')) {
                $output = [$this, 'showOutput'];
            }
            if ($attributes['output'] ?? false) {
                $output = $attributes['output'];
            }
            if (method_exists($this, 'showJavascript')) {
                $javascript = [$this, 'showJavascript'];
            }
            if ($attributes['javascript'] ?? false) {
                $javascript = $attributes['javascript'];
            }
            $attributes = $attributes['attributes'] ?? [];
            $field = compact('name', 'options', 'attributes', 'relation', 'type', 'title', 'output', 'javascript', 'relation');
            $field = array_replace($field, Arr::only($this->attributes, $this->specificAttributes));

            return $this->attributes = $field;
        }

        return false;
    }

    public function toCreate()
    {
        return $this->toForm('create');
    }

    public function toEdit()
    {
        return $this->toForm('edit');
    }

    public function toForm($type = null)
    {
        $mode = 'on_'.$type;
        $attributes = $this->attributes;
        $onForm = $attributes['on_form'] ?? [];
        $onMode = $attributes[$mode] ?? [];
        if ($onForm !== false && false !== $onMode) {
            unset($attributes['on_form']);
            unset($attributes[$mode]);
            $onForm = array_replace($onForm, $onMode);
            $attributes = array_replace($attributes, $onForm);

            return $this->attributes = $this->formField($attributes);

            //     $attributes[$mode] = array_replace($onForm, $onMode);
        // dd($attributes[$mode], $onF);
        //     $field = $this->formField($attributes, $onForm);
        //     return $field === false ? false : ($this->attributes = $field);
        }

        return false;
    }

    public function getName()
    {
        return $this->attributes['name'] ?? Str::snake($this->getTitle());
    }

    public function getTitle()
    {
        return $this->attributes['title'] ?? '';
    }

    public function getType()
    {
        $type = $this->attributes['type'] ?? null;
        if (!$type) {
            $type = strtolower(basename(str_replace('\\', '/', static::class)));
        }

        return $type;
    }

    protected function formField(array $attributes = null, $onForm = [])
    {
        $attributes = $attributes ?? $this->attributes;
        $title = $attributes['title'] ?? $this->getTitle();
        $type = $this->getType();
        $name = $this->getName();
        $options = $attributes['options'] ?? null;
        $input = $attributes['input'] ?? null;
        $output = $attributes['output'] ?? null;
        $relation = $attributes['relation'] ?? null;
        if ($this->attributes['value'] ?? false) {
            $value = $this->attributes['value'];
        }
        if ($attributes['no_template'] ?? false) {
            $no_template = true;
        }
        if ($attributes['javascript'] ?? false) {
            $javascript = $attributes['javascript'];
        }
        if ($attributes['build'] ?? false) {
            $build = $attributes['build'];
        }
        if ($attributes['rules'] ?? false) {
            $rules = $attributes['rules'];
        }
        $attributes = array_replace_recursive(
            config('suitcorecms.forms.inputs.attributes.default', []),
            $attributes['attributes'] ?? []
        );
        $attributes['id'] = $attributes['id'] ?? 'input-'.Str::slug($name);
        if (!isset($build) && method_exists($this, 'formBuild')) {
            $build = [$this, 'formBuild'];
        }
        if (!$input && method_exists($this, 'formInput')) {
            $input = [$this, 'formInput'];
        }
        if (!$output && method_exists($this, 'formOutput')) {
            $output = [$this, 'formOutput'];
        }
        $field = compact('type', 'name', 'title', 'options', 'relation', 'attributes', 'build', 'input', 'output', 'value', 'no_template', 'javascript', 'rules');
        $field = array_replace($field, Arr::only($this->attributes, $this->specificAttributes));

        return $field;
    }

    protected function oneTypeField($builder, $fieldType, $newName = null)
    {
        $this->attributes['name'] = $newName ?? $this->getName();
        extract($this->attributes);

        return $builder->{$fieldType}($this->getName(), $attributes ?? [])->toHtml();
    }

    protected function twoTypeField($builder, $fieldType, $defaultValue = null, $newName = null)
    {
        $this->attributes['name'] = $newName ?? $this->getName();
        extract($this->attributes);

        return $builder->{$fieldType}($this->getName(), $defaultValue, $attributes ?? [])->toHtml();
    }

    protected function threeTypeField($builder, $fieldType, $defaultValue = null, $newName = null)
    {
        $this->attributes['name'] = $newName ?? $this->getName();
        extract($this->attributes);
        if (is_callable($options)) {
            $options = call_user_func_array($options, []);
        }
        $options = [null => 'Select '.$title] + ($options ?? []);

        return $builder->{$fieldType}($this->getName(), $options, $defaultValue, $attributes ?? [])->toHtml();
    }

    protected function popoverHtml($type, $title, $content)
    {
        return ' <a href="javascript:;" '.$type.'-data-toggle="popover" title="'.$title.'" data-trigger="focus" data-content="'.$content.'" data-placement="right" data-html="true"><i class="la la-external-link"></i></a>';
    }

    protected function popoverJavascript($type, $width = '250px', $height = '150px')
    {
        return <<<JavaScript
            $('table [{$type}-data-toggle="popover"]').on('inserted.bs.popover', function () {
                var a = $(this);
                var popover = $('#'+ a.attr('aria-describedby'));
                popover.find('.popover-body').css('width', '{$width}').css('max-height', '{$height}').css('overflow-y', 'auto').css('margin-bottom', '20px');
            }).popover();
JavaScript;
    }
}
