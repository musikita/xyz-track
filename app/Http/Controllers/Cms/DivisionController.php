<?php

namespace App\Http\Controllers\Cms;

class DivisionController extends Controller
{
    public function fields()
    {
        return [
            'Nomor' => [
                'name' => 'number',
                'type' => 'number',
                'attributes' => [
                    'min' => '1',
                ],
            ],
            'Nama' => [
                'name' => 'name',
                'type' => 'text',
            ],
        ];
    }
}
