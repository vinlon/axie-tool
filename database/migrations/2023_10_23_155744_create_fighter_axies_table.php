<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFighterAxiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fighter_axies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('user_id', 64)->index()->comment('用户ID');
            $table->integer('team_id')->index()->comment('队伍ID');
            $table->integer('axie_id')->index()->comment('AXIE编号');
            $table->string('axie_type', 16)->comment('AXIE类型');
            $table->tinyInteger('position')->comment('AXIE站位');
            $table->string('gene', 512)->comment('AXIE基因');
            $table->string('rune', 32)->comment('RuneID');
            $table->string('class', 16)->nullable()->comment('AXIE种族');
            $table->string('eyes_part_id', 32)->nullable();
            $table->string('eyes_part_name', 32)->nullable();
            $table->string('eyes_charm', 32);
            $table->string('ears_part_id', 32)->nullable();
            $table->string('ears_part_name', 32)->nullable();
            $table->string('ears_charm', 32);
            $table->string('horn_part_id', 32)->nullable();
            $table->string('horn_part_name', 32)->nullable();
            $table->string('horn_charm', 32);
            $table->string('mouth_part_id', 32)->nullable();
            $table->string('mouth_part_name', 32)->nullable();
            $table->string('mouth_charm', 32);
            $table->string('back_part_id', 32)->nullable();
            $table->string('back_part_name', 32)->nullable();
            $table->string('back_charm', 32);
            $table->string('tail_part_id', 32)->nullable();
            $table->string('tail_part_name', 32)->nullable();
            $table->string('tail_charm', 32);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fighter_axies');
    }
}
