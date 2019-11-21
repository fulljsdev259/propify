<template>
    <div class="quarters">
        <heading :title="$t('models.quarter.title')" icon="icon-share" shadow="heavy" class="padding-right-300">
            <template>
                <list-check-box />
            </template>
            <template v-if="$can($permissions.create.quarter)">
                <el-button 
                    @click="add" 
                    icon="ti-plus" 
                    size="mini"
                    class="transparent-button"
                >
                    {{$t('models.quarter.add')}}
                </el-button>
            </template>
            <template>
                <list-field-filter :fields="header" @field-changed="fields=$event"  @order-changed="header=$event"></list-field-filter>
            </template>
           
            <template>
                <el-dropdown placement="bottom" trigger="click" @command="handleMenuClick">
                    <i class="el-icon-more" style="transform: rotate(90deg)"></i>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item 
                            v-if="$can($permissions.assign.manager)" 
                            icon="ti-user"
                            command="assign"
                            :disabled="!selectedItems.length"
                        >
                            {{$t('models.building.assign_managers')}}
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
            @selectionChanged="selectionChanged"
        >
        </list-table>
        <el-dialog :close-on-click-modal="false" :title="$t('models.building.assign_managers')"
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
                <el-button @click="assignManagers" size="mini" type="primary">{{$t('models.building.assign_managers')}}</el-button>
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
                    label: 'models.quarter.type',
                    prop: 'types'
                }, {
                    label: 'models.building.request_status',
                    withCounts: true,
                    width: 230,
                    counts: [{
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
                },{
                    label: 'models.quarter.buildings_count',
                    prop: 'buildings_count'
                }, {
                    label: 'models.quarter.total_units_count',
                    prop: 'count_of_apartments_units'
                }, {
                    label: 'general.filters.status',
                    withStatus: true,
                    data: [ {
                            prop: 'requests_received_count',
                            background: '#bbb',
                            color: '#fff',
                            label: this.$t('models.request.status.received')
                        }, {
                            prop: 'requests_assigned_count',
                            background: '#ebb563',
                            color: '#fff',
                            label: this.$t('models.request.status.assigned')
                        }
                    ]
                }, {
                    label: 'models.request.assigned_to',
                    withStatus: true,
                    data: [ {
                            prop: 'name',
                            background: '#67C23A',
                            color: '#fff',
                            label: this.$t('models.request.status.received')
                        }
                    ]
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
                        key: 'quarter_id',
                        data: this.quarter,
                        searchBox: true,
                    },{
                        name: this.$t('models.quarter.project_ort'),
                        type: 'select',
                        key: 'city_id',
                        data: this.cities,
                    },{
                        name: this.$t('models.quarter.type'),
                        type: 'select',
                        key: 'quarter_type',
                        data: this.types,
                        searchBox: true,
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
                const buildingsResp = await this.getBuildings({get_all: true, quarter_id: quarter.id});
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
        },
        async created() {
            this.getRoles();
            this.getTypes();
            this.quarter = await this.fetchRemoteQuarters();
        },
        mounted() {
            this.$root.$on('changeLanguage', () => {
                this.getState();
            });
        },
    }
</script>
