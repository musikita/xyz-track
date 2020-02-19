<?php

namespace Suitcorecms\Excel\Exports;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ExportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    protected $jobId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ExportModel $model)
    {
        $this->jobId = $model->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle($download = false, $fallback = 'create')
    {
        if ($job = ExportModel::find($this->jobId)) {
            $controller = app($job->controller);
            $datas = new Collection([]);
            foreach ($controller->exportExcelBaseQuery($job->parameters)->get() as $model) {
                $instance = clone $controller;
                $instance->show($model->getKey());

                try {
                    $datas->push($instance->getPrepedResource('show')->showAsArray());
                } catch (\Exception $e) {
                    //
                }
            }
            $headers = $datas->count() ? array_keys($datas->first()) : null;
            if (!$headers) {
                $headers = [];
                $instance = clone $controller;
                $instance->{$fallback}();
                $fields = $instance->getPrepedResource($fallback)->fields();
                foreach ($fields as $field) {
                    if ($field['type'] != 'hidden') {
                        $headers[] = $field['title'];
                    }
                }
            }

            $collection = (new \Suitcorecms\Excel\Exports\Collection())->setExportHeader($headers)->setExportData($datas);
            if ($download) {
                $job->delete();

                return Excel::download($collection, $job->file);
            }

            Excel::store($collection, 'excel_exports/'.$job->file);
            $job->finished_at = Carbon::now();
            $job->save();
        }
    }
}
