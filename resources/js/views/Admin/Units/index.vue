<template>
    <div class="units">
        <heading :title="$t('models.unit.title')" icon="icon-unit" shadow="heavy" class="padding-right-300">
            <template>
                <list-check-box />
            </template>
            <template>
                <list-field-filter :fields="header" @field-changed="fields=$event" @order-changed="header=$event"></list-field-filter>
            </template>
            <template v-if="$can($permissions.create.unit)">
                <el-button 
                    @click="add" 
                    icon="ti-plus" 
                    size="mini"
                    class="transparent-button"
                >
                    {{$t('models.unit.add')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.unit)">
                <el-button 
                    :disabled="!selectedItems.length" 
                    @click="batchDeleteWithIds" 
                    icon="ti-trash" 
                    size="mini"
                    class="transparent-button"
                >
                    {{$t('general.actions.delete')}}
                </el-button>
            </template>
        </heading>
        <list-table
            :fetchMore="fetchMore"
            :fetchMoreParams="fetchParams"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="headerFilter"
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
    import {mapActions, mapGetters} from 'vuex';
    import ListTableMixin from 'mixins/ListTableMixin';
    import getFilterQuarters from 'mixins/methods/getFilterQuarters';
    import getFilterStates from "mixins/methods/getFilterStates";
    import getFilterPropertyManager from "mixins/methods/getFilterPropertyManager";
    import ListCheckBox from 'components/ListCheckBox';


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
            Heading,
            ListCheckBox,
        },
        mixins: [mixin, getFilterQuarters, getFilterStates, getFilterPropertyManager],
        data() {
            return {
                isReady: false,
                fetchParams: {},
                states:{},
                propertyManagers:{},
                types:[],
                quarters:[],
                buildings:[],
                cities: [],
                quarterTypes: [],
                roles:[],
                header: [{
                    label: 'models.quarter.quarter_format',
                    prop: 'internal_quarter_id'
                }, {
                    label: 'models.unit.unit_id',
                    prop: 'name'
                }, {
                    label: 'models.unit.type.label',
                    prop: 'formatted_type_label'
                }, {
                    label: 'models.unit.floor',
                    prop: 'floor'
                },{
                    label: 'models.unit.room_no',
                    prop: 'room_no'
                },
                {
                    label: 'models.building.request_status',
                    withCounts: true,
                    counts: [
                        {
                            prop: 'requests_count',
                            background: '#aaa',
                            color: '#fff',
                            label: this.$t('dashboard.requests.total_request')
                        }, {
                            prop: 'requests_received_count',
                            background: '#bbb',
                            color: '#fff',
                            label: this.$t('models.request.status.received')
                        }, {
                            prop: 'requests_assigned_count',
                            background: '#ebb563',
                            color: '#fff',
                            label: this.$t('models.request.status.assigned')
                        }, {
                            prop: 'requests_in_processing_count',
                            background: '#ebb563',
                            color: '#fff',
                            label: this.$t('models.request.status.in_processing')
                        }, {
                            prop: 'requests_reactivated_count',
                            background: '#ebb563',
                            color: '#fff',
                            label: this.$t('models.request.status.reactivated')
                        }, {
                            prop: 'requests_done_count',
                            background: '#67C23A',
                            color: '#fff',
                            label: this.$t('models.request.status.done')
                        }, {
                            prop: 'requests_archived_count',
                            background: '#67C23A',
                            color: '#fff',
                            label: this.$t('models.request.status.archived')
                        }
                    ]
                },
                ],
                building: {},
                isLoadingFilters: false,
            };
        },
        methods: {
            ...mapActions(['getBuildings', 'getPropertyManagers', 'getQuarters']),

            async getRoles() {
                this.roles = [];
                this.roles.push({
                    id: 1,
                    name: this.$constants.propertyManager.type[1],
                })
            },
            async getTypes() {
                this.types = [];
                for(let item in this.$constants.quarters.type) {
                    this.types.push({
                        id: item,
                        name: this.$t(`models.quarter.types.${this.$constants.quarters.type[item]}`),
                    })
                }
            },
            async fetchRemoteQuarters(search = '') {
                const quarters = await this.getQuarters({get_all: true, search});

                return quarters.data
            },
            async fetchRemoteBuildings(search = '') {
                const buildings = await this.getBuildings({get_all: true, search});

                return buildings.data
            },
            async fetchRemotePropertyManagers(search = '') {
                const propertyManagers = await this.getPropertyManagers({get_all: true, search});

                return propertyManagers.data
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
            ...mapGetters('application', {
                constants: 'constants'
            }),
            title() {
                return `${this.building.name} - ${this.$t('general.admin_menu.units')}`;
            },
            formattedItems() {
                return this.items.map((unit) => {
                    unit.residents = unit.relations.map(relation => relation.resident)
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
                    }, {
                        name: this.$t('general.filters.quarters'),
                        type: 'select',
                        key: 'quarter_id',
                        data: this.quarters,
                    },{
                        name: this.$t('models.quarter.project_ort'),
                        type: 'select',
                        key: 'city_id',
                        data: this.cities,
                    },{
                        name: this.$t('models.quarter.type'),
                        type: 'select',
                        key: 'type_id',
                        data: this.types,
                    },{
                        name: this.$t('general.roles.manager'),
                        type: 'select',
                        key: 'role',
                        data: this.roles
                    },{
                        name: this.$t('general.filters.saved_filters'),
                        type: 'select',
                        key: 'saved_filter',
                        data: []
                    }
                ]
            },
        },
        async created() {
            this.isLoadingFilters = true;
            this.isReady = true;

            this.getTypes();
            this.getRoles();
            this.isLoadingFilters = false;

            this.quarters = await this.fetchRemoteQuarters();
            this.buildings = await this.fetchRemoteBuildings();
            this.propertyManagers = await this.fetchRemotePropertyManagers();
        }
    }
</script>
