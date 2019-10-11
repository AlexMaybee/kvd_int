<?
CModule::IncludeModule('highloadblock');
function createElementStore ($data, $status)
{
    global $USER;
    $checkRegister = [];
    $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(1)->fetch();
    $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();


    if($data['ACTION'] > 0) {
        $action = $data['ACTION'];
    } else {
        if ($data['PROPERTY_VALUES']['99']['n0'] > 0) {
            $action = 1407;
        } else {
            $action = 1408;
        }
    }

    $arElementFields['UF_SECTION_ID'] = $data['IBLOCK_SECTION_ID'];
    $arElementFields['UF_DATE_CREATE'] = date('d.m.Y H:i:s');
    $arElementFields['UF_ACTIONS'] = $action;
    $arElementFields['UF_PRODUCT'] = $data['PROPERTY_VALUES'][98];
    $arElementFields['UF_STORE_EL'] = abs($data['PROPERTY_VALUES']['99']['n0']);
    $arElementFields['UF_USER'] = $USER->GetID();
    $arElementFields['UF_STATUS_PROD'] = $status;
    $arElementFields['UF_DEAL'] = $data['UF_DEAL'];
    $arElementFields['UF_STORE_ELEMENT'] = $data['UF_STORE_ELEMENT'];

    $obResult = $strEntityDataClass::add($arElementFields);

    $ID = $obResult->getID();
    $bSuccess = $obResult->isSuccess();
    if($bSuccess){
        createStoreReportsEl(['UF_QUANTITY'=> abs($data['PROPERTY_VALUES']['99']['n0']), 'UF_PRODUCT'=> $data['PROPERTY_VALUES'][98], 'UF_STORE_TO'=> $data['UF_STORE_TO'], 'UF_STORE_FROM'=> $data['IBLOCK_SECTION_ID']], $status);
    }
    return $bSuccess;
}

function updateElementStore ($ID, $data)
{
    global $USER;
    $id_s = $ID;
    $checkRegister = [];
    $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(1)->fetch();
    $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();

    $arElementFields['UF_STATUS_PROD'] = $data['STATUS'];

    if ($data['UF_SECTION_ID']) {
        $arElementFields['UF_SECTION_ID'] = $data['UF_SECTION_ID'];
    }

    $obResult = $strEntityDataClass::update($ID, $arElementFields);

    $ID = $obResult->getID();
    $bSuccess = $obResult->isSuccess();
    if($bSuccess){
        $fields = getStoreFieldList($id_s);
        createStoreReportsEl(['UF_QUANTITY'=> 1, 'UF_PRODUCT'=> $fields['UF_PRODUCT'], 'UF_STORE_FROM'=> $fields['UF_STORE_TO']], $data['STATUS']);
    }
    return $bSuccess;
}

function checkStoreElement($data)
{
    $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(1)->fetch();
    $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();
    $rsData = $strEntityDataClass::getList(array(
        'select' => array('ID', 'UF_DEAL', 'UF_PRODUCT'),
        'order' => array('ID' => 'DESC'),
        'limit' => '50',
        'filter' => array('UF_DEAL'=> $data['UF_DEAL'], 'UF_PRODUCT'=> $data['UF_PRODUCT'])
    ));
    if($arItem = $rsData->Fetch())
        if($arItem['ID']) {
            $res = $arItem['ID'];
        } else {
            $res = 0;
        }
    return $res;
}

function checkStoreTransit($data, $sect)
{
    $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(1)->fetch();
    $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();
    $rsData = $strEntityDataClass::getList(array(
        'select' => array('ID', 'UF_DEAL', 'UF_PRODUCT'),
        'order' => array('ID' => 'DESC'),
        'limit' => '50',
        'filter' => array('UF_DEAL'=> $data['UF_DEAL'], 'UF_PRODUCT'=> $data['UF_PRODUCT'], 'UF_SECTION_ID'=> $sect)
    ));
    if($arItem = $rsData->Fetch())
        if($arItem['ID']) {
            $res = $arItem['ID'];
        } else {
            $res = 0;
        }
    return $res;
}

function getStoreFieldList($id)
{
    $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(1)->fetch();
    $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();
    $rsData = $strEntityDataClass::getList(array(
        'select' => array('ID', 'UF_STORE_TO', 'UF_PRODUCT'),
        'order' => array('ID' => 'DESC'),
        'limit' => '50',
        'filter' => array('ID'=> $id)
    ));
    if($arItem = $rsData->Fetch())
        return $arItem;
}

function checkStoreTransitEl($data, $sect)
{
    $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(1)->fetch();
    $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();
    $rsData = $strEntityDataClass::getList(array(
        'select' => array('ID', 'UF_DEAL', 'UF_PRODUCT', 'UF_STORE_ELEMENT'),
        'order' => array('ID' => 'DESC'),
        'limit' => '50',
        'filter' => array('UF_DEAL'=> $data['UF_DEAL'], 'UF_PRODUCT'=> $data['UF_PRODUCT'], 'UF_SECTION_ID'=> $sect)
    ));
    if($arItem = $rsData->Fetch())
        if($arItem['UF_STORE_ELEMENT'] > 0) {
            $res = $arItem['UF_STORE_ELEMENT'];
        } else {
            $res = 0;
        }
    return $res;
}

function createStoreReportsEl($data, $status)
{
    if($data['UF_QUANTITY'] > 0) {
        global $USER;
        $checkRegister = [];
        $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(2)->fetch();
        $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        $arElementFields['UF_USER'] = $USER->GetID();
        $arElementFields['UF_DATE_CREATE'] = date('d.m.Y H:i:s');
        $arElementFields['UF_QUANTITY'] = $data['UF_QUANTITY'];
        $arElementFields['UF_PRODUCT'] = $data['UF_PRODUCT'];
        $arElementFields['UF_STORE_FROM'] = $data['UF_STORE_FROM'];

        if ($status == 1413) {
            $arElementFields['UF_STATUS_EL'] = 1414;
        } else if ($status == 1412) {
            $arElementFields['UF_STATUS_EL'] = 1416;
            $arElementFields['UF_STORE_TO'] = $data['UF_STORE_TO'];
        } else if ($status == 1410) {
            $arElementFields['UF_STATUS_EL'] = 1415;
        } else if ($status == 1411) {
            $arElementFields['UF_STATUS_EL'] = 1417;
        } else if ($status == 1421) {
            $arElementFields['UF_STATUS_EL'] = 1420;
        }

        $obResult = $strEntityDataClass::add($arElementFields);

        $ID = $obResult->getID();
        $bSuccess = $obResult->isSuccess();
        return $bSuccess;
    }
}
?>
