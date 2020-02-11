<?php

namespace Suitcorecms\Seo;

use BadMethodCallException;
use Collective\Html\HtmlBuilder;
use Illuminate\Support\Arr;

class SeoTool
{
    protected static $title;
    protected static $seoTags = [];
    protected static $render;

    protected $html;
    protected $config;

    public function __construct(HtmlBuilder $html)
    {
        $this->html = $html;
        $this->config = config('suitcorecms.seo', []);
        $this->create();
    }

    public function title()
    {
        return static::$title;
    }

    public function meta()
    {
        if (static::$render) {
            return static::$render;
        }

        $this->create();
        $meta = '';
        foreach (static::$seoTags as $tag) {
            extract($tag);
            $meta .= $this->html->meta($name, $content, $attributes ?? [])."\n";
        }

        return static::$render = $meta;
    }

    public function setRawMeta(array $meta = [])
    {
        return $this->readRawData($meta);
    }

    public function setTitle($title)
    {
        static::$title = $title;

        return $this;
    }

    public function setMeta($name, $content, array $attributes = [])
    {
        static::$render = null;
        static::$seoTags[$name] = array_merge(compact('name', 'content'), compact('attributes'));

        return $this;
    }

    public function setOg($key, $value)
    {
        $name = null;
        $property = strpos($key, ':') === false ? 'og:'.$key : $key;
        $content = $value;

        return $this->setMeta($property, $content, compact('property', 'name'));
    }

    public function setFacebook($key, $value)
    {
        return $this->setOg($key, $value);
    }

    public function setTwitter($key, $value)
    {
        if ($key != 'card' && !array_key_exists('twitter:card', static::$seoTags)) {
            $this->setMeta('twitter:card', 'summary');
        }
        $name = 'twitter:'.str_replace('twitter:', '', $key);
        $content = $value;

        return $this->setMeta($name, $content);
    }

    public function create()
    {
        if (count(static::$seoTags) == 0) {
            $this->readConfig();
        }

        foreach (config('suitcorecms.seo.duplication', []) as $key => $value) {
            if (!isset(static::$seoTags[$key]) || static::$seoTags[$key]['content'] == null) {
                if ($content = $value == 'title' ? static::$title : (static::$seoTags[$value]['content'] ?? null)) {
                    $slices = explode(':', $key);
                    $method = $slices[0] == 'og' ? 'setOg' : ($slices[0] == 'twitter' ? 'setTwitter' : 'setMeta');
                    $this->{$method}($key, $content);
                }
            }
        }

        ksort(static::$seoTags);

        return $this;
    }

    protected function processValue($value)
    {
        return is_array($value) && is_callable($value) ? call_user_func($value) : $value;
    }

    protected function readRawData(array $meta = [])
    {
        $this->setTitle($meta['title'] ?? '');
        if ($og = $meta['og'] ?? false) {
            if (is_array($og)) {
                foreach ($og as $key => $value) {
                    $this->setOg($key, $this->processValue($value));
                }
            }
        }
        if ($twitter = $meta['twitter'] ?? false) {
            if (is_array($twitter)) {
                foreach ($twitter as $key => $value) {
                    $this->setTwitter($key, $this->processValue($value));
                }
            }
        }
        foreach (Arr::except($meta, ['title', 'og', 'twitter']) as $key => $value) {
            $this->setMeta($key, $this->processValue($value));
        }

        return $this;
    }

    protected function readConfig()
    {
        $meta = $this->config['meta'] ?? [];

        return $this->readRawData($meta);
    }

    public static function __callStatic($method, $params)
    {
        $instance = app(static::class);
        if (method_exists($instance, $method)) {
            return call_user_func_array([$instance, $method], $params);
        }

        throw new BadMethodCallException();
    }
}
