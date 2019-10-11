<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("crm");

$res_prod = 0;
if($_POST['id'] > 0) {
    $db_list = CCrmDeal::LoadProductRows($_POST['id']);
    if(!empty($db_list)) {
        $res_prod = 1;
    }
}

echo json_encode($res_prod);