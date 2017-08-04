<?php
addRoute('/test', function(){
    View::set('passed', array(
        'P',
        'a',
        's',
        's',
        'e',
        'd')
    );
    View::set('template', 'test/test.tpl');
    View::set('var', 'Passed');
});
function stopwatch()
{
    list ($usec, $sec) = explode (' ', microtime ());
    return ((float) $usec + (float) $sec);
}
