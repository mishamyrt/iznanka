<?php
if (uri == '/test') {
    $view->set('passed', array(
        'P',
        'a',
        's',
        's',
        'e',
        'd')
    );
    $view->set('template', 'test.tpl');
}
function stopwatch()
{
    list ($usec, $sec) = explode (' ', microtime ());
    return ((float) $usec + (float) $sec);
}
