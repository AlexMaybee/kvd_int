<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
header('Content-type: application/json');

CModule::IncludeModule("iblock");
$result = [];

$data = json_decode(file_get_contents("php://input"));

if($data->action!=''){
    $_POST['action'] = $data->action;
}

if(!empty($_POST['action'])) {

    switch ($_POST['action']) {

        case 'createStore':
            $el = new CIBlockElement;
            $arLoadProductArray = (array)$data->data;
            $arLoadProductArray['PROPERTY_VALUES'] = (array)$arLoadProductArray['PROPERTY_VALUES'];
            if (checkProduct($arLoadProductArray['PROPERTY_VALUES']['TOVAR'], $arLoadProductArray['IBLOCK_SECTION_ID'], null) > 0) {
                $result = false;
            } else {
                if ($PRODUCT_ID = $el->Add($arLoadProductArray))
                    $result = "New ID: " . $PRODUCT_ID;
                else
                    $result = "Error: " . $el->LAST_ERROR;
            }
            break;

        case 'updateStore':
            $el = new CIBlockElement;
            $arLoadProductArray = (array)$data->data;
            $arLoadProductArray['PROPERTY_VALUES'] = (array)$arLoadProductArray['PROPERTY_VALUES'];
            if (checkProduct($arLoadProductArray['PROPERTY_VALUES']['TOVAR'], $arLoadProductArray['IBLOCK_SECTION_ID'], $data->id) > 0) {
                $result = false;
            } else {
                if ($PRODUCT_ID = $el->Update($data->id, $arLoadProductArray))
                    $result = "New ID: " . $PRODUCT_ID;
                else
                    $result = "Error: " . $el->LAST_ERROR;
            }
            break;

        case 'removeStore':
            $result = CIBlockElement::Delete($data->id);
            break;

        case 'get_list_city':
            $arFilter = Array('IBLOCK_ID'=> 31, 'GLOBAL_ACTIVE'=>'Y', 'DEPTH_LEVEL'=> 1);
            $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true);
            while($ar_result = $db_list->GetNext())
            {
                $result[] = ['id'=> $ar_result['ID'], 'name'=> $ar_result['NAME']];
            }
            break;

        case 'get_list_store':
            $arFilter = Array('IBLOCK_ID'=> 31, 'GLOBAL_ACTIVE'=>'Y', 'DEPTH_LEVEL'=> 2, 'SECTION_ID'=> $data->parent_id);
            $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true);
            while($ar_result = $db_list->GetNext())
            {
                $result[] = ['id'=> $ar_result['ID'], 'name'=> $ar_result['NAME']];
            }
            break;

        case 'get_list_product':
            $arSelect = Array("ID", "PROPERTY_KOLICHESTVO", "IBLOCK_SECTION_ID");
            $arFilter = Array("IBLOCK_ID"=> 31, "ACTIVE"=>"Y", "PROPERTY_TOVAR" => $data->ID);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            while($ob = $res->GetNextElement())
            {
                $arFields = $ob->GetFields();
                $parent_id = getParentSectionId($arFields['IBLOCK_SECTION_ID']);
                if($parent_id > 0 ) {
                    $result[] = [
                        'edit' => $arFields['ID'],
                        'CITY_ID' => ['ID' => $parent_id, 'VALUE' => getSectionName($parent_id)],
                        'STORE_ID' => ['ID' => $arFields['IBLOCK_SECTION_ID'], 'VALUE' => getSectionName($arFields['IBLOCK_SECTION_ID'])],
                        'STORE_COUNT' => $arFields['PROPERTY_KOLICHESTVO_VALUE'],
                    ];
                }
            }
            break;

    }

}

function getParentSectionId($ID) {
    $res = CIBlockSection::GetByID($ID);
    if($ar_res = $res->GetNext())
        return $ar_res['IBLOCK_SECTION_ID'];
}

function getSectionName($ID) {
    $res = CIBlockSection::GetByID($ID);
    if($ar_res = $res->GetNext())
        return $ar_res['NAME'];
}

function checkProduct($ID, $SECTION_ID, $LIST_ID) {
    $c = [];
    $arSelect = Array("ID", "PROPERTY_KOLICHESTVO", "IBLOCK_SECTION_ID");
    if($LIST_ID > 0) {
        $arFilter = Array("IBLOCK_ID" => 31, "ACTIVE" => "Y", "PROPERTY_TOVAR" => $ID, "IBLOCK_SECTION_ID" => $SECTION_ID, '!ID'=> $LIST_ID);
    } else {
        $arFilter = Array("IBLOCK_ID" => 31, "ACTIVE" => "Y", "PROPERTY_TOVAR" => $ID, "IBLOCK_SECTION_ID" => $SECTION_ID);
    }
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
        $parent_id = getParentSectionId($arFields['IBLOCK_SECTION_ID']);
        $c[] = $arFields['ID'];
    }
    return count($c);
}

echo json_encode($result);

?>