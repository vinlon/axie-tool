<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErc1155TokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('erc1155_tokens', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('token_id')->index()->comment('TokenId');
            $table->string('item_id', 32)->index()->comment('ItemId');
            $table->string('type', 16)->comment('装备类型: rune/charm');
            $table->integer('season_id')->comment('SeasonId');
            $table->string('season_name')->comment('Season名称');
            $table->string('name', 32)->comment('装备名称');
            $table->string('class', 16)->comment('适用种族');
            $table->string('rarity', 16)->comment('稀有度');
            $table->string('logo_url', 256)->comment('LOGO图片链接');
            $table->text('description')->comment('装备描述');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('erc1155_tokens');
    }
}
