<?php
global $APPLICATION;
//JS
$APPLICATION->AddHeadScript('/local/lib/app/js/jquery.min.js');
$APPLICATION->AddHeadScript('/local/lib/app/js/angular.min.js');
$APPLICATION->AddHeadScript('/local/lib/app/js/ui-grid.min.js');
$APPLICATION->AddHeadScript('/local/lib/app/js/app.js');
$APPLICATION->AddHeadScript('/local/lib/app/js/bootstrap.min.js');


$APPLICATION->SetAdditionalCSS('/local/lib/app/css/bootstrap.min.css');
$APPLICATION->SetAdditionalCSS('/local/lib/app/css/ui-grid.min.css');
$APPLICATION->SetAdditionalCSS('/local/lib/app/css/style.css');
?>




<div ng-app="storeProduct" ng-controller="storeController">
    <div ng-init="ListItem()"></div>
    <div ng-if="errorMessage">{{ errorMessage }}</div>

<?if( !(strstr($APPLICATION->GetCurPage(), '/crm/product/show/')) ):?>

    <a class="crm-items-add-row" id="crm-items-add-row" ng-click="addNewItem()" href="#add"><i class="glyphicon glyphicon-plus"></i> Додати Склад</a>

<?endif;?>

    <div class="table_owner_list">
        <div id="grid1" ui-grid="gridOptions" ui-grid-selection ui-grid-edit class="grid">
            <!-- Preloader Start
            <div id="loader-wrapper" ng-show="loading">
                <div id="loader"></div>
                <div class="loader-section section-left"></div>
                <div class="loader-section section-right"></div>
            </div>
             Preloader End -->
        </div>
    </div>
</div>
