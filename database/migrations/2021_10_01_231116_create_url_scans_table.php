<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrlScansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('url_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scan_id');
            $table->foreignId('url_id');   
            $table->enum('status', ['queued', 'processing', 'success', 'failed']);
            $table->string('token');

            $table->json('data');
            $table->json('messages');
            $table->timestamps();
            $table->foreign('scan_id')
                ->references('id')
                ->on('scans');
            $table->foreign('url_id')
                ->references('id')
                ->on('urls');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('url_scans');
    }
}
