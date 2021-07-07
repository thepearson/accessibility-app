<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRulesTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id');
            $table->foreignId('rule_id');
            $table->timestamps();

            $table->foreign('tag_id')
                ->references('id')
                ->on('tags');

            $table->foreign('rule_id')
                ->references('id')
                ->on('rules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rule_tags');
    }
}
