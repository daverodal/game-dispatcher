@extends('layouts.admin')

@section('content')
<div class="boxed">
    <h1>Bug Reports</h1>

    <h2>Welcome Markarian</h2>
    <div class="container dark-boxed">
        <div class="row bold">
            <span class="col-md-2">Creator</span><span class="col-md-2">Game</span><span class="col-md-2">Name</span>
            <span class="col-md-1">Type</span><span class="col-md-3">Date</span><span class="col-md-1">Watch</span><span class="col-md-1">Delete</span></li>
        </div>
        <div class="row bold">&nbsp;</div>
            @foreach ($lobbies as $lobby)

                <div class="row {{$lobby['odd']}}">

                    <span class="col-md-2">{{$lobby['keys'][0]}}</span>
                    <span class="col-md-2">{{$lobby['keys'][1]}}</span>
                    <span class="col-md-2">{{$lobby['clicks']}}</span>


                    <a href="/wargame/make-new-game/{{$lobby['id']}}"><span class="col-md-1">Spawn</span></a>


                    <a class="col-md-1" href="/admin/delete-bug/{{$lobby['id']}}/">delete</a>
                </div>
            @endforeach
    </div>
</div>
@endsection
