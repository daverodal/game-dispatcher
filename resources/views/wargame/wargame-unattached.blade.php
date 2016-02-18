@extends('layouts.wargame-unattached')

@section('content')
    <div id="container" <?= $theGame ? "class='wideGame coolBox'" : 'class="coolBox"'; ?>>
        <ul id="theGrid">
            @foreach($games as $game)
                <li class="gridRow">
                    <a class="leftGrid" href="{{$siteUrl}}/{{$game->dir}}/{{$game->urlGenre}}">{{$game->genre}}</a>
                    <a class="rightGrid" href="{{$siteUrl}}/{{$game->dir}}/{{$game->urlGenre}}">{{$game->value}}
                        Available</a>
                </li>
            @endforeach

        </ul>
        Or
        <a href="{{url("wargame/leave-game")}}">back to lobby</a>
        <a href="{{url("/logout")}}">Logout</a>
    </div>
@endsection