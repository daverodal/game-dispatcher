<?php
/**
 *
 * Copyright 2011-2015 David Rodal
 *
 *  This program is free software; you can redistribute it
 *  and/or modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?><!doctype html>
<?php
/**
 * User: David Markarian Rodal
 * Date: 5/3/12
 * Time: 10:21 PM
 */
?>
<html ng-app="scenarioApp">

<head>
    <meta charset="UTF-8">

    <link href="{{ mix('css/app.css')}}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{mix('vendor/css/wargame/all-nations-colors.css')}}">

    <meta charset="UTF-8">

    <style type="text/css">
        body {
            background: url('{{asset("vendor/wargame/genre/images/$backgroundImage")}}');
            background-repeat: no-repeat;
            background-size: 100%;
            background-attachment:fixed;
        }
        .spinner-div{
            position: absolute;
        }
        .fa-spinner{
            position:absolute;
            font-size:200px;
            margin-left:150px;
            color:#666;
        }

        .spinner-div{
            opacity:0;  /* make things invisible upon start */
            -webkit-animation:fadeIn ease-in 1;  /* call our keyframe named fadeIn, use animattion ease-in and repeat it only 1 time */
            -moz-animation:fadeIn ease-in 1;
            animation:fadeIn ease-in 1;

            -webkit-animation-fill-mode:forwards;  /* this makes sure that after animation is done we remain at the last keyframe value (opacity: 1)*/
            -moz-animation-fill-mode:forwards;
            animation-fill-mode:forwards;

            -webkit-animation-duration:3s;
            -moz-animation-duration:3s;
            animation-duration:3s;
        }
        h4{
            margin:15px 0;
        }

        .coolBox{
            margin: 25px 0;
        }

        @-webkit-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        @-moz-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

        .back-screen{
            background:white;
            width:100%;
            height:100%;
            position:fixed;
            left:0px;top:0px;
            opacity:.9;
        }

        .close{
        }

        .modal-box{
            font-size:40px;
            padding:40px;
            text-align:center;
            background:white;
            width:50%;
            height:auto;
            position:fixed;
            left:25%;
            top:15%;
            opacity:.9;
            border: 10px solid black;
            border-radius:20px;
            box-shadow: 10px 10px 10px #666;
        }
        .modal-box input{
            font-size: 30px;
        }

        #container{
            max-width:1650px;
            position: relative;
        }

        /*ol {*/
        /*counter-reset: item;*/
        /*padding-left: 10px;*/
        /*}*/
        /*li {*/
        /*list-style-type: none;*/
        /*}*/

        /*li::before{*/
        /*content: "[" counters(item,".") "] ";*/
        /*counter-increment: item;*/
        /*font-weight: bold;*/
        /*}*/
    </style>
</head>
<body >
<div id="container">
@yield('content')
</div>
<footer class="unattached attribution">
    <?=$backgroundAttr;?>
</footer>
<script type="text/javascript" src="{{asset('js/app.js')}}"></script>
</body>
</html>
