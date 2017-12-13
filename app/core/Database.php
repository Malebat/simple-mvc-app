<?php
/*
	Класс для работы с базой данных (SQLite или MySQL).

	Разработка: 2017 mk@ad-res.ru по заказу АО "ЦКБ ТМ"
*/


class Database {

    protected $db;
    protected $table;
    protected $sts;


    // Первый параметр - настройки, относящиеся к модели
	// Второй параметр - имя таблицы
    public function __construct ($Sets, $table_name = '') {
    	$this->sts = $Sets;

        // Подключаемся к базе данных
		$pdo_opts = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false
		];

		if($this->sts['DatabaseType'] == 'MySQL')
			$dsn = 	'mysql:host='.$this->sts['DatabaseServer'].
					';dbname='.$this->sts['DatabaseDatabase'].
					';charset=utf8';
		else
			$dsn = 'sqlite:'.$this->sts['DatabaseDatabase'];

		try {
			$this->db = new PDO(
				$dsn,
				$this->sts['DatabaseUser'],
				$this->sts['DatabasePassword'],
				$pdo_opts);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

        // Формируем имя таблицы
		if(empty($table_name)) {
			// Из имени класса вида 'CategoryListModel' получаем 'category_list'
			$modelName = strstr(get_class($this), 'Model', true);
			$aTableNameParts = preg_split('/(?=[A-Z0-9])/u', $modelName);
			$this->table = '';
			foreach($aTableNameParts as $TableNamePart) {
				if(!empty($TableNamePart)) {
					if(!empty($this->table))
						$this->table .= '_';
					$this->table .= strtolower($TableNamePart);
				}
			}
		}
		else {
			$this->table = $table_name;
		}

		if($this->sts['DatabaseType'] == 'SQLite') {
			// Проверяем наличие таблиц
			$st = $this->db->query("SELECT name FROM sqlite_master WHERE type = 'table'");
			$tables = $st->fetchAll();
			$bFound = 0;
			foreach($tables as $table) {
				if($table['name'] == $this->table) $bFound |= 2;
			}
			if(!$bFound)
				die("Неверная структура БД - таблица $this->table не найдена.");
		}
    }


    public function __destruct () {
        $this->db = null;
    }



    // получить имя таблицы
    public function getTableName () {
        return $this->table;
    }



    // получить запись по id
    public function getRowById ($id) {
    	$id = (int) $id;
		$stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id = ?");
		$stmt->execute(array($id));
		return $stmt->fetch();
    }



	public function getUser ($login) {
		$stmt = $this->db->prepare("SELECT * FROM user WHERE login = ?");
		$stmt->execute(array($login));
		$row = $stmt->fetch();
		$row['pwd'] = openssl_decrypt($row['pwd'], $this->sts['Cipher'], $this->sts['OpenSSLKey']);

		return $row;
	}



	public function updateUser ($user) {
    	$user['pwd'] = @openssl_encrypt($user['pwd'], $this->sts['Cipher'], $this->sts['OpenSSLKey']);

		$sql = "UPDATE users SET ".$this->PrepareRequestString($user)." WHERE id = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->execute($user);

		return $stmt;
	}



	public function PrepareRequestString ($aRow) {
		$set = '';
		foreach($aRow as $key => $val)
			if($key != 'id')
				$set .= "$key = :$key, ";

		return substr($set, 0, -2);
	}



	public function PrepareInsertString ($aRow) {
		$sql = "(";
		foreach($aRow as $key => $val)
			$sql .= $key.',';
		$sql  = substr($sql, 0, -1);
		$sql .= ") VALUES (";
		foreach($aRow as $key => $val)
			$sql .= ':'.$key.',';
		$sql  = substr($sql, 0, -1);
		$sql .= ")";

		return $sql;
	}



	public function getPageIdByURI ($uri) {
		$stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM page WHERE url LIKE ?");
		$stmt->execute(array($uri));
		$row  = $stmt->fetch();
		if($row['cnt'] == 1) {
			$stmt = $this->db->prepare("SELECT * FROM page WHERE url LIKE ?");
			$stmt->execute(array($uri));
			$row  = $stmt->fetch();
			return $row['id'];
		}
		return false;
	}


	// Список отделов, взятый из json-файла складываем в таблицу 'dept' локальной БД
	public function SyncronizeDeptsList ($aDepts) {
    	$this->db->exec("DELETE FROM dept"); // TRUNCATE в SQLite нет
		$stmt = $this->db->prepare("INSERT INTO dept (id,title) VALUES (?,?)");
		$this->db->beginTransaction();
		foreach($aDepts as $key => $val)
			$stmt->execute(array($key, $val));
		$this->db->commit();

		if($this->sts['DatabaseType'] == 'SQLite')
			$this->db->exec("REINDEX dept");
		else
			$this->db->exec("OPTIMIZE TABLE dept");
	}


	// Список сотрудников, взятый из json-файла складываем в таблицу 'sotrudnik' локальной БД
	// SQLite не поддерживает регистронезависимый поиск, поэтому создаём
	// вспомогательный столбец 'fio_low'
	public function SyncronizeSotrudnik ($aSotr) {
		$this->db->exec("DELETE FROM sotrudnik"); // TRUNCATE в SQLite нет
		$aSotr[0]['fio_low'] = mb_strtolower($aSotr[0]['fio']);
		$stmt = $this->db->prepare("INSERT INTO sotrudnik ".$this->PrepareInsertString($aSotr[0]));
		$this->db->beginTransaction();
		foreach($aSotr as $key => $val) {
			$val['fio_low'] = mb_strtolower($val['fio']);
			$stmt->execute($val);
		}
		$this->db->commit();

		if($this->sts['DatabaseType'] == 'SQLite')
			$this->db->exec("REINDEX sotrudnik");
		else
			$this->db->exec("OPTIMIZE TABLE sotrudnik");
	}


	// Возвращает список отделов в виде массива, где ключи - номера отделов
	public function get_dept_list () {

		$st = $this->db->query("SELECT * FROM dept");
		$aOut = array();
		while($row = $st->fetch()) {
			$aOut[$row['id']] = $row['title'];
		}
		return $aOut;
	}


	// Возвращает содержимое таблицы 'sotrudnik'
	public function get_sotr_list ($dept_id) {

		$st = $this->db->query("SELECT * FROM sotrudnik WHERE dept = $dept_id ORDER BY boss DESC, fio");

		return $st->fetchAll();
	}


	// Возвращает строку - название отдела
	public function get_dept_by_id ($dept_id) {

		$st = $this->db->query("SELECT title FROM dept WHERE id = $dept_id");
		$a = $st->fetch();
		return $a['title'];
	}




}


