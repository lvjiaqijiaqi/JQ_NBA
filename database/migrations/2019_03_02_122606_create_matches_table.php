<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('match_date');
            $table->string('match_id');
            $table->integer('match_team_id1');
            $table->string('match_team_name1');
            $table->integer('match_team_score1');
            $table->integer('match_team_id2');
            $table->string('match_team_name2');
            $table->integer('match_team_score2');
            $table->integer('match_period');
            $table->string('match_start_time');
            $table->string('match_end_time');
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
        Schema::dropIfExists('matches');
    }
}
