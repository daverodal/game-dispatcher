@extends('layouts.analytics', ['indexTab'=>'active'])
@section('content')
    <script>
        var displayData = '<?= json_encode($displayData);?>';
    </script>
    <div id="container" <?= true ? "class='wideGame coolBox'" : 'class="coolBox"'; ?>>
        <ul id="analytics-grid">
            @foreach($displayData as $gameName => $gameData)
                <li class="grid-row">
                    <span class="colOne">{{$gameName}}</span>
                    <span class="colTwo">Tie: {{$gameData[0]}}</span>
                    <span class="colThree"> {{$gameData['playerOne']}}: {{$gameData[1]}}</span>
                    <span class="colFour">{{$gameData['playerTwo']}}: {{$gameData[2]}}</span>
                </li>
            @endforeach

        </ul>
    </div>
@endsection
