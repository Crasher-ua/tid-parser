/* global require */

const gulp = require('gulp');
const webpack = require('webpack');
const webpackConfig = require('./webpack.config.js');
const del = require('del');
const concat = require('gulp-concat');
const runSequence = require('run-sequence');
const eslint = require('gulp-eslint');

gulp.task('clean', (callback) => {
    del(['./build'], callback);
});

gulp.task('eslint', () => {
    return gulp.src(['ng/*.js', '*.js'])
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failAfterError());
});

gulp.task('webpack', (callback) => {
    webpack(webpackConfig, callback);
});

gulp.task('concat-js', ['eslint', 'webpack'], () => {
    return gulp.src([
            '../../vendors/jquery.js',
            '../../vendors/bootstrap-slider.js',
            'build/tid-engine.js'
        ])
        .pipe(concat('tid-engine.js'))
        .pipe(gulp.dest('build'));
});

gulp.task('concat-css', () => {
    return gulp.src([
            '../../vendors/bootstrap.css',
            '../../vendors/bootstrap-slider.css',
            'style.css'
        ])
        .pipe(concat('tid-styles.css'))
        .pipe(gulp.dest('build'));
});

gulp.task('copy-css-fonts', () => {
    return gulp.src(['../../vendors/fonts/**']).pipe(gulp.dest('build/fonts'));
});

gulp.task('default', (done) => {
    runSequence('clean', ['concat-js', 'concat-css', 'copy-css-fonts'], done);
});

gulp.task('watch', () => {
    gulp.watch('style.css', ['concat-css']);
    gulp.watch('ng/*.js', ['concat-js']);
});
