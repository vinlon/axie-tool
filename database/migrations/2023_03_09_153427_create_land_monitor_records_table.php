<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLandMonitorRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('land_monitor_records', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('land_type', 16)->comment('土地类型');
            $table->integer('on_sale')->comment('在售数量');
            $table->bigInteger('floor_price_eth')->comment('地板价ETH');
            $table->float('floor_price_usd')->comment('地板价USD');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('land_monitor_records');
    }
}
