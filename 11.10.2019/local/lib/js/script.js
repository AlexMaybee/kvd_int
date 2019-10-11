//Переключение языков
function action_lang()
{
    window.location = '?lang_ui=' + document.getElementsByName('Lang')[0].value;
}

$(document).ready(function() {


    var div_select = document.createElement('div');
    div_select.className = 'lang_left';

    $('.timeman-right-side').hide();

    $('#timeman-block').addClass('disabled');

    div_select.innerHTML =
        '<select name="Lang" class="typeselect" onchange="action_lang()" style="float: right;position: absolute; right: 0;top: 0;">\n' +
        '<option value="ru">Русский</option>\n' +
        '<option value="en">English</option>\n' +
        //'<option value="ua">Українська</option>\n' +
        '</select>';

    //$('.header-search-inner').appendChild(div_select);
    if(document.querySelectorAll(".header-search-inner").length > 0) {
        document.querySelectorAll(".header-search-inner")[0].appendChild(div_select);
        //console.log(document.querySelectorAll(".header-search-inner"));
        setSelected();
    }

})

/*Получаем "?lang_ui= "*/

function setSelected(  ) {
    console.log(BX.message.LANGUAGE_ID);
    $('.typeselect').val(BX.message.LANGUAGE_ID);

}

BX.ready(function() {
    $(document).on("DOMNodeInserted", function (event) {
    var inp_quan = document.querySelectorAll('td.crm-item-qua input');
    if(inp_quan.length > 0) {
        for(var i = 0; i < inp_quan.length; i++) {
            inp_quan[i].disabled = true;
        }
    }

    var select_store_from = document.querySelectorAll('select[name$="UF_CRM_1541664033"]');
    if(select_store_from.length > 0) {
        for(var i = 0; i < select_store_from.length; i++) {
            select_store_from[i].disabled = true;
        }
    }

    var select_store_transit = document.querySelectorAll('select[name$="UF_CRM_1541664081"]');
    if(select_store_transit.length > 0) {
        for(var i = 0; i < select_store_transit.length; i++) {
            select_store_transit[i].disabled = true;
            select_store_transit[i].value = 260;
        }
    }

    })

    $(document).on("DOMNodeInserted", function (event) {

        var prop = document.querySelectorAll('.menu-popup-item.menu-popup-no-icon');
        if ( prop.length > 0) {
            for (var i = 0; i < prop.length; i++) {
                if( prop[i].innerText == 'Панель управления' ) {
                    prop[i].remove();
                }
            }
        }

    });
})

