<?php

namespace Suitcorecms\Excel\Imports;

use Illuminate\Support\Facades\Route;

trait ImportTrait
{
    public static function importTraitRoute($slug = 'import')
    {
        Route::get($slug, [static::class, 'getImport'])->name('import');
        Route::post($slug, [static::class, 'postImport'])->name('import-post');
    }

    public function getImport()
    {
        $resource = $this->prepResource('create')->dontUseSeo()->setFields($this->importFormFields());
        $resource->form()->setActionUrl($resource->route('import-post'));
        $this->showBreadcrumb($resource, [null => 'Import']);

        return $this->view(compact('resource'));
    }

    public function postImport($file = null, $sheet = null)
    {
        ini_set('max_execution_time', 0);
        $resource = $this->prepResource('create')->setRules($this->importFormRules())->setFields($this->importFormFields());
        $this->validate(request(), $this->importFormRules());
        ControllerDataImporter::process($this, $file ?? request('file'), $sheet ?? $resource->getName());

        $this
            ->message()
            ->flash('Import is Succeed.')
            ->success();

        return redirect($resource->routeIndex());
    }

    public function importFormFields()
    {
        return [
            'File' => [
                'type' => 'file',
            ],
        ];
    }

    public function importFormRules()
    {
        return [
            'file' => 'required',
        ];
    }
}
