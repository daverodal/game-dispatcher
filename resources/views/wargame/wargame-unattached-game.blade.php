@extends('layouts.wargame-unattached')

@section('content')
    <div id="container" class="{{$theGame ? 'wideGame coolBox' : 'coolBox'}}">

        @if ($theGame)
            <ul id='theGameGrid' class="row">
                <li class='leftGrid'>
                    <a class='breadcrumb' href='{{url("wargame/unattached-game/")}}'>top</a>
                    <?php $up = $theGame['dir'] . "/" . rawurlencode($theGame['genre']);?>
                    <a class='breadcrumb' href='{{url("wargame/unattached-game/$up")}}'>back</a>
                    @if($theGameMeta['path'] === 'Mollwitz')

                    <a class='breadcrumb' ng-click="toggleRules()">Show/Hide Rules</a><br>
                    <div ng-if="viewRules" class="coolBox rules-wrapper">
                        <style type="text/css">
                            .rules-wrapper #GR {
                                display: block !important;
                                background: white;
                                position: static;

                            }
                            .rules-wrapper #GR .unit{
                                box-sizing: content-box;
                            }
                            .rules-wrapper #GR .unit .counterWrapper {
                                box-sizing: content-box;
                            }
                            .rules-wrapper #GR .unit p{
                                margin-top:15px;
                                margin-bottom: 15px;
                            }
                        </style>
                        @include('wargame::Mollwitz.commonRules', ["name" => $theGameMeta['name'], "forceName" => $theGameMeta['playerInfo']['forceName'], "deployName" =>  $theGameMeta['playerInfo']['deployName'], ])
                        <h2>Exclusive Rules </h2>
                        <ol>
                            @isset($theGameMeta['corePath'])
                                @if(view()->exists($theGameMeta['corePath'].".exclusiveRules"))
                                    @include($theGameMeta['corePath'].".exclusiveRules")
                                @endif
                            @else
                                @include('wargame::Mollwitz.common-exclusive-rules')
                                @include("wargame::".$theGameMeta['curPath'].".exclusiveRules", ["name" => $theGameMeta['name'], "forceName" => $theGameMeta['playerInfo']['forceName'], "deployName" =>  $theGameMeta['playerInfo']['deployName'], ])
                            @endisset

                        </ol>
                        <h2>Victory Conditions </h2>
                        <ol>
                            @isset($theGameMeta['corePath'])
                                @if(view()->exists($theGameMeta['corePath'].".victoryConditions"))
                                    @include($theGameMeta['corePath'].".victoryConditions")
                                @endif
                            @else
                                @include("wargame::".$theGameMeta['curPath'].".victoryConditions", ["name" => $theGameMeta['name'], "forceName" => $theGameMeta['playerInfo']['forceName'], "deployName" =>  $theGameMeta['playerInfo']['deployName'], ])
                            @endisset
                        </ol>
                        <div class="clear"></div>
                    </div>

                    @endif
                    <h2>@{{name}}</h2>
                    <p>@{{description}}</p>
                    <div ng-if="options.length > 0" class="coolBox">
                        <h4>Game Options</h4>
                        <div ng-repeat="option in options">
                            <input type="checkbox" ng-model="option.value">
                            @{{option.name}}
                        </div>
                    </div>
                    <div class="coolBox">
                        <p class='softVoice'> Click on a scenario below</p>
                        <div class="kewl-box" ng-repeat="(sName, scenario) in scenarios" ng-click="clickityclick(scenario)"
                             class="clearWrapper">
                            <span class="scenarioWrapper @{{scenario.selected}}">@{{scenario.description}}</span>

                            <a ng-if="editor" class='scenarioWrapper custom-scenario play copy'
                               href='<?= url("wargame/clone-scenario/$dir/".rawurlencode($plainGenre)."/$game") ?>/@{{scenario.sName}}'><i class="fa fa-files-o"></i></a>
                            <a ng-if="editor && scenario.id" class="scenarioWrapper custom-scenario play edit" href='{{url("wargame/scenario-vue-edit/")}}/@{{scenario.id}}'><i class="fa fa-pencil-square-o"></i></a>
                            <a ng-if="editor && scenario.id" class="scenarioWrapper custom-scenario play delete" href='{{url("wargame/scenario-delete/")}}/@{{scenario.id}}'><i class="fa fa-times"></i></a>

                            <div class="clear"></div>

                            <a class='scenarioWrapper play'
                               ng-click="showCustom('<?= url("wargame/create-wargame/hotseat/") ?>/'+game+'/'+scenario.sName+'?')">Play Solo&raquo;</a>
                            <a class='scenarioWrapper play'
                               ng-click="showCustom('<?= url("wargame/create-wargame/multi/") ?>/'+game+'/'+scenario.sName+'?')">Play Multi&raquo;</a>
                            <div class="clear"></div>
                        </div>
                        <p ng-bind-html="scenario.longDescription"
                           class="scenarioDescription long-description selected"></p>
                        <p ng-show="scenario.units">
                            <ul>
                                <li ng-repeat="unit in scenario.units">
                                   @{{ unit.num }} @{{unit.nationality}} @{{ unit.class }} @{{ unit.combat }} @{{ unit.movement }} <span ng-show="unit.range > 1">@{{ unit.range }}</span>
                                </li>
                            </ul>
                        </p>
                        <div class="clear"></div>
                    </div>

                    <h3>Historical Context</h3>

                    <div class="coolBox wordpress-wrapper">
                        <a ng-if="editor" target='_blank' ng-href="@{{histEditLink}}">Edit</a>
                        <?php //echo $theGame->value->histEditLink;?>

                        {!! $theGameMeta['longDesc'] ?? '' !!}
                    </div>
                </li>
                <li class='rightGrid'>
                    <div class='spinner-div' ng-if="imageUpdating"><i class="fa fa-spinner fa-spin"></i></div>
                    <img id="mapImage" imageonload ng-src="@{{scenario.mapUrl}}">
                    <h3>Designer Notes {!! $theGameMeta['playerEditLink'] ?? '' !!}
                    </h3>
                    <div class='coolBox wordpress-wrapper'>
                        {!! $theGameMeta['designerNotes'] ?? '' !!}
                    </div>
                </li>
            </ul>
            @endif
    </div>
    @endsection

<style type="text/css">
    .rules-wrapper #GRr {
        display: block !important;
        background: white;
        left: 200px;
    }
</style>
<link rel="stylesheet" type="text/css" href="{{mix('vendor/css/wargame/horse-musket.css')}}">

