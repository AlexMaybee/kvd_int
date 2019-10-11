BX.ready(function() {
    $(document).on("DOMNodeInserted", function (event) {

        var click_edit = document.querySelectorAll('div#bx-component-scope-bitrix_tasks_widget_member_selectorview_3 span.task-user-selector-change');
        if (click_edit.length > 0) {
            for (var i = 0; i < click_edit.length; i++) {
                click_edit[i].remove();
            }
        }


        var href = window.location.href, matches, taskId;
        //узнаем id задачи из URL
        if (matches = href.match(/\/task\/edit\/([\d]+)\//i)) {
            taskId = matches[1];
        }
        if(taskId > 0){
            var disamled_change_respons = document.querySelectorAll('div#bx-component-scope-bitrix_tasks_task_default_1-responsible');
            if (disamled_change_respons.length > 0) {
                for (var i = 0; i < disamled_change_respons.length; i++) {
                    disamled_change_respons[i].style = 'pointer-events: none; opacity: 0.4;';
                }
            }
        }


    })

})