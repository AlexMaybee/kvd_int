<?
$_SERVER["DOCUMENT_ROOT"]='/home/bitrix/www';
define("NOT_CHECK_PERMISSIONS",true);
define("CHECK_PERMISSIONS", "N");
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("CRM");
CModule::IncludeModule("socialnetwork");
CModule::IncludeModule('im');
CModule::IncludeModule('iblock');

startBPDeal();

function startBPDeal ()
{
    $arFilter = Array('STAGE_ID' => ['C3:NEW'], 'CATEGORY_ID'=> 3, 'CHECK_PERMISSIONS' => 'N');
    $arSelect = Array();
    $db_list = CCrmDeal::GetListEx(Array("ID" => "ASC"), $arFilter, false, false, $arSelect, array());
    while ($ar_result = $db_list->GetNext()) {

        $date = $ar_result['DATE_CREATE'];

        $time = date('d.m.Y', strtotime($date));

        $res = getDescription(112);

        $datetime1 = new DateTime($time);
        $datetime2 = new DateTime(date('d.m.Y'));
        $diff = date_diff($datetime1, $datetime2);

        if ($diff->days > $res['PROPERTY_PERIOD_DNEY_VALUE']) {
            //echo $ar_result['ID'].' - '.$time.' - '.$ar_result['ASSIGNED_BY_ID'].' - '.$diff->days.'<br/>';
            $params = array('ASSIGNED_BY_ID' => $res['PROPERTY_SOISPOLNITELI_VALUE'], 'MESSAGE' => $res['PROPERTY_TEKST_UVEDOMLENIYA_VALUE']['TEXT'].' <a href="/crm/deal/details/' . $ar_result['ID'] . '/">' . $ar_result['TITLE'] . '</a>');
            sendNotify($params);
        }
    }
}

function sendNotify($params) {
    CIMMessenger::Add(array(
        "MESSAGE_TYPE"  => "S",
        "TO_USER_ID"    => intval($params["ASSIGNED_BY_ID"]),
        "MESSAGE"       => $params["MESSAGE"],
        "NOTIFY_TYPE" => 4
    ));
}

function getDescription ( $TYPE ) {
    $arSelect = Array("NAME", "ID", "IBLOCK_ID", "PROPERTY_TEKST_UVEDOMLENIYA", "PROPERTY_SOISPOLNITELI", "PROPERTY_TIP", "PROPERTY_PERIOD_DNEY"); // Уменьшил количество полей
    $arFilter = Array("IBLOCK_ID"=> 33, "ACTIVE"=> "Y", 'CHECK_PERMISSIONS' => 'N', "PROPERTY_TIP" => $TYPE);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if($ob = $res->GetNextElement())
        $arFields = $ob->GetFields();
    return $arFields;
}

?>