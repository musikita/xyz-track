<?php

namespace Suitcorecms\Excel\Exports;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Suitcorecms\Excel\Config;

trait ExportCsvTrait
{
    protected $exportCsvFileName;

    public static function exportCsvTraitRoute()
    {
        Route::get('export/csv', [static::class, 'getExportCsv'])->name('export-csv');
    }

    public function getExportCsv(Request $request)
    {
        // preparing table export
        Config::setDatabaseConfig();

        $parameters = $request->query('parameters', []);
        if (method_exists($this, 'taggingSetup')) {
            $tag = $this->tagging_id;
            $parameters[$tag] = $request->query($tag);
        }

        $job = ExportModel::create([
            'user_id'    => auth()->user()->id ?? null,
            'controller' => static::class,
            'parameters' => array_filter($parameters),
            'file'       => $this->exportCsvFileName(),
            'estimated'  => $this->exportExcelEstimated(),
        ]);
        // sending job to process
        ExportJob::dispatch($job);

        // Show message to wait
        $this->message()
            ->flash('File will automatically download when ready.')
            ->success();

        // Redirect to Index
        return $this->redirect($this->prepResource('index'));
    }

    public function exportExcelBaseQuery(array $whereClouse = [])
    {
        $baseQuery = $this->baseResourceable()->select($this->baseResourceable()->getKeyName());
        if (count($whereClouse)) {
            $baseQuery->where($whereClouse);
        }

        return $baseQuery;
    }

    protected function exportExcelEstimated()
    {
        return $this->exportExcelBaseQuery(request('parameters', []))->count();
    }

    protected function exportCsvFileName()
    {
        $unique = Carbon::now()->format('YmdHis');

        return $this->exportCsvFileName ?? Str::snake($this->baseResourceable()->getName().'-'.$unique.'.csv');
    }
}
