<template>
    <div class="buildings">
        <heading :title="$t('models.building.title')" icon="icon-commerical-building" shadow="heavy" :searchBar="true" @search-change="search=$event">
            <template>
                <list-check-box />
            </template>
            <template>
                <list-field-filter :fields="header" @field-changed="fields=$event" @order-changed="header=$event"></list-field-filter>
            </template>
            <template v-if="$can($permissions.create.building)">
                <el-button 
                    @click="add" 
                    icon="ti-plus" 
                    size="mini" 
                    class="transparent-button"
                >
                    {{$t('models.building.add')}}
                </el-button>
            </template>
            <!-- <template v-if="$can($permissions.assign.manager)">
                <el-button 
                    :disabled="!selectedItems.length" 
                    @click="batchAssignManagers" 
                    icon="ti-user"
                    size="mini"
                    class="transparent-button"
                >
                    {{$t('models.building.assign_managers')}}
                </el-button>
            </template> -->
            <template>
                <el-dropdown placement="bottom" trigger="click" @command="handleMenuClick">
                    <el-button size="mini" class="transparent-button menu-button">
                        <i class="el-icon-more" style="transform: rotate(90deg)"></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item
                            v-if="$can($permissions.delete.building)"
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
            :items="items"
            :loading="{state: loading}"
            :searchText="search"
            :isLoadingFilters="{state: isLoadingFilters}"
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

        <DeleteBuildingModal 
            :deleteBuildingVisible="deleteBuildingVisible"
            :delBuildingStatus="delBuildingStatus"
            :closeModal="closeDeleteBuildModal"
            :deleteSelectedBuilding="deleteSelectedBuilding"
        />
    </div>
</template>

<script>
    import {mapState, mapActions} from 'vuex';
    import Heading from 'components/Heading';
    import ListTableMixin from 'mixins/ListTableMixin';
    import getFilterStates from 'mixins/methods/getFilterStates';
    import getFilterQuarters from 'mixins/methods/getFilterQuarters';
    import getFilterPropertyManager from 'mixins/methods/getFilterPropertyManager';
    import {displaySuccess, displayError} from "helpers/messages";
    import DeleteBuildingModal from 'components/DeleteBuildingModal';
    import ListCheckBox from 'components/ListCheckBox';

    const mixin = ListTableMixin({
        actions: {
            get: 'getBuildings',
            delete: 'deleteBuilding'
        },
        getters: {
            items: 'buildings',
            pagination: 'buildingsMeta'
        }
    });

    export default {
        mixins: [mixin, getFilterStates, getFilterQuarters, getFilterPropertyManager],
        components: {
            Heading,
            DeleteBuildingModal,
            ListCheckBox,
        },
        data() {
            return {
                assignManagersVisible: false,
                deleteBuildingVisible: false,
                processAssignment: false,
                managersForm: {},
                toAssignList: '',
                states: [],
                propertyManagers:{},
                toAssign: [],
                isLoadingFilters: false,
                quarters: [],
                cities: [],
                quarterTypes: [],
                roles:[],
                search: '',
                remoteLoading: false,
                delBuildingStatus: -1, // 0: unit, 1: request, 2: both
                header: [{
                    label: 'models.building.building_format',
                    prop: 'internal_quarter_id',
                }, {
                    label: 'models.building.building_no',
                    prop: 'address.house_num'
                }, {
                    label: 'models.building.type',
                    prop: ''
                }, {
                    label: 'models.building.units',
                    prop: 'units_count'
                }, {
                    label: 'general.box_titles.managers',
                    withUsers: true,
                    prop: 'managers',
                    count: 'managerscount'
                }, {
                    label: 'models.building.request_status',
                    withCounts: true,
                    width: 230,
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
                },  {
                    label: 'models.building.active_residents_count',
                    prop: 'count_of_apartments_units'
                },{
                    label: 'general.filters.status',
                    withStatus: true,
                    data: [ {
                            prop: 'requests_received_count',
                            background: '#67C23A',
                            color: '#fff',
                            label: this.$t('models.request.status.received')
                        }, {
                            prop: 'requests_assigned_count',
                            background: '#ebb563',
                            color: '#fff',
                            label: this.$t('models.request.status.assigned')
                        }
                    ]
                },
                // {
                //     width: 150,
                //     actions: [{
                //         type: '',
                //         icon: 'ti-search',
                //         title: 'general.actions.edit',
                //         onClick: this.edit,
                //         editUrl: 'adminBuildingsEdit',
                //         permissions: [
                //             this.$permissions.update.building
                //         ]
                //     }]
                // }
                ]
            };
        },
        computed: {
            ...mapState("application", {
                requestConstants(state) {
                    return state.constants.requests;
                }
            }),
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
                        key: 'quarter_type',
                        data: this.types,
                    },{
                        name: this.$t('models.building.type'),
                        type: 'select',
                        key: 'building_type',
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
        methods: {
            ...mapActions(['getPropertyManagers', 'assignManagerToBuilding', 'deleteBuildingWithIds', 'checkUnitRequestWidthIds', 'getQuarters']),
            handleMenuClick(command) {
                if(command == 'delete')
                    this.batchDeleteWithIds();
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
            async fetchRemotePropertyManagers(search = '') {
                const propertyManagers = await this.getPropertyManagers({get_all: true, search});

                return propertyManagers.data
            },
            prepareRequestFilters(property) {
                return Object.keys(this.requestConstants[property]).map((id) => {
                    return {
                        id: parseInt(id),
                        name: this.$t(`models.request.${property}.${this.requestConstants[property][id]}`)
                    };
                });
            },
            units(building) {
                this.$router.push({
                    name: 'adminBuildingUnits',
                    params: {
                        building,
                        id: building.id
                    }
                });
            },
            add() {
                this.$router.push({
                    name: 'adminBuildingsAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminBuildingsEdit',
                    params: {
                        id
                    }
                });
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
            async batchDeleteBuilding() {
                try {                    
                    const resp = await this.checkUnitRequestWidthIds({ids:_.map(this.selectedItems, 'id')});                    
                    this.delBuildingStatus = resp.data;

                    if(this.delBuildingStatus == -1) {
                        this.$confirm(this.$t('general.swal.delete.text'), this.$t('general.swal.delete.title'), {
                            type: 'warning'
                        }).then(() => {
                            Promise.all(this.selectedItems.map((item) => {
                                return this.deleteBuilding(item)
                                    .then(r => {
                                        displaySuccess(r);
                                    })
                                    .catch(err => displayError(err));
                            })).then(() => {
                                this.fetchMore();
                            })
                        }).catch(() => {
                        });
                    }else {
                        this.deleteBuildingVisible = true;
                    }
                } catch(err) {
                    displayError(err);
                } finally {
                }
            },     
            async deleteSelectedBuilding(isUnits, isRequests) {
                try {
                    const resp = await this.deleteBuildingWithIds({
                        ids: _.map(this.selectedItems, 'id'),
                        is_units: isUnits,
                        is_requests: isRequests
                    });
                    this.deleteBuildingVisible = false;
                    displaySuccess(resp);                    
                } catch (err) {
                    displayError(err);
                } finally {
                    this.fetchMore();
                }
            },
            closeDeleteBuildModal() {
                this.deleteBuildingVisible = false;
            },            
        },
        async created() {
            this.isLoadingFilters = true;
            this.getRoles();
            this.getTypes();
            this.isLoadingFilters = false;
            this.quarters = await this.fetchRemoteQuarters();
            this.propertyManagers = await this.fetchRemotePropertyManagers();
        }
    };
</script>

<style lang="scss" scoped>

    /deep/ .el-dialog {
        /deep/ .el-dialog__body {
            padding: 10px 20px;
            word-break: break-word;
        }
    }
</style>