<?php

class DeptModel extends Database {


	public function getPageByID($page_id) {

		return $this->getRowById($page_id);
	}

}
