@extends('layouts.wargame-unattached')

@section('content')
    <div id="container" <?= $theGame ? "class='wideGame coolBox'" : 'class="coolBox"'; ?>>

            <header class='coolBox'><h2>{{$plainGenre}}</h2>
            <a class='breadcrumb' href='{{url('wargame/unattached-game')}}'>back</a>
                        Or
            <a href="<?= url("wargame/leave-game"); ?>">back to lobby</a>
            <a href="<?= url("/logout"); ?>">Logout</a>
            </header>
            <ul id = "theGamesGrid" >
            @foreach ($games as $game)
                <li class="gridRow">
                    <div class="rightGrid"><p><a href="{{$siteUrl}}/{{$game->dir}}/{{$game->urlGenre}}/{{$game->game}}">{{$game->name}}</a></p><p>{{$game->description}}</p></div>
                        <a href="{{$siteUrl}}/{{$game->dir}}/{{$game->urlGenre}}/{{$game->game}}">
                            <span class="softVoice">{{$game->maxCol}} x {{$game->maxRow}} Hexes</span>
                            <img src="{{$game->mapUrl}}">
                        </a>
                    <div class="clear"></div>
                    {{$game->value}} Scenarios Available.
                </li>
            @endforeach
            </ul>


    </div>
@endsection