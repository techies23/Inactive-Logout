/**
 * Load Plugins.
 *
 * Load gulp plugins and assing them semantic names.
 */
var gulp = require('gulp');

// CSS related plugins.
var sass = require('gulp-sass'); // Gulp pluign for Sass compilation
var autoprefixer = require('gulp-autoprefixer'); // Autoprefixing magic
var minifycss = require('gulp-uglifycss'); // Minifies CSS files

// JS related plugins.
var concat = require('gulp-concat'); // Concatenates JS files
var plumber = require('gulp-plumber');
var jshint = require('gulp-jshint');
var uglify = require('gulp-uglify'); // Minifies CSS files

// Utility related plugins.
var rename = require('gulp-rename');
var sourcemaps = require('gulp-sourcemaps');
var notify = require('gulp-notify');

/**
 * Configuration.
 *
 * Project Configuration for gulp tasks.
 *
 * Edit the variables as per your project requirements.
 */
var styleSRC = './dist/sass/**/*.scss'; // Path to main .scss file
var styleDestination = './assets/css/'; // Path to place the compiled CSS file

var inactiveLogoutSrc = './dist/js/inactive-logout.js'; // Path to JS custom scripts folder
var inactiveLogoutSrcDestination = './assets/js/'; // Path to place the compiled JS custom scripts file

var inactiveLogoutOtherSrc = './dist/js/inactive-logout-other.js';
var inactiveLogoutOtherDestination = './assets/js/';

// Copy third party libraries from /node_modules into /vendor
gulp.task('vendor', function () {

    //Select2
    gulp.src([
        './node_modules/select2/dist/**/*',
    ])
        .pipe(gulp.dest('./vendor/select2'));
});

/**
 * Task: vendorJS
 *
 * Concatenate and uglify vendor JS scripts.
 *
 * This task does the following:
 *    1. Gets the source folder for JS vendor files
 *    2. Concatenates all the files and generates vendors.js
 *    3. Renames the JS file with suffix .min.js
 *    4. Uglifes/Minifies the JS file and generates vendors.min.js
 */
gulp.task('styles', function () {
    return gulp.src(styleSRC)
        .pipe(plumber({errorHandler: notify.onError("Error: <%= error.message %>")}))
        .pipe(sourcemaps.init())
        .pipe(sass({
            errLogToConsole: true,
            outputStyle: 'compact',
            precision: 10
        }))
        .pipe(sourcemaps.write({includeContent: false}))
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(autoprefixer(
            'last 2 version',
            '> 1%',
            'safari 5',
            'ie 8',
            'ie 9',
            'opera 12.1',
            'ios 6',
            'android 4'))
        .pipe(sourcemaps.write('./maps'))
        .pipe(gulp.dest(styleDestination))
        .pipe(minifycss({
            "maxLineLen": 80,
            "uglyComments": true
        }))
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(styleDestination))
    // .pipe( notify( { message: 'TASK: "styles" Completed! 💯', onLast: true } ) );
});

/**
 * Task: customJS
 *
 * Concatenate and uglify custom JS scripts.
 *
 * This task does the following:
 *    1. Gets the source folder for JS custom files
 *    2. Concatenates all the files and generates custom.js
 *    3. Renames the JS file with suffix .min.js
 *    4. Uglifes/Minifies the JS file and generates custom.min.js
 */
gulp.task('mainJS', function () {
    gulp.src(inactiveLogoutSrc)
        .pipe(plumber({errorHandler: notify.onError("Error: <%= error.message %>")}))
        .pipe(jshint())
        .pipe(jshint.reporter('jshint-stylish'))
        .pipe(concat('scripts.js'))
        .pipe(gulp.dest(inactiveLogoutSrcDestination))
        .pipe(rename({
            basename: 'scripts',
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest(inactiveLogoutSrcDestination))
    // .pipe( notify( { message: 'TASK: "customJs" Completed!', onLast: true } ) );
});

gulp.task('helperJS', function () {
    gulp.src(inactiveLogoutOtherSrc)
        .pipe(plumber({errorHandler: notify.onError("Error: <%= error.message %>")}))
        .pipe(jshint())
        .pipe(jshint.reporter('jshint-stylish'))
        .pipe(concat('scripts-helper.js'))
        .pipe(gulp.dest(inactiveLogoutOtherDestination))
        .pipe(rename({
            basename: 'scripts-helper',
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest(inactiveLogoutOtherDestination))
});

// Default task
gulp.task('default', ['styles', 'mainJS', 'helperJS', 'vendor'], function () {
    gulp.watch('./dist/sass/*.scss', ['styles']);
    gulp.watch('./dist/js/*.js', ['mainJS', 'helperJS']);
});
