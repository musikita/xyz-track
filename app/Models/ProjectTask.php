<?php

namespace App\Models;

use Suitcorecms\ControllerTagging\HasTaggingModelTrait;

class ProjectTask extends Model
{
    use HasTaggingModelTrait;

    protected $tagging_field = 'project_id';

    protected $baseName = 'Pekerjaan Proyek';

    protected $fillable = [
        'project_id',
        'division',
        'segment',
        'code',
        'name',
        'unit',
        'value',
        'cost_per_unit',
        'total_cost',
    ];

    public function rules($method)
    {
        return [
            'project_id' => 'required',
            'division' => 'required',
            'segment' => 'required',
            'code' => 'required',
            'name' => 'required',
            'unit' => 'required',
            'name' => 'required',
            'cost_per_unit' => 'required',
            'total_cost' => 'required',
        ];
    }
}
