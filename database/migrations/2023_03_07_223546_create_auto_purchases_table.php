<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_purchases', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('query_monitor_id')->index();
            $table->bigInteger('max_purchase_price')->comment('最高购买价格');
            $table->integer('max_purchase_count')->comment('最大购买数量');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auto_purchases');
    }
}
