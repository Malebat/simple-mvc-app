<?php

class View
{
	protected $sts;

	function __construct($Sets) {
		$this->sts = $Sets;
	}

	/*
	$pg_view - виды отображающие контент страниц;
	$tpl_view - общий для всех страниц шаблон;
	$data - массив, содержащий элементы контента страницы. Обычно заполняется в модели.
	*/
	function render($data = null, $pg_view = 'Page/Index', $tpl_view = 'Default') {
		// Подключаем базовый шаблон страницы
		// Виды, хранящиеся в папках по именам контроллеров, подключаются из него
		include(APP.'templates'.DS.$tpl_view.'_tpl.php');
	}
}
