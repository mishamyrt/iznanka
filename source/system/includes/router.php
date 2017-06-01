<?php
switch ($view->uri) {
    case "/about":
        $view->set('title', 'Тесты');
        $view->set('template', 'test.tpl');
        break;
    case "/":
       $view->set('items', array(
    array ('title' => 'Первый',
           'value' => '1'),
        array ('title' => 'Первый',
           'value' => '1'),
               array ('title' => 'Первый',
           'value' => '1'),
               array ('title' => 'Первый',
           'value' => '1')));
    break;
}
