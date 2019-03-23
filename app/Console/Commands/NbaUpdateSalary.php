<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Team;
use App\Models\Player;
use App\Models\Match;
use App\Models\RealTimeData;
use Carbon\Carbon;

class NbaUpdateSalary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nba:updateSalary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $t = RealTimeData::find(1);
        $player = $t->player;
        $this->info("playerName = $player->player_name",$player->player_name);
        return;

        foreach (Player::cursor() as $player) {
            $positionNameStr = $player->player_position;
            $positionNameArr = explode("-",$positionNameStr);
            $positionId = 0;
            foreach ($positionNameArr as $positionName) {
                $positionId |= $this->tranPosition($positionName);
            }
            $player->player_position_id = $positionId;
            $player->player_per_score = $this->calculateSalary($player);
            $player->save();
            //$this->info("playerId = $player->id , positionId =  $positionId , player_per_score =  $player->player_per_score" ,$player->id , $player->player_per_score);
        }
    }

    public function tranPosition($positionName){
        $positionName = trim($positionName);
        if ($positionName == "前锋") {
            return (1<<3)|(1<<2);
        }else if($positionName == "中锋"){
            return 1<<4;
        }else if($positionName == "后卫"){
            return (1<<0)|(1<<1);
        }
        return 0;
    }

    public function calculateSalary(Player $player){
        $value = $player->player_per_point 
                + $player->player_per_rebound * 1.2 
                + $player->player_per_assist * 1.5 
                + $player->player_per_steal * 2 
                + $player->player_per_block * 2 
                - $player->player_per_turnover * 1;
        return $value;
    }
}
