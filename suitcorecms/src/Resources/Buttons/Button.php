<?php

namespace Suitcorecms\Resources\Buttons;

use Illuminate\Support\Str;

class Button
{
    protected $resourceable;
    protected $name;
    protected $attributes = [];
    protected $caption;
    protected $url;
    protected $target;
    protected $onclick;
    protected $javascript;
    protected $icon;
    protected $beforeHtml;
    protected $afterHtml;
    protected $hidden = false;

    public function __construct($caption)
    {
        $this->caption = $caption;
        $this->name = Str::slug($caption);
    }

    public function setResourceable($resourceable)
    {
        $this->resourceable = $resourceable;

        return $this;
    }

    public function getCaption()
    {
        return $this->getCalled($this->caption);
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function url($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl()
    {
        return $this->getCalled($this->url);
    }

    public function target($target)
    {
        $this->target = $target;

        return $this;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function onclick($onclick)
    {
        $this->onclick = $onclick;

        return $this;
    }

    public function getOnclick()
    {
        return $this->getCalled($this->onclick);
    }

    public function javascript($javascript)
    {
        $this->javascript = $javascript;

        return $this;
    }

    public function showJavascript()
    {
        return $this->getCalled($this->javascript);
    }

    public function icon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon()
    {
        return $this->getCalled($this->icon);
    }

    public function beforeHtml($beforeHtml)
    {
        $this->beforeHtml = $beforeHtml;

        return $this;
    }

    public function getBeforeHtml()
    {
        return $this->getCalled($this->beforeHtml);
    }

    public function afterHtml($afterHtml)
    {
        $this->afterHtml = $afterHtml;

        return $this;
    }

    public function getAfterHtml()
    {
        return $this->getCalled($this->afterHtml);
    }

    public function getCalled($key)
    {
        return is_callable($key) ? call_user_func_array($key, [$this->resourceable, $this]) : $key;
    }

    protected function build(array $attributes = [])
    {
        extract($attributes);
        $content = $content ?? $icon.' '.$text;
        $tagAttributes = [];
        collect($attributes ?? [])->each(function ($val, $key) use (&$tagAttributes) {
            $tagAttributes[] = $key.'="'.$val.'"';
        });
        $attributes = implode(' ', $tagAttributes);

        return "{$this->getBeforeHtml()}<{$tagS} class=\"{$class}\" {$attributes} {$onclick}>{$content}</{$tagE}>{$this->getafterHtml()}";
    }

    public function show(array $attributes = [])
    {
        $url = $this->getUrl();
        $target = $this->getTarget() ? 'target="'.$this->getTarget().'"' : '';
        $tagS = $url ? 'a href="'.$url.'" '.$target : 'button';
        $tagE = $url ? 'a' : 'button';
        $icon = $this->icon ? '<i class="'.$this->icon.'"></i>' : '';
        $onclick = $this->getOnClick();
        $text = $attributes['text'] ?? $this->getCaption();
        $content = $attributes['content'] ?? null;
        $class = $attributes['class'] ?? 'btn';
        $attributes['tagS'] = $tagS;
        $attributes['tagE'] = $tagE;
        $attributes['icon'] = $icon;
        $attributes['onclick'] = $onclick ? 'onclick="'.$onclick.'"' : null;
        $attributes['text'] = is_callable($text) ? call_user_func_array($text, [$this]) : $text;
        $attributes['content'] = is_callable($content) ? call_user_func_array($content, [$this]) : $content;
        $attributes['class'] = $class;
        $attributes['attributes'] = array_replace($this->attributes, $attributes['attributes'] ?? []);

        return $this->build($attributes);
    }

    public function hideInIndex($bool = true)
    {
        $this->hidden['index'] = $bool;

        return $this;
    }

    public function hideInCreate($bool = true)
    {
        $this->hidden['create'] = $bool;

        return $this;
    }

    public function hideInShow($bool = true)
    {
        $this->hidden['show'] = $bool;

        return $this;
    }

    public function __get($key)
    {
        if (strpos($key, 'showIn') === 0) {
            $method = strtolower(str_replace('showIn', '', $key));

            $hidden = $this->hidden[$method] ?? false;

            return $this->getCalled($hidden) === false;
        }

        return null;
    }
}
