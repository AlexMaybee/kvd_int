<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
header('Content-type: application/json');

require_once ($_SERVER["DOCUMENT_ROOT"] . "/local/lib/app_reports_store/class/getStoreList.php");


$data = json_decode(file_get_contents("php://input"));
$array = [];
$TasksReports = new StoreReports();
if($data[0]->action) {
    switch ($data[0]->action) {
        case 'getStoreList':
            $array = $TasksReports->getStoreList($data[0]->page, $data[0]->filter);
            break;

        case 'getStatusList':
            $array = $TasksReports->getStatusList();
            break;

        case 'getStoreListSection':
            $array = $TasksReports->getStoreListSection();
            break;

        case 'getUsersList':
            $array = $TasksReports->getUsersList();
            break;

        /*case 'getTaskListPrint':
            $array = $TasksReports->getTaskListPrint($data[0]->filter);
            break;

        case 'getDepartmentList':
            $array = $TasksReports->getDepartmentList();
            break;

        case 'getSocNetGroupList':
            $array = $TasksReports->getSocNetGroupList();
            break;*/
    }
}


echo json_encode($array);
?>