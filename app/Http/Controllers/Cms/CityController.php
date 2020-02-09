<?php

namespace App\Http\Controllers\Cms;

use App\Models\Province;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function fields()
    {
        return [
            'Propinsi' => [
                'name'      => 'province_id',
                'type'      => 'select2',
                'options'   => [$this, 'getProvinceOptions'],
            ],
            'Nama' => [
                'name' => 'name',
                'type' => 'text',
            ],
        ];
    }

    public function getProvinceOptions()
    {
        return Province::get()->pluck('name', 'id')->toArray();
    }
}
