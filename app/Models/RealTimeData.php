<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealTimeData extends Model
{
    protected $table = 'real_time_data';
    protected $fillable = ['team_id','player_id','player_name','player_en_name','player_image','point','rebound','assist','steal','block','turnover','values','team_side_id','match_time'];
}
