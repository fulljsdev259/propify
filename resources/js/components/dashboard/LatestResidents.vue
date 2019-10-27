<template>
    <div class="latest-residents dashboard-table">
        <div class="link-container">
            <router-link :to="{name: 'adminResidents'}">
                <span class="title">{{ $t('dashboard.residents.go_to_residents') }} </span>
                <i class="icon-right icon"/>
            </router-link>
        </div>
        <list-table
            :header="header"
            :items="latestResidents"
            :loading="{state: loading}"
            @selectionChanged="selectionChanged"
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
                    type: 'resident-details',
                    label: 'general.name',
                    props: ['name', 'image_url'],
                    minWidth: '100px'
                }, {
                    type: 'plain',
                    label: 'general.address',
                    prop: 'address',
                    width: 300,
                },{
                    type: 'icon-circle',
                    label: 'models.resident.status.label',
                    width: 130,
                    prop: 'status_label',
                    classSuffix: 'status_class_suffix',
                }, {
                    type: 'actions',
                    label: 'dashboard.actions',
                    width: 130,
                    actions: [ 
                        {
                            type: 'default',
                            icon: 'el-icon-user',
                            title: 'models.resident.view',
                            editUrl: 'adminResidentsView',
                            permissions: [
                                this.$permissions.update.resident
                            ]
                        }
                    ]
                }],
                items: []
            };
        },
        computed: {
            ...mapGetters(['latestResidents']),
        },
        methods: {
            ...mapActions(['getLatestResidents']),
            async fetchData() {
                const residents = await this.getLatestResidents({
                    limit: 5
                });
                this.items = residents.data
            }
        },
        created() {
            this.fetchData();
        }
    }
</script>
