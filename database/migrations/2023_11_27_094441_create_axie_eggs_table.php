<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAxieEggsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('axie_eggs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('axie_id')->index()->comment('AxieId');
            $table->dateTime('birth_time')->comment('出生时间');
            $table->string('owner_address', 64)->index()->comment('所有人地址');
            $table->string('owner_name', 32)->comment('所有人昵称');
            $table->integer('matron_id')->index()->comment('母亲ID');
            $table->integer('sire_id')->index()->comment('父亲ID');
            $table->string('matron_class')->nullable();
            $table->string('matron_breed_count')->nullable();
            $table->string('matron_eyes_part_id', 32)->nullable();
            $table->string('matron_ears_part_id', 32)->nullable();
            $table->string('matron_horn_part_id', 32)->nullable();
            $table->string('matron_mouth_part_id', 32)->nullable();
            $table->string('matron_back_part_id', 32)->nullable();
            $table->string('matron_tail_part_id', 32)->nullable();
            $table->string('sire_class')->nullable();
            $table->string('sire_breed_count')->nullable();
            $table->string('sire_eyes_part_id', 32)->nullable();
            $table->string('sire_ears_part_id', 32)->nullable();
            $table->string('sire_horn_part_id', 32)->nullable();
            $table->string('sire_mouth_part_id', 32)->nullable();
            $table->string('sire_back_part_id', 32)->nullable();
            $table->string('sire_tail_part_id', 32)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('axie_eggs');
    }
}
