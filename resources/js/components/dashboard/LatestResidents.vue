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
                    type: 'resident-details',
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
                    label: 'models.resident.status.label',
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
                            title: 'models.resident.view',
                            editUrl: 'adminResidentsView',
                            permissions: [
                                this.$permissions.update.resident
                            ]
                        }
                    ]
                }],
            };
        },
        computed: {
            residentConstants() {
                return this.$constants.residents;
            },
        },
        methods: {
            fetchData() {
              let that = this;
              let url = 'residents/latest';
              return axios.get(url)
              .then(function (response) {
                const items = response.data.data.map(item => {
                  item.status_label = `models.resident.status.${that.residentConstants.status[item.status]}`;
                  item.name = item.first_name + ' ' + item.last_name;
                  item.address = item.address? item.address['street'] + ' ' + item.address['house_num']:'';
                  item.status_class_suffix = that.residentConstants.status[item.status];
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
