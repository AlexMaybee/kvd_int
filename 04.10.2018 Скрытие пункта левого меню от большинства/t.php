<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use \Bitrix\Tasks\Kanban;

CModule::IncludeModule("tasks");
CModule::IncludeModule("iblock");
CModule::IncludeModule("im");

//echo getCheckList1(2843, 5703);

//$res = CIBlockSection::GetByID(217);
//if($ar_res = $res->GetNext())
//    print_r($ar_res);

//$arFilter = Array('IBLOCK_ID'=> 31, 'GLOBAL_ACTIVE'=>'Y', 'ID'=> 218);
//$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true);
//while($ar_result = $db_list->GetNext())
//{
//    echo '<pre>';
//    print_r($ar_result);
//    echo '</pre>';
//}
//$_POST['CHAT_ID'] = 1;
//
//$CIMChat = new CIMChat();
//$result = $CIMChat->MuteNotify($_POST['CHAT_ID'], $_POST['MUTE'] == 'N');
//echo \Bitrix\Im\Common::objectEncode(Array(
//    'CHAT_ID' => intval($_POST['CHAT_ID']),
//    'ERROR' => $result? '': 'ACCESS_ERROR'
//));
//$userId = 1;
//$chatId = 1;
//
//
//if (\Bitrix\Main\Loader::includeModule('pull'))
//{
//    $res = \Bitrix\Pull\Event::add($userId, Array(
//        'module_id' => 'im',
//        'command' => 'chatMuteNotify',
//        'params' => Array(
//            'dialogId' => 'chat'.$chatId,
//            'mute' => false
//        ),
//        'extra' => \Bitrix\Im\Common::getPullExtra()
//    ));
//    dg($res);
//}



//echo \Bitrix\Im\Chat::mute(1, true, 587);

////$task = CTaskItem::getInstance(2817, 1);
//$res = Kanban\StagesTable::getStages(35);
//dg($res);
//foreach ($res as $val)
//{
//
//    \CTaskCheckListItem::add($task, ['TITLE'=>$val['TITLE'], 'SORT_INDEX'=>$val['SORT'], 'IS_COMPLETE'=>'N']);
//}

//
//$c = getCheckList1(2824, 'В работе');
//dg($c);
//function getCheckList1($taskId, $value)
//{
//    $res = \CTaskCheckListItem::getByTaskId($taskId);
//    $checklist = [];
//    while ($item = $res->Fetch())
//    {
//        if($item['TITLE'] == $value ) {
//            $checklist[] = $item;
//            return $item['ID'];
//        }
//    }
//}
/*
$re = getCheckList1(2824, 'Переделать');
dg($re);
function getCheckList1($taskId, $value)
{

    global $USER;
    $res = \CTaskCheckListItem::getByTaskId($taskId);
    while ($item = $res->Fetch())
    {
        if($item['TITLE'] == $value ) {
            $task = \CTaskItem::getInstance($taskId, $USER->GetID());
            dg($task);
            dg($item);
            $itemCheck = \CTaskCheckListItem($task, $item['ID']);
            return $itemCheck->сomplete();
            //return $item['ID'];
        }

    }
}*/

//$task = \CTaskItem::getInstance(2824, 1);
//$item = new \CTaskCheckListItem($task, 5692);
//echo $item->complete();

//echo getCheckList1(2824, 'Сделаны');
//
//function getCheckList1($taskId, $value)
//{
//    global $USER;
//    $res = \CTaskCheckListItem::getByTaskId($taskId);
//    while ($item = $res->Fetch())
//    {
//        if($item['TITLE'] == $value ) {
//            $task = \CTaskItem::getInstance($taskId, $USER->GetID());
//            $itemCheck = new \CTaskCheckListItem($task, $item['ID']);
//            return $itemCheck->complete();
//            //return $item['ID'];
//        }
//
//    }
//}

//Проверяем, принадлежит ли пользователь к массиву руководства
/*function chechIfIsDirector(){
    global $USER;
    $directorsEmail = array('2090910@gmail.com','092s@mail.ru','3505@sherp.pro','3137707@gmail.com','1903@sherp.ru','Pavlov@sherp.ru');
    foreach ($directorsEmail as $email){
        echo '<pre>'.$USER->getEmail().'<br>';
        echo $email;
        echo '</pre>';

        if($email == $USER->getEmail()) return true;


    }
    return false;
}*/
/*
function userIsAdmin(){
    global $USER;
    return $USER->IsAdmin();
}

function checkUserIfPiterDepartments(){
    global $USER;
    $rsUser = CUser::GetByID($USER->getID());
    $arUser = $rsUser->Fetch();
    $userDepartments = array(174,182,185,186);
    foreach($arUser['UF_DEPARTMENT'] as $dep_id){
        if(in_array($dep_id,$userDepartments)) return true;

    }
else return false;
}*/

if(checkUserIfPiterDepartments() == false) echo "<br>Ты не в питерском отделе!";
else echo "<br> Ты в одном из питерских отделов!";


if(chechIfIsDirector() == false) echo '<br>Ты  Не Директор!';
else echo '<br>Ты директор!';

if(userIsAdmin() == true) echo '<br>Ты Админ!'; else echo '<br>Ты не админ!';



global $USER;
echo '<br>Емейл: '.$USER->getEmail();

//$rsUser = $arGroups = CUser::GetUserGroup($USER->getID());
/*echo '<pre>';
print_r($arUser);
echo '</pre>';
echo $USER->IsAdmin();*/

?>