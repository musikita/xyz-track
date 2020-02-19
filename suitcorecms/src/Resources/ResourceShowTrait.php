<?php

namespace Suitcorecms\Resources;

use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

trait ResourceShowTrait
{
    protected $showTemplate;

    protected $showHtml;

    protected $showJavascript;

    protected function getShowValueRelation($field)
    {
        if (!isset($field['relation']) && strpos($field['name'], '[')) {
            return $this->valueFromBracketName($field);
        }

        $chains = [$field['name']];
        if ($relation = $field['relation'] ?? false) {
            $chains = explode('.', $relation);
        }
        $value = $this->resourceable;
        foreach ($chains as $chain) {
            $value = $value->{$chain};
        }
        if (method_exists($value, 'getCaption')) {
            return $value->getCaption();
        }

        if ($value instanceof Collection && $value->count() == 0) {
            $value = null;
        }

        return $value;
    }

    protected function valueFromBracketName($field)
    {
        $name = change_bracket_to_dot($field['name']);
        $value = $this->resourceable;
        foreach (explode('.', $name) as $slice) {
            $value = $value[$slice] ?? null;
        }

        return $value;
    }

    protected function noShowValue($default = '-')
    {
        if ($this->noShowValue ?? false) {
            return $this->noShowValue;
        }

        return config('suitcorecms.templates.no_value_show', $default);
    }

    protected function getShowValue($field)
    {
        $name = $field['name'];
        $value = $this->getShowValueRelation($field);
        if ($output = $field['output'] ?? false) {
            $value = $output($this->resourceable, $value, $field);
        }

        return $value ?? $this->noShowValue();
    }

    protected function buildShow($field)
    {
        if (!($this->showTemplate ?? false)) {
            $this->showTemplate = config('suitcorecms.templates.show_group');
        }
        $template = $this->showTemplate;
        $title = $field['title'];
        $value = $this->getShowValue($field);

        return new HtmlString(str_replace(['{title}', '{value}'], [$title, $value], $template));
    }

    protected function showWrapper($html)
    {
        $template = config('suitcorecms.templates.show_wrapper');

        return new HtmlString(str_replace('{html}', $html, $template));
    }

    public function show()
    {
        if ($this->showHtml ?? false) {
            return $this->showHtml;
        }
        $html = '';
        foreach ($fields = $this->fields('show') as $field) {
            $html .= $this->buildShow($field);
        }

        return $this->showRenderer = $this->showWrapper($html);
    }

    public function showJavascript()
    {
        if ($this->showJavascript ?? false) {
            return $this->showJavascript;
        }
        $javascript = [];
        foreach ($fields = $this->fields('show') as $field) {
            $key = $field['name'];
            if (!isset($javascript[$key])) {
                if ($js = $field['showJavascript'] ?? false) {
                    $javascript[$key] = $js();
                }
            }
        }

        return $this->showJavascript = implode("\n", $javascript);
    }

    public function showAsArray()
    {
        $array = [];
        foreach ($fields = $this->fields('show') as $field) {
            $title = $field['title'];
            $value = $this->getShowValue($field);
            $array[$title] = $value;
        }

        return $array;
    }
}
