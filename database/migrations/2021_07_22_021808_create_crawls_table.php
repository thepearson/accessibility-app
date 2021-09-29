<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrawlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crawls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id');
            $table->string('token');
            $table->json('data');

            $table->json('messages')->nullable(true)->default(null);
            $table->integer('total')->nullable(true)->default(null);
            $table->integer('complete')->nullable(true)->default(null);
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
        Schema::dropIfExists('crawls');
    }
}
