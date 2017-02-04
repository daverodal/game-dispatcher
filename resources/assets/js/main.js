import angular from "angular";
import angularSanitize from "angular-sanitize";
import angularModalService from "angular-modal-service";
import angulaRightClick from "angular-right-click";
import $ from "jquery";
window.$ = $;
window.jQuery = $;
import MyPane from "./my-pane.js";
import MyTabs from "./my-tabs.js";
import LobbyController from "./lobby-controller.js";
import Sync from "./sync.js";

var lobbyApp = angular.module('lobbyApp', []);
lobbyApp.directive('myTabs', MyTabs)
    .directive('myPane', () =>  new MyPane);

lobbyApp.controller('LobbyController', ['$scope', '$http', 'sync', LobbyController]);

lobbyApp.factory('sync',function(){
    var sync = new Sync(fetchUrl);
    return sync;
});

export default lobbyApp;