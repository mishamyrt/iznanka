var cmd = require('node-cmd');
var gulp = require('gulp');
var cwd = '';
cmd.get('cwd',
    function(data) {
        cwd = data;
    }
);

gulp.task('default', function() {
    cmd.get('rm -r build');
    cmd.get('cp -r source build');
    cmd.get('php minify.php ' + './build/system/core.php');
    cmd.get('cp readme.txt build/readme.txt');
});