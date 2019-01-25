<?php
/**
 * Created by PhpStorm.
 * User: Almat
 * Date: 23.01.2019
 * Time: 20:39
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{

    protected $connection = 'mysql';
    protected $table = 'players';

    public function games()
    {
        return $this->belongsToMany('App\Game', 'players_games');
    }
}
