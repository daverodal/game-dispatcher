import LobbyController from "./lobby-controller.js";
export default class SubLobbyController extends LobbyController {
    constructor ($scope, $http, sync) {
        super($scope,$http,sync);

        sync.register('lobbies', function (lobbies) {
            console.log("Sub Hi lobbies");
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
    }
}