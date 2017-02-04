export default function() {
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
}