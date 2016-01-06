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
<div>
    <a href='{{ url('/admin/addGame') }}'>add</a>
    <ul>
        <?php foreach ($games as $key => $game) { ?>
        <li>

            <?php
            if ($game->name) {
                echo $game->name;
            }
            $delUrl = "deleteGameType/?";
            $delUrl .= "killGame=" . $game->key[2];

            echo " <a href='$delUrl'>delete</a>";
            ?>
            <form action="/admin/addGame">
                <input type="hidden" name="dir" value="<?= $game->path; ?>">
                <!--    <input type="text" name="newgame[]">-->
                <!--    <input type="text" name="newgame[]">-->
                <!--    <input type="text" name="newgame[]">-->
                <input value="refresh" type="submit">
            </form>

        </li>
        <?php } ?>
    </ul>
</div>
@endsection