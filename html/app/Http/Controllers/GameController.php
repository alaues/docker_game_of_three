<?php
/**
 * Created by PhpStorm.
 * User: Almat
 * Date: 22.01.2019
 * Time: 14:28
 */

namespace App\Http\Controllers;

use App\Events\GameStatusNotification;
use App\Events\NextMoveNotification;
use App\Exceptions\GameMoveException;
use App\Game;
use App\Player;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    /**
     * Function returns game screen
     * @param Request $request
     * @param string $game_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function index(Request $request, $game_id = '')
    {
        $player_name = $request->session()->get('users');
        $player = Player::where('player_name', $player_name)->first();

        $status = '';
        $game = new Game();
        if ($game_id){
            $game = Game::find($game_id);
            /* If game is not found, redirect to new game screen */
            if (!$game){
                return redirect('/game');
            }
            $status = $game->status;
        }

        if (!$status){
            /* Find who's next move */
            $next_player = $game->getNextMovePlayer();

            if ($next_player instanceof Model && $next_player->id == $player->id){
                $status = 'Your move' ;
            } elseif($next_player instanceof Model && $next_player->id != $player->id){
                $status = $next_player->player_name . '\'s move' ;
            }
        }

        return view('game', ['current_player' => $player_name, 'game' => $game, 'status' => $status]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request)
    {
        /* save player in session */
        $request->session()->forget('users');
        $request->session()->forget('game');

        $player_name = trim($request->get('username'));
        if ($player = Player::where('player_name', $player_name)->first()){
            flash('Name is in use already, please, choose another name')->error();
            return redirect('/game');
        }
        if (strlen($player_name) > 20){
            flash('Please, 20 chars, not more')->error();
            return redirect('/game');
        }

        $request->session()->put('users', $player_name);

        /* create player record */
        $player = new Player();
        $player->player_name = $player_name;
        $player->save();

        /* create game record */
        $game = new Game();
        $game->create($player);

        $request->session()->put('game', $game->id);

        return redirect('/game/' . $game->id);
    }

    /**
     * Function deattaches player from game
     * and removes his session
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function quit(Request $request)
    {
        $player_name = $request->session()->get('users');
        $player = Player::where('player_name', $player_name)->first();

        $game_id = $request->session()->get('game');
        if ($game_id) {
            $game = Game::find($game_id);
            if ($game) {
                $game->playerQuit($player);
                $request->session()->forget('game');
            }
        }

        $request->session()->forget('users');
        return redirect('/');
    }

    /**
     * Function creates second player
     * and attaches to game
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function join(Request $request)
    {
        $player_name = trim($request->get('username'));
        $game_id = $request->get('game_id');
        $game_id = filter_var($game_id, FILTER_VALIDATE_INT);

        if ($player = Player::where('player_name', $player_name)->first()){
            flash('Name is in use already, please, choose another name')->error();
            return redirect('/game/' . $game_id);
        }
        if (strlen($player_name) > 20){
            flash('Please, 20 chars, not more')->error();
            return redirect('/game');
        }

        $request->session()->put('users', $player_name);

        /* create player record */
        $player = new Player();
        $player->player_name = $player_name;
        $player->save();

        /* update game with random number */
        $game = Game::findOrFail($game_id);
        $game->score = mt_rand(100, 250);
        $game->status = '';
        $game->save();
        $game->players()->attach($player->id);

        /* send to broadcast */
        event(new GameStatusNotification($game));
        event(new NextMoveNotification($game->players->last()));

        $request->session()->put('game', $game->id);
        return redirect('/game/' . $game->id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function move(Request $request)
    {
        $game_id = $request->get('game_id');
        $game_id = filter_var($game_id, FILTER_VALIDATE_INT);
        $game = Game::findOrFail($game_id);

        $player_name = $request->session()->get('users');
        $player = Player::where('player_name', $player_name)->first();

        $step = $request->get('step');

        try {
            $game->createMove($player, $step);
        } catch (GameMoveException $e){
            Log::debug($e->getMessage());
            flash($e->getMessage())->error();
            return redirect('/game/' . $game->id);
        }

        return redirect('/game/' . $game->id);
    }
}
