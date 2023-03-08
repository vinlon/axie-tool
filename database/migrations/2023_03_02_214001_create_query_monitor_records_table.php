<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueryMonitorRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('query_monitor_records', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('query_id')->index();
            $table->bigInteger('on_sale')->comment('在售数量');
            $table->bigInteger('floor_price')->comment('地板价');
            $table->bigInteger('floor_axie_id')->comment('最低价axieID');
            $table->bigInteger('average_price')->comment('均价，只取价格最低的特定数量axie');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('query_monitor_records');
    }
}
