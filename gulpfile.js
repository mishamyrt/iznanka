const cmd     = require('node-cmd');
const gulp    = require('gulp');
const zip     = require('gulp-zip');
const pkg     = require('./package.json');
const release = './release';
const build   = './build';
const source  = './source';

gulp.task('default', function () {
    cmd.get('rm -r build');
    cmd.get('cp -r ' + source + ' ' + build);
    cmd.get('php minify.php ' + build + '/system/core.php');
    cmd.get('cp readme.txt ' + build + '/readme.txt');
    cmd.get('rm ' + build + '/system/includes/test.php');
    cmd.get('rm ' + build + '/system/tpl/test.tpl');
    cmd.get('rm ' + build + '/system/tpl/subtest.tpl');
});

gulp.task('zip', function () {
    var version = pkg.version;
    return gulp.src(build + '/**/*', { dot: true })
        .pipe(zip('mishamyrt-iznanka-' + version + '.zip'))
        .pipe(gulp.dest(release));
});