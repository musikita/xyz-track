<?php

namespace Suitcorecms\Cms;

use BadMethodCallException;
use Illuminate\Support\Facades\Route as LaravelRoute;
use Illuminate\Support\Str;
use ReflectionClass;

class Route
{
    protected $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    protected static function controllerName($controller)
    {
        $basename = basename(str_replace('\\', '/', $controller));
        $namespace = str_replace($basename, '', $controller);
        if ($namespace == '') {
            $namespace = rtrim(config('suitcorecms.namespace'), '\\').'\\';
        }

        return '\\'.$namespace.$basename;
    }

    protected static function crawlRoutes($controller)
    {
        $reflection = new ReflectionClass($controller);
        $class = $controller;
        $traits = [];
        do {
            $traits = array_merge(class_uses($class, true), $traits);
        } while ($class = get_parent_class($class));
        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, true), $traits);
        }
        $traits = array_unique($traits);
        foreach ($traits as $trait) {
            $method = Str::camel(basename(str_replace('\\', '/', $trait))).'Route';
            if (method_exists($controller, $method)) {
                call_user_func_array([$controller, $method], []);
            }
        }
        if (method_exists($controller, 'additionalRoute')) {
            call_user_func_array([$controller, 'additionalRoute'], []);
        }
    }

    public static function resource($uri, $controller, array $attributes = [])
    {
        $controller = static::controllerName($controller);
        $resource = LaravelRoute::resource($uri, $controller, $attributes);
        $name = $attributes['name'] = $attributes['name'] ?? str_replace('/', '.', Str::snake($uri));
        LaravelRoute::name($name.'.')
            ->prefix($uri)
            ->group(function () use ($controller) {
                static::crawlRoutes($controller);
            });

        return new static($resource);
    }

    public function __call($method, $args)
    {
        if (method_exists($this->resource, $method)) {
            return call_user_func_array([$this->resource, $method], $args);
        }

        throw new BadMethodCallException();
    }
}
