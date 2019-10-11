<?php
CModule::IncludeModule("CRM");

//Событие - после создания сделки (когда уже есть ID), если CATEGORY_ID = 4 - Карьера в Sherp
//Получаем из лида его ACTIVITIES и вставляем в сделку

AddEventHandler('crm', 'OnAfterCrmDealAdd', ['CopyLeadActivitiesToDeal', 'main']);

class CopyLeadActivitiesToDeal{

    const CATEGORY_4 = 4;
    const ENTITY_TYPE_LEAD = 1;

    public function main(&$arFields){

        if(isset($arFields['LEAD_ID']) && $arFields['LEAD_ID'] > 0 && $arFields['CATEGORY_ID'] == self::CATEGORY_4){

            $activitiesIds = self::getActivitiesByFilter(['OWNER_ID' => $arFields['LEAD_ID'],'OWNER_TYPE_ID' => self::ENTITY_TYPE_LEAD],
                ['ID']);

            if($activitiesIds){
                foreach ($activitiesIds as $activityId){

                    $copyResult[$activityId] = self::copyActivityToDeal($activityId,$arFields['ID']);
                }
            }
//            self::logData([$copyResult,$activitiesIds,$arFields]);
        }
    }

    private function getActivitiesByFilter($filter,$select){
        $activityIds = [];
        $activeMassive = CCrmActivity::GetList(['ID' => 'DESC'],$filter,false,false, $select,[]);
        while($obj = $activeMassive->GetNext())
            $activityIds[] = $obj['ID'];
        return $activityIds;
    }

    //таким образом копируем собітия из лида в сделку при конвертации
    private function copyActivityToDeal($activityId,$dealId){
        $data = CCrmActivity::GetByID($activityId);
        unset($data["ID"]);
        $data["OWNER_ID"] = $dealId;
        $data["OWNER_TYPE_ID"] = self::ENTITY_TYPE_LEAD;
        $result = CCrmActivity::Add($data);
        return $result;
    }

    private function logData($data){
        $file = $_SERVER["DOCUMENT_ROOT"].'/onAfterDealCreate.log';
        file_put_contents($file, print_r([date('d.m.Y H:i:s'),$data],true), FILE_APPEND | LOCK_EX);
    }

}
