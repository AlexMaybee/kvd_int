var app = angular.module("storeProduct", ['ui.grid', 'ui.grid.edit']);
app.controller("storeController", function($scope, $filter, $http,) {

    $scope.products = ["Milk", "Bread", "Cheese"];
    $scope.url = '/local/ajax/list_store.php';
    $scope.cityListData = [];
    $scope.storeListData = [];

    //НАстройки таблицы
    $scope.gridOptions = {
        enableRowSelection: true,
        selectionRowHeaderWidth: 40,
        rowHeight: 60,
        showGridFooter: false,
        enableColumnMenus: false,
        paginationPageSizes: [10, 50, 75],
        paginationPageSize: 10,
        enableCellEditOnFocus: true,
        enableFiltering: false
    };


    //Добавление нового елемента
    $scope.addNewItem=function()
    {
        $scope.gridOptions.data = $scope.gridOptions.data || [];
        var key = $scope.gridOptions.data.unshift({

        });
        $scope.gridOptions.data[0].editrow = true;
    };

    //Таблица со всеми данными
    $scope.ListItem = function () {
        $scope.cityList();
        $scope.loading = true;
        $http.post('/local/ajax/list_store.php', {'ID': productId, action: 'get_list_product'} )
            .then(function (response) {
                console.log(response.data);
                    $scope.loading = false;
                    $scope.gridOptions = {
                        data: response.data,
                        columnDefs: [
                            {
                                field: 'CITY_ID',
                                displayName: 'Страна',
                                name: 'CITY_ID',
                                width: 200,
                                enableCellEdit: false,
                                visible: true,
                                cellTemplate: '<div ng-if="!row.entity.editrow">{{COL_FIELD.VALUE}}</div>' +
                                '<div class="form-group" ng-if="row.entity.editrow">' +
                                '<select class="form-control" ng-model="MODEL_COL_FIELD.ID" ng-show="grid.appScope.cityListData.length > 0" ng-change="grid.appScope.changeCity(row.entity)">' +
                                '<option ng-repeat="result in grid.appScope.cityListData" value="{{result.id}}">{{result.name}}</option>' +
                                '</select>' +
                                '</div>'
                            },
                            {
                                field: 'STORE_ID',
                                displayName: 'Склад',
                                name: 'STORE_ID',
                                width: 200,
                                enableCellEdit: false,
                                visible: true,
                                cellTemplate: '<div ng-if="!row.entity.editrow">{{COL_FIELD.VALUE}}</div>' +
                                '<div class="form-group" ng-if="row.entity.editrow">' +
                                '<select class="form-control" ng-model="MODEL_COL_FIELD.ID" ng-disabled="grid.appScope.storeListData.length == 0">' +
                                '<option ng-repeat="result in grid.appScope.storeListData" value="{{result.id}}">{{result.name}}</option>' +
                                '</select>' +
                                '</div>'
                            },
                            {
                                field: 'STORE_COUNT',
                                displayName: 'К-во на складе',
                                name: 'STORE_COUNT',
                                width: 200,
                                enableCellEdit: false,
                                visible: true,
                                cellTemplate: '<div ng-if="!row.entity.editrow">{{COL_FIELD}}</div>' +
                                '<div class="form-group" ng-if="row.entity.editrow"><input type="text" class="form-control" ng-model="MODEL_COL_FIELD"></div>'
                            },
                            { field: 'edit',
                                displayName: 'Инструменты',
                                cellClass: 'dis_inherit',
                                width: 100,
                                name: 'edit',
                                cellTemplate: '<a ng-show="!row.entity.editrow" class="btn primary" ng-click="grid.appScope.edit(row.entity)"><i class="glyphicon glyphicon-pencil"></i></a>' +
                                '<a ng-show="row.entity.editrow" class="btn primary" ng-click="grid.appScope.saveRow(row.entity)"><i class="glyphicon glyphicon-ok"></i></a>' +
                                '<a ng-show="!row.entity.editrow" class="btn primary delete_button" ng-click="grid.appScope.Delete(row.entity)"><i class="glyphicon glyphicon-trash"></i></a>',
                                enableFiltering: false,
                                enableCellEdit: false,
                                visible: edit_panel
                            }
                        ]
                    };

                },
                function (error) {
                });
    };


    $scope.cityList = function () {
        $http({
            method: 'post',
            url: $scope.url,
            data: {action: 'get_list_city', depth: 1}
        }).then(function successCallback(response) {
            console.log(response.data);
            $scope.cityListData = response.data;
        });
    }

    $scope.storeList = function (id) {
        $http({
            method: 'post',
            url: $scope.url,
            data: {action: 'get_list_store', depth: 2, parent_id: id}
        }).then(function successCallback(response) {
            console.log(response.data);
            $scope.storeListData = response.data;
        });
    }

    // Set value to search box компаний
    $scope.setValueC = function(index, row){
        row.CITY_ID = $scope.searchResultC[index].name;
        $scope.searchResultC = {};
    }

    $scope.changeCity = function (row) {
        console.log('changeCity')
        console.log(row);
        $scope.storeList(row.CITY_ID.ID);
        //console.log($scope.gridOptions.data)
    }

    $scope.saveRow = function (row) {
        var action = 'createStore';
        if(!row.STORE_ID) {
            alert('Выберите склад');
            return false;
        } else {
            if (row.STORE_ID.ID == null) {
                alert('Выберите склад');
                return false;
            }
        }
        var index = $scope.gridOptions.data.indexOf(row);
        $scope.gridOptions.data[index].editrow = false;
        var params = {
            NAME: productName,
            IBLOCK_SECTION_ID: row.STORE_ID.ID,
            PROPERTY_VALUES: {
                TOVAR: productId,
                KOLICHESTVO: row.STORE_COUNT,
            },
            IBLOCK_ID: 31
        }
        if( row.edit > 0) {
            action = 'updateStore';
        }
            $http({
                method: 'post',
                url: $scope.url,
                data: {action: action, id: row.edit, data: params}
            }).then(function successCallback(response) {
                if (response.data == false) {
                    alert('Склад заполнен к товару');
                } else {
                    console.log(response.data);
                }
                $scope.ListItem();
            });
    }

    $scope.Delete = function (row) {
        $http({
            method: 'post',
            url: $scope.url,
            data: {action: 'removeStore', id: row.edit}
        }).then(function successCallback(response) {
            $scope.ListItem();
        });
    };

    //Редактирование елемента
    $scope.edit = function (row) {
        $scope.storeList(row.CITY_ID.ID);
        //Get the index of selected row from row object
        var index = $scope.gridOptions.data.indexOf(row);

        //Use that to set the editrow attrbute value for seleted rows
        $scope.gridOptions.data[index].editrow = !$scope.gridOptions.data[index].editrow;
    };




});