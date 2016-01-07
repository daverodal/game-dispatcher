<!DOCTYPE html>
<html>
<head>
    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}
     <link href="{{ asset('css/app.css') }}" rel="stylesheet">


    <style type="text/css">
        body{
            font-family:times;
            font-size:16px;
        }
        div.row{
            margin-left:0px;
            margin-right:0px;
            padding:0;
            /*padding:2px;*/
            border-radius:0px;
            border:0px solid gray;
        }
        div.row:hover{
        background: rgba(0,0,0,.5);
        }
        h1, h2{
            font-weight:bold;;
        }
        h3{
            font-size:16px;
        }
        span{
            display:inline-block;
        }
        .row{
            color:white;
        }
        .bold{
            font-family:Lucida;
            font-weight:bold;
            font-size:18px;
            color:white;
            text-shadow:0px 0px 3px black,0px 0px 3px black,0px 0px 3px black,0px 0px 3px black,0px 0px 3px black,0px 0px 3px black;
        }
        .colOne{
            width:150px;
        }
        .colTwo{
            width:120px;
        }
        .colThree{
            width:180px;
        }
        .colFour{
            width:160px;
        }
        .colFive{
            width:170px;
        }
        h2{
            font-style:italic;
        }
        li{
            list-style: none;
        }
        .myTurn{
            color:white;
            text-shadow:0px 0px 3px green,0px 0px 3px green,0px 0px 3px green,0px 0px 3px green;
        }
        body{
            background-color:pink;
            background-repeat: no-repeat;
            color:#666;
            background-position: center top;
            height:100%;

        }
        .odd{
            background:rgba(66,66,66,.4);
        }
        a{
            color:black;
            font-size:120%;
        }
        #logout{
            color:white;
            text-shadow:0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00,
            0 0 3px #c00;
            text-decoration: none;
        }
        #create{
            color:white;
            text-shadow:0 0 3px #0c0,
            0 0 3px #0c0,
            0 0 3px #0c0,
            0 0 3px #0c0,
            0 0 3px #0c0,
            0 0 3px #0c0,
            0 0 3px #0c0,
            0 0 3px #0c0,
            0 0 3px #0c0,
            0 0 3px #0c0,
            0 0 3px #0c0,
            0 0 3px #0c0,
            0 0 3px #0c0,
            0 0 3px #0c0,
            0 0 3px #0c0;
            text-decoration: none;
            border-bottom: 3px solid #afa;
            font-family: sans-serif;
            font-style:italic;
        }
        ul{
            padding:5px 10px;
            border-radius:10px;
            color:white;
        }
        ul a{
            color:white;
        }
        ul li:hover{
            background:rgba(0,0,0,.5);
        }
        .dark-boxed{
            padding: 5px 10px;
            border-radius: 10px;
            background: rgba(33,33,33,.5);
            color: white;
        }
        .boxed{
            color:black;
            min-width:900px;
            /*float:left;*/
            /*max-width:1000px;*/
            /*margin-top: 50px;*/
            border-radius:20px;
            border:5px solid gray;
            padding:    15px 30px 15px 15px;
            background: rgba(160,160,160,.15);
            width:80%;
            margin:0 auto;
        }
        #adminMenu div{
            width:auto;
            min-width:initial;
            display:inline-block;
        }
    </style>
</head>
<body>
<div class="boxed" id="adminMenu">
    @section($peace)
        bluelbue
    @show

    <a href="{{ url('/login') }}">Login</a>
    <div class="boxed" ><a href="{{ url('wargame/play') }}">home</a></div>
    <div class="boxed" ><a href="{{ url('admin/allgames') }}">All Games</a></div>
    <div class="boxed" ><a href="{{ url('admin/games') }}">All Games Avail</a></div>
    <div class="boxed" ><a href="{{ url('admin/users') }}">Users</a></div>
    <div class="boxed" ><a href="{{ url('admin/logins') }}">Logins</a></div>
</div>
    @yield('content')
</body>
</html>