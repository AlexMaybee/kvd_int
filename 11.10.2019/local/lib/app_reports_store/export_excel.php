<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/local/lib/app_reports_store/class/getStoreList.php");



if($_REQUEST['user'] > 0) {
    $users = $_REQUEST['user'];
} else {
    $users = '';
}

$filter_object = (object) [
    'STATUS' => $_REQUEST['status'],
    'SECTIONS' => [$_REQUEST['section']],
    'USERS' => $users,
    'FROM_DATE' => $_REQUEST['from_date'],
    'TO_DATE' => $_REQUEST['to_date'],
];

$class = new StoreReports();
$list_stores = $class->getStoreList(1, $filter_object);
//dg($list_stores);

//df($_REQUEST);
global $APPLICATION;


$APPLICATION->RestartBuffer();
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: filename=list_store.xls");?>

    <html>
    <head>
        <title>Отчет по складам</title>
        <meta http-equiv="Content-Type" content="text/html; charset='<?= LANG_CHARSET ?>">
        <style>
            td {mso-number-format:\@;}
            .number0 {mso-number-format:0;}
            .number2 {mso-number-format:Fixed;}
        </style>
    </head>
    <body>
    <table border="1">
        <tr>
            <th></th>
            <th>Движения товара</th>
            <th>Склад получателя</th>
            <th>Дата</th>
            <th>Количество</th>
            <th>Товар</th>
            <th>№ машины</th>
            <th>VIN машины</th>
            <th>VIN двигателя</th>
            <th>Дата изготов.</th>
            <th>Примечание</th>
            <th>Менеджер</th>
        </tr>
    <?if(!empty($list_stores['STORES'])):?>
        <?foreach($list_stores['STORES'] as $store):?>
        <tr>
            <td><?=$store['KEY']?></td>
            <td><?=$store['UF_STATUS_EL_NAME']?></td>
            <td><?=$store['UF_STORE_TO_NAME']?></td>
            <td><?=$store['DATE_CREATE_CUSTOM']?></a></td>
            <td><?=$store['UF_QUANTITY_CUSTOM']?></td>
            <td><a href="<?=$store['UF_PRODUCT_URL']?>" target="_blank"><?=$store['UF_PRODUCT_NAME']?></a></td>
            <td><?=$store['UF_PRODUCT_PROPERTIES']['PROPERTY_104_VALUE']?></td>
            <td><?=$store['UF_PRODUCT_PROPERTIES']['PROPERTY_105_VALUE']?></td>
            <td><?=$store['UF_PRODUCT_PROPERTIES']['PROPERTY_100_VALUE']?></td>
            <td><?=$store['UF_PRODUCT_PROPERTIES']['PROPERTY_150_VALUE']?></td>
            <td><?=$store['UF_PRODUCT_PROPERTIES']['PROPERTY_109_VALUE']?></td>
            <td><?=$store['USER_FULL_NAME']?></td>
        </tr>
        <?endforeach;?>
    <?endif;?>


    </table>
    </body>
    </html>


<?
$r = $APPLICATION->EndBufferContentMan();
echo $r;
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");

?>