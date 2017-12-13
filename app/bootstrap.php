<?php
// Bootstrap = Начальная загрузка

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
	die ("PHP 5.4 or higher is required");
}

// Default = (E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED)
ini_set('display_errors', E_STRICT);
setlocale(LC_ALL, 'ru_RU.UTF-8', 'Russian_Russia.65001');
setlocale(LC_NUMERIC, 'en_US.UTF8'); // Символ десятичного разделения
mb_internal_encoding('UTF-8');


session_start();
if(stripos($_SERVER['REQUEST_URI'], '/user/') === false)
	$_SESSION['Referrer'] = $_SERVER['REQUEST_URI'];


// Автоматическая загрузка классов из  app/core/.
// SITE_PATH - локальный путь в корень сайта с финальным слэшем, определена в index.php
spl_autoload_register(function ($ClassName) {
    $filename = $ClassName.'.php';
    $file = APP.'core'.DS.$filename;

    if(!file_exists($file))
        return false;

    include($file);
    return true;
});



// Объект для хранения глобальных настроек
// implements ArrayAccess, поэтому можно обращаться как к массиву
$Stgs = Settings::getInstance();


// Дополнительные модули, реализующие различный функционал
// ...


// запускаем маршрутизатор
$FirstRoute = new Route;
$FirstRoute->start($Stgs);
