const cmd = require('node-cmd');
const gulp = require('gulp');
const zip = require('gulp-zip');
const pkg = require('./package.json');
const release = './release/';

gulp.task('default', function() {
    cmd.get('rm -r build');
    cmd.get('cp -r source build');
    cmd.get('php minify.php ' + './build/system/core.php');
    cmd.get('cp readme.txt build/readme.txt');
});

gulp.task('zip', function() {
    var version = pkg.version;
    return gulp.src('build/**/*')
        .pipe(zip('mishamyrt-averto-' + version + '.zip'))
        .pipe(gulp.dest(release));
});