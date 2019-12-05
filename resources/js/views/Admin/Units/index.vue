<template>
    <div class="units list-view">
        <heading :title="$t('models.unit.title')" icon="icon-unit" shadow="heavy" :searchBar="true" @search-change="search=$event">
            <template v-if="$can($permissions.create.unit)">
                <el-button 
                    @click="add" 
                    icon="ti-plus" 
                    size="mini"
                    class="el-button--transparent mr-0"
                >
                    {{$t('models.unit.add')}}
                </el-button>
            </template>
            <template>
                <list-field-filter :fields="header" @field-changed="fields=$event" @order-changed="header=$event"></list-field-filter>
            </template>
            <template>
                <el-dropdown placement="bottom" trigger="click" @command="handleMenuClick">
                    <el-button size="mini" class="el-button--transparent more-actions">
                        <i class="el-icon-more" style="transform: rotate(90deg)"></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item
                            v-if="$can($permissions.delete.unit)"
                            :disabled="!selectedItems.length" 
                            icon="ti-trash" 
                            command="delete"
                        >
                          {{$t('general.actions.delete')}}
                        </el-dropdown-item>
                    </el-dropdown-menu>
                    </el-dropdown>
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
            :searchText="search"
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
                states:[],
                propertyManagers:[],
                types:[],
                quarters:[],
                buildings:[],
                cities: [],
                quarterTypes: [],
                roles:[],
                search: '',
                header: [{
                    label: 'models.quarter.quarter_format',
                    prop: 'internal_quarter_id'
                }, {
                    label: 'models.unit.unit_id',
                    prop: 'name'
                }, {
                    label: 'general.filters.status',
                    withStatusSign: true,
                    prop: 'status',
                }, {
                    label: 'models.unit.type.label',
                    prop: 'formatted_type_label'
                }, {
                    label: 'models.unit.location',
                    withTranslatedFloor: true,
                    prop: 'floor',
                },{
                    label: 'models.unit.room_no',
                    prop: 'room_no'
                }, {
                    label: 'models.building.request_status',
                    withCounts: true,
                    width: 300,
                    prop: 'request_count'
                }, {
                    label: 'models.unit.appendix',
                    withIcon: true,
                    align: 'center',
                    prop: 'appendix'
                }
                ],
                building: {},
                isLoadingFilters: false,
            };
        },
        methods: {
            ...mapActions(['getBuildings', 'getPropertyManagers', 'getQuarters']),
            handleMenuClick(command) {
                if(command == 'delete')
                    this.batchDeleteWithIds();
            },
            async getCities() {
                const cities = await this.axios.get('cities?get_all=true&units=true');
                this.cities = [];
                cities.data.data.forEach((city) => {
                    this.cities.push({
                        id: city,
                        name: city
                    })
                });
            },
            async getRoles() {
                const roles = await this.axios.get('users?get_all=true&role=manager');
                this.roles = [];
                roles.data.data.forEach((role) => {
                    this.roles.push({
                        id: role.id,
                        name: role.name,
                    })
                })
            },
            async getTypes() {
                this.quarterTypes = [];
                this.types = [];
                for(let item in this.$constants.quarters.type) {
                    this.quarterTypes.push({
                        id: parseInt(item),
                        name: this.$t(`models.quarter.types.${this.$constants.quarters.type[item]}`),
                    })
                }
                for(let item in this.$constants.units.type) {
                    this.types.push({
                        id: parseInt(item),
                        name: this.$t(`models.unit.type.${this.$constants.units.type[item]}`),
                    })
                }
            },
            async fetchRemoteQuarters(search = '') {
                const quarters = await this.getQuarters({get_all: true, search});

                return quarters.data
            },
            async fetchRemoteBuildings(search = '') {
                let buildings = await this.getBuildings({get_all: true, search});

                buildings.data.map( building => {
                    building.name = building.address.street + ' ' + building.address.house_num
                })
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
                        key: 'quarter_ids',
                        data: this.quarters,
                    },{
                        name: this.$t('general.filters.buildings'),
                        type: 'select',
                        key: 'building_id',
                        data: this.buildings,
                    },
                    // {
                    //     name: this.$t('models.building.type'),
                    //     type: 'select',
                    //     key: 'btypes',
                    //     data: this.quarterTypes,
                    // },
                    {
                        name: this.$t('models.unit.type.label'),
                        type: 'select',
                        key: 'types',
                        data: this.types,
                    },{
                        name: this.$t('general.roles.manager'),
                        type: 'select',
                        key: 'user_ids',
                        hidden: true,
                        data: this.roles
                    },{
                        name: this.$t('general.filters.more_filters'),
                        type: 'toggle',
                        key: 'saved_filter',
                    },{
                        name: this.$t('general.filters.my_filters'),
                        type: 'popover',
                        key: 'my_filter',
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
