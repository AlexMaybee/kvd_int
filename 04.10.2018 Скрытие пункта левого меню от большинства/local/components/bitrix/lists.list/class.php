<?php

function checkPermissionSectionList()
{
    $result = [];
    $arFilter = Array('IBLOCK_ID'=> 31, 'GLOBAL_ACTIVE'=>'Y', 'DEPTH_LEVEL'=> 1);
    $db_list = CIBlockSection::GetList(Array('ID'=>'ASC'), $arFilter, true);
    while($ar_result = $db_list->GetNext())
    {
        $result[] = $ar_result['ID'];
        $rsParentSection = CIBlockSection::GetByID($ar_result['ID']);
        if ($arParentSection = $rsParentSection->GetNext())
        {
            $arFilter = array('IBLOCK_ID' => $arParentSection['IBLOCK_ID'], '>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'], '<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'], '>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']);
            $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter);
            while ($arSect = $rsSect->GetNext())
            {
                $result[] = $arSect['ID'];
            }
        }

    }
    return $result;
}

?>