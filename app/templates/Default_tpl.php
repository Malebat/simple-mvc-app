<?php
if($data['Page']['title'] == 'Страница не найдена') {
	header("HTTP/1.0 404 Not Found");
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
}


include(APP.'templates'.DS.$tpl_view.'_header_tpl.php');

include(APP.'views'.DS.$pg_view.'.php');

include(APP.'templates'.DS.$tpl_view.'_footer_tpl.php');

