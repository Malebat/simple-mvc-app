<?php
/*
	Файл импорта данных из Active Directory для телефонного справочника.
	Запускается по необходимости.
	Периодичность задаётся в app\core\Settings.php
	Создаёт два json-файла: список отделов и абонентов.
	Разработка: 2017 mk@ad-res.ru по заказу АО "ЦКБ ТМ"
*/

mb_internal_encoding('UTF-8');


$ldaphost = "192.168.0.0";
$ldapport = "";
$domain = "";
$dn = "";
$ldapuser = "";
$ldappas = "";


$dc = ldap_connect($ldaphost, $ldapport);
ldap_set_option($dc, LDAP_OPT_PROTOCOL_VERSION, 3);

$filter = "(objectcategory=user)";
$attr = array();

if($dc) {	  
	$ldap_success = ldap_bind($dc, $ldapuser.$domain, $ldappas);
	if($ldap_success) {
		// Успешно подключились к AD
		$result = ldap_search($dc, $dn, $filter, $attr);
		$result_entries = ldap_get_entries($dc, $result);
		ldap_unbind($dc);

		// Построение списка отделов
		$depts = array();

		for($i=0; $i < $result_entries['count']; $i++) {
			if(strlen($result_entries[$i]['displayname'][0]) < 10) continue; // Короткое ФИО
			
			if(!preg_match('/^[\d]{1,6}$/u', $result_entries[$i]['department'][0])) {
				// Номер отдела - не число -> костыли
				if($result_entries[$i]['department'][0] == '!!') 
					$result_entries[$i]['department'][0] = 2; // Секретариат
				else if($result_entries[$i]['department'][0] == '!') 
					$result_entries[$i]['department'][0] = 1; // Управление
				else if($result_entries[$i]['department'][0] == 'Профком') {
					$result_entries[$i]['department'][0] = 3;
					$result_entries[$i]['streetaddress'][0] = 'Профком'; // Задаём текстовое название отдела
					}
				else if($result_entries[$i]['department'][0] == 'Медпункт') {
					$result_entries[$i]['department'][0] = 8;
					$result_entries[$i]['streetaddress'][0] = 'Медпункт'; // Задаём текстовое название отдела
					}
				else $result_entries[$i]['department'][0] = 0;
				}
			else {
				// Номер отдела - число
				$result_entries[$i]['department'][0] = (int) $result_entries[$i]['department'][0];
			}
			
			if($result_entries[$i]['streetaddress'][0] == '') continue; // Отсутствует текстовое название отдела
			
			if(!key_exists($result_entries[$i]['department'][0], $depts))
				$depts[$result_entries[$i]['department'][0]] = $result_entries[$i]['streetaddress'][0];
		}
		$depts[999] = "Не определено";

		ksort($depts);
		file_put_contents('phone_dir_depts_list.json', json_encode($depts, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
		unset($depts);


		// Построение списка абонентов
		$dirs = array();

		for($i=0; $i < $result_entries['count']; $i++) {
			$dir = array();
			if(empty($result_entries[$i]['department'][0])) {
				// Не указан номер отдела
				if(strlen($result_entries[$i]['displayname'][0]) < 10) continue; // Короткое ФИО
				$dir['dept'] = 999; // Неизвестный отдел
			}
			else {
				// Входная запись содержит номер отдела
				$dir['dept'] = $result_entries[$i]['department'][0];
			}
			$dir['login'] = $result_entries[$i]['samaccountname'][0];
			$dir['fio'] = $result_entries[$i]['displayname'][0];
			$dir['title'] = $result_entries[$i]['title'][0];
			$dir['ph_loc'] = $result_entries[$i]['telephonenumber'][0];
			$dir['ph_cty'] = $result_entries[$i]['wwwhomepage'][0];
			$dir['email'] = $result_entries[$i]['mail'][0];
			$dir['room'] = $result_entries[$i]['physicaldeliveryofficename'][0];
			$dir['descr'] = $result_entries[$i]['description'][0];
			$boss = 0;
			// Выявление начальников и замов
			if(mb_stristr($dir['title'], 'начальник ') !== false)			$boss |= 2;
			if(mb_stristr($dir['title'], 'начальник сектора') !== false)	$boss  = 0;
			if(mb_stristr($dir['title'], 'директор') !== false)				$boss |= 4;
			if(mb_stristr($dir['title'], 'директора') !== false)			$boss  = 0;
			if(mb_stristr($dir['title'], 'заместитель') !== false)			$boss |= 1;
			if($dir['dept'] == 1)	$boss |= 8; // Отдел = управление
			$dir['boss'] = $boss;
			$dirs[] = $dir;
		}

		unset($result_entries);
		file_put_contents('phone_directory.json', json_encode($dirs, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
		unset($dirs);
		}
}
