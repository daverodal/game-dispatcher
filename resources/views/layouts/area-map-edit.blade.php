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
<html>

<head>
    <title>my app</title>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ mix('css/app4.css')}}" rel="stylesheet" type="text/css">

    <meta charset="UTF-8">

    <style type="text/css">

        #container{
            max-width:1650px;
            position: relative;
            z-index: 3;
        }


    </style>
</head>
<body >

<div id="container">
@yield('content')
</div>
<script type="text/javascript">
    debugger;
    var fetchUrl = 'api/areaa-map';
</script>

<script type="text/javascript" src="{{mix('js/area-map-editor/app.js')}}"></script>
</body>
</html>
