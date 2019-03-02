<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    protected $table = 'matches';
    protected $fillable = ['match_date','match_id','match_team_id1','match_team_name1','match_team_score1','match_team_id2','match_team_name2','match_team_score2','match_period','match_start_time','match_end_time'];
}
