@extends('layouts.analytics', ['indexTab'=>'active'])
@section('content')
    <script>
        displayData = false;
    </script>
    <div  id="container" <?= true ? "class='wideGame coolBox'" : 'class="coolBox"'; ?>>
        <ul id="analytics-grid">
            @foreach($displayData as $gameName => $gameData)
                <li class="row">
                    <div class="col-xs-5">{{$gameData->key[2]}}</div>
                    <div class="col-xs-1">{{$gameData->key[3]}}</div>
                    <div class="col-xs-3">{{date("r",$gameData->key[7])}}</div>
                    <div class="col-xs-1">{{$gameData->value->winningSide}}</div>
                    <div>
                        <collapsable-box>
                            @foreach($gameData->value->scenario as $key => $val)
                                @if( $key !== 'units')
                                    {{ $key }}:<br>
                                @endif
                            @endforeach
                            {{ var_dump($gameData->value) }}
                        </collapsable-box>
                    </div>
                </li>
            @endforeach

        </ul>
    </div>
@endsection