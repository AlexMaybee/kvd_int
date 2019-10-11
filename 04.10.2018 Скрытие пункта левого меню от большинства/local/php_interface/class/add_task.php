<?
use \Bitrix\Tasks\Kanban;
CModule::IncludeModule("tasks");
//Событие срабатывает при обновлении Задачи
AddEventHandler('tasks', 'OnTaskAdd', Array('СTaskHandlers', 'OnTaskAddHandler'));

class СTaskHandlers
{
    public static function OnTaskAddHandler(&$ID, &$arFields)
    {
        if( $arFields['GROUP_ID'] > 0 && $arFields['UF_AUTO_267993806107'] == 1) {
            self::getStagesTable($arFields['GROUP_ID'], $arFields['ID']);
    }
    }

    private function getStagesTable($id, $task_id)
    {

        global $USER;

        $res = Kanban\StagesTable::getStages($id);

        $task = CTaskItem::getInstance($task_id, $USER->GetID());

        foreach ($res as $val)
        {
            \CTaskCheckListItem::add($task, ['TITLE'=>$val['TITLE'], 'SORT_INDEX'=>$val['SORT'], 'IS_COMPLETE'=>'N']);
        }
    }
}

//Событие срабатывает при обновлении Задачи
AddEventHandler('tasks', 'OnTaskUpdate', Array('СTaskHandlers_1', 'OnTaskUpdateHandler'));

class СTaskHandlers_1
{
    public static function OnTaskUpdateHandler($ID, &$arFields, &$arTaskCopy)
    {
        if($arFields['STAGE_ID'] && $arFields['META:PREV_FIELDS']['GROUP_ID']){
            $result = self::getNameStageId($arFields['META:PREV_FIELDS']['GROUP_ID'], $arFields['STAGE_ID'], $arFields['ID']);
            //df($result);
        }
    }
    
    private function getCheckList($taskId, $value)
    {
        global $USER;
        $res = \CTaskCheckListItem::getByTaskId($taskId);
        while ($item = $res->Fetch())
        {
            if($item['TITLE'] == $value ) {
                $task = \CTaskItem::getInstance($taskId, $USER->GetID());
                $itemCheck = new \CTaskCheckListItem($task, $item['ID']);
                return $itemCheck->complete();
                //return $item['ID'];
            }
            
        }
    }

    private function getNameStageId($groupId, $id, $taskId)
    {
        $res = Kanban\StagesTable::getStages($groupId);
        foreach ($res as $val)
        {
            if($val['ID'] == $id) {
                $result = self::getCheckList($taskId, $val['TITLE']);
                return $result;
            }
        }
    }
}
?>