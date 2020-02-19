<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewslettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $templateTable = config('suitcoresite.newsletters.template_table');
        $newsletterTable = config('suitcoresite.newsletters.table');
        $transportTable = config('suitcoresite.newsletters.transport_table');

        Schema::create($templateTable, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->mediumText('html');
            $table->timestamps();
        });

        Schema::create($newsletterTable, function (Blueprint $table) use ($templateTable) {
            $table->bigIncrements('id');
            $table->unsignedInteger('template_id');
            $table->string('title');
            $table->mediumText('content');
            $table->dateTime('published_at');
            $table->boolean('is_draft');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('template_id')
                ->references('id')
                ->on($templateTable);
        });

        Schema::create($transportTable, function (Blueprint $table) use ($newsletterTable) {
            $table->bigIncrements('id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('newsletter_id');
            $table->string('email', 50);
            $table->string('status', 7);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('newsletter_id')
                ->references('id')
                ->on($newsletterTable);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $templateTable = config('suitcoresite.newsletters.template_table');
        $newsletterTable = config('suitcoresite.newsletters.table');
        $transportTable = config('suitcoresite.newsletters.transport_table');

        Schema::dropIfExists($transportTable);
        Schema::dropIfExists($newsletterTable);
        Schema::dropIfExists($templateTable);
    }
}
