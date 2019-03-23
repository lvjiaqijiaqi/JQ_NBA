<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Team;
use App\Models\Player;
use App\Models\Match;
use App\Models\RealTimeData;
use Carbon\Carbon;

class NbaRequireRealTimeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nba:requireRealTimeData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '即时数据';

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
        $matchs = $this->getMatches(Carbon::now()->toDateString());
        //$this->info("need update matchId = $matchs" , $matchs);
        foreach ($matchs as $matchId) {
             $this->info("start matchId = $matchId" , $matchId);
             $this->getRealData($matchId);
             $this->info("end matchId = $matchId" , $matchId);
        }

    }

    public function getMatches($date){
        $this->info("getMatchs date = $date" , $date);
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
                    'date' => $date,
                 ];
        $r = $client->request('GET', '/match/listByDate', [
                'query' => $query
            ]);
        $matches = json_decode($r->getBody()->getContents() ,  JSON_OBJECT_AS_ARRAY)["data"]["matches"];
        $res = [];
        foreach ($matches as $match) {
            $match = $match["matchInfo"];
            $t = Match::where('match_id' , $match["mid"])->first();
            if ($t) {
                if ($match["mid"] != 0) {
                    $res[] = $match["mid"];
                }
            }else{
                $t = Match::updateOrCreate(['match_id' => $match["mid"]],[
                    'match_date' => $date,
                    'match_id' => $match["mid"],
                    'match_team_id1' => $match["leftId"],
                    'match_team_name1' => $match["leftName"],
                    'match_team_score1' => $match["leftGoal"],
                    'match_team_id2' => $match["rightId"],
                    'match_team_name2' => $match["rightName"],
                    'match_team_score2' => $match["rightGoal"],
                    'match_period' => $match["matchPeriod"],
                    'match_start_time' => $match["startTime"],
                    'match_end_time' => $match["endTime"],
                ]);
                $res[] = $match["mid"];
            }
        }
        return $res;
    }

    public function getRealData($matchId){
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
                    'tabType' => '2',
                    'mid' => $matchId,
                 ];
        $r = $client->request('GET', '/match/stat', [
                'query' => $query
            ]);
        $playerDetails = json_decode($r->getBody()->getContents() ,  JSON_OBJECT_AS_ARRAY)["data"]["stats"];
        //if (count($playerDetails) == 0) return;

        $playerDetails = $playerDetails[0]["playerStats"];
        $playerDetail = [];
        foreach ($playerDetails as $detail) {
            if (array_key_exists("row" , $detail)) {
                $playerDetail[] = $detail;
            }
        }
        foreach ($playerDetail as $detail) {
            if($detail["playerId"] > 0){
                $t = RealTimeData::updateOrCreate(['player_id' => $detail["playerId"]],[
                    'player_name' => $detail["row"][0],
                    'point' => $detail["row"][2],
                    'rebound' => $detail["row"][3],
                    'assist' => $detail["row"][4],
                    'steal' => $detail["row"][5],
                    'block' => $detail["row"][6],
                    'turnover' => $detail["row"][15],
                ]);
                $player = $t->player;
                if ($player) {
                    $t->position_id = $player->player_position_id;
                    $t->salary = $player->player_per_score;
                    $t->save();
                }else{
                    $this->info("find error : playerId = $t->player_id" , $t->player_id);
                }
            }
        }
    }
}
