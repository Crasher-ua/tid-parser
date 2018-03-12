const gulp = require('gulp');
const webpack = require('webpack');
const webpackConfig = require('./webpack.config.js');
const del = require('del');
const concat = require('gulp-concat');

gulp.task('clean', (callback) => {
    del(['./build'], callback);
});

gulp.task('webpack', ['clean'], (callback) => {
    webpack(webpackConfig, callback);
});

gulp.task('concat-js', ['webpack'], () => {
    return gulp.src([
            '../../vendors/jquery.js',
            '../../vendors/bootstrap-slider.js',
            'build/tid-engine.js'
        ])
        .pipe(concat('tid-engine.js'))
        .pipe(gulp.dest('build'));
});

gulp.task('concat-css', ['clean'], () => {
    return gulp.src([
            '../../vendors/bootstrap.css',
            '../../vendors/bootstrap-slider.css',
            'style.css'
        ])
        .pipe(concat('tid-styles.css'))
        .pipe(gulp.dest('build'));
});

gulp.task('copy-css-fonts', ['clean'], () => {
    return gulp.src(['../../vendors/fonts/**']).pipe(gulp.dest('build/fonts'));
});

gulp.task('default', ['concat-js', 'concat-css', 'copy-css-fonts']);
