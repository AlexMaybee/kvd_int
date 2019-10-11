<?php
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
}



?>