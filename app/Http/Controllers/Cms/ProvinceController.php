<?php

namespace App\Http\Controllers\Cms;

use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function fields()
    {
        return [
            'Nama' => [
                'name' => 'name',
                'type' => 'text',
            ],
        ];
    }
}
