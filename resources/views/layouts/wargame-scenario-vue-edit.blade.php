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
  </style>
</head>
<body >
<div id="container">
@yield('content')
</div>
<footer class="unattached attribution">
    <?=$backgroundAttr;?>
</footer>
<script type="text/javascript" src="{{mix('js/app.js')}}"></script>
</body>
</html>
