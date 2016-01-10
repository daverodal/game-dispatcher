@extends('layouts.wargame-unattached')

@section('content')
    <div id="container" <?= $theGame ? "class='wideGame coolBox'" : 'class="coolBox"'; ?>>

        <?php

        if ($theGame) {
        echo "<ul id='theGameGrid'>";
        echo "<li class='leftGrid'>";
        $href = url("wargame/unattached-game/");
        echo "<a class='breadcrumb' href='$href'>top</a> ";
        $up = $theGame['dir'] . "/" . rawurlencode($theGame['genre']);
        $href = url("wargame/unattached-game/$up");
        echo "<a class='breadcrumb' href='$href'>back</a><br> ";
        echo "";
        ?>

        <h2>@{{name}}</h2>
        <p>@{{description}}</p>
        <div ng-if="options.length > 0" class="coolBox">
            <h4>Game Options</h4>
            <div ng-repeat="option in options">
                <input type="checkbox" ng-click="updateOptions()" ng-model="option.value">
                @{{option.name}}
            </div>
        </div>
        <div class="coolBox">
            <p class='softVoice'> Click on a scenario below</p>
            <div ng-repeat="(sName, scenario) in scenarios" ng-click="clickityclick(scenario)" class="clearWrapper">
                <span  class="scenarioWrapper @{{scenario.selected}}">@{{scenario.description}}</span>
                <a class='scenarioWrapper play' ng-click="showCustom('<?= url("wargame/create-wargame/") ?>/'+game+'/'+scenario.sName+'?'+setOptions)">Play &raquo;</a>
                <div class="clear"></div>
            </div>
            <p ng-bind-html="scenario.longDescription" class="scenarioDescription long-description selected"></p>
            <div class="clear"></div>
        </div>

        <h3>Historical Context</h3>

        <div class="coolBox wordpress-wrapper">
            <a ng-if="editor" target='_blank' ng-href="@{{histEditLink}}">Edit</a>
            <?php //echo $theGame->value->histEditLink;
            echo $theGameMeta['longDesc']; ?>
        </div>
        </li>
        <li class='rightGrid'>
            <div class='spinner-div' ng-if="imageUpdating" ><i class="fa fa-spinner fa-spin"></i></div>
            <img id="mapImage" imageonload ng-src="@{{scenario.mapUrl}}">
            <?php
            echo "<h3>Designer Notes</h3><div class='coolBox wordpress-wrapper'>";
            echo $theGameMeta['playerEditLink'];
            echo $theGameMeta['playerNotes'];
            echo "</div></li>";
            echo "</ul>";
            } ?>
    </div>
@endsection