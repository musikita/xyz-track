<?php

namespace App\Models;

use Illuminate\Validation\Rule;

class Role extends Model
{
    protected $baseName = 'Role';

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
