<?php
AddEventHandler("crm", "OnBeforeCrmDealUpdate", "updateDealBefore");

//AddEventHandler("crm", "OnAfterCrmDealProductRowsSave", "updateDealProductRowsSave");

function updateDealBefore(&$arFields)
{

    $category_id = getDealFieldsMore($arFields['ID']);

    if ($category_id['CATEGORY_ID'] != 3) {
        if ($arFields['STAGE_ID'] == 3 || $arFields['STAGE_ID'] == 'C2:3' || $arFields['STAGE_ID'] == 'WON' || $arFields['STAGE_ID'] == 'C2:WON') {

            $res_prod = getProductDeal($arFields['ID']);
            if ($res_prod == 0) {
                $arFields['RESULT_MESSAGE'] = "Заполните Товар в сделке";
                return false;
            }
            setShipmentDeal($arFields);
        }

        if ($arFields['STAGE_ID'] == 'LOSE' || $arFields['STAGE_ID'] == 'APOLOGY' || $arFields['STAGE_ID'] == 'C2:LOSE') {
            setLoseDeal($arFields);
        }
    } elseif($category_id['CATEGORY_ID'] == 3) {
        if ($arFields['STAGE_ID'] == 'C3:WON') {
            setTransitElementtoStore($arFields);
        }

        if ($arFields['STAGE_ID'] == 'C3:LOSE') {
            setLoseTransitElementtoStore($arFields);
        }
    }
}

function getProductDeal($ID) {
    $res_prod = 0;
    if($ID > 0) {
        $db_list = CCrmDeal::LoadProductRows($ID);
        if(!empty($db_list)) {
            $res_prod = 1;
        }
    }
    return $res_prod;
}

function setShipmentDeal($arFields)
{
    /*$res_id = getDealFields($arFields['ID']);
    if(!$res_id['UF_CRM_1541598125']) {
        $resProduct = getProductsDeal($arFields['ID']);
        foreach ($resProduct as $prod) {
            $check_store = checkStoreElement(['UF_DEAL'=> $arFields['ID'], 'UF_PRODUCT' => $prod['PRODUCT_ID']]);
            if($check_store > 0) {
                $suc = updateElementStore($check_store, ['STATUS'=> 1410]);
                if($suc){
                    updateDealStatus($arFields['ID']);
                }
            }
        }
    }*/
    $resProduct = getProductsDeal($arFields['ID']);
    foreach ($resProduct as $prod) {
        $res_id = getStoreProduct($prod['PRODUCT_ID']);
        $additional_equipment = [272, 271, 244];
        if(in_array($res_id['IBLOCK_SECTION_ID'], $additional_equipment)) {
            return false;
        }
        $data['ACTION'] = 1408;
        $data['UF_DEAL'] = $arFields['ID'];
        $data['PROPERTY_VALUES'][98] = $prod['PRODUCT_ID'];
        $data['IBLOCK_SECTION_ID'] = $res_id['IBLOCK_SECTION_ID'];
        $data['PROPERTY_VALUES']['99']['n0'] = $prod['QUANTITY'];
        $suc = createElementStore($data, 1421);
        if ($suc) {
            $quan = $res_id['PROPERTY_99_VALUE'] - $prod['QUANTITY'];
            updateElementStoreQuantity($res_id['ID'], 'KOLICHESTVO', $quan);
        }
    }
}

function setLoseDeal($arFields)
{
    $res_id = getDealFields($arFields['ID']);
    if(!$res_id['UF_CRM_1541598125']) {
        $resProduct = getProductsDeal($arFields['ID']);
        foreach ($resProduct as $prod) {
            $res_id = getStoreProduct($prod['PRODUCT_ID']);
            $check_store = checkStoreElement(['UF_DEAL'=> $arFields['ID'], 'UF_PRODUCT' => $prod['PRODUCT_ID']]);
            if($check_store > 0) {
                $suc = updateElementStore($check_store, ['STATUS'=> 1411]);
                if($suc){
                    updateDealStatus($arFields['ID']);
                    $quan = $res_id['PROPERTY_99_VALUE'] + 1;
                    updateElementStoreQuantity($res_id['ID'], 'KOLICHESTVO', $quan);
                }
            }
        }
    }
}


function getDealFields($ID)
{
    $arFilter = Array('ID' => $ID);
    $arSelect = Array('ID', 'UF_CRM_1541598125');
    $db_list = CCrmDeal::GetListEx(Array("ID" => "ASC"), $arFilter, false, false, $arSelect, array());
    if($ar_result = $db_list->GetNext())
        return $ar_result;
}

function getDealFieldsMore($ID)
{
    $arFilter = Array('ID' => $ID);
    $arSelect = Array('ID', 'UF_CRM_1541598125', 'UF_CRM_1541664058', 'UF_CRM_1541664033', 'CATEGORY_ID');
    $db_list = CCrmDeal::GetListEx(Array("ID" => "ASC"), $arFilter, false, false, $arSelect, array());
    if($ar_result = $db_list->GetNext())
        return $ar_result;
}

function getProductsDeal($ID)
{
    $db_list = CCrmDeal::LoadProductRows($ID);
    return $db_list;
}

function updateDealProductRowsSave($ID, &$arFields)
{
    $category_id = getDealFieldsMore($ID);

    if($category_id['CATEGORY_ID'] != 3) {
        $data = [];
        if (!empty($arFields)) {
            foreach ($arFields as $key => $prod) {
                $res_id = getStoreProduct($prod['PRODUCT_ID']);
                if ($res_id['PROPERTY_99_VALUE'] > 0) {
                    $data['ACTION'] = 1408;
                    $data['UF_DEAL'] = $ID;
                    $data['PROPERTY_VALUES'][98] = $prod['PRODUCT_ID'];
                    $data['IBLOCK_SECTION_ID'] = $res_id['IBLOCK_SECTION_ID'];
                    $data['PROPERTY_VALUES']['99']['n0'] = $prod['QUANTITY'];
                    $check_store = checkStoreElement(['UF_DEAL' => $ID, 'UF_PRODUCT' => $prod['PRODUCT_ID']]);
                    if ($check_store == 0) {
                        $suc = createElementStore($data, 1409);
                        if ($suc) {
                            $quan = $res_id['PROPERTY_99_VALUE'] - $prod['QUANTITY'];
                            updateElementStoreQuantity($res_id['ID'], 'KOLICHESTVO', $quan);
                        }
                    }

                }
            }
        }
    }
    return $arFields;
}

function getStoreProduct($ID)
{
    $arSelect = Array("ID", "PROPERTY_99", "PROPERTY_98", "IBLOCK_ID", "IBLOCK_SECTION_ID");
    $arFilter = Array("IBLOCK_ID"=> 31, 'PROPERTY_98'=> $ID);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement())
        $arFields = $ob->GetFields();
    return $arFields;
}

function getStoreProductTransit($ID, $sect_id)
{
    $arSelect = Array("ID", "PROPERTY_99", "PROPERTY_98", "IBLOCK_ID", "IBLOCK_SECTION_ID");
    $arFilter = Array("IBLOCK_ID"=> 31, 'PROPERTY_98'=> $ID, 'IBLOCK_SECTION_ID'=> $sect_id);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement())
        $arFields = $ob->GetFields();
    return $arFields;
}

function updateElementStoreQuantity($ID, $code, $value)
{
    if($code =='KOLICHESTVO' && $value < 1) {
        deleteElementStore($ID);
    } else {
        CIBlockElement::SetPropertyValues($ID, 31, $value, $code);
    }
}

function updateDealStatus($ID)
{
    // Обновление сделки
    $Deal = new CCrmDeal;
    $fields = array(
        "UF_CRM_1541598125" => 1
    );
    $result = $Deal->Update($ID, $fields);
    return $result;
}
?>
