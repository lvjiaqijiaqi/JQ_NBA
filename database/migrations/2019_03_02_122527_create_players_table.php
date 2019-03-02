<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('team_id');
            $table->integer('player_id');
            $table->string('player_name');
            $table->string('player_en_name');
            $table->string('player_image')->default("");
            $table->string('player_psition')->default("");
            $table->integer('player_position_id')->default(0);
            $table->integer('player_match_num')->default(0);
            $table->float('player_per_time')->default(0);
            $table->float('player_per_point')->default(0);
            $table->float('player_per_rebound')->default(0);
            $table->float('player_per_assist')->default(0);
            $table->float('player_per_steal')->default(0);
            $table->float('player_per_block')->default(0);
            $table->float('player_per_turnover')->default(0);
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
        Schema::dropIfExists('players');
    }
}
