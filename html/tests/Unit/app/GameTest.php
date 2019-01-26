<?php

namespace Tests\Unit\app;

use App\Player;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Game;

class GameTest extends TestCase
{

    public function testCreateMoveGameWithOnePlayer()
    {
        $game = new Game();
        $game->status = '';
        $game->save();

        $player1 = new Player();
        $player1->player_name = 'player ' . rand();
        $player1->save();
        $game->players()->attach($player1->id);

        $this->expectException(\App\Exceptions\GameMoveException::class);
        $game->createMove($player1, 1);//bcs should return 'Game not started'

        $player1->delete();
        $game->delete();

    }

    public function testCreateMoveWrongPlayerMove()
    {
        $game = new Game();
        $game->status = '';
        $game->save();

        $player1 = new Player();
        $player1->player_name = 'player ' . rand();
        $player1->save();
        $game->players()->attach($player1->id);

        $player2 = new Player();
        $player2->player_name = 'player ' . rand();
        $player2->save();
        $game->players()->attach($player2->id);

        $this->expectException(\App\Exceptions\GameMoveException::class);
        $game->createMove($player1, 1);//bcs should return 'Its not your move now'

        $player1->delete();
        $player2->delete();
        $game->delete();
    }

    public function testCreateMoveOnly3ValuesAreAllowed()
    {
        $game = new Game();
        $game->status = '';
        $game->score = 12;
        $game->save();

        $player1 = new Player();
        $player1->player_name = 'player ' . rand();
        $player1->save();
        $game->players()->attach($player1->id);

        $player2 = new Player();
        $player2->player_name = 'player ' . rand();
        $player2->save();
        $game->players()->attach($player2->id);

        $this->expectException(\App\Exceptions\GameMoveException::class);
        $game->createMove($player2, 100);//bcs should return 'Only -1, 0, 1 are allowed'

        $player1->delete();
        $player2->delete();
        $game->delete();
    }

    public function testCreateMoveNotDivisibleBy3()
    {
        $game = new Game();
        $game->status = '';
        $game->score = 12;
        $game->save();

        $player1 = new Player();
        $player1->player_name = 'player ' . rand();
        $player1->save();
        $game->players()->attach($player1->id);

        $player2 = new Player();
        $player2->player_name = 'player ' . rand();
        $player2->save();
        $game->players()->attach($player2->id);

        $this->expectException(\App\Exceptions\GameMoveException::class);
        $game->createMove($player2, 1);//bcs should return 'Not divisible by 3'

        $player1->delete();
        $player2->delete();
        $game->delete();
    }

    public function testGetNextMovePlayer()
    {
        /* empty game */
        $game = new Game();
        $game->status = '';
        $game->save();

        $nextPlayer = $game->getNextMovePlayer();
        $this->assertEquals($nextPlayer, null);

        $game->delete();


        /* completed game */
        $game = new Game();
        $game->status = '';
        $game->score = 1;
        $game->save();

        $nextPlayer = $game->getNextMovePlayer();
        $this->assertEquals($nextPlayer, null);

        $game->delete();


        /* game with 2 players */
        $game = new Game();
        $game->status = '';
        $game->score = 100;
        $game->save();

        $player1 = new Player();
        $player1->player_name = 'player ' . rand();
        $player1->save();
        $game->players()->attach($player1->id);


        $player2 = new Player();
        $player2->player_name = 'player ' . rand();
        $player2->save();
        $game->players()->attach($player2->id);

        $nextPlayer = $game->getNextMovePlayer();
        $this->assertEquals($nextPlayer->id, $player2->id);

        $game->delete();
        $player2->delete();
        $player1->delete();
    }
}
