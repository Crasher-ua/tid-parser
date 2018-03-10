const TidApp = angular.module('TidApp', []);

TidApp.directive('tidEngineForm', () => ({
    restrict: 'A',
    controller: TidEngineFormController,
    controllerAs: 'tidEngineFormCtrl'
}));

function TidEngineFormController() {
}
