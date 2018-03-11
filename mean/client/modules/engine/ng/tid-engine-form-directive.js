import TidEngineFormController from 'tid-engine-form-controller';

export default function TidEngineForm() {
    return {
        restrict: 'A',
        controller: TidEngineFormController,
        controllerAs: 'tidEngineFormCtrl',
        link($scope, $element, $attrs, tidEngineFormCtrl) {
            tidEngineFormCtrl.init();
        }
    };
}
