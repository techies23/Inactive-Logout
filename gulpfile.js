var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var header = require('gulp-header');
var cleanCSS = require('gulp-clean-css');
var rename = require("gulp-rename");
var uglify = require('gulp-uglify');
var pkg = require('./package.json');
var concat = require('gulp-concat');

// Set the banner content
var banner = ['/*!\n',
    ' * Deepen - <%= pkg.title %> v<%= pkg.version %>\n',
    ' * Copyright 2019-' + (new Date()).getFullYear(), ' <%= pkg.author %>\n',
    ' */\n',
    '\n'
].join('');

// Copy third party libraries from /node_modules into /vendor
gulp.task('vendor', function () {
   //Select2
    gulp.src([
        './node_modules/select2/dist/**/*',
    ])
        .pipe(gulp.dest('./vendor/select2'));
});

// Compile SCSS
gulp.task('css:compile', function () {
    return gulp.src('./dist/css/**/*.css')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write('./maps'))
        .pipe(header(banner, {
            pkg: pkg
        }))
        .pipe(gulp.dest('./assets/css'))
});

// Minify CSS
gulp.task('css:minify', ['css:compile'], function () {
    return gulp.src([
        './assets/css/*.css',
        '!./assets/css/*.min.css'
    ])
        .pipe(cleanCSS())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('./assets/css'))
});

// CSS
gulp.task('css', ['css:compile', 'css:minify']);

// Minify JavaScript
var jsFiles = 'dist/js/inactive-logout.js', jsDest = 'assets/js';
var jsFilesOther = 'dist/js/inactive-logout-other.js', jsDest = 'assets/js';
gulp.task('js:minify', function () {
    return gulp.src(jsFiles)
        .pipe(concat('scripts.js'))
        .pipe(gulp.dest(jsDest))
        .pipe(rename('scripts.min.js'))
        .pipe(uglify())
        .pipe(header(banner, {
            pkg: pkg
        }))
        .on('error', function (err) {
            console.log(err)
        })
        .pipe(gulp.dest(jsDest))
});

gulp.task('js_other:minify', function () {
    return gulp.src(jsFilesOther)
        .pipe(concat('scripts-other.js'))
        .pipe(gulp.dest(jsDest))
        .pipe(rename('scripts-other.min.js'))
        .pipe(uglify())
        .pipe(header(banner, {
            pkg: pkg
        }))
        .on('error', function (err) {
            console.log(err)
        })
        .pipe(gulp.dest(jsDest))
        .pipe( gulp.src(jsFilesOther) )
});

// JS
gulp.task('js', ['js:minify', 'js_other:minify']);

// Default task
gulp.task('default', ['css', 'js', 'vendor'], function () {
    gulp.watch('./dist/css/*.css', ['css']);
    gulp.watch('./dist/js/*.js', ['js']);
});
