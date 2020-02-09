<?php

namespace App\Http\Controllers\Cms;

class ProjectController extends Controller
{
    public function fields($method)
    {
        return [
            'Nama' => [
                'name' => 'name',
                'type' => 'text',
            ],
            'Tanggal Mulai' => [
                'name' => 'start_date',
                'type' => 'date',
            ],
            'Tanggal Berakhir' => [
                'name' => 'end_date',
                'type' => 'date',
            ],
            'Total Hari' => [
                'name' => 'plan_day_total',
                'type' => 'number',
            ],
            'Total Budget' => [
                'name' => 'plan_budget_total',
                'type' => 'number',
            ],
        ];
    }
}
