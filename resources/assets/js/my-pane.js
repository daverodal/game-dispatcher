export default class MyPane {
    constructor() {
        this.require = '^myTabs',
            this.restrict = 'E',
            this.transclude = true,
            this.scope = {
                title: '@',
                isSelected: '@'

            }
        this.templateUrl = '/tab-pane.html'

    }

    link(scope, element, attrs, tabsCtrl) {
        tabsCtrl.addPane(scope);
    }
}