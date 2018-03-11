import TidEngineForm from 'tid-engine-form-directive';
import TidEngineSlider from 'tid-engine-slider-directive';

const TidApp = angular.module('TidApp', []);

TidApp
    .directive('tidEngineForm', TidEngineForm)
    .directive('tidEngineSlider', TidEngineSlider);
