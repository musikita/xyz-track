<?php

namespace App\Models;

use Illuminate\Validation\Rule;

class City extends Model
{
    protected $baseName = 'Kota';

    protected $fillable = ['province_id', 'name'];

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function rules($method, $request)
    {
        $provinceId = $request->get('province_id');
        return [
            'province_id' => 'required',
            'name' => [
                'required',
                $method == 'create' ? Rule::unique('cities')->where('province_id', $provinceId) : Rule::unique('cities')->ignore($this->id)->where('province_id', $provinceId),
            ],
        ];
    }
}
