/**
 * @author Michael Hillcox
 * @desc The purpose of this file is used to compile our static assets
 *       dynamically. This file is a general purpose example for building
 *       out a gulp file for each project
 */

const gulp = require('gulp'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    autoprefixer = require('gulp-autoprefixer'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify'),
    livereload = require('gulp-livereload'),
    concat = require('gulp-concat');

// Todo: move this to somewhere else :P
const config = {
    deploy: false,
    scss: {
        main: ["src/scss/master.scss", "src/scss/error.scss"],
        files: "src/scss/**/*.scss",
        output: "public/assets/styles/",
        compileOptions: {
            errLogToConsole: true,
            outputStyle: 'compressed'
        },
        prefixOptions: {
            browsers: ['last 3 versions'],
            cascade: false
        }
    },
    php: {
        files: ['app/**/*.php', "public/**/*.php", 'plugins/**/*.php', 'index.html']
    }
};

// Normal tasks
gulp.task('default', ['scss', 'watch']);
gulp.task('build', ['scss']);

gulp.task('scss', function () {
    return gulp
        .src(config.scss.main)
        .pipe(sourcemaps.init())
        .pipe(sass(config.scss.compileOptions).on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(autoprefixer(config.scss.prefixOptions))
        .pipe(rename(function (path) {
            path.basename += ".min";
        }))
        .pipe(gulp.dest(config.scss.output))
        .pipe(livereload())
});

gulp.task('watch', function() {
    livereload.listen();
    gulp.watch(config.scss.files, ['scss']);
    // gulp.watch(config.js.files, ['js']);
    gulp.watch(config.php.files, livereload.reload);
});