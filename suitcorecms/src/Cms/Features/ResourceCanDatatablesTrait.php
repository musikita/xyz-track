<?php

namespace Suitcorecms\Cms\Features;

use Illuminate\Support\Facades\Route;

trait ResourceCanDatatablesTrait
{
    public static function resourceCanDatatablesTraitRoute()
    {
        Route::get('datatables', [static::class, 'getDatatablesJson'])->name('datatables');
    }

    public function getDatatablesJson()
    {
        return 'Fuck';
    }
}
