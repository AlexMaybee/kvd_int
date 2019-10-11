<?

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("CRM");

$arFilter = Array('>=OPPORTUNITY' => 1000000, 'ID'=>19);
$arSelect = Array('ID', 'CURRENCY_ID', 'OPPORTUNITY');
$db_list = CCrmDeal::GetListEx(Array("ID" => "ASC"), $arFilter, false, false, $arSelect, array());
while($ar_result = $db_list->GetNext()){
    /*echo '<pre>';
    print_r($ar_result);
    echo '</pre>';*/
    //updateDeal($ar_result['OPPORTUNITY'], $ar_result['ID']);
}

function updateDeal ($val, $ID)
{
  $Deal = new CCrmDeal;
      $fields = array(
          "OPPORTUNITY" => $val
      );
      $result = $Deal->Update($ID, $fields);
      return $result;
}


?>
