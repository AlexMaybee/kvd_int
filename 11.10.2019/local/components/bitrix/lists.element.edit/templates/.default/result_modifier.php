<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arResult['DEAL_ID_LINK'] = '';

if($arResult['ELEMENT_ID'] > 0) {
    $deal_id = getPropertyProductCrm($arResult['ELEMENT_ID']);
    if($deal_id['PROPERTY_144_VALUE'] > 0){
        $arResult['DEAL_ID_LINK'] = '/crm/deal/details/'.$deal_id['PROPERTY_144_VALUE'].'/';
    }
}

?>