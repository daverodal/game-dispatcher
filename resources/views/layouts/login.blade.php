<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}
    <style type="text/css">
        body{
            background: url("{{ asset('images/Gen._Ulysses_S._Grant_and_portion_of_staff,_Gen._John_A._Rawlins._-_NARA_-_524492.jpg') }}");
            background-repeat: no-repeat;
            background-size:    100%;

        }
        .container{
            font-size:22px;
            background:rgba(255,255,255,.9);
            border:1px solid #333;
            border-radius:15px;
            margin:40px;
            padding:20px;
            float:left;
            box-shadow: 10px 10px 10px rgba(20,20,20,.7);
        }
        input{
            margin-left:15px;
        }
        a{
            color:#333;
        }
        .attribution{
            font-size:22px;
            background:rgba(255,255,255,.9);
            border:1px solid #333;
            border-radius:15px;
            margin:40px;
            padding:20px;
            float:left;
            box-shadow: 10px 10px 10px rgba(20,20,20,.7);
            position:absolute;
            bottom:0px;
        }
    </style>
</head>
<body>
<div>
    @yield('content')

    {{--Please login:--}}
    {{--<form method="POST">--}}
        {{--Username<input type="text" name="name"><br>--}}
        {{--Password--}}
        {{--<input type="password" name="password">--}}
        {{--<input type="submit">--}}
    {{--</form>--}}
    {{--<a href="/">Or back to front page</a>--}}
</div>
<div class="attribution">
    Mathew Brady [Public domain], <a target='blank' href="http://commons.wikimedia.org/wiki/File%3AGen._Ulysses_S._Grant_and_portion_of_staff%2C_Gen._John_A._Rawlins._-_NARA_-_524492.jpg">via Wikimedia Commons</a>
</div>
</body>
</html>
