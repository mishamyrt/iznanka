<?php
switch ($view->uri) {
 	case "/test":
 		$view->set('title', 'Тесты');
		$view->set('template', 'test.tpl');   
 	break;
}	