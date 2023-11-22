<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAxieSoldHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('axie_sold_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('axie_id')->index()->comment('AXIE ID');
            $table->string('class')->comment('品种');
            $table->integer('breed_count')->comment('繁殖次数');
            $table->boolean('is_origin', 64)->comment('是否是Origin');
            $table->boolean('is_mystic')->comment('是否是神秘');
            $table->integer('japan_part_count')->default(0)->comment('japan部位数量');
            $table->integer('xmas_part_count')->default(0)->comment('xmas部位数量');
            $table->integer('summer_part_count')->default(0)->comment('summer部位数量');
            $table->integer('axp_level')->comment('AXP等级');
            $table->bigInteger('price')->comment('交易价格');
            $table->string('price_usd')->comment('交易USD价格');
            $table->string('trans_hash')->comment('交易哈希');
            $table->dateTime('trans_time')->comment('交易时间');
            $table->string('from')->comment('卖方地址');
            $table->string('to')->comment('买方地址');
            $table->string('eyes_part_id', 32)->nullable();
            $table->string('eyes_part_name', 32)->nullable();
            $table->string('ears_part_id', 32)->nullable();
            $table->string('ears_part_name', 32)->nullable();
            $table->string('horn_part_id', 32)->nullable();
            $table->string('horn_part_name', 32)->nullable();
            $table->string('mouth_part_id', 32)->nullable();
            $table->string('mouth_part_name', 32)->nullable();
            $table->string('back_part_id', 32)->nullable();
            $table->string('back_part_name', 32)->nullable();
            $table->string('tail_part_id', 32)->nullable();
            $table->string('tail_part_name', 32)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('axie_sold_histories');
    }
}
