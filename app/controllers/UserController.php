<?php

class UserController extends ApplicationController
{

    function __construct($Settings) {
		parent::__construct($Settings);
        $this->model = new UserModel($Settings['model']);
    }

	function actionLogin() {

		$login = trim($_POST['f_login']);
		$pwd = trim($_POST['f_password']);

		$data = array('Page' => [
			'title' => 'Добро пожаловать',
			'content' => $_SESSION['Referrer'],
		],
			'List' => array()
		);

		if(!empty($login) && !empty($pwd)) {
			// Пришли данные формы
			if(preg_match('/^[\w\-]{1,64}$/u', $login)) {
				$user = $this->model->getUser($login);
					if($user['pwd'] == $pwd) {
						// Логин и пароль соответствуют указанным в БД
						$_SESSION['User'] = $user;
						header('Location: '.$_SESSION['Referrer']);
					}
			}
			if(!isset($_SESSION['User']))
				$data['Page']['title'] = "В доступе отказано";
		}
		else {
			// Начальная загрузка - рисуем форму
			$data['Page']['title'] = "Авторизация";
		}

		$this->view->render($data, 'User'.DS.'Login');
	}



	function actionLogout() {
		$data = array('Page' => [
			'Title' => 'Всего наилучшего',
			'Content' => $_SESSION['Referrer'],
		],
			'List' => array()
		);
		unset($_SESSION['User']);
		header('Location: '.$_SESSION['Referrer']);
		$this->view->render($data, 'User'.DS.'Login');
	}
}

