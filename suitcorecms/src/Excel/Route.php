<?php

namespace Suitcorecms\Excel;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as FacadesRoute;
use Suitcorecms\Excel\Exports\ExportModel;

class Route
{
    public static function routes($prefix = 'excel', $routeName = 'excel.checker')
    {
        if (!defined('EXCEL_CHECKER_ROUTE')) {
            define('EXCEL_CHECKER_ROUTE', $routeName);
        }
        FacadesRoute::get($prefix.'/checker', [static::class, 'checker'])->name(EXCEL_CHECKER_ROUTE);
    }

    public function checker(Request $request)
    {
        Config::setDatabaseConfig();

        if ($file = $request->query('file')) {
            return $this->download($file);
        }

        return [
            'export' => ExportModel::whereNotNull('finished_at')->whereNull('downloaded_at')->get()->map(function ($job) {
                $job->update(['downloaded_at' => Carbon::now()]);

                return [
                    'url'      => route('cms.'.EXCEL_CHECKER_ROUTE, ['file' => $job->file]),
                    'filename' => $job->file,
                ];
            })->toArray(),
        ];
    }

    public function download($file)
    {
        return response()->download(storage_path('app/excel_exports/'.$file), $file);
    }
}
