<?php

namespace Suitcorecms\Resources;

use Illuminate\Support\Facades\Route;

trait ResourceUrlTrait
{
    protected $baseRoute;

    protected $defaultQueryParameters;

    public function setBaseRoute($route)
    {
        $this->baseRoute = $route;

        return $this;
    }

    public function setDefaultQueryParameters(array $params = [])
    {
        $this->defaultQueryParameters = $params;

        return $this;
    }

    public function baseRoute()
    {
        return $this->baseRoute ?? cms_base_route();
    }

    public function routeExist($name)
    {
        $route = $this->baseRoute().'.'.$name;

        return Route::has($route);
    }

    public function route($name, $parameters = [], array $queryString = [], $reset = false)
    {
        if (!$reset) {
            $queryString = array_merge(request()->query(), $queryString);
        }
        list($url, $query) = explode('?', route($this->baseRoute().'.'.$name, $parameters).'?');
        $query = trim($query.'&'.http_build_query(array_merge($this->defaultQueryParameters ?? [], $queryString)), '&');

        return rtrim($url.'?'.$query, '?');
    }

    public function routeIndex($query = [])
    {
        return $this->route('index', null, $query, true);
    }

    public function routeCreate($query = [])
    {
        return $this->route('create', null, $query, true);
    }

    public function routeStore($query = [])
    {
        return $this->route('store', null, $query, true);
    }

    public function routeShow($id = null, $query = [])
    {
        return $this->route('show', $id ?? $this->getKey(), $query, true);
    }

    public function routeEdit($id = null)
    {
        return $this->route('edit', $id ?? $this->getKey(), [], true);
    }

    public function routeUpdate($id = null)
    {
        return $this->route('update', $id ?? $this->getKey(), [], true);
    }

    public function routeDestroy($id = null)
    {
        return $this->route('destroy', $id ?? $this->getKey(), [], true);
    }
}
