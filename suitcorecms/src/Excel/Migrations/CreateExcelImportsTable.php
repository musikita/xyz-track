<?php

namespace Suitcorecms\Excel\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Suitcorecms\Excel\Config;

class CreateExcelImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public static function up()
    {
        if (!Schema::connection('excel_sqlite')->hasTable('excel_imports')) {
            Schema::create('excel_imports', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id');
                $table->string('controller');
                $table->string('file');
                $table->json('error_validations');
                $table->json('failed_items');
                $table->dateTime('finished_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public static function down()
    {
        Config::setDatabaseConfig();
        Schema::connection('excel_sqlite')->dropIfExists('excel_imports');
    }
}
