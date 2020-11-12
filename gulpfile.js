const gulp = require('gulp');
const babel = require('gulp-babel');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');
const cleanCSS = require('gulp-clean-css');
const sourcemaps = require('gulp-sourcemaps');
const sass = require('gulp-sass');
const autoprefixer = require('autoprefixer');
const postcss = require('gulp-postcss');
const del = require('del');

const paths = {
    styles: {
        src: 'dist/sass/**/*.scss',
        dest: 'assets/css/'
    },
    scripts: {
        dest: 'assets/js/',
        main: {
            src: 'dist/js/scripts.js',
        },
        additional: {
            src: 'dist/js/scripts-helper.js'
        }
    },
    vendors: {
        src: './node_modules',
        dest: 'assets/vendor'
    }
};

/**
 * Copy Vendor Files
 * @returns {*}
 */
function copyVendors() {
    return gulp.src(
        [
            paths.vendors.src + '/select2/dist/**/*',
        ]
    ).pipe(gulp.dest(paths.vendors.dest + '/select2/'))
}

/* Not all tasks need to use streams, a gulpfile is just another node program
 * and you can use all packages available on npm, but it must return either a
 * Promise, a Stream or take a callback and call it
 */
function clean() {
    // You can use multiple globbing patterns as you would with `gulp.src`,
    // for example if you are using del 2.0 or above, return its promise
    return del(['assets/css', 'assets/js']);
}

/*
 * Define our tasks using plain functions
 */
function styles() {
    return gulp.src(paths.styles.src)
    // .pipe(sourcemaps.init())
        .pipe(sass({
            errLogToConsole: true,
            outputStyle: 'compact',
            precision: 10
        }))
        .pipe(postcss([autoprefixer]))
        .pipe(gulp.dest(paths.styles.dest))
        .pipe(cleanCSS({
            level: {
                1: {
                    cleanupCharsets: true,
                    removeEmpty: true,
                    removeWhitespace: true,
                    specialComments: 0
                }
            }
        }))
        // pass in options to the stream
        .pipe(rename({
            basename: 'style',
            suffix: '.min'
        }))
        // .pipe(sourcemaps.write('./maps'))
        .pipe(gulp.dest(paths.styles.dest));
}

function main_script() {
    return gulp.src(paths.scripts.main.src, {sourcemaps: true})
        .pipe(babel({
            presets: ['@babel/env']
        }))
        .pipe(gulp.dest(paths.scripts.dest))
        .pipe(uglify())
        .pipe(concat('scripts.min.js'))
        .pipe(gulp.dest(paths.scripts.dest));
}

function additional_script() {
    return gulp.src(paths.scripts.additional.src, {sourcemaps: true})
        .pipe(babel({
            presets: ['@babel/env']
        }))
        .pipe(gulp.dest(paths.scripts.dest))
        .pipe(uglify())
        .pipe(concat('scripts-helper.min.js'))
        .pipe(gulp.dest(paths.scripts.dest));
}

function watchFiles() {
    gulp.watch(paths.scripts.main.src, main_script);
    gulp.watch(paths.scripts.additional.src, additional_script);
    gulp.watch(paths.styles.src, styles);
}

/*
 * Specify if tasks run in series or parallel using `gulp.series` and `gulp.parallel`
 */
const build = gulp.series(clean, gulp.parallel(styles, main_script, additional_script, copyVendors));
// const build = gulp.series(modules, gulp.parallel(styles, scripts));
const watch = gulp.series(build, gulp.parallel(watchFiles));

/*
 * You can use CommonJS `exports` module notation to declare tasks
 */
exports.clean = clean;
exports.styles = styles;
exports.watch = watch;
exports.build = build;
/*
 * Define default task that can be called by just running `gulp` from cli
 */
exports.default = build;