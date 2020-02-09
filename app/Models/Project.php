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
}
