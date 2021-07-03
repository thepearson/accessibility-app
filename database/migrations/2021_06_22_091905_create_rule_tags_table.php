<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRuleTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rule_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tags_id');
            $table->foreignId('rules_id');
            $table->timestamps();

            $table->foreign('tags_id')
                ->references('id')
                ->on('tags');

            $table->foreign('rules_id')
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
