<?php

namespace App\Http\Controllers\Cms;

use App\Models\Division;

class TaskController extends Controller
{
    public function fields($method)
    {
        return [
            'Divisi' => [
                'name' => 'division_id',
                'type' => 'select2',
                'options' => [$this, 'getDivisionOptions'],
            ],
            'Code' => [
                'name' => 'code',
                'type' => 'text',
            ],
            'Nama' => [
                'name' => 'name',
                'type' => 'text',
            ],
            'Satuan' => [
                'name' => 'unit',
                'type' => 'text',
            ],
            'Biaya per Satuan' => [
                'name' => 'cost_per_unit',
                'type' => 'number',
                'attributes' => [
                    'step' => '.01',
                    'min' => '0',
                ],
            ],
        ];
    }

    public function getDivisionOptions()
    {
        return Division::get()->pluck('name', 'id')->toArray();
    }
}
