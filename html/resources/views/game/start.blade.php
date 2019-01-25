<div class="row mt-5">
    <div class="col-md-6">
        <h6>Starting New Game</h6>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mt-sm-2">
        <form method="POST" action="/game/create">
            @csrf
            <div class="form-group">
                <label for="username">Player name</label>
                <input type="text" name="username" id="username" class="form-control" required autocomplete=off>
            </div>
            <button type="submit" class="btn btn-primary">Start Game</button>
        </form>
    </div>
</div>
