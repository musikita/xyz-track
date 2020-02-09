<?php

namespace App\Models;

use Illuminate\Validation\Rule;

class Division extends Model
{
    protected $baseName = 'Divisi';

    protected $fillable = ['number', 'name'];

    public function rules($method)
    {
        return [
            'number' => [
                'required',
                $method == 'create' ? Rule::unique('divisions') : Rule::unique('divisions')->ignore($this->id),
            ],
            'name' => [
                'required',
                $method == 'create' ? Rule::unique('divisions') : Rule::unique('divisions')->ignore($this->id),
            ],
        ];
    }
}
