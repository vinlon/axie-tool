<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOriginUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('origin_users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('user_id', 64)->index()->comment('Origin游戏ID');
            $table->string('ronin_address', 64)->default('')->index()->comment('RONIN钱包地址');
            $table->string('rns_name', 32)->default('')->index()->comment('RNS域名');
            $table->string("nick_name", 64)->nullable()->comment('游戏昵称');
            $table->string('profile_name', 64)->comment('账号昵称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('origin_users');
    }
}
