<?php

namespace App\Models;

use Illuminate\Validation\Rule;

class Task extends Model
{
    protected $baseName = 'Pekerjaan';

    protected $fillable = [
        'division_id',
        'code',
        'name',
        'unit',
        'cost_per_unit',
    ];

    public function rules($method)
    {
        return [
            'division_id' => 'required',
            'code' => 'required',
            'name' => [
                'required',
                $method == 'create' ? Rule::unique('tasks') : Rule::unique('tasks')->ignore($this->id),
            ],
            'unit' => 'required',
            'name' => 'required',
            'cost_per_unit' => 'required',
        ];
    }
}
