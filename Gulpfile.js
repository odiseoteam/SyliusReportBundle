var concat = require('gulp-concat');
var env = process.env.GULP_ENV;
var gulp = require('gulp');
var gulpif = require('gulp-if');
var livereload = require('gulp-livereload');
var merge = require('merge-stream');
var order = require('gulp-order');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var argv = require('yargs').argv;

var rootPath = '../../../web/assets/';
var adminRootPath = rootPath + 'admin/';
var nodeModulesPath = argv.nodeModulesPath;

var paths = {
    adminReports: {
        js: [
            'Resources/public/js/**',
        ]
    }
};

gulp.task('admin-reports-js', function () {
    return gulp.src(paths.adminReports.js)
        .pipe(concat('reports.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'js/'))
    ;
});

gulp.task('admin-reports-watch', function() {
    livereload.listen();

    gulp.watch(paths.adminReports.js, ['admin-reports-js']);
});

gulp.task('default', ['admin-reports-js']);
gulp.task('watch', ['default', 'admin-reports-watch']);
