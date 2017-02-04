import SyncController from "./sync-controller.js";
export default class LobbyController extends SyncController{
    constructor($scope, $http, sync) {
        $scope.myOtherGames = $scope.myPublicGames = $scope.myMultiGames = $scope.myHotGames = [];


        $scope.lobbySort = 'timestamp';
        $scope.lobbySortDir = true;
        $scope.multiSort = 'timestamp';
        $scope.multiSortDir = true;
        $scope.otherSort = 'timestamp';
        $scope.otherSortDir = true;
        $scope.publicSort = 'timestamp';
        $scope.publicSortDir = true;
        super($scope, $http,sync);

        this.$scope = $scope;




        $scope.deleteGame = (id) => {
            $http.get('delete-game/' + id);
        };

        $scope.publized = false;

        DR.scope = $scope;

        sync.fetch(0);
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
            $http.get('make-private/' + game.id).success(function () {
                console.log("MakePrivate ");
                $scope.publized = false;
            });
        } else {
            $http.get('make-public/' + game.id).success(function () {
                console.log("MakePrivate ");
                $scope.publized = false;
            });
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