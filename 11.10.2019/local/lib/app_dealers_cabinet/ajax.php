<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
header('Content-type: application/json');

require_once ($_SERVER["DOCUMENT_ROOT"] . "/local/lib/app_dealers_cabinet/class/getElementList.php");


$data = json_decode(file_get_contents("php://input"));
$array = [];
$CompanyList = new CompanyList;
if($data[0]->action) {
    switch ($data[0]->action) {
        case 'getCompanyList':
            $array = $CompanyList->getCompanyList($data[0]->page, $data[0]->filter);
            break;
        case 'getUsersList':
            $array = $CompanyList->getUsersList();
            break;
        case 'createCompany':
            $array = $CompanyList->createCompany($data[0]->data);
            break;
        case 'editCompany':
            $array = $CompanyList->editCompany($data[0]->id);
            break;
    }
}


echo json_encode($array);
?>