<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErc1155SoldHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('erc1155_sold_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('type', 16)->comment('Token Type');
            $table->string('token_id', 16)->index()->comment('Token ID');
            $table->string('from', 64)->index()->comment('出售人');
            $table->string('to', 64)->index()->comment('购买人');
            $table->string('trans_hash', 128)->index()->comment('交易Hash');
            $table->dateTime('trans_time')->comment('交易时间');
            $table->bigInteger('price')->comment('成交价格');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('erc1155_sold_histories');
    }
}
