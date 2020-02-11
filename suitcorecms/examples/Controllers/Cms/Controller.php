<?php

namespace App\Http\Controllers\Cms;

use App\Models\Model;
use Illuminate\Support\Str;
use Suitcorecms\Cms\BulkDeleteTrait;
use Suitcorecms\Cms\Controller as BaseController;
use Suitcorecms\Excel\Exports\ExportCsvTrait;
use Suitcorecms\Excel\Imports\ImportTrait;
use Suitcorecms\Notifications\NotificationTrait;

abstract class Controller extends BaseController
{
    use BulkDeleteTrait;
    use ExportCsvTrait;
    use ImportTrait;
    use NotificationTrait;

    protected function baseResourceable()
    {
        $name = Str::replaceLast('Controller', '', basename(str_replace('\\', '/', static::class)));
        if ($repository = $this->findRepository($name)) {
            return app($repository);
        }
        if ($model = $this->findModel($name)) {
            return app($model);
        }
    }

    protected function findRepository($name)
    {
        //
    }

    protected function findModel($name)
    {
        $model = Str::replaceLast('Model', $name, Model::class);

        return class_exists($model) ? $model : false;
    }
}
