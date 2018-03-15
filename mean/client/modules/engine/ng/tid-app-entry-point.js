import 'angular';
import TidEngineForm from 'tid-engine-form-directive';
import TidEngineSlider from 'tid-engine-slider-directive';
import TidEngineResource from 'tid-engine-resource';

const TidApp = angular.module('TidApp', []);

TidApp
    .directive('tidEngineForm', TidEngineForm)
    .directive('tidEngineSlider', TidEngineSlider)
    .service('tidEngineResource', TidEngineResource);
