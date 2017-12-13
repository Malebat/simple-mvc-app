<?php

class DeptController extends ApplicationController {

    function __construct($Settings) {
		parent::__construct($Settings);
        $this->model = new DeptModel($Settings['model']);
    }


	function actionView($dept_id) {
		$data = array('Page' => array(), 'DeptList' => array(), 'SotrList' => array());
		$data['DeptList'] = $this->model->get_dept_list();
		$data['SotrList'] = $this->model->get_sotr_list($dept_id);
		$data['Page'] = array('title' => $this->model->get_dept_by_id($dept_id));

		$this->view->render($data, 'Dept/View', 'Default');
	}


	function actionIndex() {

		$data = array('Page' => ['title' => 'Добро пожаловать!', 'content' => ''], 'List' => array());
		$this->view->render($data);
	}


}

