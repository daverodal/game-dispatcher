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
                    @foreach ($users as $user)
                        <li>
                            {{$user->value->username}}
                        </li>
                    @endforeach
                </ul>
    </div>
</div>
@endsection
