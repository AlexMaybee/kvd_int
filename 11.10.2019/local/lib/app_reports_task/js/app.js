var app = new Vue({
    components: {
        Multiselect: window.VueMultiselect.default,
        Loading: VueLoading
    },

    el: '#app',
    data: function () {
        return {
            selectedFilter: {},
            tasks: [],
            tasks_print: [],
            request_url: '/local/lib/app_reports_task/ajax.php',
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
            print_task: {display: 'none'},
            cssText: '#print_task .table-bordered td, #print_task .table-bordered th {border: 1px solid #000000; margin:0; padding:0;}table {border-collapse: collapse;}#print_task{display: block}'
    };
    },

    mounted() {
        this.getTaskList(1);
        this.getUsersList();
        this.getStatusList();
        this.getDepartmentList();
        this.getSocNetGroupList();

        const { Printd } = window.printd
        this.d = new Printd( document.getElementById('print_task') )

        // Print dialog events (v0.0.9+)
        const { contentWindow } = this.d.getIFrame()

        contentWindow.addEventListener(
            'beforeprint', () => console.log('before print event!')
        )
        contentWindow.addEventListener(
            'afterprint', () => this.print_task = {display: 'none'}
    )


    },

    watch: {
        users_accomplices: function() {
            this.filterElement();
        },
        users_created_by: function() {
            this.filterElement();
        },
        users: function() {
            this.filterElement();
        },

        tasks_print: function() {
            this.print_task = {display: 'block'};
            this.visible = false;
            setTimeout(() => {
                this.d.print( document.getElementById('print_task'), this.cssText);

            }, 500);
        },
    },

    methods: {

        updateResource(num) {
            this.tasks_paginations = [];
            this.current_page = num;
            this.getTaskList(num);
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

        getTaskList(page) {
            let headers = {'Content-Type': 'application/json'}
            axios.post(this.request_url, [{action: 'getTaskList', page: page, filter: this.selectedFilter}], {headers}).then(response => {
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

        removeElement(actionName) {
            //console.log(this.data_users_id);
            //console.log(this.findObjectByKey(this.users, 'id', actionName.id));


            // this.delete_key = this.findObjectByKey(this.users, 'id', actionName.id);
            // delete this.users[this.delete_key];

            // console.log(this.users);
            this.data_users_id.splice(this.data_users_id.indexOf(actionName.id), 1);
            //console.log(this.data_users);
            this.filterElement();
        },

        filterElement() {

            this.selectedFilter.ACCOMPLICES = [];
            this.selectedFilter.RESPONSIBLE_ID = [];
            this.selectedFilter.CREATED_BY = [];

            if (this.users_accomplices.length > 0) {
                for(let i = 0; i < this.users_accomplices.length; i++) {
                    this.selectedFilter.ACCOMPLICES.push(this.users_accomplices[i].id);
                }
            }

            if (this.users_created_by.length > 0) {
                for(let i = 0; i < this.users_created_by.length; i++) {
                    this.selectedFilter.CREATED_BY.push(this.users_created_by[i].id);
                }
            }

            if (this.users.length > 0) {
                for(let i = 0; i < this.users.length; i++) {
                    this.selectedFilter.RESPONSIBLE_ID.push(this.users[i].id);
                }
            }

            this.getTaskList(this.current_page);
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

        getDepartmentList() {
            let headers = {'Content-Type': 'application/json'}
            axios.post(this.request_url, [{action: 'getDepartmentList'}], {headers}).then(response => {
                this.data_department = response.data;
        })
        .catch(err => console.log(err));
        },

        getSocNetGroupList() {
            let headers = {'Content-Type': 'application/json'}
            axios.post(this.request_url, [{action: 'getSocNetGroupList'}], {headers}).then(response => {
                this.data_group = response.data;
        })
        .catch(err => console.log(err));
        },

        print () {
            this.visible = true
            this.print_task = {display: 'block'};
            this.getTaskListPrint(this.current_page);
            // setTimeout(
            //     function()
            //     {
            //         this.d.print( document.getElementById('print_task'), this.cssText)
            //     }, 500)

        },

        getTaskListPrint () {
            let headers = {'Content-Type': 'application/json'}
            axios.post(this.request_url, [{action: 'getTaskListPrint', filter: this.selectedFilter}], {headers}).then(response => {
            this.tasks_print = response.data;
        })
        .catch(err => console.log(err));
        }

    }

})