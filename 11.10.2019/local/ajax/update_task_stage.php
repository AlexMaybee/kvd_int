<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use \Bitrix\Tasks\Kanban;

CModule::IncludeModule("tasks");

$id = $_POST['id'];
//$id = 2824;
//$status_id = $_POST['id'];
$status_id = $_POST['status_id'];

$status_name = getAjaxCheckListName($id, $status_id);

//$status_name = 'Новые';
$task = new \Bitrix\Tasks\Item\Task($id);
//$fields = $task->getData(array('~'));
//dg($task['UF_AUTO_267993806107']);
if($task['UF_AUTO_267993806107'] == 1 && $task['GROUP_ID'] > 0 && $status_name) {
    $res = Kanban\StagesTable::getStages($task['GROUP_ID']);
    foreach ($res as $val) {
        if ($val['TITLE'] == $status_name) {
            updateStageTask($id, $val['ID']);
        }
    }
}

function updateStageTask($taskId, $stage)
{
    global $USER;
    $oTaskItem = new CTaskItem($taskId, $USER->GetID());
    try
    {
        $rs = $oTaskItem->Update(array("STAGE_ID" => $stage));
    }
    catch(Exception $e)
    {
        $rs = 'Error';
    }
    echo json_encode($stage);
}

function getAjaxCheckListName($taskId, $value)
{
    global $USER;
    $res = \CTaskCheckListItem::getByTaskId($taskId);
    while ($item = $res->Fetch())
    {
        if($item['ID'] == $value ) {
            return $item['TITLE'];
        }
    }
}



?>