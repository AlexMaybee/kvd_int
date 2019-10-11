BX.ready(function() {
    $(document).on("DOMNodeInserted", function (event) {
        var click_edit = document.querySelectorAll('div#toolbar_deal_list div.ui-btn-split');
        if (click_edit.length > 0) {
            for (var i = 0; i < click_edit.length; i++) {
                click_edit[i].remove();
            }
        }
    })

})