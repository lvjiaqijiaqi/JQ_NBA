<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRealTimeDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('real_time_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('team_id');
            $table->integer('team_side_id');
            $table->integer('player_id');
            $table->string('player_name');
            $table->string('player_image')->default("");
            $table->float('point')->default(0);
            $table->float('rebound')->default(0);
            $table->float('assist')->default(0);
            $table->float('steal')->default(0);
            $table->float('block')->default(0);
            $table->float('turnover')->default(0);
            $table->float('values')->default(0);
            $table->string('match_time')->default("");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('real_time_data');
    }
}
