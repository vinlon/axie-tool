<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAxieBodyPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('axie_body_parts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('version')->comment('版本');
            $table->integer('origin_card_id')->index()->comment('Origin Card ID');
            $table->string('part_id', 32)->index()->comment('部位ID');
            $table->string('part_type', 16)->comment('部位类型');
            $table->string('cls', 16)->comment('种族');
            $table->string('part_name', 32)->comment('部位名称');
            $table->string('ability_type', 16)->comment('能力类型');
            $table->text('description')->comment('能力描述');
            $table->integer('energy')->comment('能量消耗');
            $table->integer('attack')->comment('攻击');
            $table->integer('defense')->comment('护盾');
            $table->integer('healing')->comment('治疗');
            $table->string('special_genes', 16)->comment('特殊基因');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('axie_body_parts');
    }
}
