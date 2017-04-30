<?php
/**
 * Created by PhpStorm.
 * Ugulpser: david
 * Date: 4/29/17
 * Time: 12:45 PM
 *
 * /*
 * Copyright 2012-2017 David Rodal
 * This program is free software; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation;
 * either version 2 of the License, or (at your option) any later version
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
@extends('layouts.wargame-scenario-edit', ['backgroundImage' => '18th_century_gun.jpg'])

@section('content')
<div id="container" class="coolBox">
<se-root>Loading...</se-root>
</div>
<script type="text/javascript" src="{{url('assets/ng-4/inline.bundle.js')}}"></script>
<script type="text/javascript" src="{{url('assets/ng-4/polyfills.bundle.js')}}"></script>
<script type="text/javascript" src="{{url('assets/ng-4/styles.bundle.js')}}"></script>
<script type="text/javascript" src="{{url('assets/ng-4/vendor.bundle.js')}}"></script>
<script type="text/javascript" src="{{url('assets/ng-4/main.bundle.js')}}"></script>
@endsection