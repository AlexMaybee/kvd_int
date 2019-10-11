<?php
AddEventHandler("iblock", "OnBeforeIBlockElementAdd", Array("createEventStore", "OnAfterIBlockElementAddHandler"));

AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("updateEventStore", "OnAfterIBlockElementUpdateHandler"));

AddEventHandler("iblock", "OnBeforeIBlockElementDelete", Array("deleteEventStore", "OnAfterIBlockElementDeleteHandler"));

class createEventStore
{
    function OnAfterIBlockElementAddHandler(&$arFields)
    {

        global $APPLICATION;

        if($arFields['IBLOCK_ID'] == 27 ) {

            if($arFields['PROPERTY_VALUES'][105]){
                $cartID = getCartOriginal($arFields['PROPERTY_VALUES'][105]['n0']['VALUE'], 27, 105);
                if($cartID > 0) {
                    $APPLICATION->throwException("Такой VIN code существует, ИД товара - ".$cartID);
                    return false;
                }
            }
        }

        if($arFields['IBLOCK_ID'] == 31 )
        {
            if($arFields['PROPERTY_VALUES'][125]){
                $cartID = getCartOriginal($arFields['PROPERTY_VALUES'][125]['n0']['VALUE'], 31, 125);
                if($cartID > 0) {
                    $APPLICATION->throwException("Такой VIN code существует, ИД скдада - ".$cartID);
                    return false;
                }
            }


            if($arFields['PROPERTY_VALUES'][98] > 0) {
                $res_id = $arFields['PROPERTY_VALUES'][98];

                if ($arFields['PROPERTY_VALUES']['TOVAR']) {
                    $code = 'TOVAR';
                } else {
                    $code = '98';
                }

                $prop = getProductProperty($arFields['PROPERTY_VALUES'][$code]);

                $arFields['PROPERTY_VALUES'][120] = $prop['PROPERTY_100_VALUE'];
                $arFields['PROPERTY_VALUES'][121] = $prop['PROPERTY_101_VALUE'];
                $arFields['PROPERTY_VALUES'][122] = $prop['PROPERTY_102_VALUE'];
                $arFields['PROPERTY_VALUES'][130] = $prop['PROPERTY_103_VALUE'];
                $arFields['PROPERTY_VALUES'][124] = $prop['PROPERTY_104_VALUE'];
                $arFields['PROPERTY_VALUES'][125] = $prop['PROPERTY_105_VALUE'];
                $arFields['PROPERTY_VALUES'][126] = $prop['PROPERTY_150_VALUE'];
                $arFields['PROPERTY_VALUES'][127] = $prop['PROPERTY_107_VALUE'];
                $arFields['PROPERTY_VALUES'][128] = $prop['PROPERTY_108_VALUE'];
                $arFields['PROPERTY_VALUES'][129] = $prop['PROPERTY_109_VALUE'];
                $arFields['PROPERTY_VALUES'][142] = $prop['PROPERTY_137_VALUE'];
                $arFields['PROPERTY_VALUES'][143] = $prop['PROPERTY_136_VALUE'];

            } elseif($arFields['PROPERTY_VALUES']['TOVAR'] > 0) {
                $res_id = $arFields['PROPERTY_VALUES']['TOVAR'];
                if ($arFields['PROPERTY_VALUES']['TOVAR']) {
                    $code = 'TOVAR';
                } else {
                    $code = '98';
                }

                $prop = getProductProperty($arFields['PROPERTY_VALUES'][$code]);

                $arFields['PROPERTY_VALUES'][120] = $prop['PROPERTY_100_VALUE'];
                $arFields['PROPERTY_VALUES'][121] = $prop['PROPERTY_101_VALUE'];
                $arFields['PROPERTY_VALUES'][122] = $prop['PROPERTY_102_VALUE'];
                $arFields['PROPERTY_VALUES'][130] = $prop['PROPERTY_103_VALUE'];
                $arFields['PROPERTY_VALUES'][124] = $prop['PROPERTY_104_VALUE'];
                $arFields['PROPERTY_VALUES'][125] = $prop['PROPERTY_105_VALUE'];
                $arFields['PROPERTY_VALUES'][126] = $prop['PROPERTY_150_VALUE'];
                $arFields['PROPERTY_VALUES'][127] = $prop['PROPERTY_107_VALUE'];
                $arFields['PROPERTY_VALUES'][128] = $prop['PROPERTY_108_VALUE'];
                $arFields['PROPERTY_VALUES'][129] = $prop['PROPERTY_109_VALUE'];
                $arFields['PROPERTY_VALUES'][142] = $prop['PROPERTY_137_VALUE'];
                $arFields['PROPERTY_VALUES'][143] = $prop['PROPERTY_136_VALUE'];
            } else {
                $res_id = createProduct($arFields);
            }

            if($res_id > 0) {
                $arFields['PROPERTY_VALUES'][98] = $res_id;
                $arFields['ACTION'] = 1407;
            }

            if($arFields['PROPERTY_VALUES']['KOLICHESTVO']) {
                $arFields['PROPERTY_VALUES']['99']['n0'] = $arFields['PROPERTY_VALUES']['KOLICHESTVO'];
            }

            if($arFields['PROPERTY_VALUES']['SDELKA']) {
                $arFields['UF_DEAL'] = $arFields['PROPERTY_VALUES']['SDELKA'];
            }

            //df($arFields);
            $store_id = createElementStore($arFields, 1413);
            return $arFields;

        }



            //getProductProperty

            /*if ($arFields['IBLOCK_ID'] == 31) {
                $prop = getPropertyProductCrm($arFields['PROPERTY_VALUES'][$code]);
                df($arFields);
                df($prop);
                CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_100_VALUE'], 'DVIGATEL');
                CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_101_VALUE'], 'TIP_KUZOVA');
                CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_102_VALUE'], 'SISTEMA_OKHLAZHDENIYA');
                CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_103_VALUE'], 'DOP_OBORUDOVANIE');
                CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_104_VALUE'], '_MASHINY');
                CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_105_VALUE'], 'VIN_CODE');
                CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_106_VALUE'], 'DATA_IZGOTOVLENIYA');
                CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_107_VALUE'], 'ROPS');
                CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_108_VALUE'], 'VEBASTO');
                CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_109_VALUE'], 'KOMMENTARIY');
            }*/


    }
}

function updatePropertyElStore($arFields) {
    if($arFields['PROPERTY_VALUES'][98] > 0 || $arFields['PROPERTY_VALUES']['TOVAR'] > 0) {
        if ($arFields['PROPERTY_VALUES']['TOVAR']) {
            $code = 'TOVAR';
        } else {
            $code = '98';
        }


        $prop = getProductProperty($arFields['PROPERTY_VALUES'][$code]);
        CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_120_VALUE'], 'DVIGATEL');
        CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_121_VALUE'], 'TIP_KUZOVA');
        CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_122_VALUE'], 'SISTEMA_OKHLAZHDENIYA');
        CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_130_VALUE'], 'DOP_OBORUDOVANIE');
        CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_124_VALUE'], '_MASHINY');
        CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_125_VALUE'], 'VIN_CODE');
        CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_126_VALUE'], 'DATA_IZGOTOVLENIYA');
        CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_127_VALUE'], 'ROPS');
        CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_128_VALUE'], 'VEBASTO');
        CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_129_VALUE'], 'KOMMENTARIY');
        CIBlockElement::SetPropertyValues($arFields['ID'], 31, $prop['PROPERTY_136_VALUE'], 'STRANA');
    }
}

class updateEventStore
{
    function OnAfterIBlockElementUpdateHandler(&$arFields)
    {
        if($arFields['IBLOCK_ID'] == 31 ) {
            $data = [];
            $result_quan = getPropertyProductCrm($arFields['ID']);

            foreach ($arFields['PROPERTY_VALUES'][99] as $key=> $prop) {
                $result_quan_osn = $prop;
            }

            if ($result_quan['PROPERTY_99_VALUE'] != $result_quan_osn) {
                if($result_quan_osn > $result_quan['PROPERTY_99_VALUE']) {
                    $data['ACTION']= 1407;
                } else {
                    $data['ACTION']= 1408;
                }
                $data['PROPERTY_VALUES'][98] = $arFields['PROPERTY_VALUES'][98];
                $data['IBLOCK_SECTION_ID'] = $arFields['IBLOCK_SECTION_ID'];
                $data['PROPERTY_VALUES']['99']['n0'] = $result_quan_osn - $result_quan['PROPERTY_99_VALUE'];

                createElementStore($data, 1410);
            }

            if($result_quan_osn < 1) {
                deleteElementStore($arFields['ID']);
            }

        }
    }
}

class deleteEventStore
{
    // создаем обработчик события "OnAfterIBlockElementDelete"
    function OnAfterIBlockElementDeleteHandler($arFields)
    {
        $data = [];
        $resFields = getPropertyProductCrm($arFields);
        if ($resFields['IBLOCK_ID'] == 31)
        {
            $data['ACTION']= 1408;
            $data['PROPERTY_VALUES'][98] = $resFields['PROPERTY_98_VALUE'];
            $data['IBLOCK_SECTION_ID'] = $resFields['IBLOCK_SECTION_ID'];
            $data['PROPERTY_VALUES']['99']['n0'] = $resFields['PROPERTY_99_VALUE'];
            createElementStore($data, 1410);
        }
    }
}

function deleteElementStore($ELEMENT_ID)
{
    $res = CIBlockElement::Delete($ELEMENT_ID);
    return $res;
}

function getPropertyProductCrm($id)
{
    $arSelect = Array("ID", "PROPERTY_99", "PROPERTY_98", "IBLOCK_ID", "IBLOCK_SECTION_ID", "PROPERTY_144");
    $arFilter = Array("IBLOCK_ID"=> 31, 'ID'=> $id);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement())
        $arFields = $ob->GetFields();
        return $arFields;
}

function getQuantityProduct($id)
{
    $arSelect = Array("ID", "PROPERTY_100", "PROPERTY_101", "PROPERTY_102", "PROPERTY_103", "PROPERTY_104", "PROPERTY_105", "PROPERTY_150", "PROPERTY_107", "PROPERTY_108", "PROPERTY_109");
    $arFilter = Array("IBLOCK_ID"=> 27, 'ID'=> $id);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement())
        $arFields = $ob->GetFields();
    //$arFields['PROPERTY_106_VALUE'] = str_replace('/', '.', $arFields['PROPERTY_106_VALUE']);
    //$arFields['PROPERTY_106_VALUE'] = date("d.m.Y", strtotime($arFields['PROPERTY_106_VALUE']));
    return $arFields;

}

function getProductProperty($id)
{
    $arSelect = Array("ID", "PROPERTY_100", "PROPERTY_101", "PROPERTY_102", "PROPERTY_103", "PROPERTY_104", "PROPERTY_105", "PROPERTY_150", "PROPERTY_107", "PROPERTY_108", "PROPERTY_109", "PROPERTY_136", "PROPERTY_137");
    $arFilter = Array("IBLOCK_ID"=> 27, 'ID'=> $id);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement())
        $arFields = $ob->GetFields();
    //$arFields['PROPERTY_106_VALUE'] = str_replace('/', '.', $arFields['PROPERTY_106_VALUE']);
    //$arFields['PROPERTY_106_VALUE'] = date("d.m.Y", strtotime($arFields['PROPERTY_106_VALUE']));
    return $arFields;

}

function createProduct( $datas )
{
    global $USER;
    $el = new CIBlockElement;

    $data = $datas['PROPERTY_VALUES'];
    $PROP = array();
    $PROP[100] = $data['120']['n0']['VALUE']; // Двигатель
    $PROP[101] = $data['121']['n0']['VALUE']; // Тип кузова
    $PROP[102] = $data['122']['n0']['VALUE']; // Система охлаждения
    $PROP[103] = $data['130']['n0']['VALUE']; // Доп оборудование
    $PROP[104] = $data['124']['n0']['VALUE']; // № машины
    $PROP[105] = $data['125']['n0']['VALUE']; // VIN code
    $PROP[150] = $data['126']['n0']['VALUE']; // Дата изготовления
    $PROP[107] = $data['127']['n0']['VALUE']; // ROPS
    $PROP[108] = $data['128']['n0']['VALUE']; // Vebasto
    $PROP[109] = $data['129']['n0']['VALUE']; // Комментарий
    $PROP[136] = $data['134']['n0']['VALUE']; // Страна (country)
    $PROP[137] = $data['135']['n0']['VALUE']; // Название диллера (dealer name)

    $sect_id = getSectionIDProduct($datas['IBLOCK_SECTION_ID']);

    $arLoadProductArray = Array(
        "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
        "IBLOCK_ID"      => 27,
        "IBLOCK_SECTION_ID" => $sect_id,
        "PROPERTY_VALUES"=> $PROP,
        "NAME"           => $datas['NAME'],
        "ACTIVE"         => "Y",            // активен
    );

    if($PRODUCT_ID = $el->Add($arLoadProductArray)) {
        return $PRODUCT_ID;
    } else {
        return $el->LAST_ERROR;
    }
}

function getSectionIDProduct ($ID)
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
        default:
            $res = 262;
    }
    return $res;
}

function getCartOriginal($vin, $iblock_id, $code)
{
    df($vin);
    $arSelect = Array("ID", "NAME"); // Уменьшил количество полей
    $arFilter = Array("IBLOCK_ID"=> $iblock_id, '=PROPERTY_'.$code.'_VALUE'=> $vin);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if($ob = $res->GetNextElement())
        $arFields = $ob->GetFields();
    return $arFields['ID'];
}
?>
