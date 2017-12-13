<?php

class SearchModel extends Database {
	
	public function get_sotr_list($srch_request) {
/*		Странно, но такой вариант не работает
		$stmt = $this->db->prepare("SELECT * FROM sotrudnik WHERE fio LIKE %?%");
		$stmt->execute(array($srch_request));
*/
		$srch_request = mb_strtolower($srch_request);
		$stmt = $this->db->query("SELECT * FROM sotrudnik WHERE fio_low LIKE '%$srch_request%' ORDER BY boss DESC, fio");
		$res = $stmt->fetchAll();

		return $res;
	}

}
