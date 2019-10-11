
//код исполняем, только когда DOM загружен
BX.ready(function() {


    // Отчеты, автоматическое заполнение
    $(document).on("DOMNodeInserted", function (event) {


        // Карточка сделки Страна (Country)
        var field_deal = document.querySelectorAll('div[data-cid="UF_CRM_5BB36C07CECDE"] select');
        var cust_field_deal = document.querySelectorAll('div[data-cid="UF_CRM_5BB36C07CECDE"] select.js-example-basic-single');

        if(field_deal.length > 0 && cust_field_deal.length == 0) {
            field_deal[0].classList.add('js-example-basic-single');
            $('.js-example-basic-single').select2();

            $('.js-example-basic-single').on('select2:select', function (e) {
                BX.fireEvent(
                    $('select[name$="UF_CRM_5BB36C07CECDE"]')[0],
                    "change"
                );
            });
        }
        // Карточка сделки Страна (Country)


        // Фильтр Страна (Country)
        var field_deal_filter = document.querySelectorAll('div.main-ui-filter-field-container-list div.main-ui-control-field div[data-name="UF_CRM_5BB36C07CECDE"]');
        var field_deal_filter_custom = document.querySelectorAll('div.main-ui-filter-field-container-list div[data-name="UF_CRM_5BB36C07CECDE"]');
        var cust_field_deal_filter = document.querySelectorAll('div.main-ui-filter-field-container-list div.main-ui-control-field select');

        if(field_deal_filter.length > 0 && cust_field_deal_filter.length == 0) {
            var json_data = field_deal_filter[0].getAttribute("data-items");
            var json_data_1 = JSON.parse(json_data);
            ///console.log(JSON.parse(json_data));


            if(json_data_1.length > 0) {
                //Create array of options to be added
                field_deal_filter[0].style.display = 'none';
                //Create and append select list
                var selectList = document.createElement("select");
                selectList.id = "UF_CRM_5BB36C07CECDE";
                selectList.name = "UF_CRM_5BB36C07CECDE";
                selectList.className = "js-example-basic-single";
                field_deal_filter_custom[0].appendChild(selectList);

                //Create and append the options
                for (var i = 0; i < json_data_1.length; i++) {
                    var option = document.createElement("option");
                    option.value = json_data_1[i].VALUE;
                    option.text = json_data_1[i].NAME;
                    selectList.appendChild(option);
                }
                $('.js-example-basic-single').select2();
                $('.js-example-basic-single').on('select2:select', function (e) {
                    var params = [
                        {"NAME": e.params.data.text, "VALUE": e.params.data.id}
                    ]
                    field_deal_filter[0].setAttribute("data-value", JSON.stringify(params));
                    BX.fireEvent(
                        $('select[name$="UF_CRM_5BB36C07CECDE"]')[0],
                        "change"
                    );
                });
            }
        }
        // Фильтр Страна (Country)

    });


    // обьязательные поля имитация
    $(document).on("DOMNodeInserted", function (event) {
        var fields = ['UF_CRM_1558687740', 'UF_CRM_1558685749', 'UF_CRM_1558685790', 'UF_CRM_1558685698', 'UF_CRM_1558688023'];
        // Отчеты, автоматическое заполнение
        for (var g = 0; g < fields.length; g++) {
            var required_fields = document.querySelectorAll('div[data-cid="' + fields[g] + '"] div.crm-entity-widget-content-block-title span.crm-entity-widget-content-block-title-text');
            var required_fields_active = document.querySelectorAll('div[data-cid="' + fields[g] + '"] div.crm-entity-widget-content-block-title span.crm-entity-widget-content-block-title-text span');
            if (required_fields.length > 0 && required_fields_active.length == 0) {
                for (var i = 0; i < required_fields.length; i++) {
                        var span = document.createElement('span');
                        span.innerHTML = '*';
                        span.style = 'color: rgb(255, 0, 0); vertical-align: super;';
                        required_fields[i].appendChild(span);
                }
            }
        }

    });


});