<?php

namespace Suitcorecms\Cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

trait BulkDeleteTrait
{
    public static function bulkDeleteTraitRoute()
    {
        Route::delete('bulk-delete', [static::class, 'bulkDelete'])->name('bulk-delete');
    }

    public function bulkDelete(Request $request)
    {
        $resource = $this->prepResource('create');
        $ids = explode(',', $request->get('deleted_ids'));
        $resource->whereIn('id', $ids)->get()->each(function ($data) {
            $data->delete();
        });

        return $this->redirect($resource);
    }
}
