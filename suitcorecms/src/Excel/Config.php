<?php

namespace Suitcorecms\Excel;

use Suitcorecms\Excel\Migrations\CreateExcelExportsTable;

class Config
{
    protected static function config()
    {
        return [
            'driver'                  => 'sqlite',
            'database'                => env('EXCEL_DATABASE', database_path('excel_database.sqlite')),
            'prefix'                  => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ];
    }

    public static function setDatabaseConfig()
    {
        if (config('database.connections.excel_sqlite') === null) {
            $dbConfig = static::config();
            config(['database.connections.excel_sqlite' => $dbConfig]);
            if (!file_exists($file = $dbConfig['database'])) {
                file_put_contents($file, null);
            }
            CreateExcelExportsTable::up();
        }
    }
}
