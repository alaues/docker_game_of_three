<?php
/**
 * Created by PhpStorm.
 * User: Almat
 * Date: 22.01.2019
 * Time: 16:32
 */

namespace App;

use App\Events\GameStatusNotification;
use App\Events\MoveSubmittedNotification;
use App\Events\NextMoveNotification;
use App\Exceptions\GameMoveException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Game extends Model
{
    protected $connection = 'mysql';
    protected $table = 'games';

    const DIVIDE_BY = 3;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function moves()
    {
        return $this->hasMany('App\Move')->orderBy('created_at', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function players()
    {
        return $this->belongsToMany('App\Player', 'players_games');
    }

    /**
     * Function validates players, submitted step
     * creates move
     * and sends event to broadcasting
     * @param Player $player
     * @param $step
     * @throws GameMoveException
     */
    public function createMove(Player $player, $step = null): void
    {
        /* check players count */
        $players = $this->players->all();
        if (count($players) != 2){
            throw new GameMoveException('Game not started');
        }

        /* check game status */
        if (preg_match('/(Game Over|quit)/', $this->status)){
            throw new GameMoveException($this->status);
        }

        if ($this->score === 1){
            throw new GameMoveException($this->status);
        }

        /* check who's move now */
        $next_player = $this->getNextMovePlayer();
        if ($next_player instanceof Model && $next_player->id != $player->id){
            throw new GameMoveException('It\'s not your move now');
        }

        $step = filter_var($step, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^(-1|0|1)$/']]);
        if ($step === FALSE){
            throw new GameMoveException('Only -1, 0, 1 are allowed');
        }

        /* check if number is divisible by 3 */
        if (($this->score + $step) % self::DIVIDE_BY != 0){
            throw new GameMoveException($this->score . ($step == -1 ? ' - 1' : ' + '. $step) . ' is not divisible by ' . self::DIVIDE_BY);
        }

        /* saving move */
        $score = ($this->score + $step) / self::DIVIDE_BY;

        $move = new Move();
        $move->player_id = $player->id;
        $move->step = $step;
        $move->game_id = $this->id;
        $move->score = $score;
        $move->save();

        $this->score = $score;

        if ($this->score === 1){
            $this->status = 'Game over. ' . $player->player_name  . ' win!';
        }

        $this->save();
        /* send events */
        event(new MoveSubmittedNotification($move));
        event(new GameStatusNotification($this));

        if ($this->score !== 1) {
            /* find who's move is next */
            $players = array_values(array_filter($players, function ($p) use ($player) {
                return $p->id != $player->id;
            }));

            if (isset($players[0])) {
                event(new NextMoveNotification($players[0]));
            }
        }
    }

    /**
     * Function updates status in the game
     * and removes relation with player
     *
     * @param Player $player
     */
    public function playerQuit(Player $player): void
    {
        $this->status = 'Game Over. Player ' . $player->player_name . ' quited';
        $this->save();
        if ($player) {
            $this->players()->detach($player->id);
        }

        event(new GameStatusNotification($this));
    }

    /**
     * Function creates game record
     * and sends event
     * @param Player $player
     */
    public function create(Player $player): void
    {
        $this->status = 'Waiting for second player..';
        $this->save();
        $this->players()->attach($player->id);

        /* send event */
        event(new GameStatusNotification($this));
    }

    /**
     * Function returns player for next move
     * @return Player|null
     */
    public function getNextMovePlayer()
    {
        $players = $this->players->all();

        if (count($players) != 2)
            return null;

        $lastMove = $this->moves()->first();
        if (!$lastMove)
            return $this->players->last();

        if ($this->score === 1)
            return null;


        $lastPlayer = $lastMove->player;
        $next_player = array_values(array_filter($players, function ($p) use ($lastPlayer) {
            return $p->id != $lastPlayer->id;
        }));

        if (isset($next_player[0])) {
            return $next_player[0];
        }
        return null;
    }
}
