import SyncController from "./sync-controller.js";
import axios from "axios";
export default class LobbyController extends SyncController{
    constructor($scope, $http, sync) {
        $scope.myOtherGames = $scope.myPublicGames = $scope.myMultiGames = $scope.myHotGames = $scope.chats =  [];


        $scope.lobbySort = 'timestamp';
        $scope.lobbySortDir = true;
        $scope.multiSort = 'timestamp';
        $scope.multiSortDir = true;
        $scope.otherSort = 'timestamp';
        $scope.otherSortDir = true;
        $scope.publicSort = 'timestamp';
        $scope.publicSortDir = true;
        $scope.speak = '';
        super($scope, $http,sync);

        this.$http = $http;
        this.$scope = $scope;






        $scope.publized = false;

        DR.scope = $scope;

        sync.fetch(0);
    }

    doChatPostRequest(chat) {
        debugger;
        var mychat = $("#mychat").attr("value");
        let data = {chat: chat};
        let obj;
        axios.post("/wargame/chatPoke", data).then((response) => {
        }).catch(error => {
        });
        this.$scope.speak = '';
    }

    deleteGame(game) {
        let gameOver = game.myTurn;
        if(game.gameType === 'multi' && gameOver !== 'gameOver'){
            let answer = confirm('Game not over, still want to delete? ');
            if(answer === false){
                return;
            }
        }
        this.$http.get('delete-game/' + game.id);
    }

    colSort(column) {
        console.log("sorry");
        let $scope = this.$scope;
        if (column === $scope.lobbySort) {
            $scope.lobbySortDir = !$scope.lobbySortDir;
            return;
        }
        $scope.lobbySort = column;
        $scope.lobbySortDir = false;
    }

    otherColSort(column) {
        let $scope = this.$scope;
        if (column === $scope.otherSort) {
            $scope.otherSortDir = !$scope.otherSortDir;
            return;
        }
        $scope.otherSort = column;
        $scope.otherSortDir = false;
    };

    publicColSort(column) {
        let $scope = this.$scope;

        if (column === $scope.publicSort) {
            $scope.publicSortDir = !$scope.publicSortDir;
            return;
        }
        $scope.publicSort = column;
        $scope.publicSortDir = false;
    };


    publicGame(game) {
        let $scope = this.$scope;

        /* don't think this flow control is needed. Seems to be on the getting side that fails */
        if ($scope.publized) {
            return;
        }
        $scope.publized = true;
        if (game.public === "public") {
            this.$http.get('make-private/' + game.id).then((arg) => {
                console.log("prive it");
                $scope.publized = false;

            });
        } else {
            this.$http.get('make-public/' + game.id).then((arg) => {
                console.log("publ it");
                $scope.publized = false;
            })
        }
    }

    multiColSort(column) {
        let $scope = this.$scope;

        if (column === $scope.multiSort) {
            $scope.multiSortDir = !$scope.multiSortDir;
            return;
        }
        $scope.multiSort = column;
        $scope.multiSortDir = false;
    };
}
LobbyController.$inject = ['$scope', '$http', 'sync'];