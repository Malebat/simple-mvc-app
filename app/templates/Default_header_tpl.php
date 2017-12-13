<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $data['Page']['title'].$this->sts['TitleSuffix']; ?></title>
    <meta name="description" content="<?= $data['Page']['descr']; ?>">
    <meta name="keywords" content="<?= $data['Page']['keywords']; ?>">
    <meta name="generator" content="MK MVC App Engine mk@ad-res.ru">
    <script src="/js/jquery-3.1.1.min.js" type="text/javascript"></script>
    <script src="/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="/js/jquery.json.min.js" type="text/javascript"></script>
    <script src="/js/common.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="/css/common.css">
    <link rel="shortcut icon" href="favicon.ico">
</head>


<body>
<div class="wrap">
    <article>

<div class="pagetopstrip">
Справочник абонентов АО &laquo;ЦКБ Транспортного Машиностроения&raquo;
</div>

<?php require(APP.'templates/Search_form.php'); ?>
<div style="clear:both;"></div>
