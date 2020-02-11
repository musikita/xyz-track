<?php

namespace Suitcorecms\Excel\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Collection implements FromCollection, WithHeadings
{
    protected $exportHeader;
    protected $exportCollection;

    public function setExportHeader($header)
    {
        $this->exportHeader = $header;

        return $this;
    }

    public function setExportData($datas)
    {
        $this->exportCollection = $datas;

        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->exportCollection;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return $this->exportHeader;
    }
}
