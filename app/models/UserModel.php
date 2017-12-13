<?php

class UserModel extends Database {
	
	public function get_data() {

        $st = $this->db->query("SELECT * FROM $this->table");

		return $st->fetchAll();
	}

}
