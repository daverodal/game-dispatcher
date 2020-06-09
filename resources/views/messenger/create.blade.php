<?php
/**
 * Copyright 2016 David Rodal
 * User: David Markarian Rodal
 * Date: 2/1/16
 * Time: 4:06 PM
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */?>
@extends('layouts.master', ['createTab'=>'active'])

@section('content')

    @if (count($errors) > 0)
        <div class="coolBox alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h1>Create a new message</h1>
    <form action="{{ url('/messages') }}" method="POST" class="form-horizontal">

        {!! csrf_field() !!}

        <input type="hidden" id="_token" value="{{ csrf_token() }}">

    @if($users->count() > 0)
            <div class="checkbox">TO:
                @foreach($users as $user)
                    <label title="{!!$user->name!!}"><input type="checkbox" name="recipients[]" value="{!!$user->id!!}">{!!$user->name!!}</label>
                @endforeach
            </div>
            @endif

                    <p></p>
                <!-- Task Name -->
        <div class="form-group">
            <label for="task" class="col-sm-3 control-label">Subject</label>

            <div class="col-sm-6">
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="form-control">
            </div>
        </div>

        <!-- Task Name -->
        <div class="form-group">
            <label for="task" class="col-sm-3 control-label">Message</label>

            <div class="col-sm-6">
                <textarea rows="4" name="message" id="message" class="form-control">{{ old('message') }}</textarea>
            </div>
        </div>


        <!-- Add Task Button -->
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <button type="submit" class="btn btn-default">
                    <i class="fa fa-plus"></i> New Message
                </button>
            </div>
        </div>




    </form>





    </div>
@stop