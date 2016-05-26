
@section('content')
    <div id="container" <?= true ? "class='wideGame coolBox'" : 'class="coolBox"'; ?>>
        <ul id="theGrid">
            @foreach($displayData as $gameName => $gameData)
                <li class="gridRow">
                    {{$gameName}} Tie: {{$gameData[0]}} Player One: {{$gameData[1]}} Player Two: {{$gameData[2]}}
                </li>
            @endforeach

        </ul>
        Or
        <a href="{{url("wargame/leave-game")}}">back to lobby</a>
        <a href="{{url("/logout")}}">Logout</a>
    </div>
@show