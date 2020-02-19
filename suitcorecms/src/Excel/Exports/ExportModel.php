<?php

namespace Suitcorecms\Excel\Exports;

use Illuminate\Database\Eloquent\Model;

class ExportModel extends Model
{
    protected $connection = 'excel_sqlite';

    protected $table = 'excel_exports';

    protected $fillable = [
        'user_id',
        'controller',
        'parameters',
        'file',
        'file',
        'estimated',
        'counter',
        'finished_at',
        'downloaded_at',
    ];

    protected $dates = [
        'finished_at',
        'downloaded_at',
    ];

    protected $casts = [
        'parameters' => 'array',
    ];
}
