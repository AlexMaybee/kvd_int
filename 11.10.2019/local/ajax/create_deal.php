<?php
//define("NOT_CHECK_PERMISSIONS",true);
//define("CHECK_PERMISSIONS", "N");
//header('Content-type: application/json');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("crm");

$Deal = new CCrmDeal;



$prod_id = $_POST['PRODUCTS'];
$store_to = $_POST['UF_CRM_1541664058'];
$store_transit = $_POST['UF_CRM_1541664081'];
$cat_id = getStoreProduct($prod_id);

$store_id = $_POST['STORE_ID'];

$fields = array(
    "TITLE" => 'Транзит',
    "UF_CRM_1541664033" => $cat_id['IBLOCK_SECTION_ID'],
    "UF_CRM_1541664058" => $store_to,
    "UF_CRM_1541664081" => $store_transit,
    "CATEGORY_ID" => 3,
    "UF_CRM_1550838110" => date('d.m.Y'),
    "UF_CRM_1550838189" => 1572,
);

$result = $Deal->add($fields);


if($result > 0) {
    $oLead = new CCrmDeal;
    $arRows = array(
        ['ID' => 0, "PRODUCT_NAME" => "test44", "PRODUCT_ID" => $prod_id, "QUANTITY" => "1", "MEASURE_CODE" => 796]
    );
    $res = $oLead->SaveProductRows($result, $arRows, $checkPerms = true, $regEvent = true, $syncOwner = true);
    createStoreElDeal($result, $prod_id, $cat_id['IBLOCK_SECTION_ID'], $store_id, $store_transit, $store_to);
    echo json_encode($res);
}

function createStoreElDeal($deal_id, $prod_id, $section_id, $store_id, $store_transit, $store_to)
{
    $res_id = getStoreProductTransit($prod_id, $section_id);

    $data['ACTION'] = 1408;
    $data['UF_DEAL'] = $deal_id;
    $data['PROPERTY_VALUES'][98] = $prod_id;
    $data['IBLOCK_SECTION_ID'] = $section_id;
    $data['PROPERTY_VALUES']['99']['n0'] = 1;
    $data['UF_STORE_ELEMENT'] = $store_id;
    $data['UF_STORE_TO'] = $store_to;
    $suc = createElementStore($data, 1412);
    if($suc) {
        $quan = $res_id['PROPERTY_99_VALUE'] - 1;

        addNewStoreElementTransit($prod_id, $store_transit, 1, $deal_id);

        updateElementStoreQuantity($res_id['ID'], 'KOLICHESTVO', $quan);
    }
}

?>