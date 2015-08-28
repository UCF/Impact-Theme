var gulp = require('gulp'),
    sass = require('gulp-sass'),
    minifyCss = require('gulp-minify-css'),
    bless = require('gulp-bless'),
    notify = require('gulp-notify'),
    bower = require('gulp-bower'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    jshint = require('gulp-jshint'),
    jshintStylish = require('jshint-stylish'),
    scsslint = require('gulp-scss-lint'),
    vinylPaths = require('vinyl-paths');
    // browserSync = require('browser-sync').create(),
    // reload = browserSync.reload;

var config = {
  sassPath: './static/scss',
  cssPath: './static/css',
  jsPath: './static/js',
  fontPath: './static/fonts',
  phpPath: './',
  bowerDir: './static/bower_components',
  devPath: './dev'
};


// Run Bower
gulp.task('bower', function() {
  return bower()
    .pipe(gulp.dest(config.bowerDir))
    .on('end', function() {

      // Add Glyphicons to fonts dir
      gulp.src(config.bowerDir + '/bootstrap-sass-official/assets/fonts/*/*')
        .pipe(gulp.dest(config.fontPath));

    });
});


// Process .scss files in /static/scss/
gulp.task('css-static', function() {
  return gulp.src(config.sassPath + '/*.scss')
    .pipe(scsslint())
    .pipe(sass().on('error', sass.logError))
    .pipe(minifyCss({compatibility: 'ie8'}))
    .pipe(rename('style.min.css'))
    .pipe(bless())
    .pipe(gulp.dest(config.cssPath));
    // .pipe(browserSync.stream());
});


// Process .scss files in /dev/ directory
gulp.task('css-dev', function() {
  return gulp.src(config.devPath + '/**/*.scss')
    .pipe(scsslint())
    .pipe(sass().on('error', sass.logError))
    .pipe(minifyCss({compatibility: 'ie8'}))
    .pipe(gulp.dest(config.devPath));
});


// Process all .scss files
gulp.task('css', ['css-static', 'css-dev']);


// Lint, concat and uglify js files.
gulp.task('js', function() {

  // Run jshint on all js files in jsPath (except already minified files.)
  return gulp.src([config.jsPath + '/*.js', '!' + config.jsPath + '/*.min.js'])
    .pipe(jshint())
    .pipe(jshint.reporter('jshint-stylish'))
    .pipe(jshint.reporter('fail'))
    .on('end', function() {

      // Combine and uglify js files to create script.min.js.
      var minified = [
        config.bowerDir + '/bootstrap-sass-official/assets/javascripts/bootstrap.js',
        config.jsPath + '/generic-base.js',
        config.jsPath + '/script.js'
      ];

      gulp.src(minified)
        .pipe(concat('script.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(config.jsPath));

    });
});


// Rerun tasks when files change
gulp.task('watch', function() {
  // browserSync.init({
  //     proxy: {
  //       target: "localhost/wordpress/impact"
  //     }
  // });
  // gulp.watch(config.jsPath + '/*.js', ['js']).on('change', reload);
  // gulp.watch(config.phpPath + '/*.php').on('change', reload);
  // gulp.watch(config.phpPath + '/*.php');

  gulp.watch(config.sassPath + '/*.scss', ['css-static']);
  gulp.watch(config.devPath + '/**/*.scss', ['css-dev']);
  gulp.watch(config.jsPath + '/*.js', ['js']);
});


// Default task
gulp.task('default', ['bower', 'css', 'js']);
