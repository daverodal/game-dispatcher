/**
 * Created by david on 2/4/17.
 */
/**
 * Created by PhpStorm.
 * User: david
 * Date: 2/4/17
 * Time: 12:43 PM

 /*
 * Copyright 2012-2017 David Rodal

 * This program is free software; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation;
 * either version 2 of the License, or (at your option) any later version

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

export default class SyncController{

    constructor($scope, $http,sync) {

        sync.register('chats', function(chats) {
            var hotChats = [];
            for (var i in chats) {
                if(chats[i].date){
                    let yesterday = new Date();
                    yesterday.setDate(yesterday.getDate() - 1);
                    if(yesterday.getTime() <= (chats[i].date * 1000)){
                        hotChats.unshift(chats[i]);
                    }
                }
            }
            $scope.chats = hotChats;
            $scope.$apply();
        });
        sync.register('lobbies', function (lobbies) {
            console.log("Hi lobbies");
            var myHotLobbies = [];
            for (var i in lobbies) {
                var myHotLobby = {};
                myHotLobby = lobbies[i];
                myHotLobby.pubLinkLabel = lobbies[i].public === 'public' ? 'private' : 'public';
                myHotLobbies.push(myHotLobby);
            }
            $scope.myHotGames = myHotLobbies;
            $scope.$apply();
        });

        sync.register('multiLobbies', function (multiGames) {
            var myMultiGames = [];
            for (var i in multiGames) {
                var myMultiGame = {};
                myMultiGame = multiGames[i];
                myMultiGame.pubLinkLabel = multiGames[i].public === 'public' ? 'private' : 'public';
                myMultiGames.push(myMultiGame);
            }
            $scope.myMultiGames = myMultiGames;
            $scope.$apply();
        });


        sync.register('otherGames', function (otherGames) {
            var myOtherGames = [];
            for (var i in otherGames) {
                var myOtherGame = {};
                myOtherGame = otherGames[i];
                myOtherGame.pubLinkLabel = otherGames[i].public === 'public' ? 'private' : 'public';
                myOtherGames.push(myOtherGame);
            }
            $scope.myOtherGames = myOtherGames;
            $scope.$apply();
        });

        sync.register('publicGames', function (publicGames) {
            var myPublicGames = [];
            for (var i in publicGames) {
                var myPublicGame = {};
                myPublicGame = publicGames[i];
                myPublicGame.pubLinkLabel = publicGames[i].public === 'public' ? 'private' : 'public';
                myPublicGames.push(myPublicGame);
            }
            $scope.myPublicGames = myPublicGames;
            $scope.$apply();
        });


        sync.register("results", function (results) {
            for (var i in results) {
                var id = results[0].id;
                if (id.match(/^_/)) {
                    continue;
                }
                $("#" + id).animate({color: "#000", backgroundColor: "#fff"}, 800, function () {
                    $(this).animate({color: "#fff", backgroundColor: "rgb(170,0,0)"}, 800, function () {
                        $(this).animate({color: "#000", backgroundColor: "#fff"}, 800, function () {
                            $(this).animate({color: "#fff", backgroundColor: "rgb(170,0,0)"}, 800, function () {
                                $(this).css("background-color", "").css("color", "");
                            });
                        });
                    });
                });
            }
        });
    }
}