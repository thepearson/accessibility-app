<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrlScanResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('url_scan_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('url_scan_id');
            $table->foreignId('rule_id');
            $table->enum('result', ['passes', 'violations', 'inapplicable', 'incomplete']);
            $table->enum('impact', ['minor', 'moderate', 'serious', 'critical']);
            $table->text('html');
            $table->text('message');
            $table->foreign('url_scan_id')
                ->references('id')
                ->on('url_scans');
            $table->foreign('rule_id')
                ->references('id')
                ->on('rules');
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
        Schema::dropIfExists('url_scan_results');
    }
}
