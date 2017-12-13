<div style="display:table; width:100%;">
<div style="display:table-row;">

<div id="leftcontainer">
	<?php
	$sotr_tbl  = "<div style=\"display:table; border-collapse:collapse;\">\r\n";
	$sotr_tbl .= "<div class=\"pglr\">\r\n";
	$sotr_tbl .= "<div class=\"pglc_h w223\">Подразделение</div>\r\n";
	$sotr_tbl .= "</div>\r\n";
	$sotr_tbl .= "</div>\r\n";
	echo $sotr_tbl;
	?>
    <div id="leftchild">
        <div id="leftlist">
			<?php
			$sTable = '';
			$i = 1;
			foreach($data['DeptList'] as $key => $val) {
				if($key == 999) continue; // Пропускаем строку
				$sTable .= "<div class=\"pglrn";
				if($val == $data['Page']['title']) {
					$sTable .= " active";
					$dept_id = $key;
					$dept_order = $i;
				}
				$sTable .= "\" id=\"dep_lst_$key\">\r\n";
				$sTable .= "<div class=\"pglc_id\">".sprintf("%03d", $key)."</div>\r\n";
				$sTable .= "<div class=\"pglc\">$val</div>\r\n";
				$sTable .= "</div>\r\n";
				$i++;
			}
			echo $sTable;
			?>
        </div>
    </div>
</div>

<div id="centercontainer">
<div id="centerchild">
<?php if($data['Page']['title'] == "Страница не найдена"): ?>
    <h1>Страница не найдена</h1>
<?php else: ?>
    <h1>Официальные контакты предприятия</h1>

    <div class="frontpagecontacts">
        <p>Секретариат: (4822) 55-91-23</p>
        <p>Факс предприятия: (4822) 55-45-18</p>
        <p>Почтовый адрес: г.Тверь, Петербургское шоссе, 45в, 170003</p>
        <p>Электронная почта: mail@ckbtm.ru</p>
    </div>
<?php endif; ?>
</div>
</div>

</div>
</div>
