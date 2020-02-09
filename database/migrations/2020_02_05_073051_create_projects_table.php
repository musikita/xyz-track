<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('province');
            $table->string('city');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->json('divisions');
            $table->unsignedTinyInteger('total_segment');
            $table->unsignedInteger('plan_total_day');
            $table->unsignedBigInteger('plan_total_budget');
            $table->unsignedInteger('real_total_day')->nullable();
            $table->unsignedInteger('real_total_task')->nullable();
            $table->unsignedBigInteger('real_total_cost')->nullable();
            $table->unsignedBigInteger('total_member')->nullable();
            $table->string('status', 15)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
