<?php
//$_SERVER["DOCUMENT_ROOT"] =
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("CRM");
CModule::IncludeModule("iblock");

//getStoreList();

function getStoreList()
{
    $arSelect = Array("ID", "PROPERTY_98"); // Уменьшил количество полей
    $arFilter = Array("IBLOCK_ID"=> 31, "ACTIVE"=>"Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
        if( $arFields['PROPERTY_98_VALUE'] > 0 )
        {
            $prop = getPropertyProduct($arFields['PROPERTY_98_VALUE']);
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
        }

    }
}

function getPropertyProduct($id)
{
    $arSelect = Array("ID", "PROPERTY_100", "PROPERTY_101", "PROPERTY_102", "PROPERTY_103", "PROPERTY_104", "PROPERTY_105", "PROPERTY_106", "PROPERTY_107", "PROPERTY_108", "PROPERTY_109");
    $arFilter = Array("IBLOCK_ID"=> 27, 'ID'=> $id);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement())
        $arFields = $ob->GetFields();
        $arFields['PROPERTY_106_VALUE'] = date("d.m.Y", strtotime($arFields['PROPERTY_106_VALUE']));
        return $arFields;

}
?>