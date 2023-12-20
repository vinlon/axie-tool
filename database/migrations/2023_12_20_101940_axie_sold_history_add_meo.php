<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AxieSoldHistoryAddMeo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('axie_sold_histories', function (Blueprint $table) {
            $table->boolean('is_meo')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('axie_sold_histories', function (Blueprint $table) {
            $table->dropColumn(['is_meo']);
        });
    }
}
