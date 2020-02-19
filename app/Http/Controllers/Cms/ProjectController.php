<?php

namespace App\Http\Controllers\Cms;

use App\Models\City;
use App\Models\Division;
use App\Models\Province;

class ProjectController extends Controller
{
    public function show($id)
    {
        $resource = $this->prepResource('show', $id);
        $project_id = $id;
        $model = $resource->getResourceable();

        $resource
            ->addChild(app(ProjectTaskController::class)->setBaseRoute('cms.project.task')->setDefaultQueryParameters(compact('project_id'))->prepResource('index'));

        $this->showBreadcrumb($resource, [null => $resource->getCaption()]);

        return $this->view(compact('resource'));
    }

    public function fields($method)
    {
        return [
            'Nama' => [
                'name' => 'name',
                'type' => 'text',
            ],
            'Propinsi' => [
                'name' => 'province',
                'type' => 'select2',
                'options' => [$this, 'getProvinceOptions'],
            ],
            'Kab / Kota' => [
                'name' => 'city',
                'type' => 'select2',
                'options' => [$this, 'getCityOptions'],
            ],
            'Tanggal Mulai' => [
                'name' => 'start_date',
                'type' => 'date',
            ],
            'Divisi' => [
                'name' => 'divisions',
                'type' => 'multipleselect2',
                'options' => [$this, 'getDivisionOptions'],
            ],
            'Total Segment' => [
                'name' => 'total_segment',
                'type' => 'number',
                'attributes' => [
                    'min' => 1,
                    'step' => 1,
                ],
            ],
            'Total Hari' => [
                'name' => 'plan_total_day',
                'type' => 'number',
            ],
            'Total Budget' => [
                'name' => 'plan_total_budget',
                'type' => 'number',
            ],
        ];
    }

    public function getProvinceOptions()
    {
        return Province::get()->pluck('name', 'name')->toArray();
    }

    public function getCityOptions()
    {
        return City::get()->pluck('name', 'name')->toArray();
    }

    public function getDivisionOptions()
    {
        return Division::get()->map(function ($item) {
            $item->name = $item->number.'. '.$item->name;
            return $item;
        })->pluck('name', 'number')->toArray();
    }
}
