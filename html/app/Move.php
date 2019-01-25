<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Move extends Model
{
    protected $connection = 'mysql';
    protected $table = 'moves';

    public function player()
    {
        return $this->belongsTo('App\Player', 'player_id', 'id');
    }
}
