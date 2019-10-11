var app = new Vue({
    components: {
        Multiselect: window.VueMultiselect.default,
        Loading: VueLoading
    },

    el: '#app',
    data: function () {
        return {
            selectedFilter: {},
            stores: [],
            request_url: '/local/lib/app_reports_store/ajax.php',
            tasks_paginations: [],
            current_page: 1,
            max_page: 1,
            quan: null,
            users: [],
            users_accomplices: [],
            store_section: [],
            data_store: [],
            data_users: [],
            data_group: [],
            data_status: [],
            data_department: [],
            delete_key: [],
            count_task: null,
            visible: false,
            get_section: '',
            get_status: '',
            get_date_from: '',
            get_date_to: '',
            get_manager: '',
    };
    },

    mounted() {
        this.getTaskList(1);
        this.getStoreListSection();
        this.getStatusList();
        this.getUsersList();
        // this.getDepartmentList();
        // this.getSocNetGroupList();
    },

    watch: {
        store_section: function() {
            this.filterElement();
        },

        users: function() {
            this.filterElement();
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
            var url = new URL(window.location.href);
            var product_id = url.searchParams.get("product_id");
            if(product_id > 0){
                this.selectedFilter.UF_PRODUCT = product_id;
            }
            let headers = {'Content-Type': 'application/json'}
            axios.post(this.request_url, [{action: 'getStoreList', page: page, filter: this.selectedFilter}], {headers}).then(response => {
                this.stores = response.data.STORES;
                this.count_task = response.data.MAX_PAGE;
                this.quan = response.data.QUANTITY;
                this.max_page = (response.data.MAX_PAGER == 1)? 0 : response.data.MAX_PAGER;
                this.buildPagination()

        })
        .catch(err => console.log(err));
        },

        filterElement() {
            this.selectedFilter.SECTIONS = [];
            this.selectedFilter.USERS = [];

            if (this.store_section.length > 0) {
                for(let i = 0; i < this.store_section.length; i++) {
                    this.selectedFilter.SECTIONS.push(this.store_section[i].id);
                }
            }

            if (this.users.length > 0) {
                for(let i = 0; i < this.users.length; i++) {
                    this.selectedFilter.USERS.push(this.users[i].id);
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

        getStoreListSection() {
            let headers = {'Content-Type': 'application/json'}
            axios.post(this.request_url, [{action: 'getStoreListSection'}], {headers}).then(response => {
                this.data_store = response.data;
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

        getUsersList() {
            let headers = {'Content-Type': 'application/json'}
            axios.post(this.request_url, [{action: 'getUsersList'}], {headers}).then(response => {
                this.data_users = response.data;
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

        saveToExcel() {

            if(this.selectedFilter.SECTIONS){
                if(this.selectedFilter.SECTIONS.length > 0) {
                    this.get_section = this.selectedFilter.SECTIONS[0];
                }
            }

            if(this.selectedFilter.USERS){
                if(this.selectedFilter.USERS.length > 0) {
                    this.get_manager = this.selectedFilter.USERS[0];
                }
            }

            if(this.selectedFilter.STATUS){
                this.get_status = this.selectedFilter.STATUS;
            }

            if(this.selectedFilter.FROM_DATE){
                this.get_date_from = this.selectedFilter.FROM_DATE;
            }

            if(this.selectedFilter.TO_DATE){
                this.get_date_to = this.selectedFilter.TO_DATE;
            }

            window.location.href = "/local/lib/app_reports_store/export_excel.php?section=" + this.get_section + "&user=" + this.get_manager + "&status=" + this.get_status + "&from_date=" + this.get_date_from + "&to_date=" + this.get_date_to;

        }

    }

})