<?php

class PageModel extends Database {


	public function get_data() {

		$st = $this->db->query("SELECT * FROM $this->table");

		return $st->fetchAll();
	}



	public function getPageByID($page_id) {

		return $this->getRowById($page_id);
	}

}
