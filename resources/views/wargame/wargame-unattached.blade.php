@extends('layouts.wargame-unattached')
<style type="text/css">
    body {

        background: url('{{asset("vendor/wargame/genre/images/$backgroundImage")}}');
    }
</style>
@section('content')
    <div id="container" class="coolBox">
        <a href="{{url("wargame/leave-game")}}">back to lobby</a>

        <ul id="theGrid">
            @foreach($games as $game)
                <li class="gridRow row">
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
<footer class="attribution">
    {!! $backgroundAttr !!}
</footer>