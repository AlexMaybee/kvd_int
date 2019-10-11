<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!empty($arResult["ELEMENTS_ROWS"]))
{
	foreach ($arResult["ELEMENTS_ROWS"] as $key => &$row)
	{
		if (!empty($row["actions"]))
		{
			foreach($row["actions"] as &$action)
			{
				if ($action["ID"] == "delete")
				{
					$action["ONCLICK"] = "javascript:BX.Lists['".$arResult['JS_OBJECT']."'].deleteElement('".
						$arResult["GRID_ID"]."', '".$row["id"]."')";
				}
			}
		}
	}
}

$arResult["ELEMENTS_HEADERS_CUSTOM"] = [];
if(!empty($arResult["ELEMENTS_HEADERS"])) {
    foreach ($arResult["ELEMENTS_HEADERS"] as $ar) {
        $name_f = explode('|', $ar['name']);
        if (LANGUAGE_ID == 'en'){
            $ar['name'] = ($name_f[1]) ? trim($name_f[1]) : trim($name_f[0]);
        } else {
            $ar['name'] = trim($name_f[0]);
        }
        $arResult["ELEMENTS_HEADERS_CUSTOM"][] = $ar;
    }
}
