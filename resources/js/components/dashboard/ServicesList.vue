<template>
    <div class="services-list dashboard-table">
        <div class="link-container">
            <router-link :to="{name: 'adminServices'}">
                <span class="title">{{ $t('dashboard.requests.go_to_service_partners') }} </span>
                <i class="icon-right icon"/>
            </router-link>
        </div>
        <list-table
            :header="header"
            :items="services"
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
                            editUrl: 'adminServicesEdit',
                            permissions: [
                                this.$permissions.update.provider
                            ]
                        }
                    ]
                }],
                items: []
            };
        },
        methods: {
            ...mapActions(['getServices']),
            async fetchData() {

                const services = await this.getServices({
                    get_all : true,
                    has_req : 1
                });
                this.items = services.data
            }
        },
        computed: {
            ...mapGetters(['services']),
        },
        created() {
            this.fetchData();
        }
    }
</script>