var gulp = require('gulp'),
    del = require('del'),
    less = require('gulp-less'), // Компилятор .less
    runSeq = require('run-sequence'), // Порядок выполнения тасков: return runSeq('build:sass', ['build:css', 'build:js', 'copy:static'], done);
    browserSync = require('browser-sync').create(), // Перезагрузает браузер при сохранении изменений в файлах
    autoprefixer = require('gulp-autoprefixer'), // Автопрефиксер для CSS
    minifyCSS = require('gulp-csso'),  // Минификатор css
    rename = require('gulp-rename'), // Переименование файлов
    plumber = require('gulp-plumber'), // Обработчик ошибок в потоке
    notify = require('gulp-notify'),  // Генератор сообщений в консоли и области уведомлений
    concat = require('gulp-concat'),
    uglify = require('gulp-uglifyjs'),
    htmlmin = require('gulp-htmlmin'),
    rcs = require('gulp-rcs');

// Files & Paths
var MAIN_LESS = 'assets/less/main.less',
    MAIN_SCRIPT = 'assets/js/main.js',
    LESS_FILES = 'assets/less/**/*.less',
    HTML_FILES = 'assets/*.html',
    JS_FILES = 'assets/js/**/*.js';
ALL_FILES = 'assets/**/*';

// Browser synchronization start task
gulp.task('browser:sync', function() {
  browserSync.init({
    server: {
      baseDir: './app',
    },
    port: 8080,
    open: true,
    notify: false,
  });
});

// Html minmazer
gulp.task('compress:html', function() {
  var opts = {
    collapseWhitespace: true,
    removeAttributeQuotes: true,
    removeComments: true,
    minifyJS: true,
    minifyCSS: true,
  };

  return gulp.src(HTML_FILES).
      pipe(htmlmin(opts)).
      pipe(gulp.dest('app/html-min/'));
});

gulp.task('rcs:less', function() {
  return gulp.src(MAIN_LESS).
      pipe(plumber({
        errorHandler(err) {
          notify.onError(
              {message: 'Error: <%= error.message %>', title: 'LESS ERROR'})(
              err);
          this.emit('end');
        },
      })).
      pipe(less()).
      pipe(plumber.stop()).
      pipe(autoprefixer(['> 1%', 'ie 9'], {cascade: true})).
      pipe(gulp.dest('./dist/')).
      pipe(notify(
          {message: 'Finished: <%= file.relative %>', title: 'rcs:less OKAY'}));
});

gulp.task('rcs:all', function() {
  return gulp.src(['./dist/main.css', MAIN_SCRIPT, HTML_FILES]).
      pipe(rcs()).
      pipe(gulp.dest('./dist/'));
});

gulp.task('rcs:main-script-min', function() {
  return gulp.src(['./dist/main.js']).
      pipe(uglify()).
      pipe(rename(function(path) {
        path.basename += '.min';
      })).
      pipe(gulp.dest('./dist/'));
});

gulp.task('rcs:css-min', function() {
  return gulp.src('./dist/main.css').
      pipe(minifyCSS({restructure: false})).
      pipe(rename(function(path) {
        path.basename += '.min';
      })).
      pipe(gulp.dest('./dist/')).
      pipe(notify(
          {message: 'Finished: <%= file.relative %>', title: 'LESS OKAY'}));
});

// Less-to-css compiler
gulp.task('build:less', function() {
  return gulp.src(MAIN_LESS).
      pipe(plumber({
        errorHandler(err) {
          notify.onError(
              {message: 'Error: <%= error.message %>', title: 'LESS ERROR'})(
              err);
          this.emit('end');
        },
      })).
      pipe(less()).
      pipe(plumber.stop()).
      pipe(autoprefixer(['> 1%', 'ie 9'], {cascade: true})).
      pipe(minifyCSS({restructure: false})).
      pipe(rename(function(path) {
        path.basename += '.min';
      })).
      pipe(gulp.dest('public/assets/build')).
      pipe(notify(
          {message: 'Finished: <%= file.relative %>', title: 'LESS OKAY'})).
      pipe(browserSync.stream());
});

// Concatination js-scripts
gulp.task('build:scripts', function() {
  return gulp.src([
    'node_modules/jquery/dist/jquery.min.js',  // V-2.2.4
    'node_modules/jquery-scrollify/jquery.scrollify.js',
    'node_modules/slick-carousel/slick/slick.min.js',
    'node_modules/jquery-viewport-checker/src/jquery.viewportchecker.js',
    'node_modules/jquery.scroolly/src/jquery.scroolly.js',
    'node_modules/salvattore/dist/salvattore.min.js',
    'node_modules/noty/lib/noty.min.js',
    'node_modules/fotorama/fotorama.js',
    'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
    'node_modules/bootstrap-datepicker/dist/locales/bootstrap-datepicker.ru.min.js',
    'node_modules/jquery.maskedinput/src/jquery.maskedinput.js',
    'node_modules/jquery-validation/dist/jquery.validate.js',
    'node_modules/jquery-validation/dist/localization/messages_ru.js',
    'node_modules/velocity-animate/velocity.min.js',
    'node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.js',
    //'node_modules/lazysizes/lazysizes.js',
    'node_modules/odometer/odometer.js', // Прокрутка цифр
    'node_modules/gsap/TweenLite.js', // Анимации GreenSock
    'node_modules/gsap/CSSPlugin.js',  // Анимации GreenSock
    'assets/js/helpers/transition.js', // для плавного зума картинок
    'node_modules/zoom.js/dist/zoom.js',
  ]).
      pipe(concat('libs.min.js')).
      pipe(uglify()).
      pipe(gulp.dest('public/assets/build'));
});

// Concatination main script
gulp.task('build:main-script', function() {
  return gulp.src([MAIN_SCRIPT]).pipe(uglify()).pipe(rename(function(path) {
    path.basename += '.min';
  })).pipe(gulp.dest('public/assets/build'));
});

// Markup watcher
gulp.task('watch:files', function() {
  gulp.watch(LESS_FILES, ['build:less']);
  gulp.watch(HTML_FILES, browserSync.reload);
  gulp.watch(JS_FILES, ['build:main-script'], browserSync.reload);
});

// Autostart task
gulp.task('default',
    ['browser:sync', 'watch:files', 'build:scripts', 'build:main-script'],
    function() {
      console.log('Watching files init - OK');
    });
