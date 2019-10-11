<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/local/webhook/class.php");

// Срабатывает при заполнении формы на сайте, сюда посылаем пост запросом данные из формы, поля описаны ниже, нужно вставить туда нужные значения
$DATA = array();
$DATA['EMAIL'] = 'test@gmail.com'; //EMAIL клиента
$DATA['PHONE'] = '380939996633'; //Телефон клиента
$DATA['URL_UTM'] = 'https://sherp.ua/kontakti/?utm_source=google&utm_medium=cpc&utm_campaign=utm_metki'; //Ссылка на сайт
$DATA['NAME'] = "Имя"; //Имя клиента
$DATA['LAST_NAME'] = "Фамилия"; //Фамилия клиента
$DATA['CITY'] = "Киев"; //город клиента
$DATA['POST_INDEX'] = "002569"; //Индекс клиента




$res = new addWebForm();
$result = $res->createLead($DATA); //отправляем поля на обработку



?>