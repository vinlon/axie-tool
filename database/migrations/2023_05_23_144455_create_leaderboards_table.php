<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderboardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('user_id', 64)->comment('用户ID')->index();
            $table->string('user_name', 68)->comment('用户昵称');
            $table->integer('vstar')->comment('分数');
            $table->integer('last_team_id')->nullable()->comment('最新使用的队伍ID');
            $table->dateTime('last_active_time')->nullable()->comment('最近一次活跃时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leaderboards');
    }
}
