<?php

namespace Suitcorecms\Excel\Exports;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

trait ExportCsvTrait
{
    protected $exportCsvFileName;

    public static function exportCsvTraitRoute()
    {
        Route::get('export/csv', [static::class, 'getExportCsv'])->name('export-csv');
    }

    public function getExportCsv()
    {
        set_time_limit(0);
        $collection = (new Collection())->setExportHeader($this->exportCsvHeader())->setExportData($this->exportCsvData());

        return Excel::download($collection, $this->exportCsvFileName());
    }

    protected function exportCsvData()
    {
        $data = $this->baseResourceable()->get();

        return $data->map(function ($item) {
            $data = $item->toArray();
            $new = [];
            foreach ($this->exportCsvHeader() as $header) {
                $new[$header] = $data[$header] ?? null;
            }

            return $new;
        });
    }

    protected function exportCsvHeader()
    {
        if ($this->exportCsvHeader ?? false) {
            return $this->exportCsvHeader;
        }
        $r = $this->baseResourceable();
        $items = array_merge([$r->getKeyName()], property_exists(get_class($r), 'slugBy') ? ['slug'] : [], array_values(array_sort(array_keys($r->toArray()))), $r->getDates());

        return array_filter($items, function ($item) {
            return $item && $item != 'translations';
        });
    }

    protected function exportCsvFileName()
    {
        return $this->exportCsvFileName ?? Str::snake($this->baseResourceable()->getName().'.csv');
    }
}
