<html><head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/jquery-ui.js')}}"></script>
<script src="{{ asset('js/sync.js')}}"></script>
<script src="{{ asset('js/angular.js')}}"></script>
<link href="{{ asset('css/app.css')}}" rel="stylesheet" type="text/css">
<style type="text/css">

    body{
        background: url("{{ asset('images/armoredKnight.jpg')}}") #fff;
        background-repeat: no-repeat;
        background-position: center top;
    }
    pre{
        font-size:15px;
    }



</style>
<script type="text/javascript">
    var DR = {};
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var fetchUrl = '{{ url("wargame/fetch-lobby/") }}';
</script>

</head>
@section('content')
    <div id="container" <?= true ? "class='wideGame coolBox'" : 'class="coolBox"'; ?>>
        <ul id="analytics-grid">
            @foreach($displayData as $gameName => $gameData)
                <li class="grid-row">
                    <span class="colOne">{{$gameName}}</span>
                    <span class="colTwo">Tie: {{$gameData[0]}}</span>
                    <span class="colThree"> Player One: {{$gameData[1]}}</span>
                    <span class="colFour">Player Two: {{$gameData[2]}}</span>
                </li>
            @endforeach

        </ul>
        Or
        <a href="{{url("wargame/leave-game")}}">back to lobby</a>
        <a href="{{url("/logout")}}">Logout</a>
    </div>
@show

</html>