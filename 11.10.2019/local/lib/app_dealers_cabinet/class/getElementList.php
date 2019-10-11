<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule('highloadblock');
CModule::IncludeModule("crm");
CModule::IncludeModule("tasks");
CModule::IncludeModule("socialnetwork");


class CompanyList
{

    const UF_DEPARTMENT = 195;

    public function getCompanyList ($page, $filter)
    {
        global $USER;
        $arFilterCustom = self::buildFilter($filter);
        $companys = [];

        $arFilter = $arFilterCustom;
        $arSelect = array('ID', 'TITLE', 'ASSIGNED_BY_ID', 'ASSIGNED_BY_NAME', 'ASSIGNED_BY_LAST_NAME', 'UF_CRM_1533026672', 'COMMENTS', 'UF_CREATED_USER');
        $db_list = CCrmCompany::GetListEx(Array("ID" => "DESC"), $arFilter, false, Array("nPageSize"=>30, "iNumPage"=>$page, "bShowAll"=> false), $arSelect, array());
        while($ar_result = $db_list->GetNext()){
            //dg($ar_result);
            $company['ID'] = $ar_result['ID'];
            $company['EDIT'] = ($ar_result['UF_CREATED_USER'] == $USER->GetID()) ? 'Y' : 'N';
            $company['TITLE'] = $ar_result['TITLE'];
            $company['ASSIGNED_BY_NAME'] = $ar_result['ASSIGNED_BY_LAST_NAME'].' '.$ar_result['ASSIGNED_BY_NAME'];
            $company['PHONE'] = implode(','.PHP_EOL, self::getContactFields($ar_result['ID'], 'PHONE'));
            $company['EMAIL'] = implode(','.PHP_EOL, self::getContactFields($ar_result['ID'], 'EMAIL'));
            $company['ADRESS'] = $ar_result['UF_CRM_1533026672'];
            $company['COMMENTS'] = HTMLToTxt($ar_result['COMMENTS'], "http://www.bitrix.ru");
            $companys[] = $company;
        }
        $count = $db_list->NavRecordCount;

        return ['TASKS'=> $companys, 'MAX_PAGE'=> $count, 'MAX_PAGER'=> ceil($count / 30)];

    }


    private function buildFilter ($filter)
    {
        global $USER;
        $arFilter = [];

        if($filter->ASSIGNED_BY_ID) {
            $arFilter['ASSIGNED_BY_ID'] = $filter->ASSIGNED_BY_ID;
        } else {
            $arFilter['ASSIGNED_BY_ID'] = self::getUserDepartment(self::UF_DEPARTMENT);
        }

        if($filter->TITLE)
        {
            $arFilter['%TITLE'] = $filter->TITLE;
        }

        $arFilter['CHECK_PERMISSIONS'] = 'N';

        return $arFilter;
    }

    private function getUserDepartment($id)
    {
        $data = [];
        $filter = Array
        (
            "UF_DEPARTMENT"=> $id
        );
        $rsUsers = CUser::GetList(($by="id"), ($order="desc"), $filter);
        while($arItem = $rsUsers->GetNext())
        {
            $data[] = $arItem['ID'];
        }
        return $data;
    }

    private function getContactFields( $ID, $TYPE_ID )
    {
        if($ID > 0) {
            $res = [];
            \Bitrix\Main\Loader::IncludeModule('crm');
            $dbResMultiFields = CCrmFieldMulti::GetList(array(), array('ENTITY_ID' => 'COMPANY', 'ELEMENT_ID' => $ID, 'TYPE_ID' => $TYPE_ID));
            while ($arMultiFields = $dbResMultiFields->Fetch()) {
                $res[] = $arMultiFields['VALUE'];
            }
            return $res;
        }
    }

    public function getUsersList ()
    {
        $data = [];
        $filter = Array
        (
            "UF_DEPARTMENT"=> self::UF_DEPARTMENT
        );
        $rsUsers = CUser::GetList(($by="id"), ($order="desc"), $filter);
        while($arItem = $rsUsers->GetNext())
        {
            $data[] = ['id'=> $arItem['ID'], 'name'=> $arItem['LAST_NAME'].' '.$arItem['NAME']];
        }
        return $data;
    }

    public function createCompany($data)
    {
        global $USER;
        $Company = new CCrmCompany;
        $fields = array(
            'TITLE' => $data->TITLE,
            'ASSIGNED_BY_ID' => $data->ASSIGNED_BY_ID,
            'COMMENTS' => $data->COMMENTS,
            'UF_CRM_1533026672' => $data->ADRESS,
            'UF_CRM_5BB36C08352C2' => 890,
            'UF_CRM_1548263039694' => 1491,
            'UF_CRM_1548263423552' => 1494,
            'UF_CRM_1548413441888' => 1560,
            'UF_CREATED_USER' => $USER->GetID(),
            'FM' => array(
                'EMAIL' => array(
                    'n0' => array('VALUE' => $data->EMAIL, 'VALUE_TYPE' => 'WORK'),
                ),
                'PHONE' => array(
                    'n0' => array('VALUE' => $data->PHONE, 'VALUE_TYPE' => 'WORK'),
                ),
            ),
        );
        if($data->ID) {
            self::DeleteDoublePhone($data->ID, "COMPANY");
            $result = $Company->Update($data->ID, $fields);
        } else {
            $result = $Company->Add($fields);
        }

        if($result){
            return $result;
        } else {
            return $Company->LAST_ERROR;
        }
    }

    public function editCompany($id)
    {
        $company = [];
        $arFilter = ['ID'=> $id];
        $arSelect = array('ID', 'TITLE', 'ASSIGNED_BY_ID', 'UF_CRM_1533026672', 'COMMENTS', 'UF_CREATED_USER');
        $db_list = CCrmCompany::GetListEx(Array("ID" => "DESC"), $arFilter, false, false, $arSelect, array());
        while($ar_result = $db_list->GetNext()){
            $company['ID'] = $ar_result['ID'];
            $company['TITLE'] = $ar_result['TITLE'];
            $company['ASSIGNED_BY_ID'] = $ar_result['ASSIGNED_BY_ID'];
            $company['PHONE'] = implode(','.PHP_EOL, self::getContactFields($ar_result['ID'], 'PHONE'));
            $company['EMAIL'] = implode(','.PHP_EOL, self::getContactFields($ar_result['ID'], 'EMAIL'));
            $company['ADRESS'] = $ar_result['UF_CRM_1533026672'];
            $company['COMMENTS'] = HTMLToTxt($ar_result['COMMENTS'], "http://www.bitrix.ru");
        }
        return $company;
    }

    private function DeleteDoublePhone($id, $entity_id)
    {
        $types = array('PHONE', 'EMAIL');
        //TYPE_ID=EMAIL, TYPE_ID=PHONE, TYPE_ID=WEB
        foreach ($types as $type) {
            global $DB;
            $query = 'SELECT * FROM b_crm_field_multi WHERE ENTITY_ID="' . $entity_id . '" AND ELEMENT_ID=' . $id . ' AND TYPE_ID="' . $type . '"';
            $result = $DB->Query($query);
            while ($row = $result->Fetch()) {
                $phone[] = $row;
            }
            if (count($phone) > 1) {
                foreach ($phone as $key => $el) {
                    $query = 'DELETE FROM b_crm_field_multi WHERE ID=' . $el["ID"] . '';
                    $result = $DB->Query($query);
                }
            }
        }
        return $result;
    }
}


?>
