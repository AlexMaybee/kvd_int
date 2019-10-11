BX.ready(function(){

    var editButton = BX.findChild(
        BX('bx-component-scope-bitrix_tasks_widget_buttonstask_1'),
        {//с такими вот свойствами
            tag: 'a',
            className: 'task-view-button edit'
        },
        true//поиск рекурсивно от родителя
    );
    if (editButton)
    {

        var href = window.location.href, matches, taskId;
        //узнаем id задачи из URL
        if (matches = href.match(/\/task\/view\/([\d]+)\//i)) {
            taskId = matches[1];
        }


        if(taskId > 0) {
            BX.ajax({
                url: '/local/ajax/task_employee.php',
                data: {'id': taskId, 'action': 'GetByIDTask'},
                method: 'POST',
                dataType: 'json',
                timeout: 30,
                async: true,
                processData: true,
                scriptsRunFirst: true,
                emulateOnload: true,
                start: true,
                cache: false,
                onsuccess: function(data){
                    console.log(data);
                    if(data) {
                        //создаем кнопку
                        var newButton = BX.create('span', {
                            attrs: {
                                className: 'webform-small-button webform-small-button-exit_aud-task'
                            },
                            events: {
                                click: function() {
                                    // Событие при клике на кнопку
                                    updateTaskAuditors(taskId);
                                }
                            },
                            text: 'Не наблюдать'
                        });
                        //вставляем кнопку
                        BX.insertAfter(newButton, editButton);
                    }
                },
                onfailure: function(){

                }
            });
        }

    }


    function updateTaskAuditors(id) {
        BX.ajax({
            url: '/local/ajax/task_employee.php',
            data: {'id': id, 'action': 'updateTask'},
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
                if(data > 0) {
                    var div_block = document.querySelectorAll("#bx-component-scope-auditor-selector [data-item-value='" + data + "']");
                    if (div_block.length > 0) {
                        div_block[0].remove();
                        $('.webform-small-button-exit_aud-task').remove();
                    }
                }
            },
            onfailure: function(){

            }
        });
    }


});
//#Печатать задачи

// не наблюдать