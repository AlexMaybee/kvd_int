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
    $('.typeselect').val(BX.message.LANGUAGE_ID);

}


//#Переключение языков


//Печатать задачи
//код исполняем, только когда DOM загружен
/*BX.ready(function(){

    BX.addCustomEvent('onPopupFirstShow', function(p) {
        var menuId = 'task-view-b';
        if (p.uniquePopupId === 'menu-popup-' + menuId)
        {
            var menu = BX.PopupMenu.getMenuById(menuId);

            menu.addMenuItem({
                text: 'Печатать',
                className: 'menu-popup-item-create',
                onclick: function()
                {
                    var printContents = document.getElementById('tasks-iframe-popup-scope').innerHTML;
                    var originalContents = document.body.innerHTML;
                    document.body.innerHTML = printContents;
                    window.print();
                    document.body.innerHTML = originalContents;
                }.bind(this)
            });
        }
    });


});*/
