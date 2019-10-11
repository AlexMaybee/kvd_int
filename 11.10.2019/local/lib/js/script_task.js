BX.ready(function(){
    var editButton = BX.findChild(
        BX('bx-component-scope-bitrix_tasks_widget_buttonstask_1'),//...для родителя
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
        //создаем кнопку
        var newButton = BX.create('span', {
            attrs: {
                className: 'webform-small-button webform-small-button-print-task'
            },
            events: {
                click: function() {
                    // Событие при клике на кнопку
                    var printContents = document.getElementById('tasks-iframe-popup-scope').innerHTML;
                    var originalContents = document.body.innerHTML;
                    document.body.innerHTML = printContents;
                    window.print();
                    document.body.innerHTML = originalContents;
                }
            },
            text: 'Печатать'
        });
        //вставляем кнопку
        BX.insertAfter(newButton, editButton);
    }


    //Клик на ЧекЛист в задаче
    $('.js-id-checklist-is-items .js-id-checklist-is-item').click(function(){
        //console.log('click check');
        //console.log(this);
        var href = window.location.href,
            matches, taskId;
        //узнаем id задачи из URL
        if (matches = href.match(/\/task\/view\/([\d]+)\//i)) {
            taskId = matches[1];
        }
        //console.log(taskId);
        //console.log($(this).data("item-value"));


        if(taskId > 0) {
            BX.ajax({
                url: '/local/ajax/update_task_stage.php',
                data: {'id': taskId, 'status_id': $(this).data("item-value")},
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
                    if(data > 0) {
                        var url_task = document.querySelectorAll('a.edit')[0].href + '&SAVE_TASK=Y';
                        document.querySelectorAll('a.edit')[0].href = url_task;
                        document.querySelectorAll('a.edit')[0].click();
                    }
                },
                onfailure: function(){

                }
            });
        }

    });

    // Симулятор кнопки сохранить в задачах
    var urlParams = new URLSearchParams(window.location.search);
    if(urlParams.has('SAVE_TASK')){
        document.querySelectorAll('[data-bx-id="task-edit-submit"')[0].click();
    }

});
//#Печатать задачи

// не наблюдать