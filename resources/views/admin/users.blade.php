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
                    <li class="users-row">
                        <span class="users-layout">name</span>
                        <span class="users-layout">email</span>
                        <span class="users-layout">is_admin</span>
                        <span class="users-layout">is_editor</span>

                    </li>
                    @foreach ($users as $user)
                        <li class="users-row">
                            <span class="users-layout">{{$user['name']}}</span>
                            <span class="users-layout">{{$user['email']}}</span>
                                <span class="users-layout">@if($user->can('admin'))Yes @else No @endif</span>
                            <span class="users-layout">@if($user->can('editor')) Yes @else No @endif</span>

                        </li>
                    @endforeach
                </ul>
    </div>
</div>
@endsection
