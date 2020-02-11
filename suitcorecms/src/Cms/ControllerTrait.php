<?php

namespace Suitcorecms\Cms;

trait ControllerTrait
{
    protected $name;
    protected $baseRoute;
    protected $defaultQueryParameters = [];
    protected $datatablesAjaxUrl;

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    protected function indexResourceable()
    {
        return $this->baseResourceable();
    }

    protected function createResourceable()
    {
        return $this->baseResourceable();
    }

    protected function updateResourceable($id)
    {
        $resource = $this->baseResourceable();

        return $resource->findOrFail($id);
    }

    protected function showResourceable($id)
    {
        $resource = $this->baseResourceable();

        return $resource->findOrFail($id);
    }

    protected function deleteResourceable($id)
    {
        $resource = $this->baseResourceable();

        return $resource->findOrFail($id);
    }

    protected function baseRoute()
    {
        if ($this->baseRoute ?? false) {
            return $this->baseRoute;
        }

        return $this->baseRoute = cms_base_route();
    }

    public function setBaseRoute($baseRoute)
    {
        $this->baseRoute = $baseRoute;

        return $this;
    }

    public function setDefaultQueryParameters(array $params = [])
    {
        $this->defaultQueryParameters = $params;

        return $this;
    }

    protected function indexUrl()
    {
        $params = $this->defaultQueryParameters;

        return route($this->baseRoute().'.index', $params);
    }

    protected function editUrl($id)
    {
        $params = array_merge($this->defaultQueryParameters, compact('id'));

        return route($this->baseRoute().'.edit', $params);
    }

    protected function showUrl($id)
    {
        $params = array_merge($this->defaultQueryParameters, compact('id'));

        return route($this->baseRoute().'.show', $params);
    }

    protected function createFormUrl()
    {
        $params = array_merge($this->defaultQueryParameters, compact('id'));

        return route($this->baseRoute().'.store', $params);
    }

    protected function updateFormUrl($id)
    {
        $params = array_merge($this->defaultQueryParameters, compact('id'));

        return route($this->baseRoute().'.update', $params);
    }

    public function setDatatablesAjaxUrl($url)
    {
        $this->datatablesAjaxUrl = $url;

        return $this;
    }

    public function getDatatablesUrl()
    {
        return $this->datatablesAjaxUrl ?? $this->indexUrl();
    }

    protected function defaultTimestamp()
    {
        return [
            // 'Created At' => [
            //     'on_form' => false,
            //     'on_show' => false,
            // ],
            // 'Updated At' => [
            //     'on_form' => false,
            //     'on_show' => false,
            // ],
        ];
    }

    protected function registerObserver()
    {
        //
    }
}
