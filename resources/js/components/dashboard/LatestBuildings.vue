<template>
    <div class="latest-buildings dashboard-table">
        <div class="link-container">
            <router-link :to="{name: 'adminBuildings'}">
                <span class="title">{{ $t('dashboard.buildings.go_to_buildings') }} </span>
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
                    type: 'plain',
                    label: 'general.name',
                    prop: 'name',
                    minWidth: '280px'
                }, {
                    type: 'plain',
                    label: 'models.building.units',
                    prop: 'units_count',
                    
                }, {
                    type: 'users',
                    label: 'general.residents',
                    prop: 'residents',
                }, {
                    type: 'actions',
                    label: 'dashboard.actions',
                    width: 130,
                    actions: [ 
                        {
                            type: 'default',
                            title: 'general.actions.edit',
                            // onClick: this.edit,
                            editUrl: 'adminBuildingsEdit',
                            permissions: [
                                this.$permissions.update.building
                            ]
                        }
                    ]
                }],
            };
        },
        methods: {
            // edit({id}) {
            //     this.$router.push({
            //         name: 'adminBuildingsEdit',
            //         params: {
            //             id
            //         }
            //     });
            // },
            fetchData() {
                let that = this;
                const url = 'buildings/?&page=1&per_page=5';
                return axios.get(url)
                .then(function (response) {
                    that.items = response.data.data.data;
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
