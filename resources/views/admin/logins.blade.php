@extends('layouts.admin')


@section('content')
    <style type="text/css">
        li {
            list-style: none;
        }

        form {
            display: inline;
        }
    </style>
<div class="boxed">
    <a href='{{ url('/users/adduser') }}'>add</a>
    <div class="container dark-boxed">


                <ul>
                    @foreach ($logins as $login)
                        <li>
                            @if(isset($login->name->username))
                                {{$login->name->username}}
                            @elseif(isset($login->name->name))
                                {{$login->name->name}}
                            @else
                                {{$login->name}}
                            @endif
                            {{$login->time}} {{$login->action ?? "(unknown -- assumed login)"}}
                        </li>
                    @endforeach
                </ul>
    </div>
</div>
@endsection
