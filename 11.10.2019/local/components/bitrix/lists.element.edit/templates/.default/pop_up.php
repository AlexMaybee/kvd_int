<?
CJSCore::Init(array('window'));


$category_id = getTreeList(31);

$category_id_select = '';
foreach ($category_id as $cat) {
    $category_id_select .= '<option value="'.$cat['ID'].'">'.$cat['NAME'].'</option>';
}





$arDialogParams = array(
    'title' => GetMessage("TRANSIT_STORE"),
    'content' => htmlspecialchars(
            '<form method="POST" style="overflow:hidden;" action="" id="SpecPriceForm2" enctype="multipart/form-data">
    <div class="crm-entity-widget-content-block crm-entity-widget-content-block-field-custom-select" data-cid="store_transit">
    <div class="crm-entity-widget-content-block-title">
    <span class="crm-entity-widget-content-block-title-text">'.GetMessage("TRANSIT_STORE").'<span class="mustHave">*</span>
    </span>
    </div>
    <div class="crm-entity-widget-content-block-inner">
    <span class="fields enumeration field-wrap">
    <span class="fields enumeration enumeration-select field-item">
    <select name="store_transit">
    <option value="260" selected>'.GetMessage("TRANSIT_STORE").'</option>
	</select></span></span></div>
	</div>
	
	<div class="crm-entity-widget-content-block crm-entity-widget-content-block-field-custom-select" data-cid="store_to">
    <div class="crm-entity-widget-content-block-title">
    <span class="crm-entity-widget-content-block-title-text">'.GetMessage('TRANSIT_MOVE').'<span class="mustHave">*</span>
    </span>
    </div>
    <div class="crm-entity-widget-content-block-inner">
    <span class="fields enumeration field-wrap">
    <span class="fields enumeration enumeration-select field-item">
    <select name="store_to">
    <option value="">'.GetMessage("CT_BLEE_NO_VALUE").'</option>
    '.$category_id_select.'
	</select></span></span></div>
	</div>
				
	</form>'),
    'width' => 500,
    'height' => 200,
    'resizable' => true,
	'draggable' => true,
    'closeByEsc' => true,
    'buttons' => array(
        array(
            "title" => GetMessage('CT_BLEE_BIZPROC_SAVE_BUTTON'),
            "name" => "save_transit",
            "id" => "save_transit",
            "action" => "[code]function(){saveTransit();}[code]", // Кастомная кнопка
        ),
        '[code]BX.CDialog.prototype.btnClose[code]'
    ),
);

// преобразование в объект и замена кавычек
$strParams = CUtil::PhpToJsObject($arDialogParams);
$strParams = str_replace('\'[code]', '', $strParams);
$strParams = str_replace('[code]\'', '', $strParams);

// ссылка для открытия окна
$url = 'javascript:(new BX.CDialog('.$strParams.')).Show()';





function getTreeList($iblock)
{
    $res_name = [];
    $res = CIBlockSection::GetList(
        Array('name' => 'asc'),
        Array('IBLOCK_ID' => $iblock , 'ACTIVE' => 'Y', 'DEPTH_LEVEL'=> 1, '!ID'=> 260, 'CHECK_PERMISSIONS'=> 'N')
    );
    while ($row = $res->GetNext())
    {
        $res_name[] = ['ID'=> $row['ID'], 'NAME'=> $row['NAME']];
        //$rsParentSection = CIBlockSection::GetByID($row['ID']);

        //if ($arParentSection = $rsParentSection->GetNext())
        //{
            //df($arParentSection);
            $arFilter = array('IBLOCK_ID' => $iblock, 'CHECK_PERMISSIONS'=> 'N', '>DEPTH_LEVEL' => 1, 'SECTION_ID'=> $row['ID']);
            $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'), $arFilter);
            while ($arSect = $rsSect->GetNext())
            {
                $res_name[] = ['ID'=> $arSect['ID'], 'NAME'=> '---'.$arSect['NAME']];
            }
        //}
    }
    return $res_name;
}
?>



<?
/*
 * <a href="<?=$url?>">Транзит</a>

if($arResult['DEAL_ID_LINK']){?>
    <a target="_top" href="<?=$arResult['DEAL_ID_LINK']?>">Сделка</a>
<?}
 */
?>
<table cellspacing="0" cellpadding="0" border="0">
    <tbody>
    <tr>
        <td class="crm-list-end-deal-buttons-block">
            <?if($arResult['DEAL_ID_LINK']):?>
            <a class="webform-small-button webform-small-button-blue" target="_top" href="<?=$arResult['DEAL_ID_LINK']?>"><span class="webform-small-button-left"></span><span class="webform-small-button-text"><?=GetMessage('VIEW_BUTTON')?></span><span class="webform-small-button-right"></span></a>
            <?else:?>
            <a class="webform-small-button webform-small-button-accept" href="<?=$url?>"><span class="webform-small-button-left"></span><span class="webform-small-button-text"><?=GetMessage("TRANSIT_BUTTON");?></span><span class="webform-small-button-right"></span></a>
            <?endif;?>
        </td>

    </tr>
    </tbody>
</table>
<style>
.crm-entity-widget-content-block {
    margin-bottom: 20px;
}
</style>