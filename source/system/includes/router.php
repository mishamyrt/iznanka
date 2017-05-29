<?php
switch ($view->uri) {
 	case "/about":
 		$view->set('title', 'Тесты');
		$view->set('template', 'test.tpl');   
 	break;
}	