"use strict";

var gulp = require("gulp");
var sass = require("gulp-sass")(require("sass"));
var sourcemaps = require("gulp-sourcemaps");
var autoprefixer = require("gulp-autoprefixer");
var importer = require("node-sass-globbing");
var plumber = require("gulp-plumber");
var cssmin = require("gulp-cssmin");
var browserSync = require("browser-sync").create();
var concat = require("gulp-concat");
var minify = require("gulp-minify");

var cleanCss = require("gulp-clean-css");
var rename = require("gulp-rename");

var sass_config = {
  importer: importer,
  includePaths: [
    "node_modules/breakpoint-sass/stylesheets/",
    "node_modules/singularitygs/stylesheets/",
    "node_modules/compass-mixins/lib/",
  ],
};

var paths = {
  modify: ["./src/scss/**/*.scss", "./src/js/*.js"]
};


gulp.task("sass", function (done) {
  gulp
    gulp.src('./src/scss/*.scss')
    .pipe(sass())
    .on("error", sass.logError)
    .pipe(gulp.dest("./css/"))
    .pipe(
      cleanCss({
        keepSpecialComments: 0,
      })
    )
    .pipe(rename({ extname: ".min.css" }))
    .pipe(gulp.dest("./css/"))
    .on("end", done);
});

gulp.task("js", function (done) {
  return gulp
    .src(["./src/js/*.js"])
    .pipe(concat("js.js"))
    .pipe(minify())
    .pipe(gulp.dest("./js/"))
    .on("end", done);
});

gulp.task("watch", function () {
   gulp.watch(paths.modify, gulp.series("sass", "js"));
});


gulp.task("default", gulp.series("watch"));
