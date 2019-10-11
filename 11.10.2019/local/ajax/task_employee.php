<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
header('Content-type: application/json');

CModule::IncludeModule("tasks");
CModule::IncludeModule('calendar');
CModule::IncludeModule('timeman');
global $USER;

$return = [];

if($_REQUEST['action']) {
    switch ($_REQUEST['action']) {
        case 'GetByIDTask':
            $return = false;
            $rsTask = CTasks::GetByID($_REQUEST['id']);
            if ($arTask = $rsTask->GetNext())
                if(!empty($arTask['AUDITORS']) ) {
                    if(CSite::InGroup( array(35) ) && in_array($USER->GetID(), $arTask['AUDITORS'])) {
                        $return = true;
                    }
                }
            break;


            case 'updateTask':
            $return = false;
            $rsTask = CTasks::GetByID($_REQUEST['id']);
            if ($arTask = $rsTask->GetNext())
                if(!empty($arTask['AUDITORS']) ) {
                    if (($key = array_search($USER->GetID(), $arTask['AUDITORS'])) !== false) {
                        unset($arTask['AUDITORS'][$key]);
                    }

                    $arFields = Array(
                        "AUDITORS" => $arTask['AUDITORS'],
                    );
                    $obTask = new CTasks;
                    $success = $obTask->Update($_REQUEST['id'], $arFields);

                    if($success)
                    {
                        $return = $USER->GetID();

                    }
                    else
                    {
                        if($e = $APPLICATION->GetException())
                            $return =  "Error: ".$e->GetString();
                    }
                }
            break;

            case 'CCalendarEvent':
                $data = [];
                $date = date("d.m.Y H:i:s");
                $ts = CTimeMan::RemoveHoursTS(MakeTimeStamp($date));
                $arFilter = array('arFilter' => array("OWNER_ID" => $USER->GetID(), "FROM_LIMIT" => ConvertTimeStamp($ts, 'FULL'), "TO_LIMIT" => ConvertTimeStamp($ts + 86399, 'FULL')), 'parseRecursion' => true, 'userId' => $USER->GetID(), 'skipDeclined' => true, 'fetchAttendees' => false, 'fetchMeetings' => true);

                $results = CCalendarEvent::GetList($arFilter);
                if(!empty($results)) {
                    foreach ($results as $result) {
                        $data[] = ['NAME'=> $result['NAME'], 'LOCATION'=> $result['LOCATION'], 'DATE_FROM'=> $result['DATE_FROM'], 'HREF'=> '/company/personal/user/'.$USER->GetID().'/calendar/?EVENT_ID='.$result['ID']];
                    }
                    $return = $data;
                }
            break;

                case 'CCalendarEventCount':
                $data = [];
                $date = date("d.m.Y H:i:s");
                $ts = CTimeMan::RemoveHoursTS(MakeTimeStamp($date));
                $arFilter = array('arFilter' => array("OWNER_ID" => $USER->GetID(), "FROM_LIMIT" => ConvertTimeStamp($ts, 'FULL'), "TO_LIMIT" => ConvertTimeStamp($ts + 86399, 'FULL')), 'parseRecursion' => true, 'userId' => $USER->GetID(), 'skipDeclined' => true, 'fetchAttendees' => false, 'fetchMeetings' => true);

                $results = CCalendarEvent::GetList($arFilter);
                if(!empty($results)) {
                    foreach ($results as $result) {
                        $data[] = $result['ID'];
                    }
                    $return = count($data);
                }
            break;
    }
}

echo json_encode($return);

?>
