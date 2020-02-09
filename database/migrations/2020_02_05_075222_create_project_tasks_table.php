<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('division_id');
            $table->unsignedTinyInteger('segment');
            $table->string('code');
            $table->string('name');
            $table->string('unit');
            $table->unsignedInteger('value');
            $table->unsignedBigInteger('cost_per_unit');
            $table->unsignedBigInteger('total_cost');
            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')
                ->on('projects');

            $table->foreign('division_id')
                ->references('id')
                ->on('divisions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_tasks');
    }
}
