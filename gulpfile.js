const { series, watch, dest, src } = require('gulp');

const sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    autoprefixer = require('gulp-autoprefixer'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify'),
    livereload = require('gulp-livereload'),
    concat = require('gulp-concat');

const config = {
    deploy: false,
    scss: {
        main: ["src/scss/master.scss", "src/scss/error.scss"],
        files: "src/scss/**/*.scss",
        output: "public/assets/styles/",
        compileOptions: {
            includePaths: ['node_modules/bootstrap/scss'],
            errLogToConsole: true,
            outputStyle: 'compressed'
        },
        prefixOptions: {
            overrideBrowserslist: ['last 3 versions'],
            cascade: false
        }
    },
    js: {
        files: [
            "node_modules/jquery/dist/jquery.js",
            "node_modules/bootstrap/dist/js/bootstrap.bundle.js",
            "src/js/**/*.js"
        ],
        output: "public/assets/js/"
    },
    php: {
        files: ['app/**/*.php', "public/**/*.php", 'plugins/**/*.php', 'index.html']
    }
};

const js = () => {
    return src(config.js.files)
        .pipe(concat('app.min.js'))
        .pipe(uglify())
        .pipe(dest(config.js.output))
        .pipe(livereload())
};

const scss = () => {
    return src(config.scss.main)
        .pipe(sourcemaps.init())
        .pipe(sass(config.scss.compileOptions).on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(autoprefixer(config.scss.prefixOptions))
        .pipe(rename((path) => {
            path.basename += ".min";
        }))
        .pipe(dest(config.scss.output))
        .pipe(livereload())
};

const watcher = () => {
    livereload.listen();
    watch(config.scss.files, scss);
    watch(config.js.files, js);
    watch(config.php.files, livereload.reload)
};

exports.build = series(scss, js);
exports.default = series(
    scss,
    js,
    watcher
);