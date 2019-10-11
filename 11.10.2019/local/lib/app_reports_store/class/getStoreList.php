<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule('highloadblock');
CModule::IncludeModule("crm");
CModule::IncludeModule("tasks");
CModule::IncludeModule("socialnetwork");


class StoreReports
{

    const IBLOCK_ID = 2;
    const IBLOCK_IDStore = 31;

    public function getStoreList ($page, $filter)
    {
        global $USER;

        $arFilterCustom = self::buildFilter($filter);

        if(empty($arFilterCustom['UF_STORE_FROM'])) {
            return ['STORES'=> [], 'MAX_PAGE'=> 0, 'MAX_PAGER'=> 0, 'QUANTITY'=> 0];
        }

        if ($arFilterCustom['UF_STATUS_EL'] != 1414) {
            $id_unset = self::getSoldCars($page, $arFilterCustom);
            //df($id_unset);
            $arFilterCustom['!ID'] = $id_unset;
        }


        if ($arFilterCustom['UF_STATUS_EL'] == 1414 && $arFilterCustom['>=UF_DATE_CREATE'] && $arFilterCustom['<=UF_DATE_CREATE']) {
            $id_unset = self::getSoldCarsFilter($page, $arFilterCustom);

            $arFilterCustom['ID'] = $id_unset;

            if (!empty($id_unset)) {
                unset($arFilterCustom['>=UF_DATE_CREATE']);
                unset($arFilterCustom['<=UF_DATE_CREATE']);
            }
        }
        //df($arFilterCustom);
        $store = [];
        $products = [];

        $limit = 100;
        $page = $page - 1;
        $nav = new \Bitrix\Main\UI\PageNavigation("nav-more-news");
        $nav->allowAllRecords(true)
            ->setPageSize($limit)
            ->initFromUri();

        $pagen = $page * $limit;
        $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(self::IBLOCK_ID)->fetch();
        $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();
        $rsData = $strEntityDataClass::getList(array(
            'select' => array('ID', 'UF_*'),
            'order' => array('ID' => 'DESC'),
            "offset" => $pagen,
            "limit" => $nav->getLimit(),
            'filter' => $arFilterCustom,
            'count_total' => true
        ));
        $i = 1;
        //if( $arFilterCustom['UF_STATUS_EL'] != 1414 ) {
            while ($arItem = $rsData->Fetch()) {
                $symvol = '';
                if ($arItem['UF_STATUS_EL'] != 1414) {
                    $symvol = '-';
                }

                $arItem['KEY'] = $i.' - '.$arItem['ID'];
                $arItem['UF_STATUS_EL_NAME'] = self::getUserFieldValue(238, $arItem['UF_STATUS_EL']);
                $arItem['UF_QUANTITY_CUSTOM'] = $symvol . $arItem['UF_QUANTITY'];
                $arItem['UF_PRODUCT_NAME'] = self::GetProductName($arItem['UF_PRODUCT']);
                $arItem['UF_PRODUCT_PROPERTIES'] = self::getPropertyProducttoReport($arItem['UF_PRODUCT']);
                $arItem['UF_PRODUCT_URL'] = '/crm/product/show/' . $arItem['UF_PRODUCT'] . '/';
                $arItem['UF_HISTORY_PRODUCT_URL'] = '/store_report/details.php?product_id=' . $arItem['UF_PRODUCT'];
                $arItem['UF_STORE_TO_NAME'] = ($arItem['UF_STORE_TO']) ? self::GetStoreName($arItem['UF_STORE_TO']) : ''; //UF_STORE_TO
                $arItem['UF_STORE_FROM_NAME'] = ($arItem['UF_STORE_FROM']) ? self::GetStoreName($arItem['UF_STORE_FROM']) : ''; //UF_STORE_TO
                $arItem['USER_FULL_NAME'] = self::getUserName($arItem['UF_USER']);
                $date_time_entry = $arItem['UF_DATE_CREATE'];
                $arItem['DATE_CREATE_CUSTOM'] = $date_time_entry->toString(new \Bitrix\Main\Context\Culture(array("FORMAT_DATETIME" => "d.m.Y")));
                $store[] = $arItem;
                $quantity[] = $symvol . $arItem['UF_QUANTITY'];
                if ($arItem['UF_STATUS_EL'] == 1414) {
                    $products[] = $arItem['UF_PRODUCT'];
                }
                $i++;
            }
        //}

        if (/*$arFilterCustom['UF_STATUS_EL'] == 1414 || */!$arFilterCustom['UF_STATUS_EL'] || $arFilterCustom['UF_STATUS_EL'] == 1422) {
            $additNewEls = self::getElementtoStoreNew($arFilterCustom['UF_STORE_FROM'], $products, $arFilterCustom);

            foreach ($additNewEls as $additNewEl) {
                $arItem['KEY'] = $i.' - '.$additNewEl['ID'];
                $arItem['UF_STATUS_EL_NAME'] = 'Выпуск нового 1';
                $arItem['UF_QUANTITY_CUSTOM'] = $additNewEl['PROPERTY_99_VALUE'];
                $arItem['UF_PRODUCT_NAME'] = self::GetProductName($additNewEl['PROPERTY_98_VALUE']);
                $arItem['UF_PRODUCT_PROPERTIES'] = self::getPropertyProducttoReport($additNewEl['PROPERTY_98_VALUE']);
                $arItem['UF_PRODUCT_URL'] = '/crm/product/show/' . $additNewEl['PROPERTY_98_VALUE'] . '/';
                $arItem['UF_HISTORY_PRODUCT_URL'] = '/store_report/details.php?product_id=' . $additNewEl['PROPERTY_98_VALUE'];
                $arItem['UF_STORE_TO_NAME'] = '-'; //UF_STORE_TO
                $arItem['UF_STORE_FROM_NAME'] = ($additNewEl['IBLOCK_SECTION_ID']) ? self::GetStoreName($additNewEl['IBLOCK_SECTION_ID']) : '-'; //UF_STORE_TO
                $arItem['USER_FULL_NAME'] = $additNewEl['CREATED_USER_NAME'];
                $arItem['DATE_CREATE_CUSTOM'] = $additNewEl['PROPERTY_126_VALUE'];
                $store[] = $arItem;
                $quantity[] = $additNewEl['UF_QUANTITY'];
                $i++;
            }
        }

        if(!empty($arFilterCustom['UF_STORE_FROM'])) {
            $quantity_count = CIBlockSection::GetSectionElementsCount($arFilterCustom['UF_STORE_FROM'][0], Array());
        } else {
            $quantity_count = CIBlockElement::GetList(array(), array('IBLOCK_ID' => 31), array(),false, array('ID', 'NAME'));
        }

        if($arFilterCustom['UF_STATUS_EL'] && $arFilterCustom['UF_STATUS_EL'] != 1422 && $arFilterCustom['UF_STATUS_EL'] != 1414) {
            $quantity_count = self::getStoreListQuantity($arFilterCustom);
        }

        $count =  $rsData->getCount();

        if ($arFilterCustom['UF_STATUS_EL'] == 1414) {
            $quantity_count = $count;
        }
        return ['STORES'=> $store, 'MAX_PAGE'=> $count, 'MAX_PAGER'=> ceil($count / $limit), 'QUANTITY'=> $quantity_count];

    }

    private function getSoldCars($page, $arFilterCustom)
    {
        $store = [];
        $products = [];
        $products_unset_m = [];
        $products_custom = [];
        $products_unset = [];

        $limit = 100;
        $page = $page - 1;
        $nav = new \Bitrix\Main\UI\PageNavigation("nav-more-news");
        $nav->allowAllRecords(true)
            ->setPageSize($limit)
            ->initFromUri();

        $pagen = $page * $limit;
        $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(self::IBLOCK_ID)->fetch();
        $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();
        $rsData = $strEntityDataClass::getList(array(
            'select' => array('ID', 'UF_*'),
            'order' => array('ID' => 'DESC'),
            "offset" => $pagen,
            "limit" => $nav->getLimit(),
            'filter' => $arFilterCustom,
            'count_total' => true
        ));
        $i = 1;
        while ($arItem = $rsData->Fetch()) {
            if($arItem['UF_STATUS_EL'] == 1416 || $arItem['UF_STATUS_EL'] == 1420) {
                $products[] = $arItem['UF_PRODUCT'];
            } elseif ($arItem['UF_STATUS_EL'] == 1414) {
                $products_unset_m[] = ['ID'=> $arItem['ID'], 'UF_PRODUCT'=> $arItem['UF_PRODUCT']];
            }
        }

        //df($products);

        if(!empty($products_unset_m)) {
            foreach ($products_unset_m as $prod) {
                if(in_array($prod['UF_PRODUCT'], $products)) {
                    $products_unset[] = $prod['ID'];
                }
            }
        }
//        while ($arItem = $rsData->Fetch()) {
//            df($arItem);
//            if($arItem['UF_STATUS_EL'] == 1414 && in_array($arItem['UF_PRODUCT'], $products)) {
//                $products_unset[] = $arItem['ID'];
//            }
//        }
        return $products_unset;
    }


    private function getSoldCarsFilter($page, $arFilterCustom)
    {
        //df($arFilterCustom);
        $store = [];
        $products = [];
        $products_unset_m = [];
        $products_custom = [];
        $products_unset = [];

        $arFilterCustom_1 = $arFilterCustom;
        $limit = 100;
        $page = $page - 1;
        $nav = new \Bitrix\Main\UI\PageNavigation("nav-more-news");
        $nav->allowAllRecords(true)
            ->setPageSize($limit)
            ->initFromUri();

        unset($arFilterCustom['<=UF_DATE_CREATE']);
        unset($arFilterCustom['>=UF_DATE_CREATE']);

        $pagen = $page * $limit;
        $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(self::IBLOCK_ID)->fetch();
        $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();
        $rsData = $strEntityDataClass::getList(array(
            'select' => array('ID', 'UF_*'),
            'order' => array('ID' => 'DESC'),
            "offset" => $pagen,
            "limit" => $nav->getLimit(),
            'filter' => $arFilterCustom,
            'count_total' => true
        ));
        $i = 1;
        while ($arItem = $rsData->Fetch()) {
            $products[] = $arItem['UF_PRODUCT'];
            $products_unset_m[] = ['ID'=> $arItem['ID'], 'UF_PRODUCT'=> $arItem['UF_PRODUCT']];
        }


        $products_custom = self::filterProductsEl($products_unset_m, $arFilterCustom_1);
        
        //df($products_custom);
        //df($products);
        if(!empty($products_custom)) {
            foreach ($products_custom as $prod) {
                if(in_array($prod['UF_PRODUCT'], $products)) {
                    $products_unset[] = $prod['ID'];
                }
            }
        }
//        while ($arItem = $rsData->Fetch()) {
//            df($arItem);
//            if($arItem['UF_STATUS_EL'] == 1414 && in_array($arItem['UF_PRODUCT'], $products)) {
//                $products_unset[] = $arItem['ID'];
//            }
//        }

        //df($products_unset);
        return $products_unset;
    }

    private function filterProductsEl($products_unset_m, $arFilterCustom)
    {
        $result = [];
        foreach ($products_unset_m as $id) {
            $arSelect = Array('ID', 'PROPERTY_150');
            $arFilter = Array('IBLOCK_ID' => 27, 'ID' => $id['UF_PRODUCT'],
                array(
                    "LOGIC" => "AND",
                    '<=PROPERTY_150' => trim(CDatabase::CharToDateFunction($arFilterCustom['<=UF_DATE_CREATE']), "\'"),
                    '>=PROPERTY_150' => trim(CDatabase::CharToDateFunction($arFilterCustom['>=UF_DATE_CREATE']), "\'")
                )
            );
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                if ($arFields) {
                    $result[] = $id;
                }
            }
        }
        return $result;
    }


    public function getStoreListQuantity ($arFilterCustom)
    {
        global $USER;

        $products = [];
        $limit = 20000;
        $nav = new \Bitrix\Main\UI\PageNavigation("nav-more-news");
        $nav->allowAllRecords(true)
            ->setPageSize($limit)
            ->initFromUri();

        $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(self::IBLOCK_ID)->fetch();
        $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();
        $rsData = $strEntityDataClass::getList(array(
            'select' => array('ID', 'UF_*'),
            'order' => array('ID' => 'DESC'),
            "limit" => $limit,
            'filter' => $arFilterCustom,
        ));
        $i = 1;
        while($arItem = $rsData->Fetch()) {
            $symvol = '';
            if($arItem['UF_STATUS_EL'] != 1414) {
                $symvol = '-';
            }
            $quantity[] = $symvol.$arItem['UF_QUANTITY'];
            $products[] = $arItem['UF_PRODUCT'];
        }

        if ($arFilterCustom['UF_STATUS_EL'] == 1414 || !$arFilterCustom['UF_STATUS_EL']) {
            $additNewEls = self::getElementtoStoreNew($arFilterCustom['UF_STORE_FROM'], $products, $arFilterCustom);

            foreach ($additNewEls as $additNewEl) {
                $quantity[] = $additNewEl['PROPERTY_99_VALUE'];
            }
        }

        return array_sum($quantity);
    }


    private function buildFilter($filter)
    {
        global $USER;
        $arFilter = [];

        $arFilter['!UF_STATUS_EL'] = 1415;
        if($filter->STATUS)
        {
            $arFilter['UF_STATUS_EL'] = $filter->STATUS;
        }

        if(!empty($filter->SECTIONS))
        {
            $arFilter['UF_STORE_FROM'] = $filter->SECTIONS;
        }

        if(!empty($filter->USERS))
        {
            $arFilter['UF_USER'] = $filter->USERS;
        }

        if(!empty($filter->UF_PRODUCT))
        {
            $arFilter['UF_PRODUCT'] = $filter->UF_PRODUCT;
        }

        if($filter->FROM_DATE)
        {
            $arFilter['>=UF_DATE_CREATE'] = date("d.m.Y", strtotime($filter->FROM_DATE)).' 00:00:01';
        }

        if($filter->TO_DATE)
        {
            $arFilter['<=UF_DATE_CREATE'] = date("d.m.Y", strtotime($filter->TO_DATE)).' 23:59:59';
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

    private function GetProductName($id)
    {
        $res = CIBlockElement::GetByID($id);
        if($ar_res = $res->GetNext())
            return $ar_res['NAME'];
    }

    private function GetStoreName($id)
    {
        $res = CIBlockSection::GetByID($id);
        if($ar_res = $res->GetNext())
            return $ar_res['NAME'];
    }

    private function getPropertyProducttoReport($id)
    {
        $arSelect = Array("ID", "PROPERTY_104", "PROPERTY_105", "PROPERTY_100", "PROPERTY_109", "PROPERTY_150");
        $arFilter = Array("IBLOCK_ID"=> 27, 'ID'=> $id);
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        if ($ob = $res->GetNextElement())
            $arFields = $ob->GetFields();
            $arFields['PROPERTY_104_VALUE'] = ($arFields['PROPERTY_104_VALUE']) ? $arFields['PROPERTY_104_VALUE'] : '-';
            return $arFields;
    }

    private function getUserFieldValue($USER_FIELD_ID, $ID)
    {
        if($ID > 0) {
            global $USER_FIELD_MANAGER;
            $obEnum = new CUserFieldEnum;
            $rsEnum = $obEnum->GetList(array(), array('USER_FIELD_ID'=> $USER_FIELD_ID, 'ID'=> $ID));
            if($arEnum = $rsEnum->GetNext())
                return $arEnum['VALUE'];
        }
    }

    public function getStatusList()
    {
        $result = [];
        global $USER_FIELD_MANAGER;
        $obEnum = new CUserFieldEnum;
        $rsEnum = $obEnum->GetList(array(), array('USER_FIELD_ID'=> 238));
        while($arEnum = $rsEnum->GetNext()) {
            if($arEnum['ID'] != 1415){
                $result[] = ['id' => $arEnum['ID'], 'name' => $arEnum['VALUE']];
            }
        }

            return $result;
    }

    public function getStoreListSection()
    {
        $res_name = [];
        $res = CIBlockSection::GetList(
            Array('name' => 'asc'),
            Array('IBLOCK_ID' => self::IBLOCK_IDStore , 'ACTIVE' => 'Y', 'DEPTH_LEVEL'=> 1)
        );
        while ($row = $res->GetNext())
        {
            $res_name[] = ['id'=> $row['ID'], 'name'=> $row['NAME']];
            $rsParentSection = CIBlockSection::GetByID($row['ID']);
            if ($arParentSection = $rsParentSection->GetNext())
            {
                $arFilter = array('IBLOCK_ID' => $arParentSection['IBLOCK_ID'],'>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'],'<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'],'>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']); // выберет потомков без учета активности
                $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter);
                while ($arSect = $rsSect->GetNext())
                {
                    $res_name[] = ['id'=> $arSect['ID'], 'name'=> '---'.$arSect['NAME']];
                }
            }
        }
        return $res_name;
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

    private function getElementtoStoreNew($section_id, $products, $arFilterCustom)
    {
        $result = [];
        $products_unset = [];
        $arSelect = Array('ID', 'NAME', 'IBLOCK_ID', 'PROPERTY_98', 'PROPERTY_99', 'PROPERTY_126', 'CREATED_USER_NAME', 'IBLOCK_SECTION_ID');
        $arFilter = Array('IBLOCK_ID'=> 31, 'IBLOCK_SECTION_ID'=> $section_id, "ACTIVE"=>"Y", '!PROPERTY_98'=> $products);
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $products[] = $arFields['PROPERTY_98_VALUE'];
            $result[] = $arFields;
        }

        if($arFilterCustom['UF_STATUS_EL'] != 1414) {
            return $result;
        }

        $products_custom = self::filterProductsEl_Product($products, $arFilterCustom);

        if(!empty($products_custom)) {
            foreach ($result as $prod) {
                if (in_array($prod['PROPERTY_98_VALUE'], $products_custom)) {
                    $products_unset[] = $prod;
                }
            }
        }

        return $products_unset;
    }

    private function filterProductsEl_Product($products, $arFilterCustom)
    {
        $result = [];
            $arSelect = Array('ID', 'PROPERTY_150');
            $arFilter = Array('IBLOCK_ID' => 27, 'ID' => $products,
                array(
                    "LOGIC" => "AND",
                    '<=PROPERTY_150' => trim(CDatabase::CharToDateFunction($arFilterCustom['<=UF_DATE_CREATE']), "\'"),
                    '>=PROPERTY_150' => trim(CDatabase::CharToDateFunction($arFilterCustom['>=UF_DATE_CREATE']), "\'")
                )
            );
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                if ($arFields) {
                    $result[] = $arFields['ID'];
                }
            }
        return $result;
    }
}


?>
