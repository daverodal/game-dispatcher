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
?><html>
<head>
    <style type="text/css">
        li{
            list-style: none;
        }
    </style>
    <title>All Users</title>
</head>
<body>
<h1>click on available info.json dir </h1>
<ul>
    @foreach($providers as $provider)
        <li>
            <a href="/admin/add-game?dir={{$provider}}">{{$provider}}</a>
        </li>
    @endforeach
</ul>

<h2> or go it alone. good luck!</h2>
<form>
    <input type="text" name="dir">
    <input type="submit">
</form>
</body>
</html>