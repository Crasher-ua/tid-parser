const gulp = require('gulp');
const webpack = require('webpack');
const webpackConfig = require('./webpack.config.js');
const del = require('del');

gulp.task('default', (callback) => {
    del(['./build']);
    webpack(webpackConfig, callback);
});
