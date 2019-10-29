<template>
    <div class="managers-list dashboard-table">
        <div class="link-container">
            <router-link :to="{name: 'adminPropertyManagers'}">
                <span class="title">{{ $t('dashboard.requests.go_to_property_managers') }} </span>
                <i class="icon-right icon"/>
            </router-link>
        </div>
        <list-table
            :header="header"
            :items="propertyManagers"
            :loading="{state: loading}"
            @selectionChanged="selectionChanged"
            :height="250"
        >
        </list-table>
    </div>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import DashboardListMixin from 'mixins/DashboardListMixin';
    
    const mixin = DashboardListMixin();

    export default {
        mixins: [mixin],
        props: {
          type: {
            type: String
          }
        },
        data() {
            return {
                header: [{
                    type: 'plain',
                    label: 'general.name',
                    prop: 'name',
                    minWidth: '150px'
                }, {
                    type: 'counts',
                    minWidth: '150px',
                    label: 'general.requests',
                }, {
                    type: 'actions',
                    label: 'dashboard.actions',
                    width: '130px',
                    actions: [ 
                        {
                            type: 'default',
                            title: 'general.actions.edit',
                            editUrl: 'adminPropertyManagersEdit',
                            permissions: [
                                this.$permissions.update.propertyManager
                            ]
                        }
                    ]
                }],
                items: []
            };
        },
        methods: {
            ...mapActions(['getPropertyManagers']),
            async fetchData() {
                const propertyManagers = await this.getPropertyManagers({
                    get_all : true,
                    has_req : 1
                });
                this.items = propertyManagers.data
            },
        },
        computed: {
            ...mapGetters(['propertyManagers']),
        },
        created() {
            this.fetchData();
        }
    }
</script>