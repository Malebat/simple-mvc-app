<?php
/*
	Класс для хранения настроек.
	Этот файл д.б. без BOM.

	Разработка: 2017 mk@ad-res.ru по заказу АО "ЦКБ ТМ"
*/


class Settings implements ArrayAccess {

    private $container;

	private function __construct() {
        $this->container = array(
        	'model' => array(
				'DatabaseType' => 'MySQL',				// или SQLite
				'DatabaseServer' => 'localhost',
				'DatabaseUser' => 'root',
				'DatabasePassword' => '',
				'DatabaseDatabase' => 'phonedir',		// Файл SQLite assets/common.db или имя БД MySQL
				'Cipher' => 'AES-128-CBC',
				'OpenSSLKey' => 'qweqweqdfsdfgsdfsdftyutyutyut',
			),
			'view' => array(
				'DomainName' => 'phonedir',
				'SiteName' => 'Телефонный справочник',			// Для футера
				'TitleSuffix' => ' - Телефонный справочник',	// Суффикс для Page title
			),
			'stuff' => array(
				'SyncInterval' => 1800, // Синхронизировать с Active Directory каждые N секунд
			)
        );
    }

	private static $_instance = null;

	static public function getInstance() {
		if(is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    public function offsetSet($offset, $value) {
        $this->container[$offset] = $value;
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

	private function __clone() {
		// ограничивает клонирование объекта
	}

	private function __wakeup() {
		// Private unserialize method to prevent unserializing of the Singleton
	}


    public static function DebugPrint($var) {
    	echo "<br>\r\n<pre>".print_r($var, true)."</pre><br>\r\n";
	}
}


