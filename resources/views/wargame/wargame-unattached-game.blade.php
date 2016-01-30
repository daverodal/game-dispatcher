@extends('layouts.wargame-unattached')

@section('content')
    <div id="container" class="{{$theGame ? 'wideGame coolBox' : 'coolBox'}}">

        @if ($theGame)
            <ul id='theGameGrid'>
                <li class='leftGrid'>
                    <a class='breadcrumb' href='{{url("wargame/unattached-game/")}}'>top</a>
                    <?php $up = $theGame['dir'] . "/" . rawurlencode($theGame['genre']);?>
                    <a class='breadcrumb' href='{{url("wargame/unattached-game/$up")}}'>back</a><br>


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
                        <div ng-repeat="(sName, scenario) in scenarios" ng-click="clickityclick(scenario)"
                             class="clearWrapper">
                            <span class="scenarioWrapper @{{scenario.selected}}">@{{scenario.description}}</span>
                            <a class='scenarioWrapper play'
                               ng-click="showCustom('<?= url("wargame/create-wargame/") ?>/'+game+'/'+scenario.sName+'?'+setOptions)">Play &raquo;</a>
                            <div class="clear"></div>
                        </div>
                        <p ng-bind-html="scenario.longDescription"
                           class="scenarioDescription long-description selected"></p>
                        <div class="clear"></div>
                    </div>

                    <h3>Historical Context</h3>

                    <div class="coolBox wordpress-wrapper">
                        <a ng-if="editor" target='_blank' ng-href="@{{histEditLink}}">Edit</a>
                        <?php //echo $theGame->value->histEditLink;?>

                        {!! $theGameMeta['longDesc'] or 'love' !!}
                    </div>
                </li>
                <li class='rightGrid'>
                    <div class='spinner-div' ng-if="imageUpdating"><i class="fa fa-spinner fa-spin"></i></div>
                    <img id="mapImage" imageonload ng-src="@{{scenario.mapUrl}}">
                    <?php
                    echo "<h3>Designer Notes</h3><div class='coolBox wordpress-wrapper'>";
                    //            echo $theGameMeta['playerEditLink'];?>
                    {!! $theGameMeta['playerNotes'] or 'love' !!}

                </li>
            </ul>
            @endif
    </div>
    @endsection