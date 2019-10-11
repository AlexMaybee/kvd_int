var app = new Vue({
    components: {
        Multiselect: window.VueMultiselect.default,
        Loading: VueLoading
    },

    el: '#app',
    data: function () {
        return {
            selectedFilter: {},
            fieldsCompany: {},
            tasks: [],
            tasks_print: [],
            request_url: '/local/lib/app_dealers_cabinet/ajax.php',
            tasks_paginations: [],
            current_page: 1,
            max_page: 1,
            priority: [{id: 1, name: 'Не важная'}, {id: 2, name: 'Важная'}],
            users: [],
            users_accomplices: [],
            users_created_by: [],
            data_users: [],
            data_group: [],
            data_status: [],
            data_department: [],
            data_users_id: [],
            delete_key: [],
            count_task: null,
            visible: false,
    };
    },

    mounted() {
        this.getCompanyList(1);
        this.getUsersList();
    },

    watch: {
        users: function() {
            this.filterElement();
        },
    },

    methods: {

        createCompany() {
            let self = this;
            console.log('createCompany');
            console.log(this.fieldsCompany);
            let headers = {'Content-Type': 'application/json'}
            axios.post(this.request_url, [{action: 'createCompany', data: this.fieldsCompany}], {headers}).then(response => {
                if(response.data > 0) {
                    self.getCompanyList(1);
                    $('#exampleModal').modal('hide');
                    self.fieldsCompany = {};
                } else {
                    alert(response.data);
                }
            })
            .catch(err => console.log(err));
        },

        updateResource(num) {
            this.tasks_paginations = [];
            this.current_page = num;
            this.getCompanyList(num);
            for ( i = num; i <= num + 5; i++ )
            {
                if(i <= this.max_page) {
                    this.tasks_paginations.push(i);
                }
            }
        },

        buildPagination () {
            this.tasks_paginations = [];
            if(this.max_page > 5) {
                for ( i = this.current_page; i <= this.current_page + 5; i++ )
                {
                    if(i <= this.max_page) {
                        this.tasks_paginations.push(i);
                    }
                }
            } else {
                for ( i = this.current_page; i <= this.current_page + this.max_page; i++ )
                {
                    if(i <= this.max_page) {
                        this.tasks_paginations.push(i);
                    }
                }
            }
        },

        prevUpdateResource() {
            if(this.current_page > 1)
            {
                //this.current_page = this.current_page - 1;
                this.updateResource(this.current_page - 1);
            }
        },

        nextUpdateResource() {
            if(this.current_page <= this.max_page)
            {
                //this.current_page = this.current_page - 1;
                this.updateResource(this.current_page + 1);
            }
        },

        getCompanyList(page) {
            let headers = {'Content-Type': 'application/json'}
            axios.post(this.request_url, [{action: 'getCompanyList', page: page, filter: this.selectedFilter}], {headers}).then(response => {
                this.tasks = response.data.TASKS;
                this.count_task = response.data.MAX_PAGE;
                this.max_page = (response.data.MAX_PAGER == 1)? 0 : response.data.MAX_PAGER;
                this.buildPagination()

        })
        .catch(err => console.log(err));
        },

        searchElement() {

        },

        selectElement(actionName) {
            this.data_users_id.push(actionName.id);
            this.filterElement();
        },
        

        filterElement() {

            this.selectedFilter.ASSIGNED_BY_ID = [];

            if (this.users.length > 0) {
                for(let i = 0; i < this.users.length; i++) {
                    this.selectedFilter.ASSIGNED_BY_ID.push(this.users[i].id);
                }
            }

            this.getCompanyList(this.current_page);
        },

        findObjectByKey (array, key, value) {
            if (array != null) {
                for (var i = 0; i < array.length; i++) {
                    if (array[i][key] === value) {
                        return i;
                    }
                }
                return null;
            }
        },

        getUsersList() {
            let headers = {'Content-Type': 'application/json'}
            axios.post(this.request_url, [{action: 'getUsersList'}], {headers}).then(response => {
                this.data_users = response.data;
        })
        .catch(err => console.log(err));
        },

        getStatusList() {
            let headers = {'Content-Type': 'application/json'}
            axios.post(this.request_url, [{action: 'getStatusList'}], {headers}).then(response => {
                this.data_status = response.data;
        })
        .catch(err => console.log(err));
        },

        editCompany(ID) {
            let self = this;
            console.log(ID);
            let headers = {'Content-Type': 'application/json'}
            axios.post(this.request_url, [{action: 'editCompany', id: ID}], {headers}).then(response => {
                console.log(response.data);
                self.fieldsCompany = response.data;
                $('#exampleModal').modal('show');
        })
        .catch(err => console.log(err));
        },

        clearFields() {
            this.fieldsCompany = {};
            $('#exampleModal').modal('show');
        }

    }

})