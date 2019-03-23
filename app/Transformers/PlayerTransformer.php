<?php

namespace App\Transformers;

use App\Models\Player;
use League\Fractal\TransformerAbstract;

class PlayerTransformer extends TransformerAbstract
{
    public function transform(Player $player)
    {
        return [
            'id' => $player->player_id,
            'name' => $player->player_name,
        ];
    }
}