<?php

namespace App\Http\Controllers\Cms;

class RoleController extends Controller
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
