@extends('layouts.app')

@section('title', 'Game')

@section('links')
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

    <!-- Flash notifications -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
@endsection

@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.4.0/clipboard.min.js"></script>
@endsection

@section('css')
    <style>
        div.alert {
            position: fixed;
            top: 0px;
            left: 0px;
            width: 100%;
            z-index: 9999;
            border-radius: 0px;
        }
    </style>
@endsection

@section('content')
    <div class="flex-center position-ref full-height">
        <div class="content">
            <div class="container mb-sm-5 mb-lg-5">
                @include('flash::message')
            </div>
            <div class="container mt-sm-4 ml-lg-5">
                @if (!$current_player)
                    @if ($game->id)
                        @include('game.join')
                    @else
                        @include('game.start')
                    @endif
                @else
                    @if ($game)
                        @include('game.index')
                    @endif
                @endif
            </div>
            </div>
        </div>
        </div>
    </div>

    <script>
        $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
        (function(){
            new Clipboard('#copy-button');
        })();

    </script>
@endsection
