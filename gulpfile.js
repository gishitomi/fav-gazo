'use strict';
let gulp = require('gulp');
let sass = require('gulp-sass');
let cleanCSS = require('gulp-clean-css'); // minify
let sourcemaps = require('gulp-sourcemaps'); // dev toolからscssの何行目か確認
let sassGlob = require('gulp-sass-glob'); // パーシャルの一括読み込み
let rename = require('gulp-rename'); // rename
let autoprefixer = require('gulp-autoprefixer'); // ベンダープレフィックス
gulp.task('sass', function() {
    return gulp.src('./sass/**/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sassGlob())
        .pipe(sass({ outputStyle: 'compact' }))
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./assets/css'));
});
gulp.task('minify', function() {
    return gulp.src("./assets/css/*.css")
        .pipe(cleanCSS())
        .pipe(rename({
            suffix: '.min',
        }))
        .pipe(gulp.dest('./assets/css'));
});
gulp.task('sass:watch', function() {
    gulp.watch('./sass/**/*.scss', gulp.task(['sass']));
});