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
            <div class="col-md-2">
                <div class="form-group">
                    <label for="status">Статус</label>
                    <select type="text" class="form-control" id="status" v-model="selectedFilter.STATUS" @change="filterElement">
                        <option value="">Выберите один из вариантов</option>
                        <option v-for="st in data_status" v-bind:value="st.id">
                            {{ st.name }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="from_date">Дата, от</label>
                    <input type="date" class="form-control" id="from_date" v-model="selectedFilter.FROM_DATE" @change="filterElement">
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="to_date">Дата, до</label>
                    <input type="date" class="form-control" id="to_date" v-model="selectedFilter.TO_DATE" @change="filterElement">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="priority">Склады</label>
                    <multiselect
                            v-model="store_section"
                            :options="data_store"
                            :multiple="true"
                            value="id"
                            label="name"
                            track-by="name"
                            placeholder="Выберите один или несколько элементов"
                    >
                    </multiselect>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="manager">Менеджеры</label>
                    <multiselect
                            v-model="users"
                            :options="data_users"
                            :multiple="true"
                            value="id"
                            label="name"
                            track-by="name"
                            placeholder="Выберите один или несколько элементов"
                    >
                    </multiselect>
                </div>
            </div>

        </div>

        <div class="row flex-xl-nowrap">
            <div class="col-md-8">
                <h3 v-if="stores.length == 0">Выберите склад</h3>
            </div>
            <div class="col-md-4">
                <p class="pull-right">Всего движений: <b>{{count_task}}</b></p>
            </div>
        </div>

        <div class="row flex-xl-nowrap">
            <table class="table table-bordered">
                <tr>
                    <th></th>
                    <th>Движения товара</th>
                    <th>Склад получателя</th>
                    <th>Дата</th>
                    <th>Количество</th>
                    <th>Товар</th>
                    <th>№ машины</th>
                    <th>VIN машины</th>
                    <th>VIN двигателя</th>
                    <th>Дата изготов.</th>
                    <th>Примечание</th>
                    <th>Менеджер</th>
                    <th>История</th>
                </tr>
                <tr v-for="store in stores">
                    <td>{{ store.KEY }}</td>
                    <td>{{ store.UF_STATUS_EL_NAME }}</td>
                    <td>{{ store.UF_STORE_TO_NAME }}</td>
                    <td>{{ store.DATE_CREATE_CUSTOM }}</a></td>
                    <td>{{ store.UF_QUANTITY_CUSTOM }}</td>
                    <td><a v-bind:href="store.UF_PRODUCT_URL" target="_blank">{{ store.UF_PRODUCT_NAME }}</a></td>
                    <td>{{ store.UF_PRODUCT_PROPERTIES.PROPERTY_104_VALUE }}</td>
                    <td>{{ store.UF_PRODUCT_PROPERTIES.PROPERTY_105_VALUE }}</td>
                    <td>{{ store.UF_PRODUCT_PROPERTIES.PROPERTY_100_VALUE }}</td>
                    <td>{{ store.UF_PRODUCT_PROPERTIES.PROPERTY_150_VALUE }}</td>
                    <td>{{ store.UF_PRODUCT_PROPERTIES.PROPERTY_109_VALUE }}</td>
                    <td>{{ store.USER_FULL_NAME }}</td>
                    <td><a v-bind:href="store.UF_HISTORY_PRODUCT_URL" target="_blank">История</a></td>
                </tr>
                <tr>
                    <td><b>Итого:</b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>{{quan}}</b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
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
                <button @click="saveToExcel" class="btn btn-secondary">Скачать в Excel</button>
            </div>
        </div>

    </div>
</div>


<?//$APPLICATION->AddHeadScript('/local/lib/app_reports_task/js/app.js');?>
<script src="/local/lib/app_reports_store/js/app.js"></script>


