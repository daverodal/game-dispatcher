<?php
/**
 * Copyright 2016 David Rodal
 * User: David Markarian Rodal
 * Date: 2/1/16
 * Time: 4:07 PM
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
 */ ?>
@extends('layouts.master', ['indexTab'=>'active'])

@section('content')
    <div class="col-md-6">
        <h1>{!! $thread->subject !!}</h1>

        @foreach($thread->messages as $message)
            <div class="media">
                <a class="pull-left" href="#">
                    <img src="//www.gravatar.com/avatar/{!! md5($message->user->email) !!}?s=64"
                         alt="{!! $message->user->name !!}" class="img-circle">
                </a>
                <div class="media-body">
                    <h5 class="media-heading">{!! $message->user->name !!}</h5>
                    <p>{!! $message->body !!}</p>
                    <div class="text-muted">
                        <small>Posted {!! $message->created_at->diffForHumans() !!}</small>
                    </div>
                </div>
            </div>
        @endforeach

        <h2>Add a new message</h2>
        <form action="{{ url('/messages/'.$thread->id) }}" method="POST" class="form-horizontal">
            <!-- Message Form Input -->
            <div class="form-group">
                <label for="task" class="col-sm-3 control-label">Message</label>

                <div class="col-sm-6">
                    <input type="textarea" name="message" id="message" class="form-control">
                </div>
            </div>

            @if($users->count() > 0)
                <div class="checkbox">
                    @foreach($users as $user)
                        <label title="{!! $user->name !!}"><input type="checkbox" name="recipients[]"
                                                                  value="{!! $user->id !!}">{!! $user->name !!}</label>
                    @endforeach
                </div>
            @endif

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
