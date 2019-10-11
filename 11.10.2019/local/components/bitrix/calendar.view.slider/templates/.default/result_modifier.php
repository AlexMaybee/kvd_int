<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
CModule::IncludeModule("meeting");

function getMeetingId($ID) {

    $parent_id = getParentIdNew($ID);
    if($parent_id > 0) {
        $ID = $parent_id;
    }
    $res = CMeeting::GetList($arOrder = array(),
        $arFilter = array('EVENT_ID'=> $ID, 'CHECK_PERMISSIONS'=> 'N'),
        $arGroupBy = false,
        $arNavStartParams = false,
        $arSelectFields = array());
    if ($arFields = $res->GetNext())
        if($arFields['ID']) {
            $id = $arFields['ID'];
            return $id;
        }
}

function getParentIdNew($ID)
{
    $res = 0;
    $arRes = CCalendarEvent::GetList(array('arFilter' => array('ID'=> $ID)));
    if(!empty($arRes)) {
        foreach ($arRes as $ar) {
            $res = $ar['PARENT_ID'];
        }
    }
    return $res;
}

