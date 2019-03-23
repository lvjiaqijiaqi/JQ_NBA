<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Transformers\PlayerTransformer;

class GameController extends Controller
{
    public function index()
    {
        return $this->response->collection(Player::all(), new PlayerTransformer());
    }
}
