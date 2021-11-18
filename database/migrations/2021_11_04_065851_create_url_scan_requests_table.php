<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrlScanRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('url_scan_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scan_id')->constrained('scans')->cascadeOnDelete(); 
            $table->foreignId('url_scan_id')->constrained('url_scans')->cascadeOnDelete();
            $table->foreignId('url_id')->constrained('urls')->cascadeOnDelete(); 
            $table->string('uri', 4096);
            $table->string('mime');
            $table->enum('category', ['image', 'document', 'script', 'style', 'font', 'video', 'audio', 'other'])->default('other');
            $table->integer('status')->default(200);
            $table->float('duration', 8, 2)->default(0.00);
            $table->bigInteger('size')->default(0);
            $table->string('protocol');
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
        Schema::dropIfExists('url_scan_requests');
    }
}
