export default function TidEngineSlider($parse) {
    return {
        restrict: 'A',

        //TODO: use directive with template instead
        link($scope, $element, $attrs) {
            applySlider();
            applyInitialValue();
            handleSliderChange();

            function applySlider() {
                $element.slider({
                    ticks: getTicks(),
                    // ticks_labels:['$0','$100','$200','$300','$400'],
                    ticks_snap_bounds: 10
                });
            }

            function getTicks() {
                const ticksList = $attrs.tidEngineSlider;
                const ticksArray = `[${ticksList}]`;
                return $scope.$eval(ticksArray);
            }

            function applyInitialValue() {
                setNgModelValue($attrs.sliderValue);
            }

            function handleSliderChange() {
                $element.on('slide', function({value}) {
                    setNgModelValue(value);
                    $scope.$apply();
                });
            }

            function setNgModelValue(value) {
                $parse($attrs.ngModel).assign($scope, value);
            }
        }
    };
}
