<?php

namespace Suitcorecms\Cms;

trait CalendarTrait
{
    public function prepResource($method, $id = null)
    {
        $resource = parent::prepResource($method, $id);
        if ($method == 'index') {
            $resource->setChildView('suitcorecms::crud.calendar');
        }

        return $resource;
    }

    public function index()
    {
        $resource = $this->prepResource('index');
        if (request()->ajax()) {
            return $resource->calendarJson();
        }
        $this->showBreadcrumb($resource);

        return $this->view(compact('resource'));
    }
}
