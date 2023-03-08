<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueryMonitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('query_monitors', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('query_name', 64)->comment('查询名称');
            $table->string('mp_query_url', 256)->comment('MarketPlace查询地址');
            $table->integer('duration')->comment('监测间隔，分钟');
            $table->string('status', 16)->default(\App\Enums\AvailableStatus::ENABLED)->comment('状态');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('query_monitors');
    }
}
