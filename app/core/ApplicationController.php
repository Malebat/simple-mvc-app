<?php

class ApplicationController {
	
	public $model;
	public $view;
	protected $sts;
	
	function __construct($Settings) {
		$this->sts = $Settings['stuff'];
		$this->view = new View($Settings['view']);
	}
	
	// действие, вызываемое по умолчанию
	function actionIndex() {
		return true;
	}
}
