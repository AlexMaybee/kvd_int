<?php
global $APPLICATION;
//JS
$APPLICATION->AddHeadScript('//cdn.jsdelivr.net/npm/vue/dist/vue.js');
$APPLICATION->AddHeadScript('//unpkg.com/axios/dist/axios.min.js');
$APPLICATION->AddHeadScript('//unpkg.com/vue-multiselect@2.1.0');
$APPLICATION->AddHeadScript('//cdn.jsdelivr.net/npm/vue-loading-overlay@3');

$APPLICATION->SetAdditionalCSS('//stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
$APPLICATION->SetAdditionalCSS('//unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css');
$APPLICATION->SetAdditionalCSS('//cdn.jsdelivr.net/npm/vue-loading-overlay@3/dist/vue-loading.css');
$APPLICATION->SetAdditionalCSS('/local/lib/app_reports_store/css/style.css');
?>
<div id="app">
    <loading :active.sync="visible" :can-cancel="false"></loading>
    <div class="container-fluid">
        <div class="row flex-xl-nowrap">
            <table class="table table-bordered">
                <tr>
                    <th></th>
                    <th>Движения товара</th>
                    <th>Склад</th>
                    <th>Склад получателя</th>
                    <th>Дата</th>
                    <th>Количество</th>
                    <th>Товар</th>
                    <th>№ машины</th>
                    <th>VIN машины</th>
                    <th>VIN двигателя</th>
                    <th>Примечание</th>
                    <th>Менеджер</th>
                </tr>
                <tr v-for="store in stores">
                    <td>{{ store.KEY }}</td>
                    <td>{{ store.UF_STATUS_EL_NAME }}</td>
                    <td>{{ store.UF_STORE_FROM_NAME }}</td>
                    <td>{{ store.UF_STORE_TO_NAME }}</td>
                    <td>{{ store.DATE_CREATE_CUSTOM }}</a></td>
                    <td>{{ store.UF_QUANTITY_CUSTOM }}</td>
                    <td><a v-bind:href="store.UF_PRODUCT_URL" target="_blank">{{ store.UF_PRODUCT_NAME }}</a></td>
                    <td>{{ store.UF_PRODUCT_PROPERTIES.PROPERTY_104_VALUE }}</td>
                    <td>{{ store.UF_PRODUCT_PROPERTIES.PROPERTY_105_VALUE }}</td>
                    <td>{{ store.UF_PRODUCT_PROPERTIES.PROPERTY_100_VALUE }}</td>
                    <td>{{ store.UF_PRODUCT_PROPERTIES.PROPERTY_109_VALUE }}</td>
                    <td>{{ store.USER_FULL_NAME }}</td>
                </tr>
            </table>
        </div>

        <div class="row flex-xl-nowrap">
            <div class="col-md-10">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item">
                            <a class="page-link" href="#p" aria-label="Previous" v-on:click="prevUpdateResource">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                        <li class="page-item" v-for="tasks_pagination in tasks_paginations" v-bind:class="{'active': tasks_pagination == current_page}"><a class="page-link" href="#p" v-on:click="updateResource(tasks_pagination)">{{tasks_pagination}}</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#p" aria-label="Next" v-on:click="nextUpdateResource">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="col-md-2">
            </div>
        </div>

    </div>



</div>


<?//$APPLICATION->AddHeadScript('/local/lib/app_reports_task/js/app.js');?>
<script src="/local/lib/app_reports_store/js/app.js"></script>


