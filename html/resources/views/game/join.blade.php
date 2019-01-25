<div class="row mt-5">
    <div class="col-md-6 mt-sm-2">
        <form method="POST" action="/game/join">
            @csrf
            <div class="form-group">
                <label for="username">Player name</label>
                <input type="text" name="username" id="username" class="form-control" required autocomplete=off>
                <input type="hidden" name="game_id" value="{{$game->id}}">
            </div>
            <button type="submit" class="btn btn-primary">Join Game</button>
        </form>
    </div>
</div>

