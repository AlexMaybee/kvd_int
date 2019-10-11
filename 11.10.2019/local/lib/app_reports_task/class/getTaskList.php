<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule('highloadblock');
CModule::IncludeModule("crm");
CModule::IncludeModule("tasks");
CModule::IncludeModule("socialnetwork");


class TasksReports
{

    const IBLOCK_ID = 33;
    const DEPARTMENT_IBLOCK_ID = 5;

    public function getTaskList ($page, $filter)
    {
        global $USER;
        $arFilterCustom = self::buildFilter($filter);
        $tasks = [];

        if ( CSite::InGroup (array(26) ) ) {
            $loggedInUserId = 1;
        } else {
            $loggedInUserId = $USER->GetID();
        }
        $arOrder = array("ID" => "DESC");
        $arGetListParams = array();
        $arFilter = $arFilterCustom;
        $arSelect = array();
        $arGetListParams["NAV_PARAMS"]["iNumPage"] = $page;
        $arGetListParams["NAV_PARAMS"]["nPageSize"] = 30;

        list($arTaskItems, $rsItems) = CTaskItem::fetchList($loggedInUserId, $arOrder, $arFilter, $arGetListParams, $arSelect);
        foreach ($arTaskItems as $oTaskItem) {
            $task = $oTaskItem->getData();
            $task['ACCOMPLICES_FULL_NAME'] = [];
            $task['CHANGED_BY_FULL_NAME'] = '';
            $task['CLOSED_BY_FULL_NAME'] = '';
            $task['GROUP_NAME'] = '';
            $task['PRIORITY'] = ($task['PRIORITY'] == 1)? 'Нет' : 'Важная';
            $task['STATUS'] = self::getStatusTask($task['STATUS']);
            $task['DETAIL_URL'] = '/company/personal/user/'.$task['RESPONSIBLE_ID'].'/tasks/task/view/'.$task['ID'].'/';

            if ($task['CHANGED_BY'] > 0)
            {
                $task['CHANGED_BY_FULL_NAME'] = self::getUserName($task['CHANGED_BY']);
            }

            if ($task['CLOSED_BY'] > 0)
            {
                $task['CLOSED_BY_FULL_NAME'] = self::getUserName($task['CLOSED_BY']);
            }

            if ($task['GROUP_ID'] > 0)
            {
                $task['GROUP_NAME'] = self::getSocGroupName($task['GROUP_ID']);
            }

            if (!empty($task['ACCOMPLICES']))
            {
                foreach ($task['ACCOMPLICES'] as $accomplice)
                {
                    $task['ACCOMPLICES_FULL_NAME'][] = self::getUserName($accomplice);
                }
            }

            $tasks[] = $task;
        }
        $count = $rsItems->NavRecordCount;
        return ['TASKS'=> $tasks, 'MAX_PAGE'=> $count, 'MAX_PAGER'=> ceil($count / 30)];

    }

    private function buildFilter ($filter)
    {
        global $USER;
        $arFilter = [];
        if($filter->TITLE)
        {
            $arFilter['TITLE'] = "%".$filter->TITLE."%";
        }

        if($filter->ID)
        {
            $arFilter['ID'] = $filter->ID;
        }

        if($filter->PRIORITY)
        {
            $arFilter['PRIORITY'] = $filter->PRIORITY;
        }

        if($filter->STATUS)
        {
            $arFilter['STATUS'] = $filter->STATUS;
        }

        if($filter->RESPONSIBLE_ID)
        {
            $arFilter['RESPONSIBLE_ID'] = $filter->RESPONSIBLE_ID;
        }

        if($filter->ACCOMPLICES)
        {
            $arFilter['ACCOMPLICE'] = $filter->ACCOMPLICES;
        }

        if($filter->CREATED_BY)
        {
            $arFilter['CREATED_BY'] = $filter->CREATED_BY;
        }

        if($filter->DEPARTMENT_ID)
        {
            $arFilter['DEPARTMENT_ID'] = $filter->DEPARTMENT_ID;
        }

        if($filter->GROUP_ID)
        {
            $arFilter['GROUP_ID'] = $filter->GROUP_ID;
        }

        if($filter->USER_TASK == 1)
        {
            $arFilter['!RESPONSIBLE_ID'] = $USER->GetID();
            $arFilter['!CREATED_BY'] = $USER->GetID();
        }

        return $arFilter;
    }

    public function getUsersList ()
    {
        $data = [];
        $filter = Array
        (
            //"GROUPS_ID"=> Array($groupID) // ID group
        );
        $rsUsers = CUser::GetList(($by="id"), ($order="desc"), $filter);
        while($arItem = $rsUsers->GetNext())
        {
            $data[] = ['id'=> $arItem['ID'], 'name'=> $arItem['LAST_NAME'].' '.$arItem['NAME']];
        }
        return $data;
    }

    public function getStatusList ()
    {
        $data = [
            ['id'=> '-2', 'name'=> 'Новая задача (не просмотрена)'],
            ['id'=> '-1', 'name'=> 'Задача просрочена'],
            ['id'=> '-3', 'name'=> 'Задача почти просрочена'],
            ['id'=> '1', 'name'=> 'Новая задача'],
            ['id'=> '2', 'name'=> 'Задача принята ответственным'],
            ['id'=> '3', 'name'=> 'Задача выполняется'],
            ['id'=> '4', 'name'=> 'Условно завершена (ждет контроля постановщиком)'],
            ['id'=> '5', 'name'=> 'Задача завершена'],
            ['id'=> '6', 'name'=> 'Задача отложена'],
            ['id'=> '7', 'name'=> 'Задача отклонена ответственным']
        ];
        return $data;
    }

    private function getUserName ($ID)
    {
        if($ID > 0) {
            $data = [];
            $filter = Array
            (
                "ID" => $ID // ID user
            );
            $rsUsers = CUser::GetList(($by = "id"), ($order = "desc"), $filter);
            if ($arItem = $rsUsers->GetNext())
            return $arItem['LAST_NAME'] . ' ' . $arItem['NAME'];
        } else {
            return 'ID не найдено';
        }
    }

    public function getDepartmentList ()
    {
        $dep_data = [];
        $arFilter = array('IBLOCK_ID' => self::DEPARTMENT_IBLOCK_ID, 'ACTIVE' => 'Y', "<=DEPTH_LEVEL" => 3);
        $arSelect = array('ID', 'NAME', 'DEPTH_LEVEL');
        $rsSection = CIBlockSection::GetTreeList($arFilter, $arSelect);
        while($arSection = $rsSection->Fetch()) {
            if ($arSection['DEPTH_LEVEL'] == 1) {
                $i = '-';
            } elseif($arSection['DEPTH_LEVEL'] == 2) {
                $i = '--';
            } elseif($arSection['DEPTH_LEVEL'] == 3) {
                $i = '---';
            }
            $dep_data[] = ['id'=> $arSection['ID'], 'name'=> $i.$arSection['NAME']];
        }
        return $dep_data;
    }

    public function getSocNetGroupList ()
    {
        $group_data = [];
        $arFilterTmp = array("SITE_ID" => SITE_ID, "ACTIVE" => "Y");
        $dbGroups = CSocNetGroup::GetList(
            array("NAME" => "ASC"),
            $arFilterTmp,
            false,
            false,
            array("ID", "NAME")
        );

        while ($arGroup = $dbGroups->GetNext())
        {
            $group_data[] = ['id'=> $arGroup['ID'], 'name'=> $arGroup['NAME']];
        }
        return $group_data;
    }

    public function getSocGroupName ($ID)
    {
        $arFilterTmp = array("SITE_ID" => SITE_ID, "ACTIVE" => "Y", "ID" => $ID);
        $dbGroups = CSocNetGroup::GetList(
            array("NAME" => "ASC"),
            $arFilterTmp,
            false,
            false,
            array("ID", "NAME")
        );

        if ($arGroup = $dbGroups->GetNext())
            return $arGroup['NAME'];
    }

    private function getStatusTask ($ID) {
        switch ($ID) {
            case '-2':
                $res = 'Новая задача (не просмотрена)';
                break;
            case '-1':
                $res = 'Задача просрочена';
                break;
            case '-3':
                $res = 'Задача почти просрочена';
                break;
            case '1':
                $res = 'Новая задача';
                break;
            case '2':
                $res = 'Задача принята ответственным';
                break;
            case '3':
                $res = 'Задача выполняется';
                break;
            case '4':
                $res = 'Условно завершена (ждет контроля постановщиком)';
                break;
            case '5':
                $res = 'Задача завершена';
                break;
            case '6':
                $res = 'Задача отложена';
                break;
            case '7':
                $res = 'Задача отклонена ответственным';
                break;
            default:
                $res = 'Статус не найден';
        }
        return $res;

    }

    public function getTaskListPrint ($filter)
    {
        global $USER;
        $arFilterCustom = self::buildFilter($filter);
        $tasks = [];
        if ( CSite::InGroup (array(26) ) ) {
            $loggedInUserId = 1;
        } else {
            $loggedInUserId = $USER->GetID();
        }
        $arOrder = array("ID" => "DESC");
        $arFilter = $arFilterCustom;
        $arSelect = array();

        list($arTaskItems, $rsItems) = CTaskItem::fetchList($loggedInUserId, $arOrder, $arFilter, false, $arSelect);
        foreach ($arTaskItems as $oTaskItem) {
            $task = $oTaskItem->getData();
            $task['ACCOMPLICES_FULL_NAME'] = [];
            $task['CHANGED_BY_FULL_NAME'] = '';
            $task['CLOSED_BY_FULL_NAME'] = '';
            $task['GROUP_NAME'] = '';
            $task['PRIORITY'] = ($task['PRIORITY'] == 1)? 'Нет' : 'Важная';
            $task['STATUS'] = self::getStatusTask($task['STATUS']);
            $task['DETAIL_URL'] = '/company/personal/user/'.$task['RESPONSIBLE_ID'].'/tasks/task/view/'.$task['ID'].'/';

            if ($task['CHANGED_BY'] > 0)
            {
                $task['CHANGED_BY_FULL_NAME'] = self::getUserName($task['CHANGED_BY']);
            }

            if ($task['CLOSED_BY'] > 0)
            {
                $task['CLOSED_BY_FULL_NAME'] = self::getUserName($task['CLOSED_BY']);
            }

            if ($task['GROUP_ID'] > 0)
            {
                $task['GROUP_NAME'] = self::getSocGroupName($task['GROUP_ID']);
            }

            if (!empty($task['ACCOMPLICES']))
            {
                foreach ($task['ACCOMPLICES'] as $accomplice)
                {
                    $task['ACCOMPLICES_FULL_NAME'][] = self::getUserName($accomplice);
                }
            }

            $tasks[] = $task;
        }
        return $tasks;

    }
}


?>
