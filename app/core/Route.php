<?php
/*
	Класс-маршрутизатор для определения запрашиваемой страницы.
		подключает классы контроллеров и моделей;
		создает экземпляры контроллеров страниц и вызывает действия этих контроллеров.

	Принцип именования:
		классы 			- Pascal (UpperCamelCase)
		actions 		- camelCase
		представления (виды) - пути и папки в kebab-case
		модели 			- Pascal  (таблица cat_name -> модель CatName)

	Разбор URI (похоже на Yii2):
		single 			- DefaultController + actionSingle
		one/two			- OneController + actionTwo
		one/two/three	- app\controllers\one\TwoController.php + actionThree
		one-one/two-two/three - ...\one-one\TwoTwoController.php + actionThree


	/
	/dept/view/NNN
	/search/name


	Разработка: 2017 mk@ad-res.ru по заказу АО "ЦКБ ТМ"
*/


class Route {

	public function start($Stgs) {
		// Синхронизация c Active Directory
		if(file_exists(SITE_PATH.'phone_dir_last_update.json')) {
			$syncronized = json_decode(file_get_contents('phone_dir_last_update.json'), true);
			if($syncronized['json'] < (time() - $Stgs['stuff']['SyncInterval'])) {
				$ldap_success = false; // Д.б. false, для отладки можно true и закомментировать следующую строку
				include(SITE_PATH.'phone_dir_import.php');
				if($ldap_success) {
					$syncronized['json'] = time();
					if(filesize('phone_dir_depts_list.json') > 1000) {
						$db_r = new Database($Stgs['model'], 'dept');
						$aDepts = json_decode(file_get_contents('phone_dir_depts_list.json'), true);
						$db_r->SyncronizeDeptsList($aDepts);
						unset($aDepts);
						$db_r = null;
					}
					if(filesize('phone_directory.json') > 10000) {
						$db_r = new Database($Stgs['model'], 'sotrudnik');
						$aSotr = json_decode(file_get_contents('phone_directory.json'), true);
						$db_r->SyncronizeSotrudnik($aSotr);
						unset($aSotr);
						$db_r = null;
					}
					$syncronized['sqlite'] = time();
					file_put_contents('phone_dir_last_update.json', json_encode($syncronized, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
				}
			}
		}

		$routes = urldecode($_SERVER['REQUEST_URI']);

		// Страница (контроллер и экшен) по умолчанию
		$ControllerPath = '';
		$ControllerName = 'page';
		$ActionName = 'index';

		if(preg_match('/^[\w\-\.\/&=\?]+$/ui', $routes)) {
			$routes = strtolower($routes);
			// Убираем первый и последний слеш
			$routes = trim($routes, ' /-.;&?');

			if($routes != '') {
				// Проверяем нет ли в БД страницы с запрашиваемым URI
				$db_r = new Database($Stgs['model'], 'page');
				if($page_id = $db_r->getPageIdByURI($routes)) {
					include(APP . 'controllers' . DS . 'PageController.php');
					include(APP . 'models' . DS . 'PageModel.php');
					$controller = new PageController($Stgs);
					$controller->actionShow($page_id);
					return true;
				}
				$db_r = null;
			}

			// Разбор URI на контроллеры и экшены
			$routes = explode('/', $routes);

			switch(count($routes)) {
				case 1:
					if(!empty($routes[0]))
						$ActionName = $routes[0];
					break;
				case 2:
					if(!empty($routes[0]) && !empty($routes[1])) {
						$ControllerName = $routes[0];
						$ActionName = $routes[1];
					}
					else {
						$ActionName = '404';
					}
					break;
				case 3:
					if(!empty($routes[0]) && !empty($routes[1]) && !empty($routes[2])) {
						if(preg_match('/^[\d]+$/ui', $routes[2])) {
							// Третья часть URI - параметр действия
							$ControllerName = $routes[0];
							$ActionName = $routes[1];
						}
						else {
							// Третья часть URI - не число, значит первая часть - папка
							$ControllerPath = $routes[0] . DS;
							$ControllerName = $routes[1];
							$ActionName = $routes[2];
						}
					}
					else {
						// URI содержит пустую часть
						$ActionName = '404';
					}
					break;
			}
		}
		else {
			// URI содержит запрещённые символы
			$ActionName = '404';
		}


		$ControllerName = $this->BuildName($ControllerName, 'Controller');
		$ActionName = $this->BuildName($ActionName, 'action');


		// подключаем файл с классом контроллера
		$controller_file = APP.'controllers'.DS.$ControllerPath.$ControllerName.'.php';
		if(file_exists($controller_file))
			include($controller_file);
		else {
			$ControllerName = 'PageController';
			$ActionName = 'action404';
			include(APP.'controllers'.DS.$ControllerName.'.php');
		}


		// подключаем файл с классом модели (файла модели может и не быть)
		$ModelName = strstr($ControllerName, 'Controller', true);
		$ModelName .= 'Model';
		$model_file = APP.'models'.DS.$ModelName.'.php';
		if(file_exists($model_file))
			include($model_file);

		// создаем контроллер
		$controller = new $ControllerName($Stgs);

		if(method_exists($controller, $ActionName)) {
			// Действие контроллера существует
			if(!empty($routes[2]) && preg_match('/^[\d]+$/ui', $routes[2])) {
				// Вызов действия с параметром
				$controller->$ActionName($routes[2]);
			}
			else {
				// Вызов действия без параметра
				$controller->$ActionName();
			}
		}
		else {
			// Действие не найдено - Загрузка по умолчанию
			if($ControllerName != 'PageController') {
				unset($controller);
				include(APP . 'controllers' . DS . 'PageController.php');
				$controller = new PageController($Stgs);
			}
			$controller->action404();
		}

		return true;
	}




    // Функция формирует имя класса контроллера или метода
	// Второй аргумент может быть 'Controller' или 'action'
    private function BuildName($instring, $nametype = 'action') {
		if(stripos($instring, '-')) {
			$words = explode('-', $instring);
			$instring = '';
			foreach($words as $word)
				$instring .= ucfirst($word);
		}
		else
			$instring = ucfirst($instring);

		if($nametype == 'Controller')
			$instring = $instring.$nametype;
		else
			$instring = $nametype.$instring;

		return $instring;
	}



    
}
