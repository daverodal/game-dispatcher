<?php
/**
 * Copyright 2016 David Rodal
 * User: David Markarian Rodal
 * Date: 2/1/16
 * Time: 4:03 PM
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

@extends('layouts.master',['indexTab'=>'active'])

@section('content')
    @if (Session::has('error_message'))
        <div class="alert alert-danger" role="alert">
            {!! Session::get('error_message') !!}
        </div>
    @endif
    @if($threads->count() > 0)
        @foreach($threads as $thread)
            <?php $class = $thread->isUnread($currentUserId) ? 'alert-info' : ''; ?>
            <div class="media coolBox {!!$class!!}">
                <h4 class="media-heading"><a href="{{ url('messages/' . $thread->id) }}"> {{$thread->subject}}</a></h4>
                <p>{!! $thread->latestMessage->body !!}</p>
                <p><small><strong>Creator:</strong> {!! $thread->creator()->name !!}</small></p>
                <p><small><strong>Participants:</strong> {!! $thread->participantsString(Auth::id()) !!}</small></p>
            </div>
        @endforeach
    @else
        <p>Sorry, no threads.</p>
    @endif
@stop
