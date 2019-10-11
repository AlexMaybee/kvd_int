<?php
global $APPLICATION;
//JS
$APPLICATION->AddHeadScript('//cdn.jsdelivr.net/npm/vue/dist/vue.js');
$APPLICATION->AddHeadScript('//unpkg.com/axios/dist/axios.min.js');
$APPLICATION->AddHeadScript('//unpkg.com/vue-multiselect@2.1.0');
$APPLICATION->AddHeadScript('//cdn.jsdelivr.net/npm/vue-loading-overlay@3');
$APPLICATION->AddHeadScript('//stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js');

$APPLICATION->SetAdditionalCSS('//stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
$APPLICATION->SetAdditionalCSS('//unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css');
$APPLICATION->SetAdditionalCSS('//cdn.jsdelivr.net/npm/vue-loading-overlay@3/dist/vue-loading.css');
$APPLICATION->SetAdditionalCSS('/local/lib/app_dealers_cabinet/css/style.css');
?>
<div id="app">
    <loading :active.sync="visible" :can-cancel="false"></loading>
    <div class="container-fluid">
        <div class="row flex-xl-nowrap">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="title">Название компании</label>
                    <input type="text" class="form-control" id="title" v-model="selectedFilter.TITLE" v-on:keyup="filterElement">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="users">Ответственный</label>
                    <multiselect
                            v-model="users"
                            :options="data_users"
                            :multiple="true"
                            value="id"
                            label="name"
                            track-by="name"
                            placeholder="Выберите один или несколько элементов"
                            selectLabel="Нажмите Enter для выбора"
                    >
                    </multiselect>
                </div>
            </div>
            <div class="col-md-7">
                <p class="pull-right">
                    <button type="button" class="btn btn-primary" v-on:click="clearFields()">
                        Добавить компанию
                    </button>
                </p>
            </div>
        </div>

        <div class="row flex-xl-nowrap">
            <div class="col-md-12">
                <p class="pull-right">Всего компаний: <b>{{count_task}}</b></p>
            </div>
        </div>

        <div class="row flex-xl-nowrap">
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <th>Название компании</th>
                    <th>Ответственный</th>
                    <th class="phone_class">Телефон</th>
                    <th>EMAIL</th>
                    <th>Адрес</th>
                    <th>Комментарий</th>
                </tr>
                <tr v-for="task in tasks">
                    <td>{{ task.ID }}</td>
                    <td>{{task.TITLE}}<span v-if="task.EDIT == 'Y'" class="edit_a" v-on:click="editCompany(task.ID)">Редактировать</span>
                    </td>
                    <td>{{task.ASSIGNED_BY_NAME}}</td>
                    <td>{{task.PHONE}}</td>
                    <td>{{task.EMAIL}}</td>
                    <td>{{task.ADRESS}}</td>
                    <td>{{task.COMMENTS}}</td>
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
        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавления компании</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="sendFormDeal" @submit.prevent="createCompany" method="POST">

                        <div class="form-group required">
                            <label class="form-control-label" for="TITLE">Название Компании</label>
                            <input type="text" class="form-control" v-model="fieldsCompany.TITLE" name="TITLE" id="TITLE" required data-value-missing="Заполните эту форму">
                        </div>

                        <div class="form-group required">
                            <label class="form-control-label" for="PHONE">Телефон</label>
                            <input type="text" class="form-control" v-model="fieldsCompany.PHONE" name="PHONE" id="PHONE" required data-value-missing="Заполните эту форму">
                        </div>

                        <div class="form-group required">
                            <label class="form-control-label" for="ASSIGNED_BY_ID">Ответственный</label>
                            <select v-model="fieldsCompany.ASSIGNED_BY_ID" id="ASSIGNED_BY_ID" class="form-control" required data-value-missing="Заполните эту форму">
                                <option v-for="user in data_users" v-bind:value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group required">
                            <label class="form-control-label" for="EMAIL">EMAIL</label>
                            <input type="email" class="form-control" v-model="fieldsCompany.EMAIL" name="EMAIL" id="EMAIL" required data-value-missing="Заполните эту форму">
                        </div>

                        <div class="form-group required">
                            <label class="form-control-label" for="ADRESS">Адрес</label>
                            <textarea type="text" class="form-control" v-model="fieldsCompany.ADRESS" name="ADRESS" id="ADRESS"></textarea>
                        </div>

                        <div class="form-group required">
                            <label class="form-control-label" for="COMMENTS">Комментарий</label>
                            <textarea class="form-control" v-model="fieldsCompany.COMMENTS" name="COMMENTS" id="COMMENTS"></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>


</div>


<?//$APPLICATION->AddHeadScript('/local/lib/app_reports_task/js/app.js');?>
<script src="/local/lib/app_dealers_cabinet/js/app.js"></script>


