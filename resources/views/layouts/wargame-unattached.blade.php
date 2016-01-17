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
    <script src="{{ asset('js/jquery.js')}}"></script>
    <script src="{{ asset('js/jquery-ui.js')}}"></script>
    <script src="{{ asset('js/sync.js')}}"></script>
    <script src="{{ asset('js/angular.js')}}"></script>
    <link href="{{ asset('css/app.css')}}" rel="stylesheet" type="text/css">

    <script src="{{ asset('js/angular-sanitize.js')}}"></script>
    <script src="{{ asset('js/angular-modal-service.js')}}"></script>

    <meta charset="UTF-8">

    <style type="text/css">
        body {
            background: url('{{asset("images/$backgroundImage")}}');
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
<div ng-controller="RecursiveController">
    <recursive-rules data="[]"></recursive-rules>
</div>
<footer class="unattached attribution">
    <?=$backgroundAttr;?>
</footer>
</body>
<script>
</script>
<script type="text/javascript">
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
        $scope.scenarios = $.parseJSON(jString);
        for (var i in $scope.scenarios) {
            $scope.scenario = $scope.scenarios[i];
            break;
        }
        $scope.game = '<?=$theGame['game'];?>';
        $scope.editor = '<?=$editor;?>';
        $scope.name = '<?=addslashes($theGameMeta['name']);?>';
        $scope.description = '<?=$theGameMeta['description'];?>';
        $scope.histEditLink = '<?=isset($theGameMeta['histEditLink']) ? $theGameMeta['histEditLink'] : false;?>';

        $scope.lastScenario = $scope.scenario;
        $scope.scenario.selected = 'selected';
        $scope.imageUpdating = true;
        $scope.setOptions = "";

        var oString = '<?php echo addslashes(json_encode($theGameMeta['options']));?>';
        $scope.options = $.parseJSON(oString);

        $scope.clickityclick = function (a) {
            if($scope.scenario.mapUrl !== a.mapUrl){
                $scope.imageUpdating = true;
            }
            if ($scope.lastScenario) {
                $scope.lastScenario.selected = '';
            }
            a.selected = 'selected';
            $scope.scenario = a;
            $scope.lastScenario = a;
        }
        $scope.updateOptions = function(){
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
            ModalService.showModal({
                templateUrl: "<?= url("custom.html");?>",
                controller: "CustomController"
            }).then(function(modal) {
                modal.close.then(function(result) {
                    $scope.customResult = "All good!";
                });
            });

        };

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

    scenarioApp.controller('RecursiveController', ['$scope', function($scope) {

                $scope.typeOf = function(val){
                    return (typeof val) === 'object';
                };
                $scope.data = [
                    "love",
                    "peace",
                    "war",
                    ['gold',
                        'fire',
                        'water',['gax','electric']],
                    'weapons'
                ];
            }])
            .directive('recursiveRules',function(){
                return {
                    restrict:'E',
                    scope:{
                        data: '='
                    },
                    template:'<ol><li recursive-rule ng-repeat="datum in data" data="datum"></li></ol>'
                }
            })
            .directive('recursiveRule', function ($compile) {
                return {
                    restrict: "A",
                    replace: true,
                    scope: {
                        data: '='
                    },
                    template: '',
                    link: function (scope, element, attrs) {
                        if (angular.isArray(scope.data)) {
                            element.append("Dude! <recursive-rules data='data'></recursive-rules>");
                        }else{
                            element.append('@{{data}}');
                        }
                        $compile(element.contents())(scope);
                    }
                }
            });

</script>
</html>