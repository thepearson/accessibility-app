<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id');
            $table->string('token');
            $table->enum('type', ['crawl', 'scan']);
            $table->json('data');
            $table->enum('status', ['queued', 'processing', 'success', 'failed']);
            $table->timestamps();
            $table->foreign('website_id')
                ->references('id')
                ->on('websites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
