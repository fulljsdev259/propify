<template>
    <div class="quarters list-view">
        <heading :title="$t('models.quarter.title')" icon="icon-share" shadow="heavy" :searchBar="true" @search-change="search=$event">
            <template>
                <list-check-box @clicked="handleQuickFilterClick(true)" @unclicked="handleQuickFilterClick(false)"/>
            </template>
            <template v-if="$can($permissions.create.quarter)">
                <el-button 
                    @click="add" 
                    icon="ti-plus" 
                    size="mini"
                    class="el-button--transparent mr-0"
                >
                    {{$t('models.quarter.add')}}
                </el-button>
            </template>
            <template>
                <list-field-filter :fields="header" @field-changed="fields=$event"  @order-changed="header=$event"></list-field-filter>
            </template>
           
            <template>
                <el-dropdown placement="bottom" trigger="click" @command="handleMenuClick">
                    <el-button size="mini" class="el-button--transparent more-actions">
                        <i class="el-icon-more" style="transform: rotate(90deg)"></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item 
                            v-if="$can($permissions.assign.manager)" 
                            icon="ti-user"
                            command="assign"
                            :disabled="!selectedItems.length"
                        >
                            {{$t('models.building.assign_persons')}}
                        </el-dropdown-item>
                        <el-dropdown-item
                            v-if="$can($permissions.delete.quarter)"
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
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="headerFilter"
            :items="computedItems"
            :loading="{state: loading}"
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            :searchText="search"
            @selectionChanged="selectionChanged"
        >
        </list-table>
        <el-dialog :close-on-click-modal="false" :title="$t('models.building.assign_persons')"
                   :visible.sync="assignManagersVisible"
                   v-loading="processAssignment" width="30%">
            <el-form :model="managersForm">
                <el-select
                    :loading="remoteLoading"
                    :placeholder="$t('general.placeholders.search')"
                    :remote-method="remoteSearchManagers"
                    class="custom-remote-select"
                    filterable
                    multiple
                    remote
                    reserve-keyword
                    style="width: 100%;"
                    v-model="toAssign"
                >
                    <div class="custom-prefix-wrapper" slot="prefix">
                        <i class="el-icon-search custom-icon"></i>
                    </div>
                    <el-option
                        :key="manager.id"
                        :label="`${manager.first_name} ${manager.last_name}`"
                        :value="manager.id"
                        v-for="manager in toAssignList"/>
                </el-select>
            </el-form>
            <span class="dialog-footer" slot="footer">
                <el-button @click="closeModal" size="mini">{{$t('models.building.cancel')}}</el-button>
                <el-button @click="assignManagers" size="mini" type="primary">{{$t('models.building.assign_persons')}}</el-button>
            </span>
        </el-dialog>

    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import ListTableMixin from 'mixins/ListTableMixin';
    import {mapActions} from 'vuex';
    import ListCheckBox from 'components/ListCheckBox';
    import {displaySuccess, displayError} from "helpers/messages";


    const mixin = ListTableMixin({
        actions: {
            get: 'getQuarters',
            delete: 'deleteQuarterWithIds'
        },
        getters: {
            items: 'quarters',
            pagination: 'quartersMeta'
        }
    });

    export default {
        components: {
            Heading,
            ListCheckBox,
        },
        mixins: [mixin],
        data() {
            return {
                assignManagersVisible: false,
                processAssignment: false,
                managersForm: {},
                toAssignList: '',
                toAssign: [],
                remoteLoading: false,
                states: [],
                search: '',
                quarter:[],
                cities: [],
                quarterTypes: [],
                roles:[],
                i18nName: 'quarter',
                header: [{
                    label: 'models.quarter.quarter_format',
                    prop: 'internal_quarter_id'
                }, 
                // {
                //     label: 'models.quarter.count_of_buildings',
                //     prop: 'count_of_buildings'
                // }, 
                {
                    label: 'models.quarter.url',
                    prop: 'url'
                },  {
                    label: 'models.quarter.project_ort',
                    prop: 'city'
                }, {
                    label: 'models.quarter.types.label',
                    prop: 'types'
                }, {
                    label: 'models.building.request_status',
                    withCounts: true,
                    width: 230,
                    prop: 'request_count'
                },{
                    label: 'models.quarter.buildings_count',
                    prop: 'buildings_count'
                }, {
                    label: 'models.quarter.total_units_count',
                    prop: 'count_of_apartments_units'
                }, {
                    label: 'general.filters.status',
                    withStatus: true,
                    prop: 'status'
                }, {
                    label: 'models.request.assigned_property_managers',
                    withUsers: true,
                    prop: 'users',
                    defaultHide: true
                }
                ],
                model: {
                    id: '',
                    name: '',
                    description: '',
                    buildings: []
                },
                validationRules: {
                    name: [{
                        required: true,
                        message: this.$t('models.quarter.required')
                    }],
                },
            };
        },
        computed: {
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
                        key: 'ids',
                        data: this.quarter,
                        searchBox: true,
                    },{
                        name: this.$t('models.quarter.project_ort'),
                        type: 'select',
                        key: 'cities',
                        data: this.cities,
                    },{
                        name: this.$t('models.quarter.types.label'),
                        type: 'select',
                        key: 'types',
                        data: this.types,
                        searchBox: true,
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
                    },
                    {
                        name: this.$t('general.filters.my_filters'),
                        type: 'popover',
                        key: 'my_filter',
                        data: []
                    }
                ]
            },
            computedItems() {
                if(this.items != undefined)
                    this.items.forEach((item) => {
                        let result = '';
                        for(let i = 0; i < item.types.length; i ++) {
                            result = `${result} ${this.$t(`models.quarter.types.${this.$constants.quarters.type[item.types[i]]}`)}`;
                            if(i < item.types.length - 1) 
                                result = `${result},`;
                        }
                        item.types = result;
                    });
                return this.items;
            }
        },
        methods: {
            ...mapActions(['getBuildings', 'assignManagerToBuilding', 'getPropertyManagers']),
            handleQuickFilterClick(checked) {
                if(checked === true) {
                    let quarter_ids = [];
                    quarter_ids = this.selectedItems.map((item) => {
                        return item.id;
                    });
                    localStorage.setItem('quarter_ids', JSON.stringify(quarter_ids));
                } else {
                    localStorage.setItem('quarter_ids', null);
                }
            },
            batchAssignManagers() {
                this.assignManagersVisible = true;
            },
            closeModal() {
                this.assignManagersVisible = false;
                this.toAssign = [];
                this.toAssignList = [];
            },
            assignManagers() {
                const promises = this.selectedItems.map((building) => {
                    return this.assignManagerToBuilding({
                        id: building.id,
                        managersIds: this.toAssign
                    })
                });

                Promise.all(promises).then((resp) => {
                    this.processAssignment = false;
                    this.closeModal();
                    this.fetchMore();
                    displaySuccess(resp[0]);
                }).catch((error) => {
                    this.processAssignment = false;
                    this.closeModal();
                    displayError(error);
                });
            },
            async remoteSearchManagers(search) {
                if (search === '') {
                    this.resetToAssignList();
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getPropertyManagers({
                            get_all: true,
                            search
                        });

                        this.toAssignList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            resetToAssignList() {
                this.toAssignList = [];
                this.toAssign = [];
            },
            handleMenuClick(command) {
                if(command == 'assign')
                    this.batchAssignManagers();
                else if(command == 'delete')
                    this.batchDeleteWithIds();
            },
            async openEditWithRelation(quarter) {
                this.loading = true;
                let buildingsResp = await this.getBuildings({get_all: true, quarter_id: quarter.id});
                buildingsResp.data.map(building => {
                    building.name = building.address ? building.address.street + ' ' + building.address.house_num : ''
                })
                await this.openEdit(quarter);
                this.$set(this.model, 'buildings', buildingsResp.data);
                this.loading = false;
            },
            add() {
                this.$router.push({
                    name: 'adminQuartersAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminQuartersEdit',
                    params: {
                        id
                    }
                });
            },
            async getState() {
                const states = await this.axios.get('states?filters=true')
                this.states = states.data.data;
            },
            async getCities() {
                const cities = await this.axios.get('cities?get_all=true&quarters=true');
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
                this.types = [];
                for(let item in this.$constants.quarters.type) {
                    this.types.push({
                        id: parseInt(item),
                        name: this.$t(`models.quarter.types.${this.$constants.quarters.type[item]}`),
                    })
                }
            },
            async fetchRemoteQuarters(search = '') {
                const quarters = await this.getQuarters({get_all: true, search});

                return quarters.data
            },
            async fetchRemoteBuildings(search = '') {
                let buildings = await this.getBuildings({get_all: true, search});

                buildings.data.map(building => {
                    building.name = building.address ? building.address.street + ' ' + building.address.house_num : ''
                })
                return buildings.data
            },
            selectionChanged(items) {
                this.selectedItems = items;
            }
        },
        async created() {
            localStorage.setItem('quarter_ids', null);
            this.getRoles();
            this.getTypes();
            this.getCities();
            this.quarter = await this.fetchRemoteQuarters();
        },
        mounted() {
            this.$root.$on('changeLanguage', () => {
                this.getState();
            });
        },
    }
</script>
