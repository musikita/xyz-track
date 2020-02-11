<?php

namespace Suitcorecms\Resources;

trait ResourceChildrenTrait
{
    protected $parent = null;
    protected $children = [];
    protected $childView;
    protected $defaultChildView = 'suitcorecms::child.datatable';

    public function children()
    {
        return $this->children;
    }

    public function setParent(Resource $resource)
    {
        $this->parent = $resource;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function addChild(Resource $resource)
    {
        $name = $resource->getName();
        $this->children[$name] = $resource;
        $resource->setParent($this);

        return $this;
    }

    public function setChildView($view)
    {
        $this->childView = $view;

        return $this;
    }

    public function getChildView()
    {
        return $this->childView ?? $this->defaultChildView;
    }
}
