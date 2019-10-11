<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
//header( 'Content-type: text/xml' );
//use Bitrix\Main\Web\HttpClient;
$url = '192.168.1.13/archive/B24';
$http = new \Bitrix\Main\Web\HttpClient(array($options = null));

$user = 'test';
$pass = '11112222!';
$http->setHeader('Content-Type', 'application/json', true);
$http->setAuthorization($user, $pass);


$response = $http->get($url, json_encode($json));

echo $response;

?>