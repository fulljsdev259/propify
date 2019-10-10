<template>
    <div class="latest-tenants dashboard-table">
        <div class="link-container">
            <router-link :to="{name: 'adminTenants'}">
                <span class="title">{{ $t('dashboard.tenants.go_to_tenants') }} </span>
                <i class="icon-right icon"/>
            </router-link>
        </div>
        <list-table
            :header="header"
            :items="items"
            :loading="{state: loading}"
            @selectionChanged="selectionChanged"
        >
        </list-table>
    </div>
</template>

<script>
    import {mapActions} from 'vuex';
    import axios from '@/axios';

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
                    type: 'tenant-details',
                    label: 'general.name',
                    props: ['name', 'image_url'],
                    minWidth: '100px'
                }, {
                    type: 'plain',
                    label: 'models.address.name',
                    prop: 'address',
                    width: 300,
                },{
                    type: 'icon-circle',
                    label: 'models.tenant.status.label',
                    prop: 'status_label',
                    classSuffix: 'status_class_suffix',
                }, {
                    type: 'actions',
                    label: 'dashboard.actions',
                    width: '130px',
                    actions: [ 
                        {
                            type: 'default',
                            icon: 'el-icon-user',
                            title: 'models.tenant.view',
                            editUrl: 'adminTenantsView',
                            permissions: [
                                this.$permissions.update.resident
                            ]
                        }
                    ]
                }],
            };
        },
        computed: {
            tenantConstants() {
                return this.$constants.tenants;
            },
        },
        methods: {
            fetchData() {
              let that = this;
              let url = 'tenants/latest';
              return axios.get(url)
              .then(function (response) {
                const items = response.data.data.map(item => {
                  item.status_label = `models.tenant.status.${that.tenantConstants.status[item.status]}`;
                  item.name = item.first_name + ' ' + item.last_name;
                  item.address = item.address? item.address['street'] + ' ' + item.address['house_num']:'';
                  item.status_class_suffix = that.tenantConstants.status[item.status];
                  return item;
                });
                that.items = items;
              }).catch(function (error) {
                  console.log(error);
              })
            }
        },
        created() {
          this.fetchData();
        }
    }
</script>
