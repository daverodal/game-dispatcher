@extends('layouts.admin')

@section('content')
<div class="boxed">
    <h1>ADMIN VIEW</h1>

    <h2>Welcome Markarian</h2>
    <div class="container dark-boxed">
        <div class="row bold">
            <span class="col-md-2">Creator</span><span class="col-md-2">Game</span><span class="col-md-2">Name</span>
            <span class="col-md-1">Type</span><span class="col-md-3">Date</span><span class="col-md-1">Watch</span><span class="col-md-1">Delete</span></li>
        </div>
        <div class="row bold">&nbsp;</div>
        @foreach ($lobbies as $lobby)

        <div class="row {{$lobby['odd']}}">

            <span class="col-md-2">{{$lobby['creator']}}</span>
            <span class="col-md-2">{{$lobby['gameName']}}</span>
            <span title="click to change" class="col-md-2">{{$lobby['name']}}</span>
            <span class="col-md-1 {gameType}">{{$lobby['gameType']}}</span>
            <span class="col-md-3">{{$lobby['date']}}</span>
            <a href="/wargame/change-wargame/{{$lobby['id']}}"><span class="col-md-1">Watch</span></a>


            <a class="col-md-1" href="admin/delete-game/{{$lobby['id']}}/">delete</a>
            </div>
            @endforeach
    </div>
</div>
@endsection
