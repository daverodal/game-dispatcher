<!doctype html>
<html>
<style type="text/css">
    .link-box{
        background:white;
        width:50%;
        margin:250px auto 0;
        text-align:center;
        -webkit-border-radius:10px;
        -moz-border-radius:10px;
        border-radius: 10px;
        padding: 30px 0;
    }

    .link{
        font-size:40px;
        color:black;

    }
    .link:hover{
        color:red;
    }
</style>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico" type="image/icon">
</head>
<body background="{{asset('images/Brandy.png')}}">
<div class="link-box">
    <a class="link" href="{{url('wargame/play')}}">GO!</a>

</div>
</body>