<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFighterTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fighter_teams', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('user_id')->index()->comment('用户编号');
            $table->string('team_hash', 64)->index()->comment('队伍哈希标识');
            $table->string('type_label', 64)->nullable()->comment('队伍类型标记');
            $table->string('type_sub_label', 64)->nullable()->comment('队伍类型二级标记');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fighter_teams');
    }
}
