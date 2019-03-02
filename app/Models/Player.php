<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $table = 'players';
    protected $fillable = ['team_id','player_id','player_name','player_en_name','player_image','player_position','player_position_id','player_match_num','player_per_time','player_per_point','player_per_rebound','player_per_assist','player_per_steal','player_per_block','player_per_turnover','player_per_score'];
}
