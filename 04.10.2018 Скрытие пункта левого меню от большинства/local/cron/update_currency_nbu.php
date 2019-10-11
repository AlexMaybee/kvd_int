<?
$_SERVER["DOCUMENT_ROOT"]='/home/bitrix/www';
define("NOT_CHECK_PERMISSIONS",true);
define("CHECK_PERMISSIONS", "N");
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Web\HttpClient;

CModule::IncludeModule("CRM");


$httpClient = new HttpClient();
$httpClient->setHeader('Content-Type', 'application/json', true);
$response = $httpClient->get('https://bank.gov.ua/NBU_Exchange/exchange?json');
//print_r($response);

$data = json_decode($response);
foreach ($data as $currency) {
    if($currency->CurrencyCodeL == 'CAD'){
        $CAD = $currency;
    } elseif($currency->CurrencyCodeL == 'USD'){
        $USD = $currency;
    } elseif($currency->CurrencyCodeL == 'RUB'){
        $RUB = $currency;
    }
}


$summaUsd = $USD->Amount / $USD->Units;

$curRub = 1 / ($summaUsd * ($RUB->Units/$RUB->Amount));
$curCad = 1 / ($summaUsd * (1/($CAD->Amount/$CAD->Units)));

$resCad = round($curCad, 4);
$resRub = round($curRub, 4);

/*$res = CCrmCurrency::GetList($arOrder=array());
echo '<pre>';
//print_r($res);
echo '</pre>';*/

$result = CCrmCurrency::Update('CAD', array('AMOUNT'=>$resCad));
$result1 = CCrmCurrency::Update('RUB', array('AMOUNT'=>$resRub));

//$RUB =

?>
