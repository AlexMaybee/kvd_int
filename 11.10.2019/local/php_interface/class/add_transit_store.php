<?php
AddEventHandler("crm", "OnBeforeCrmDealAdd", "addDeal");

AddEventHandler("crm", "OnAfterCrmDealAdd", "addAfterDeal");


function addAfterDeal(&$arFields)
{
    if ($GLOBALS['_POST']['PARAMS']['CATEGORY_ID'] == 3) {
        $data = [];
        $products = json_decode($GLOBALS['_POST']['DEAL_PRODUCT_DATA']);

        $res_id = getStoreProductTransit($products[0]->PRODUCT_ID, $arFields['UF_CRM_1541664033']);

        $section_id = getSectionId($products[0]->PRODUCT_ID);
        $data['ACTION'] = 1408;
        $data['UF_DEAL'] = $arFields['ID'];
        $data['PROPERTY_VALUES'][98] = $products[0]->PRODUCT_ID;
        $data['IBLOCK_SECTION_ID'] = $section_id;
        $data['PROPERTY_VALUES']['99']['n0'] = $products[0]->QUANTITY;
        $data['UF_STORE_ELEMENT'] = $res_id['ID'];
        $data['UF_STORE_TO'] = $arFields['UF_CRM_1541664058'];
        $suc = createElementStore($data, 1412);
        if($suc) {
            $quan = $res_id['PROPERTY_99_VALUE'] - round($products[0]->QUANTITY);

            updateElementStoreQuantity($res_id['ID'], 'KOLICHESTVO', $quan);

            addNewStoreElementTransit($products[0]->PRODUCT_ID, $arFields['UF_CRM_1541664081'], $products[0]->QUANTITY, $arFields['ID']);
        }
    }
}

function addDeal(&$arFields)
{
    if ($GLOBALS['_POST']['PARAMS']['CATEGORY_ID'] == 3) {

        if (empty($GLOBALS['_POST']['DEAL_PRODUCT_DATA'])) {
            $arFields['RESULT_MESSAGE'] = "Заполните Товар в сделке";
            return false;
        }

        if (count($GLOBALS['_POST']['DEAL_PRODUCT_DATA']) > 1) {
            $arFields['RESULT_MESSAGE'] = "В сделке может присутствовать только один товар";
            return false;
        }

        $products = json_decode($GLOBALS['_POST']['DEAL_PRODUCT_DATA']);

        if (count($products) > 1) {
            $arFields['RESULT_MESSAGE'] = "В сделке может присутствовать только один товар";
            return false;
        }

        $section_id = getSectionId($products[0]->PRODUCT_ID);

        if (!$GLOBALS['_POST']['UF_CRM_1541664058']) {
            $arFields['RESULT_MESSAGE'] = "Заполните Склад куда перемещаем";
            return false;
        }

//        if (!$GLOBALS['_POST']['UF_CRM_1541664081']) {
//            $arFields['RESULT_MESSAGE'] = "Заполните Транзитный склад";
//            return false;
//        }

        if($section_id > 0) {
            $arFields['UF_CRM_1541664033'] = $section_id;
            $arFields['UF_CRM_1541664081'] = 260;
        }

    }

    return $arFields;
}

function getSectionId($ID)
{
    $res = CIBlockElement::GetByID($ID);
    if($ar_res = $res->GetNext())
        return getSectionIDStore($ar_res['IBLOCK_SECTION_ID']);
}

function addElementStoretoTransit($data)
{
    $el = new CIBlockElement;
    $PROP = [];

    $PROP['TOVAR'] = $data['PROPERTY']['TOVAR'];
    $PROP['KOLICHESTVO'] = $data['KOLICHESTVO'];
    $PROP['DVIGATEL'] = $data['PROPERTY']['DVIGATEL'];
    $PROP['TIP_KUZOVA'] = $data['PROPERTY']['TIP_KUZOVA'];
    $PROP['SISTEMA_OKHLAZHDENIYA'] = $data['PROPERTY']['SISTEMA_OKHLAZHDENIYA'];
    $PROP['DOP_OBORUDOVANIE'] = $data['PROPERTY']['DOP_OBORUDOVANIE'];
    $PROP['_MASHINY'] = $data['PROPERTY']['_MASHINY'];
    $PROP['VIN_CODE'] = $data['PROPERTY']['VIN_CODE'];
    $PROP['DATA_IZGOTOVLENIYA'] = $data['PROPERTY']['DATA_IZGOTOVLENIYA'];
    $PROP['ROPS'] = $data['PROPERTY']['ROPS'];
    $PROP['VEBASTO'] = $data['PROPERTY']['VEBASTO'];
    $PROP['KOMMENTARIY'] = $data['PROPERTY']['KOMMENTARIY'];
    $PROP['NAZVANIE_DILLERA'] = $data['PROPERTY']['NAZVANIE_DILLERA'];
    $PROP['STRANA'] = $data['PROPERTY']['STRANA'];
    $PROP['SDELKA'] = $data['DEAL_ID'];
    $arLoadProductArray = Array(
        'MODIFIED_BY' => $GLOBALS['USER']->GetID(),
        'IBLOCK_SECTION_ID' => $data['IBLOCK_SECTION_ID'],
        'IBLOCK_ID' => 31,
        'PROPERTY_VALUES' => $PROP,
        'NAME' => $data['NAME'],
        'ACTIVE' => 'Y'
    );

    if($PRODUCT_ID = $el->Add($arLoadProductArray)) {
        return $PRODUCT_ID;
    } else {
        return $el->LAST_ERROR;
    }
}

function getStoreElementProp($ID)
{
    $arSelect = Array("ID", "NAME", "IBLOCK_ID");
    $arFilter = Array("IBLOCK_ID"=> 31, 'PROPERTY_98'=> $ID);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement())
        $arFields = $ob->GetFields();
        $db_props = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], array("id" => "asc"), array());
        while($ar_props = $db_props->Fetch()) {
            $arFields['PROPERTY'][$ar_props['CODE']] = $ar_props['VALUE'];
        }
        return $arFields;
}

function addNewStoreElementTransit($prod_id, $section_id, $quantity, $deal_id)
{
    $products = [];
    $products = getStoreElementProp($prod_id);
    $products['IBLOCK_SECTION_ID'] = $section_id;
    $products['KOLICHESTVO'] = $quantity;
    $products['DEAL_ID'] = $deal_id;

    return addElementStoretoTransit($products);
}

function setTransitElementtoStore($arFields)
{
    $res_id = getDealFieldsMore($arFields['ID']);

    if(!$res_id['UF_CRM_1541598125']) {
        $resProduct = getProductsDeal($arFields['ID']);
        foreach ($resProduct as $prod) {
            $check_store = checkStoreTransit(['UF_DEAL'=> $arFields['ID'], 'UF_PRODUCT' => $prod['PRODUCT_ID']], $res_id['UF_CRM_1541664033']);
            if($check_store > 0) {
                $suc = updateElementStore($check_store, ['STATUS'=> 1410]);
                if($suc){
                    updateSectionElementStore($arFields['ID'], $res_id['UF_CRM_1541664058'], $prod['PRODUCT_ID']);
                    updateDealStatus($arFields['ID']);
                }
            }
        }
    }
}

function updateSectionElementStore($ID, $sect_id, $prod_id)
{
    $arSelect = Array("ID", "PROPERTY_144", "PROPERTY_98", "IBLOCK_ID", "IBLOCK_SECTION_ID");
    $arFilter = Array("IBLOCK_ID"=> 31, 'PROPERTY_144'=> $ID);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement())
        $arFields = $ob->GetFields();
    $check_store = checkStoreElement(['UF_DEAL'=> $ID, 'UF_PRODUCT' => $prod_id]);
    if($check_store > 0) {
        $data = [];
        updateElementStore($check_store, ['STATUS'=> 1413, 'UF_SECTION_ID'=> $sect_id]);
    }

    updateSectionProduct($sect_id, $prod_id);

    $res = CIBlockElement::SetElementSection($arFields['ID'], [$sect_id]);
    return $res;
}

function updateSectionProduct($sect_id, $prod_id)
{
    df($sect_id);
    df($prod_id);
    $sect_id_prod = getSectionProductStore($sect_id);
    df($sect_id_prod);
    $res = CIBlockElement::SetElementSection($prod_id, [$sect_id_prod]);
    df($res);
    return $res;
}

function setLoseTransitElementtoStore($arFields)
{
    $res_id = getDealFieldsMore($arFields['ID']);

    if(!$res_id['UF_CRM_1541598125']) {
        $resProduct = getProductsDeal($arFields['ID']);
        $el_id = getTransitElementDeal($arFields['ID']);
        if($el_id > 0) {
            $del_el = CIBlockElement::Delete($el_id);
        }
        foreach ($resProduct as $prod) {
            $check_store = checkStoreTransitEl(['UF_DEAL'=> $arFields['ID'], 'UF_PRODUCT' => $prod['PRODUCT_ID']], $res_id['UF_CRM_1541664033']);
            if($check_store > 0) {
                $c_quan = getPropertyProductCrm($check_store);
                $quan = $c_quan['PROPERTY_99_VALUE'] + $prod['QUANTITY'];
                updateElementStoreQuantity($check_store, 'KOLICHESTVO', $quan);

                $data['PROPERTY_VALUES'][98] = $c_quan['PROPERTY_98_VALUE'];
                $data['IBLOCK_SECTION_ID'] = $c_quan['IBLOCK_SECTION_ID'];
                $data['PROPERTY_VALUES']['99']['n0'] = $prod['QUANTITY'];
                createElementStore($data, 1413);
            }
        }
    }
}

function getTransitElementDeal($ID)
{
    $arSelect = Array("ID", "PROPERTY_144", "PROPERTY_98", "IBLOCK_ID", "IBLOCK_SECTION_ID");
    $arFilter = Array("IBLOCK_ID"=> 31, 'PROPERTY_144'=> $ID);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement())
        $arFields = $ob->GetFields();
        return $arFields['ID'];
}

function getSectionIDStore($ID)
{
    $res = null;
    switch ($ID) {
        case 240:
            $res = 221;
            break;
        case 237:
            $res = 244;
            break;
        case 250:
            $res = 232;
            break;
        case 252:
            $res = 234;
            break;
    }
    return $res;
}

function getSectionProductStore($ID)
{
    $res = null;
    switch ($ID) {
        case 221:
            $res = 240;
            break;
        case 244:
            $res = 237;
            break;
        case 232:
            $res = 250;
            break;
        case 234:
            $res = 252;
            break;
    }
    return $res;
}
?>
