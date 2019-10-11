<?php
global $APPLICATION;
//JS
$APPLICATION->AddHeadScript('//cdn.jsdelivr.net/npm/vue/dist/vue.js');
$APPLICATION->AddHeadScript('//unpkg.com/axios/dist/axios.min.js');
$APPLICATION->AddHeadScript('//unpkg.com/vue-multiselect@2.1.0');
$APPLICATION->AddHeadScript('//unpkg.com/printd@0.0.12/dist/printd.umd.min.js');
$APPLICATION->AddHeadScript('//cdn.jsdelivr.net/npm/vue-loading-overlay@3');

$APPLICATION->SetAdditionalCSS('//stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
$APPLICATION->SetAdditionalCSS('//unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css');
$APPLICATION->SetAdditionalCSS('//cdn.jsdelivr.net/npm/vue-loading-overlay@3/dist/vue-loading.css');
$APPLICATION->SetAdditionalCSS('/local/lib/app_reports_task/css/style.css');
?>
<div id="app">
    <loading :active.sync="visible" :can-cancel="false"></loading>
    <div class="container-fluid">
        <div class="row flex-xl-nowrap">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="title">Название задачи</label>
                    <input type="text" class="form-control" id="title" v-model="selectedFilter.TITLE" v-on:keyup="filterElement">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="priority">Важность</label>
                    <select type="text" class="form-control" id="priority" v-model="selectedFilter.PRIORITY" @change="filterElement">
                        <option value="">Выберите один из вариантов</option>
                        <option v-for="pr in priority" v-bind:value="pr.id">
                            {{ pr.name }}
                        </option>
                    </select>
                </div>
            </div>
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
            <div class="col-md-3">
                <div class="form-group">
                    <label for="department">Департамент(отдел)</label>
                    <select type="text" class="form-control" id="department" v-model="selectedFilter.DEPARTMENT_ID" @change="filterElement">
                        <option value="">Выберите один из вариантов</option>
                        <option v-for="dep in data_department" v-bind:value="dep.id">
                            {{ dep.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="group">Группа</label>
                    <select type="text" class="form-control" id="group" v-model="selectedFilter.GROUP_ID" @change="filterElement">
                        <option value="">Выберите один из вариантов</option>
                        <option v-for="group in data_group" v-bind:value="group.id">
                            {{ group.name }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row flex-xl-nowrap">
            <div class="col-md-1">
                <div class="form-group">
                    <label for="title">Номер задачи</label>
                    <input type="text" class="form-control" id="title" v-model="selectedFilter.ID" v-on:keyup="filterElement">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="priority">Постановщик</label>
                    <multiselect
                            v-model="users_created_by"
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
            <div class="col-md-3">
                <div class="form-group">
                    <label for="users_accomplices">Соисполнитель</label>
                    <multiselect
                            v-model="users_accomplices"
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
            <div class="col-md-2">
                <div class="form-group">
                    <label for="user_task">Не показывать свои задачи</label>
                    <input type="checkbox" class="form-control" id="user_task" value="1" v-model="selectedFilter.USER_TASK" v-on:change="filterElement">
                </div>
            </div>

        </div>

        <div class="row flex-xl-nowrap">
            <div class="col-md-12">
                <p class="pull-right">Всего задач: <b>{{count_task}}</b></p>
            </div>
        </div>

        <div class="row flex-xl-nowrap">
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <th>Название задачи</th>
                    <th>Важность</th>
                    <th>Статус</th>
                    <th>Постановщик</th>
                    <th>Ответственный</th>
                    <th>Соисполнитель</th>
                    <th>Последний изменивший</th>
                    <th>Завершивший</th>
                    <th>Группа</th>
                </tr>
                <tr v-for="task in tasks">
                    <td>{{ task.ID }}</td>
                    <td><a v-bind:href="task.DETAIL_URL" class="task-title task-status-text-color-accepted">{{ task.TITLE }}</a></td>
                    <td>{{ task.PRIORITY }}</td>
                    <td>{{ task.STATUS }}</td>
                    <td><u>{{ task.CREATED_BY_WORK_POSITION }}</u>: {{ task.CREATED_BY_LAST_NAME }} {{ task.CREATED_BY_NAME }}</td>
                    <td><u>{{ task.RESPONSIBLE_WORK_POSITION }}</u>: {{ task.RESPONSIBLE_LAST_NAME }} {{ task.RESPONSIBLE_NAME }}</td>
                    <td><span v-for="item in task.ACCOMPLICES_FULL_NAME">{{item}}, </span></td>
                    <td>{{ task.CHANGED_BY_FULL_NAME }}</td>
                    <td>{{ task.CLOSED_BY_FULL_NAME }}</td>
                    <td>{{ task.GROUP_NAME }}</td>
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
                <button @click="print" class="btn btn-secondary">Печатать</button>
            </div>
        </div>

    </div>

    <div class="row flex-xl-nowrap" id="print_task" v-bind:style="print_task">
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>Название задачи</th>
                <th>Важность</th>
                <th>Статус</th>
                <th>Постановщик</th>
                <th>Ответственный</th>
                <th>Соисполнитель</th>
                <th>Последний изменивший</th>
                <th>Завершивший</th>
                <th>Группа</th>
            </tr>
            <tr v-for="task in tasks_print">
                <td>{{ task.ID }}</td>
                <td>{{ task.TITLE }}</td>
                <td>{{ task.PRIORITY }}</td>
                <td>{{ task.STATUS }}</td>
                <td><u>{{ task.CREATED_BY_WORK_POSITION }}</u>: {{ task.CREATED_BY_LAST_NAME }} {{ task.CREATED_BY_NAME }}</td>
                <td><u>{{ task.RESPONSIBLE_WORK_POSITION }}</u>: {{ task.RESPONSIBLE_LAST_NAME }} {{ task.RESPONSIBLE_NAME }}</td>
                <td><span v-for="item in task.ACCOMPLICES_FULL_NAME">{{item}}, </span></td>
                <td>{{ task.CHANGED_BY_FULL_NAME }}</td>
                <td>{{ task.CLOSED_BY_FULL_NAME }}</td>
                <td>{{ task.GROUP_NAME }}</td>
            </tr>
        </table>
    </div>


</div>


<?//$APPLICATION->AddHeadScript('/local/lib/app_reports_task/js/app.js');?>
<script src="/local/lib/app_reports_task/js/app.js"></script>


