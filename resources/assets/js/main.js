var lobbyApp = angular.module('lobbyApp', []);
lobbyApp.directive('myTabs', function() {
    return {
        restrict: 'E',
        transclude: true,
        scope: {},
        controller: function($scope) {
            var panes = $scope.panes = [];

            $scope.select = function(pane) {
                angular.forEach(panes, function(pane) {
                    pane.selected = false;
                });
                if(pane.title === "Messages"){

                    window.location = '/messages';
                }
                if(pane.title === "Analytics"){

                    window.location = '/wargame/test-analytics';
                }
                if(pane.title === "Logout"){

                    window.location = '/logout';
                }
                pane.selected = true;
            };

            this.addPane = function(pane) {

                if (panes.length === 0 ) {
//                                $scope.select(pane);
                }
                if((pane.isSelected)){
                    $scope.select(pane);
                }
                panes.push(pane);
            };
        },
        templateUrl: '/tabs.html'
    };
})
    .directive('myPane', function() {
        return {
            require: '^myTabs',
            restrict: 'E',
            transclude: true,
            scope: {
                title: '@',
                isSelected: '@'
            },
            link: function(scope, element, attrs, tabsCtrl) {
                tabsCtrl.addPane(scope);
            },
            templateUrl: '/tab-pane.html'
        };
    });

lobbyApp.controller('LobbyController', ['$scope', '$http', 'sync', function($scope, $http, sync){

    sync.register('lobbies', function(lobbies){
        var myHotLobbies = [];
        for(var i in lobbies) {
            var myHotLobby = {};
            myHotLobby = lobbies[i];
            myHotLobby.pubLinkLabel = lobbies[i].public === 'public' ? 'private' : 'public';
            myHotLobbies.push(myHotLobby);
        }
        $scope.myHotGames = myHotLobbies;
        $scope.$apply();
    });

    sync.register('multiLobbies', function(multiGames){
        var myMultiGames = [];
        for(var i in multiGames) {
            var myMultiGame = {};
            myMultiGame = multiGames[i];
            myMultiGame.pubLinkLabel = multiGames[i].public === 'public' ? 'private' : 'public';
            myMultiGames.push(myMultiGame);
        }
        $scope.myMultiGames = myMultiGames;
        $scope.$apply();
    });


    sync.register('otherGames', function(otherGames){
        var myOtherGames = [];
        for(var i in otherGames) {
            var myOtherGame = {};
            myOtherGame = otherGames[i];
            myOtherGame.pubLinkLabel = otherGames[i].public === 'public' ? 'private' : 'public';
            myOtherGames.push(myOtherGame);
        }
        $scope.myOtherGames = myOtherGames;
        $scope.$apply();
    });

    sync.register('publicGames', function(publicGames){
        var myPublicGames = [];
        for(var i in publicGames) {
            var myPublicGame = {};
            myPublicGame = publicGames[i];
            myPublicGame.pubLinkLabel = publicGames[i].public === 'public' ? 'private' : 'public';
            myPublicGames.push(myPublicGame);
        }
        $scope.myPublicGames = myPublicGames;
        $scope.$apply();
    });

    $scope.myOtherGames = $scope.myPublicGames = $scope.myMultiGames = $scope.myHotGames = [];

    $scope.deleteGame = function(id){
        $http.get('delete-game/'+id);
    };

    $scope.publized = false;

    $scope.publicGame = function(game){
        /* don't think this flow control is needed. Seems to be on the getting side that fails */
        if($scope.publized){
            return;
        }
        $scope.publized = true;
        if(game.public === "public"){
            $http.get('makePrivate/'+game.id).success(function(){console.log("MakePrivate ");$scope.publized = false;});
        }else{
            $http.get('makePublic/'+game.id).success(function(){console.log("MakePrivate ");$scope.publized = false;});
        }
    };

    sync.register("results", function(results){
        for(var i in results){
            var id = results[0].id;
            if(id.match(/^_/)){
                continue;
            }
            $("#" + id).animate({color: "#000", backgroundColor: "#fff"}, 800, function(){
                $(this).animate({color: "#fff", backgroundColor: "rgb(170,0,0)"}, 800, function(){
                    $(this).animate({color: "#000", backgroundColor: "#fff"}, 800, function(){
                        $(this).animate({color: "#fff", backgroundColor: "rgb(170,0,0)"}, 800, function(){
                            $(this).css("background-color", "").css("color", "");
                        });
                    });
                });
            });
        }
    });


    DR.scope = $scope;

    sync.fetch(0);
}]);

lobbyApp.factory('sync',function(){
    var Sync = require("./sync.js");
    var sync = new Sync(fetchUrl);
    return sync;
});

