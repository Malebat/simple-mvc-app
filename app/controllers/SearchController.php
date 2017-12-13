<?php

class SearchController extends ApplicationController
{

    function __construct($Settings) {
		parent::__construct($Settings);
        $this->model = new SearchModel($Settings['model'], 'sotrudnik');
    }

	
	
	function actionName() {

    	$data = array();
		$data['DeptList'] = $this->model->get_dept_list();
		$srch_request = trim($_POST['f_name']);

		if(!empty($srch_request)) {
			// Пришли данные формы
			if(!preg_match('/^[\w\- ]{2,64}$/u', $srch_request)) {
				// В форму поиска ввели ерунду
				$data['Page'] = array(
					'title' => 'Странный запрос',
					'content' => $srch_request,
				);
			}
			else {
				// Ищем
				$data['Page'] = array(
					'title' => 'Результаты поиска',
					'request' => $srch_request,
				);
				$data['SotrList'] = $this->model->get_sotr_list($srch_request);
			}
		}
		else {
			// Не пришли данные формы
			$data['Page']['title'] = "Поиск";
			$data['Page']['content'] = "Кого будем искать?";
		}

		$this->view->render($data, 'Dept'.DS.'View');
	}



}

