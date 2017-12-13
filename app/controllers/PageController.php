<?php

class PageController extends ApplicationController {

    function __construct($Settings) {
		parent::__construct($Settings);
        $this->model = new PageModel($Settings['model']);
    }


	function actionShow($page_id) {
		$data = array('Page' => array(), 'List' => array());
		$data['Page'] = $this->model->getPageByID($page_id);
		if(empty($data['Page'])) {
			header('Location: /page/404');
			die();
		}
		$this->view->render($data, 'Page/Show', 'Default');
	}


	function actionIndex() {
		$data = array('Page' => array(), 'DeptList' => array(), 'SotrList' => array());
		$data['DeptList'] = $this->model->get_dept_list();
		$data['Page'] = array('title' => "Добро пожаловать!");
		$this->view->render($data);
	}


	function action404() {
		$data = array(
			'Page' => [
				'title' => 'Страница не найдена',
				'content' => 'Это фиаско, братан.'],
			'List' => array()
		);
		$data['DeptList'] = $this->model->get_dept_list();
		$this->view->render($data);
	}
}

