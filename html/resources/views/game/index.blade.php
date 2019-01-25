<style>
    .invite_block {
        position: absolute;
        z-index: 2000;
        top: 40px;
        right: 90px;
        display: none;
    }
</style>
<div class="row">
    <div class="col-md-12 mt-5">
        You play as <b>{{$current_player}}</b>
        <input type="hidden" value="{{$current_player}}" class="player_name">
        <div class="float-right">

            <div class="card invite_block">
                <div class="card-header">
                    <span style="font-size: 12px;">Invite somebody to play with you by sending this link</span>
                </div>
                <div class="card-body">
                    <div class="clipboard input-group">
                        <input id="post-shortlink" class="form-control col-9" value="{{Request::url()}}">
                        <div class="input-group-append">
                            <button class="button btn btn-primary " id="copy-button" data-clipboard-target="#post-shortlink">Copy</button>
                        </div>
                    </div>
                </div>
            </div>

            <a title="Invite friend" href="#" onclick="$('.invite_block').toggle();" class="btn-link mr-5">Invite friend <i class="fas fa-share-alt"></i></a>
            <a title="Logout" href="/game/quit" class="btn-link">Quit <i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 mt-sm-2">
        <div class="card">
            <div class="card-header">
                <div class="mt-sm-4 mb-sm-4">
                    Submit -1, 0 or 1 to current score to get a number that is divisible by {{$game::DIVIDE_BY}}. If resulting number will be 1, you win.
                    If another player gets 1, you loose.
                </div>
                <form action="/game/move" method="POST" id="moveForm">
                    @csrf
                    <div class="row">
                        <div class=" col-md-4">
                            <input type="text" class="form-control" name="step" id="step" required aria-describedby="stepHelp" @if($game->getNextMovePlayer() && $game->getNextMovePlayer()->player_name == $current_player) enabled @else disabled @endif>
                            <small id="stepHelp" class="form-text text-muted">Only -1, 0 or 1 are allowed</small>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-primary" @if($game->getNextMovePlayer() && $game->getNextMovePlayer()->player_name == $current_player) enabled @else disabled @endif>Send</button>
                        </div>
                        <input type="hidden" name="game_id" value="{{$game->id}}">
                        <div class="form-group col-md-2 float-right">
                            Score: <span class="badge badge-pill badge-info game_score" style="font-size: 18px;">{{$game->score}}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <label>Status:</label>&nbsp;<span class="game_status">{{$status}}</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body game_window">
                <div class="col-md-12">
                    <ul class="list-group list-group-flush" id="gamesMovesList">
                        @foreach($game->moves as $move)
                            <li class="list-group-item">
                                <div class="col-md-4">@if($move->player->player_name == $current_player) You @else {{$move->player->player_name}} @endif moved: {{ intval($move->step) }}</div>
                                <div class="col-md-2 float-right">
                                    score: <span class="badge badge-pill badge-info" style="font-size: 18px;">{{$move->score}}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div><!-- card -->
    </div>
</div><!-- row -->

<script src="https://js.pusher.com/4.1/pusher.min.js"></script>
<script>

    Pusher.logToConsole = true;

    /* init broadcasting provider */
    var pusher = new Pusher('{{config('broadcasting.connections.pusher.key')}}', {
        cluster: '{{config('broadcasting.connections.pusher.options.cluster')}}',
        forceTLS: true
    });

    /* subscription to broadcasting channel */
    var channel = pusher.subscribe('game');

    /* subscription to broadcasting events */
    channel.bind('MoveSubmittedNotification', function(data) {
        appendMove(data.move);
    }).bind('NextMoveNotification', function(data) {
        processNextPlayer(data.player);
    }).bind('GameStatusNotification', function(data) {
        changeStatus(data.game);
    });

    var appendMove = function(move)
    {
        var player = move.player.player_name;
        if (player == $(".player_name").val()){
            player = 'You';
        }
        var list_element = '<li class="list-group-item">\n' +
            '<div class="col-md-4">' + player + ' moved: ' + parseInt(move.step) + '</div>\n' +
            '<div class="col-md-2 float-right">\n' +
            'score: <span class="badge badge-pill badge-info" style="font-size: 18px;">'+move.score+'</span>\n' +
            '</div>\n' +
            '</li>';

        if ($("#gamesMovesList").find(".list-group-item:first").length){
            $(list_element).insertBefore($("#gamesMovesList").find(".list-group-item:first"));
        } else {
            $("#gamesMovesList").append(list_element);
        }
    };

    var processNextPlayer = function(player)
    {
        var status = player.player_name + '\'s move';

        if (player.player_name == $(".player_name").val()){
            var status = 'Your move';
            $("#moveForm").find("button[type='submit']").prop('disabled', false);
            $("#moveForm").find("input#step").prop('disabled', false);
        }
        $(".game_status").html(status);
    };

    var changeStatus = function(game) {
        if (game.status) {
            $(".game_status").html(game.status);
        }
        $(".game_score").html(game.score);

        if(game.status.match('Game Over')){
            $("#moveForm").find("button[type='submit']").prop('disabled', true);
            $("#moveForm").find("input#step").prop('disabled', true);
        }
    };
</script>
