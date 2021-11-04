<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrlScanAccessibilityResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('url_scan_accessibility_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scan_id')->constrained('scans')->cascadeOnDelete(); 
            $table->foreignId('url_scan_id')->constrained('url_scans')->cascadeOnDelete();
            $table->foreignId('url_id')->constrained('urls')->cascadeOnDelete(); 
            $table->foreignId('rule_id')->constrained('rules');
            $table->enum('result', ['passes', 'violations', 'inapplicable', 'incomplete']);
            $table->enum('impact', ['minor', 'moderate', 'serious', 'critical']);
            $table->text('html');
            $table->text('message');
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
        Schema::dropIfExists('url_scan_accessibility_results');
    }
}
