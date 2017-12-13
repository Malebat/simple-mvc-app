<div style="display:table; width:100%;">
<div style="display:table-row;">

<!-- Левая колонка -->
<div id="leftcontainer">
<!-- список отделов -->
<?php
// Шапка таблицы
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
    // Список отделов
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

<!-- Скрипт для автоматической прокрутки списка отделов -->
<script type="text/javascript">
    var dept_order = <?=$dept_order;?>;
    var scroll_to = 0;
    if(dept_order > 4)
        scroll_to = $('#leftchild').prop('scrollHeight')*<?=($dept_order-4)/count($data['DeptList']);?>;
</script>

<!-- Основной (центральный) блок -->
<div id="centercontainer">

<?php
if(count($data['SotrList']) == 0) {
    echo "<div id=\"centerchild\">\r\n";
    echo "<h1>Ничего не найдено</h1>\r\n";
}
else {
    // Шапка таблицы
	$sotr_tbl  = "<div style=\"display:table; border-collapse:collapse;\">\r\n";
	$sotr_tbl .= "<div class=\"pglr\">\r\n";
	$sotr_tbl .= "<div class=\"pglc_h w340\">Абонент</div>\r\n";
	$sotr_tbl .= "<div class=\"pglc_h w38\">Отдел</div>\r\n";
	$sotr_tbl .= "<div class=\"pglc_h w54\">Комната</div>\r\n";
	$sotr_tbl .= "<div class=\"pglc_h w102\">Местный</div>\r\n";
	$sotr_tbl .= "<div class=\"pglc_h w102\">Городской</div>\r\n";
	$sotr_tbl .= "</div>\r\n";
	$sotr_tbl .= "</div>\r\n";
	echo $sotr_tbl;

	echo "<div id=\"centerchild\">\r\n";

	$sotr_tbl  = "<div style=\"display:table; border-collapse:collapse;\">\r\n";

	// Список сотрудников
	foreach($data['SotrList'] as $row) {

		if($row['boss']) $sotr_tbl .= "<div class=\"pglr active\">\r\n";
        else    $sotr_tbl .= "<div class=\"pglr\">\r\n";
		$sotr_tbl .= "<div class=\"pglc_s w340\">\r\n";
		$sotr_tbl .= "<span class=\"medm\">".$row['fio']."</span><br>\r\n";
		$sotr_tbl .= $row['title']."\r\n";
		$sotr_tbl .= "<div class=\"targt\">".$data['DeptList'][$row['dept']]."</div>\r\n";
		$sotr_tbl .= "</div>\r\n";
		$sotr_tbl .= "<div class=\"pglc_o w38\">".sprintf("%03d", $row['dept'])."</div>\r\n";
		$sotr_tbl .= "<div class=\"pglc_o w54\">{$row['room']}</div>\r\n";
		$sotr_tbl .= "<div class=\"pglc_o w102\">{$row['ph_loc']}</div>\r\n";
		$sotr_tbl .= "<div class=\"pglc_o w102\">{$row['ph_cty']}</div>\r\n";
		$sotr_tbl .= "</div>\r\n";
    }
	$sotr_tbl .= "</div>\r\n";

    echo $sotr_tbl;
}
?>
</div>
</div>

</div>
</div>