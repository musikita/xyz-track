<?php

namespace App\Models;

use Illuminate\Validation\Rule;

class Province extends Model
{
    protected $baseName = 'Propinsi';

    protected $fillable = ['name'];

    public function rules($method)
    {
        return [
            'name' => [
                'required',
                $method == 'create' ? Rule::unique('provinces') : Rule::unique('provinces')->ignore($this->id),
            ],
        ];
    }
}
