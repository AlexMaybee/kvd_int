BX.ready(function(){

    getCountEvents();
    function getCountEvents() {
        var html = '';
        BX.ajax({
            url: '/local/ajax/task_employee.php',
            data: {'action': 'CCalendarEventCount'},
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            processData: true,
            scriptsRunFirst: true,
            emulateOnload: true,
            start: true,
            cache: false,
            onsuccess: function(data) {
                var html = '<span class="menu-item-with-index"' +
                    '<span class="menu-item-index-wrap">' +
                    '<span class="menu-item-index" style="background: #f54819;color: #FFF;">' +
                    data +
                    '</span>' +
                    '</span>' +
                    '</span>';
                if(data > 0) {
                    document.querySelector('#bx_left_menu_menu_event_sect a.menu-item-link').insertAdjacentHTML("beforeend", html);
                }
                if(document.querySelector('#bx_left_menu_menu_event_sect a.menu-item-link')) {
                    document.querySelector('#bx_left_menu_menu_event_sect a.menu-item-link').setAttribute('href', '#');
                }

            },
            onfailure: function(){

            }
        });
    }


    //Клик на Меню Встречи
    $('#bx_left_menu_menu_event_sect').click(function(){
        var html = '';
        BX.ajax({
            url: '/local/ajax/task_employee.php',
            data: {'action': 'CCalendarEvent'},
            method: 'POST',
            dataType: 'json',
            timeout: 30,
            async: true,
            processData: true,
            scriptsRunFirst: true,
            emulateOnload: true,
            start: true,
            cache: false,
            onsuccess: function(data) {
                if(data.length > 0) {
                    html += '<table cellspacing="0" class="bx-interface-grid" id="fields_list">' +
                        '<tbody>';

                    html += '<tr class="bx-grid-head">' +
                        '<th width="40%">Название</th>' +
                        '<th width="40%">Локация</th>' +
                        '<th width="20%">Время</th>' +
                        '</tr>';
                    for(var i = 0; i < data.length; i++) {
                        html += '<tr class="bx-odd bx-top">' +
                            '<td><a target="_blank" href="' + data[i].HREF + '">' + data[i].NAME + '</a></td>' +
                            '<td>' + data[i].LOCATION + '</td>' +
                            '<td>' + data[i].DATE_FROM + '</td>' +
                            '</tr>';
                    }
                    html += '</tbody>' +
                        '</table>';
                }
                popup_user(html);
            },
            onfailure: function(){

            }
        });

    });

    function popup_user(html) {
        var curapp = this;

        var popup_user = BX.PopupWindowManager.create("popup-message", BX('element'), {
            content: html,
            width: 600, // ширина окна
            height: '100%', // высота окна
            zIndex: 100, // z-index
            closeIcon: {
                // объект со стилями для иконки закрытия, при null - иконки не будет
                opacity: 1
            },
            titleBar: 'Встречи',
            closeByEsc: true, // закрытие окна по esc
            darkMode: false, // окно будет светлым или темным
            autoHide: false, // закрытие при клике вне окна
            draggable: true, // можно двигать или нет
            resizable: true, // можно ресайзить
            min_height: 100, // минимальная высота окна
            min_width: 100, // минимальная ширина окна
            lightShadow: true, // использовать светлую тень у окна
            angle: true, // появится уголок
            overlay: {
                // объект со стилями фона
                backgroundColor: 'black',
                opacity: 500
            },
            buttons: [

            ],
            events: {
                onPopupShow: function() {
                    // Событие при показе окна
                },
                onPopupClose: function(PopupWindow) {
                    // Событие при закрытии окна
                    PopupWindow.destroy();
                }
            }
        });
        popup_user.show();
    }


});
//#Печатать задачи

// не наблюдать
