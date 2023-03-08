<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoPurchaseRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_purchase_records', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('auto_purchase_id')->index();
            $table->integer('axie_id')->comment('AxieID');
            $table->string('owner', 64)->comment('Axie持有人');
            $table->bigInteger('price')->comment('购买价格');
            $table->string('trans_hash', 128)->comment('交易HASH');
            $table->string('status', 16)->comment('购买状态');
            $table->text('remark')->nullable()->comment('备注信息');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auto_purchase_records');
    }
}
