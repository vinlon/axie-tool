<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBattleHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battle_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('battle_uuid', 64)->index()->comment('battle ID')->index();
            $table->string('first_fighter_id', 64)->index()->comment('玩家1用户ID')->index();
            $table->string('second_fighter_id', 64)->index()->comment('玩家2用户ID')->index();
            $table->bigInteger('first_fighter_team_id')->index()->comment('玩家1阵容ID');
            $table->bigInteger('second_fighter_team_id')->index()->comment('玩家2阵容ID');
            $table->integer('first_rank')->nullable()->comment('玩家1战斗前排名');
            $table->integer('second_rank')->nullable()->comment('玩家2战斗前排名');
            $table->string('winner_id', 64)->index()->comment('胜方用户ID');
            $table->string('loser_id', 64)->index()->comment('败方用户ID');
            $table->string('battle_type', 32)->index()->comment('战斗类型');
            $table->dateTime('battle_start_time')->comment('战斗开始');
            $table->dateTime('battle_end_time')->comment('战斗结束时间');
            $table->boolean('is_surrender')->comment('是否是投降');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('battle_histories');
    }
}
