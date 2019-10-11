<?

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("CRM");

$arFilter = Array();
$arSelect = Array('ID', 'ASSIGNED_BY_ID');
$db_list = CCrmDeal::GetListEx(Array("ID" => "ASC"), $arFilter, false, false, $arSelect, array());
while($ar_result = $db_list->GetNext()){
    $region = getRegion($ar_result['ASSIGNED_BY_ID']);
    if($region > 0) {
        //updateDeal($region, $ar_result['ID']);
        //echo $ar_result['ID'].' - '.getRegion($ar_result['ASSIGNED_BY_ID']).'<br/>';
    }

}


function getRegion($id){
    $res = 0;
    switch ($id) {
        case 484:
            $res = 1143; // Украина
            break;
        case 524:
            $res = 1143;
            break;
        case 485:
            $res = 1143;
            break;
        case 554:
            $res = 1144; // Канада
            break;
        case 526:
            $res = 1144;
            break;
        case 555:
            $res = 1144;
            break;
        case 525:
            $res = 1144;
            break;
        case 553:
            $res = 1145; // США
            break;
        case 520:
            $res = 1145; // США
            break;
        case 486:
            $res = 1145; // США
            break;
        case 535:
            $res = 1146; // Россия
            break;
        case 538:
            $res = 1146; // Россия
            break;
        case 539:
            $res = 1146; // Россия
            break;
        case 556:
            $res = 1146; // Россия
            break;
        case 537:
            $res = 1146; // Россия
            break;
        case 557:
            $res = 1146; // Россия
            break;
        case 534:
            $res = 1146; // Россия
            break;
        default:
            $res = 0;
    }

    return $res;
}

function updateDeal ($val, $ID)
{
    $Deal = new CCrmDeal;
    $fields = array(
        "UF_CRM_1539249358" => $val
    );
    $result = $Deal->Update($ID, $fields);
    return $result;
}

?>
