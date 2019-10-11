<?php
function loadIncludes()
{
    $includeFiles = scandir($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/class/");

    //df($includeFiles);
    foreach ($includeFiles as $include) :
        if ($include == "." || $include == "..") {
            continue;
        } elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/class/" . $include)) {
            require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/class/" . $include);
        }
    endforeach;
}

loadIncludes();

if( !(strstr($APPLICATION->GetCurPage(), '/bitrix/')) ) {
    $APPLICATION->AddHeadScript('//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js');
    $APPLICATION->AddHeadScript('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js');
    $APPLICATION->AddHeadScript('/local/lib/js/script.js');
    $APPLICATION->AddHeadScript('/local/lib/js/custom_select.js');

    //if(strstr($APPLICATION->GetCurPage(), '/tasks/task/view/')) {
    $APPLICATION->AddHeadScript('/local/lib/js/script_task.js');
    $APPLICATION->AddHeadScript('/local/lib/js/script_task_but.js');
    $APPLICATION->AddHeadScript('/local/lib/js/button_events.js');
    $APPLICATION->AddHeadScript('/local/lib/js/script_task_responsible_copy.js');
    $APPLICATION->AddHeadScript('/local/lib/js/script_deal_custom_copy.js');
    //}

    $APPLICATION->SetAdditionalCSS('/local/lib/css/style.css');
    $APPLICATION->SetAdditionalCSS('/local/lib/css/style_chat_copy.css');
    $APPLICATION->SetAdditionalCSS('/local/lib/css/style_deal_copy.css');
    $APPLICATION->SetAdditionalCSS('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css');

    //скрытие Mongo office в левом меню
    $APPLICATION->AddHeadScript('/local/lib/js/hideMenuPunct.js');
}

if( (strstr($APPLICATION->GetCurPage(), '/calendar/')) ) {
    // $APPLICATION->AddHeadScript('/local/lib/js/copy_event_calendar.js');
    $APPLICATION->AddHeadScript('/local/lib/js/calendar/calendar-edit-entry-slider.js');
    $APPLICATION->AddHeadScript('/local/lib/js/calendar/calendar-simple-view-popup.js');
}


function getCheckList($ID){
    $checks = [];
    $checklist = [];
    $res = \CTaskCheckListItem::getByTaskId($ID);
    while ($item = $res->Fetch())
    {
        $checklist[] = $item;
        if($item['IS_COMPLETE']=='Y'){
            $checks[] = 'Y';
        }
    }
    return array('CHECKED' => count($checks), 'LIST'=> count($checklist));
}

//дебаг
function dg($arr){
    global $USER;
    if ($USER->IsAdmin()){
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
    }
}



//запись в файл
function df($arr){
    global $USER;
        $file = $_SERVER['DOCUMENT_ROOT'].'/log.txt';
        file_put_contents($file, print_r($arr, true), FILE_APPEND | LOCK_EX);
}


/*Скрытие Mango Office от всех кроме*/
function chechIfIsDirector(){
    global $USER;
    $directorsEmail = array('2090910@gmail.com','092s@mail.ru','3505@sherp.pro','3137707@gmail.com','1903@sherp.ru','Pavlov@sherp.ru');
    foreach ($directorsEmail as $email){
        if($email == $USER->getEmail()) return true;
    }
    return false;
}

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
    return false;
}
/*Скрытие Mango Office от всех кроме*/


function getNameSectionEn($IBLOCK_ID, $NAME, $CODE)
{
    $arFilter = Array('IBLOCK_ID'=> $IBLOCK_ID, 'NAME'=> $NAME);
    $db_list = CIBlockSection::GetList(Array("ID"=>"DESC"), $arFilter, false, Array($CODE));
    if($uf_value = $db_list->GetNext())
        return ($uf_value[$CODE]) ? $uf_value[$CODE] : $NAME;
}
?>



