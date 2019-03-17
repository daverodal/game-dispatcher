<?php
/**
 *
 * Copyright 2011-2015 David Rodal
 *
 *  This program is free software; you can redistribute it
 *  and/or modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?><!doctype html>
<?php
/**
 * User: David Markarian Rodal
 * Date: 5/3/12
 * Time: 10:21 PM
 */
?>
<html ng-app="scenarioApp">

<head>
    <meta charset="UTF-8">
    <script src="{{ mix('javascripts/main.js')}}"></script>
    <link href="{{ mix('css/app.css')}}" rel="stylesheet" type="text/css">

    <meta charset="UTF-8">

    <style type="text/css">
        body {
            background: url('{{asset("vendor/wargame/genre/images/$backgroundImage")}}');
            background-repeat: no-repeat;
            background-size: 100%;
            background-attachment:fixed;
        }
        .spinner-div{
            position: absolute;
        }
        .fa-spinner{
            position:absolute;
            font-size:200px;
            margin-left:150px;
            color:#666;
        }

        .spinner-div{
            opacity:0;  /* make things invisible upon start */
            -webkit-animation:fadeIn ease-in 1;  /* call our keyframe named fadeIn, use animattion ease-in and repeat it only 1 time */
            -moz-animation:fadeIn ease-in 1;
            animation:fadeIn ease-in 1;

            -webkit-animation-fill-mode:forwards;  /* this makes sure that after animation is done we remain at the last keyframe value (opacity: 1)*/
            -moz-animation-fill-mode:forwards;
            animation-fill-mode:forwards;

            -webkit-animation-duration:3s;
            -moz-animation-duration:3s;
            animation-duration:3s;
        }
        h4{
            margin:15px 0;
        }

        .coolBox{
            margin: 25px 0;
        }

        @-webkit-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        @-moz-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

        .back-screen{
            background:white;
            width:100%;
            height:100%;
            position:fixed;
            left:0px;top:0px;
            opacity:.9;
        }

        .close{
        }

        .modal-box{
            font-size:40px;
            padding:40px;
            text-align:center;
            background:white;
            width:50%;
            height:auto;
            position:fixed;
            left:25%;
            top:15%;
            opacity:.9;
            border: 10px solid black;
            border-radius:20px;
            box-shadow: 10px 10px 10px #666;
        }
        .modal-box input{
            font-size: 30px;
        }

        /*ol {*/
        /*counter-reset: item;*/
        /*padding-left: 10px;*/
        /*}*/
        /*li {*/
        /*list-style-type: none;*/
        /*}*/

        /*li::before{*/
        /*content: "[" counters(item,".") "] ";*/
        /*counter-increment: item;*/
        /*font-weight: bold;*/
        /*}*/
    </style>
</head>
<body ng-controller="ScenarioController">
@yield('content');
<footer class="unattached attribution">
    <?=$backgroundAttr;?>
</footer>
</body>
<script>
</script>
<script type="text/javascript">
    var paramUnits = false;
    <?php if(isset($theGameMeta['params']->units)){?>
        paramUnits = '<?php echo json_encode($theGameMeta['params']->units);?>';
    <?php };?>
    var jString = '<?php echo addslashes(json_encode($theScenarios));?>';
    var scenarioApp = angular.module('scenarioApp', ['angularModalService','ngSanitize']);
    scenarioApp.controller('CustomController', ['$scope', 'close', function($scope, close) {

        $scope.display = true;
        $scope.theUrl = $scope.$root.theUrl;

        $scope.close = function() {
            $scope.display = false;
            close();
        };

    }]).directive('payAttentionToMe', function($timeout){
        return {
            restrict: 'A',
            link: function(scope, element, attrs){
                $timeout(function(){
                    element[0].focus();
                })
            }
        }
    });

    scenarioApp.controller('ScenarioController', ['$scope', 'ModalService', function ($scope, ModalService) {
        $scope.predicate = '';
        $scope.scenarios = angular.fromJson(jString);
        $scope.defaultUnits = angular.fromJson(paramUnits);
        $scope.viewRules = false;
        $scope.scenarios = $scope.scenarios.map((scenario) => {
            scenario.mapUrl = scenario.mapUrl.replace(/https?:/, '');
            scenario.bigMapUrl = scenario.bigMapUrl.replace(/https?:/, '');
            return scenario;
        });
        for (var i in $scope.scenarios) {
            $scope.scenario = $scope.scenarios[i];
            break;
        }
        if($scope.scenario) {
            if (!$scope.scenario.units) {
                $scope.scenario.units = $scope.defaultUnits;
            }
        }
        $scope.game = '<?=$theGame['game'];?>';
        $scope.editor = '<?=$editor;?>' - 0;/* turn string back to number */
        $scope.name = '<?=addslashes($theGameMeta['name']);?>';
        $scope.description = '<?=$theGameMeta['description'];?>';
        $scope.histEditLink = '<?=isset($theGameMeta['histEditLink']) ? $theGameMeta['histEditLink'] : false;?>';

        $scope.lastScenario = $scope.scenario;
        if($scope.scenario){
            $scope.scenario.selected = 'selected';
        }
        $scope.imageUpdating = true;
        $scope.setOptions = "";

        var oString = '<?php echo addslashes(json_encode($theGameMeta['options']));?>';
        $scope.options = angular.fromJson(oString);

        $scope.clickityclick = function (a) {
            if($scope.scenario.mapUrl !== a.mapUrl){
                $scope.imageUpdating = true;
            }
            if ($scope.lastScenario) {
                $scope.lastScenario.selected = '';
            }
            a.selected = 'selected';
            $scope.scenario = a;
            if(!$scope.scenario.units){
                $scope.scenario.units = $scope.defaultUnits;
            }
            $scope.lastScenario = a;
        }
        $scope.updateOptions = function(a,b){
            $scope.setOptions = "";
            for(var i in $scope.options){
                if($scope.options[i].value){
                    $scope.setOptions += $scope.options[i].keyName+"="+$scope.options[i].value+"&";
                }
            }
        }
        $scope.updateOptions();


        $scope.showCustom = function(arg) {

            $scope.$root.theUrl = arg;
            $scope.updateOptions();
            $scope.$root.theUrl += $scope.setOptions;

            ModalService.showModal({
                template: `<div class="back-screen" ></div>
                            <div class="modal-box" >

                                Name your game, then hit return.

                                <form method="post" action='@{{theUrl}}'>
                                    <input ng-model='aGameName' id="wargame" name="wargame" pay-attention-to-me>
                                    <input ng-show="aGameName.length > 0" class="go-button" type="submit" value="start">
                                    </form>
                                or
                                <a class="close" href ng-click="close()">Cancel</a>
                            </div>
                `,
                controller: "CustomController"
            }).then(function(modal) {
                modal.close.then(function(result) {
                    $scope.customResult = "All good!";
                });
            });

        };
        $scope.toggleRules = () => {
            $scope.viewRules = !$scope.viewRules;
        }

    }]).
    directive('imageonload', function() {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                element.bind('load', function() {
                    scope.imageUpdating = false;
                    scope.$apply();
                });
            }
        };
    });


</script>
</html>
