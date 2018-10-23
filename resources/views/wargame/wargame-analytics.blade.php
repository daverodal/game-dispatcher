@extends('layouts.analytics', ['indexTab'=>'active'])
@section('content')
    <script>
        var displayData = '<?= json_encode($displayData);?>';
    </script>
    <div id="container" <?= true ? "class='wideGame coolBox'" : 'class="coolBox"'; ?>>
        <ul id="analytics-grid">
            @foreach($displayData as $gameName => $gameData)
                <li class="row">
                    <span class="col-xs-4">{{$gameName}}</span>
                    <span class="col-xs-2">Tie: {{$gameData[0]}}</span>
                    <span class="col-xs-2"> {{$gameData['playerOne']}}: {{$gameData[1]}}</span>
                    <span class="col-xs-2">{{$gameData['playerTwo']}}: {{$gameData[2]}}</span>
                    <span class="col-xs-2"><a target="_blank" href="/wargame/test-scenario-analytics/{{$gameData['className']}}">Drill down</a></span>
                </li>
            @endforeach

        </ul>
    </div>
@endsection
