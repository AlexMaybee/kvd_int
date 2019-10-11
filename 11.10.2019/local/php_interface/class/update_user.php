<?
AddEventHandler("main", "OnAfterUserLogin", Array("MyClass", "OnAfterUserLoginHandler"));
class MyClass
{
    // создаем обработчик события "OnAfterUserLogin"
    function OnAfterUserLoginHandler(&$fields)
    {
        CModule::IncludeModule("im");
        $chatId = 1;
        // если логин не успешен то
        if($fields['USER_ID'] > 0)
        {
            $CIMChat = new CIMChat();
            if ( CSite::InGroup( array(23) ) ){
                $CIMChat->MuteNotify($chatId, 'Y' == 'Y');
            } else {
                $CIMChat->MuteNotify($chatId, 'N' == 'Y');
            }
        }
    }
}


AddEventHandler("main", "OnEndBufferContent", "MyOnBeforePrologHandler");
function MyOnBeforePrologHandler(&$content)
{
    global $USER;
    if ( in_array(23, $USER->GetUserGroupArray()) == 1 ) {
        $content = str_replace('style_chat_copy', 'style_chat', $content);
    }

    //скрытие Mongo office в левом меню
    if(chechIfIsDirector() == true || userIsAdmin() == true || checkUserIfPiterDepartments() == true){
        $content = str_replace('hideMenuPunct', 'hideMenuPunctcCopy', $content);
    }

    //Скрыть кнопку изменить ответственного
    if(!in_array(27, $USER->GetUserGroupArray()) == 1){
        $content = str_replace('script_task_responsible_copy', 'script_task_responsible', $content);
    }

    //Скрыть кнопку Добавить сделку
    if(!in_array(1, $USER->GetUserGroupArray()) == 1) {
        $content = str_replace('script_deal_custom_copy', 'script_deal_custom', $content);
    $content = str_replace('style_deal_copy', 'style_deal', $content);
    }

    //Скрыть чат слева
    if(in_array(15, $USER->GetUserGroupArray()) == 1){
        $content = str_replace('<div class="bx-im-users-wrap">', '<div class="bx-im-users-wrap" style="display:none">', $content);
        $content = str_replace('<div class="bx-im-informer bx-im-border-b">', '<div class="bx-im-informer bx-im-border-b" style="display:none">', $content);
        $content = str_replace('<div id="bx-im-bar-search"', '<div id="bx-im-bar-search" style="display:none"', $content);
    }

    global $USER, $APPLICATION;
    if (in_array(29, $USER->GetUserGroupArray()) == 1) {
        $content = str_replace('<div id="bx-panel-top">', '<div id="bx-panel-top" style="display:none!important">', $content);
        if (strstr($APPLICATION->GetCurPage(), '/bitrix/')) {
            $content = str_replace('<table class="adm-main-wrap">', 'Доступ запрещен<table class="adm-main-wrap" style="display:none">', $content);
        }
    }
}
?>
