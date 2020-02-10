<?php

namespace App\Models;

class Project extends Model
{
    protected $baseName = 'Proyek';

    protected $fillable = [
        'name',
        'province',
        'city',
        'start_date',
        'end_date',
        'divisions',
        'total_segment',
        'plan_total_day',
        'plan_total_budget',
        'real_total_day',
        'real_total_task',
        'real_total_cost',
        'total_member',
        'status',
    ];

    protected $dates = [
        'start_date', 'end_date',
    ];

    protected $casts = [
        'divisions' => 'array',
    ];

    public function rules($method)
    {
        return [
            'name' => 'required',
            'province' => 'required',
            'city' => 'required',
            'start_date' => 'required',
            'divisions' => 'required',
            'total_segment' => 'required',
            'plan_total_day' => 'required',
            'plan_total_budget' => 'required',
        ];
    }
}
