<template>
    <div class="units">
        <heading :title="$t('models.unit.title')" icon="icon-unit" shadow="heavy">
            <template v-if="$can($permissions.create.unit)">
                <el-button @click="add" icon="ti-plus" round size="mini" type="primary">
                    {{$t('models.unit.add')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.unit)">
                <el-button :disabled="!selectedItems.length" @click="batchDeleteWithIds" icon="ti-trash" round size="mini"
                           type="danger">
                    {{$t('general.actions.delete')}}
                </el-button>
            </template>
        </heading>
        <list-table
            :fetchMore="fetchMore"
            :fetchMoreParams="fetchParams"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="header"
            :items="formattedItems"
            :loading="{state: loading}"
            :isLoadingFilters="{state: isLoadingFilters}"
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            @selectionChanged="selectionChanged"
            v-if="isReady"
        >
        </list-table>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import {mapActions} from 'vuex';
    import ListTableMixin from 'mixins/ListTableMixin';
    import getFilterQuarters from 'mixins/methods/getFilterQuarters';
    import getFilterStates from "mixins/methods/getFilterStates";
    import getFilterPropertyManager from "mixins/methods/getFilterPropertyManager";


    const mixin = ListTableMixin({
        actions: {
            get: 'getUnits',
            delete: 'deleteUnitWithIds'
        },
        getters: {
            items: 'units',
            pagination: 'unitsMeta'
        }
    });

    export default {
        components: {
            Heading
        },
        mixins: [mixin, getFilterQuarters, getFilterStates, getFilterPropertyManager],
        data() {
            return {
                isReady: false,
                fetchParams: {},
                states:{},
                propertyManagers:{},
                quarters:{},
                buildings:{},
                header: [{
                    label: 'models.unit.name',
                    prop: 'name'
                }, {
                    label: 'models.unit.building',
                    prop: 'building.name'
                }, {
                    label: 'models.unit.type.label',
                    prop: 'formatted_type_label'
                }, {
                    label: 'models.unit.floor',
                    prop: 'floor'
                }, {
                    label: 'models.unit.room_no',
                    prop: 'room_no'
                }, {
                    label: 'general.gross_rent',
                    prop: 'monthly_rent_gross'
                }, {
                    label: 'general.resident',
                    withUsers: true,
                    prop: 'residents'
                }, {
                    width: 150,
                    actions: [{
                        type: '',
                        icon: 'ti-search',
                        title: 'general.actions.edit',
                        onClick: this.edit,
                        editUrl: 'adminUnitsEdit',
                        permissions: [
                            this.$permissions.update.unit
                        ]
                    }]
                }],
                building: {},
                isLoadingFilters: false,
            };
        },
        methods: {
            ...mapActions(['getBuildings']),
            async getFilterBuildings() {
                const buildings = await this.getBuildings({
                    get_all: true
                });

                return buildings.data;
            },
            add() {
                this.$router.push({
                    name: 'adminUnitsAdd',
                    params: {
                        id: this.building.id
                    }
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminUnitsEdit',
                    params: {
                        id,
                        buildingId: this.building.id,
                    }
                });
            }
        },
        computed: {
            title() {
                return `${this.building.name} - ${this.$t('general.admin_menu.units')}`;
            },
            formattedItems() {
                return this.items.map((unit) => {
                    unit.residents = unit.contracts.map(contract => contract.resident)
                    unit.formatted_type_label = this.$t(`models.unit.type.${unit.typeLabel}`);
                    return unit
                })
            },
            filters() {
                return [
                    {
                        name: this.$t('general.placeholders.search'),
                        type: 'text',
                        icon: 'el-icon-search',
                        key: 'search'
                    },
                    {
                        name: this.$t('general.filters.states'),
                        type: 'select',
                        key: 'state_id',
                        data: this.states,
                    },
                    {
                        name: this.$t('general.filters.quarters'),
                        type: 'select',
                        key: 'quarter_id',
                        data: this.quarters,
                    },
                    {
                        name: this.$t('general.filters.buildings'),
                        type: 'select',
                        key: 'building_id',
                        data: this.buildings,
                    },
                    {
                        name: this.$t('general.filters.property_managers'),
                        type: 'select',
                        key: 'manager_id',
                        data: this.propertyManagers,
                    },
                    {
                        name: this.$t('general.filters.requests'),
                        type: 'select',
                        key: 'request',
                        data: [{
                            id: 1,
                            name: this.$t('general.filters.open_requests')
                        }]
                    },
                    {
                        name: this.$t('general.filters.type'),
                        type: 'select',
                        key: 'type',
                        data: [{
                            id: 1,
                            name: this.$t('models.unit.type.apartment')
                        },
                        {
                            id: 2,
                            name: this.$t('models.unit.type.business')
                        }]
                    }
                ];
            }
        },
        async created() {
            this.isLoadingFilters = true;
            this.isReady = true;
            const quarters = await this.axios.get('quarters')
            this.quarters = quarters.data.data.data;

            const propertyManagers = await this.axios.get('propertyManagers?get_all=true')
            this.propertyManagers = propertyManagers.data.data.map((propertyManager) => {
                return {
                    id: propertyManager.id,
                    name: propertyManager.user.name
                }
            });

            const states = await this.axios.get('states?filters=true')
            this.states = states.data.data;

            const buildings = await this.axios.get('buildings?get_all=true')
            this.buildings = buildings.data.data;
            this.isLoadingFilters = false;
        }
    }
</script>
