<?php

namespace Suitcorecms\Excel\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Suitcorecms\Excel\Config;

class CreateExcelExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public static function up()
    {
        if (!Schema::connection('excel_sqlite')->hasTable('excel_exports')) {
            Schema::connection('excel_sqlite')->create('excel_exports', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('controller');
                $table->json('parameters');
                $table->string('file');
                $table->unsignedInteger('estimated')->default(0);
                $table->unsignedInteger('counter')->default(0);
                $table->dateTime('finished_at')->nullable();
                $table->dateTime('downloaded_at')->nullable();
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
        Schema::connection('excel_sqlite')->dropIfExists('excel_exports');
    }
}
