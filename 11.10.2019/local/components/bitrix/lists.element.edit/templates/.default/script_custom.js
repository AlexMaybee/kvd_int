
function saveTransit() {
    //console.log(store_id);
    var store_transit = document.getElementsByName("store_transit");

    var store_to = document.getElementsByName("store_to");

    var prod_id = document.getElementsByName("PROPERTY_98");

    if(!store_transit[0].value) {
        alert('Заполните поле Транзитный склад');
        return false;
    }

    if(!store_to[0].value) {
        alert('Заполните поле Куда перемещаем');
        return false;
    }

    if(!prod_id[0].value) {
        alert('Заполните поле Товар');
        return false;
    }

    var objData = {'UF_CRM_1541664058': store_to[0].value, 'UF_CRM_1541664081': store_transit[0].value, 'PRODUCTS': prod_id[0].value, 'STORE_ID': store_id};

    BX.ajax({
        url: '/local/ajax/create_deal.php',
        data: objData,
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
            if(data) {
                location.reload();
            }
            console.log(data);
        },
        onfailure: function(){

        }
    });

}


