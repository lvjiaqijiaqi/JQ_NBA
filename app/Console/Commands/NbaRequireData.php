<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Team;
use App\Models\Player;
use App\Models\Match;
use App\Models\RealTimeData;
use Carbon\Carbon;

class NbaRequireData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nba:requireData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拉取nba数据';

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
        $ts = $this->getTeams();
        $ids = [];
        foreach ($ts as $team) {
            $teamIds =$this->getTeamDetail($team->team_id);           
            $ids = array_merge($ids,$teamIds);
        }
        foreach ($ids as $playerId) {
            $this->info("start id = $playerId" , $playerId);
            $this->getPlayerDetail($playerId);
            $this->info("end id = $playerId" , $playerId);
        }
    }
    public function getTeams(){
        $client = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'http://sportsnba.qq.com',
        // You can set any number of default request options.
        'timeout'  => 5.0,
        ]);
        $query = [
                    'deviceId' => '5aab4cc468f3416eb5df8f915c3d8fcb',
                    'guid' => '5aab4cc468f3416eb5df8f915c3d8fcb',
                    'appver' => '5.3',
                    'appvid' => '5.3',
                    'from' => 'app',
                    'os' => 'iphone',
                 ];
        $r = $client->request('GET', '/team/rank', [
                'query' => $query
            ]);
        $data = json_decode($r->getBody()->getContents() ,  JSON_OBJECT_AS_ARRAY)["data"];
        $teams = [];
        foreach ($data as $value) {
                $ts = $value["rows"];
                foreach ($ts as $team) {
                    $teams[] = $team[0];
                }
        };
        $ts = [];
        foreach ($teams as $team) {
            $t = Team::updateOrCreate(['team_id' => $team["teamId"]],[
                'team_id' => $team["teamId"],
                'team_name' => $team["name"],
                'team_image' => $team["badge"],
            ]);
            $ts[] = $t;
        }
        return $ts;       
    }

    public function getTeamDetail($teamId){
        $client = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'http://sportsnba.qq.com',
        // You can set any number of default request options.
        'timeout'  => 5.0,
        ]);
        $query = [
                    'deviceId' => '5aab4cc468f3416eb5df8f915c3d8fcb',
                    'guid' => '5aab4cc468f3416eb5df8f915c3d8fcb',
                    'appver' => '5.3',
                    'appvid' => '5.3',
                    'from' => 'app',
                    'os' => 'iphone',
                    'teamId' => $teamId,
                 ];
        $r = $client->request('GET', '/player/list', [
                'query' => $query
            ]);
        $players = json_decode($r->getBody()->getContents() ,  JSON_OBJECT_AS_ARRAY)["data"];
        $playerIds = [];
        foreach ($players as $player) {
            $t = Player::updateOrCreate(['player_id' => $player["id"]],[
                'team_id' => $teamId,
                'player_id' => $player["id"],
                'player_name' => $player["cnName"],
                'player_en_name' => $player["enName"],
                'player_position' => $player["position"],
                'player_position_id' => 0,
                'player_image' => $player["icon"],
            ]);
            $playerIds[] = $player["id"];
            //$this->getPlayerDetail($player["id"]);
        }
        return $playerIds;
    }

    public function getPlayerDetail($playerId){
        $client = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'http://sportsnba.qq.com',
        // You can set any number of default request options.
        'timeout'  => 5.0,
        ]);
        $query = [
                    'deviceId' => '5aab4cc468f3416eb5df8f915c3d8fcb',
                    'guid' => '5aab4cc468f3416eb5df8f915c3d8fcb',
                    'appver' => '5.3',
                    'appvid' => '5.3',
                    'from' => 'app',
                    'os' => 'iphone',
                    'tabType' => '1',
                    'playerId' => $playerId,
                 ];
        $r = $client->request('GET', '/player/stats', [
                'query' => $query
            ]);
        $playerDetails = json_decode($r->getBody()->getContents() ,  JSON_OBJECT_AS_ARRAY)["data"]["stats"];
        if (!array_key_exists("rows",$playerDetails)) {
            return;
        }
        $playerDetails = $playerDetails["rows"];
        $playerDetail = [];
        foreach ($playerDetails as $detail) {
            if ($detail[2] == "常规赛") {
                $playerDetail = $detail;
            }
        }
        $t = Player::updateOrCreate(['player_id' => $playerId],[
                'player_match_num' => $playerDetail[3],
                'player_per_time' => $playerDetail[5],
                'player_per_point' => $playerDetail[6],
                'player_per_rebound' => $playerDetail[7],
                'player_per_assist' => $playerDetail[8],
                'player_per_steal' => $playerDetail[9],
                'player_per_block' => $playerDetail[10],
                'player_per_turnover' => $playerDetail[22],
            ]);
    }
}
