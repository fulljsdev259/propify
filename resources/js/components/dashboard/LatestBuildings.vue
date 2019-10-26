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
            :items="buildings"
            :loading="{state: loading}"
            @selectionChanged="selectionChanged"
        >
        </list-table>
    </div>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex';
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
                items: []
            };
        },
        methods: {
            ...mapActions(["getBuildings"]),
            // edit({id}) {
            //     this.$router.push({
            //         name: 'adminBuildingsEdit',
            //         params: {
            //             id
            //         }
            //     });
            // },
            async fetchData() {
                 const buildings = await this.getBuildings({
                    page : 1,
                    per_page : 5
                });

                console.log(buildings.data.data)

                buildings.data.data.map(building => {
                    building.residents = building.contracts.map(contract => contract.resident)
                    building.residentscount = building.residents.length > 2 ? (building.residents.length - 2) : 0;
                    building.residents = building.residents.splice(0, 2);
                })
                
                this.items = buildings.data.data
            }
        },
        computed: {
            ...mapGetters(['buildings']),
        },
        created() {
          this.fetchData();
        }
    }
</script>
