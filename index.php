<?php
/*
	Простое веб-приложение, построенное по шаблону проектирования MVC.
	Точка входа.
	Этот файл д.б. без BOM.

	Разработка: 2017 mk@ad-res.ru по заказу АО "ЦКБ ТМ"
*/


define ('DS', DIRECTORY_SEPARATOR);
define ('SITE_PATH', realpath(dirname(__FILE__).DS).DS);
define ('APP', SITE_PATH.'app'.DS);

require_once(APP.'bootstrap.php');
