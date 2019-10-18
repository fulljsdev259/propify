<template>
    <div class="buildings">
        <heading :title="$t('models.building.title')" icon="icon-commerical-building" shadow="heavy">
            <template v-if="$can($permissions.create.building)">
                <el-button @click="add" icon="ti-plus" round size="mini" type="primary">{{$t('models.building.add')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.assign.manager)">
                <el-button :disabled="!selectedItems.length" @click="batchAssignManagers" icon="ti-user" round
                           size="mini"
                           type="info">
                    {{$t('models.building.assign_managers')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.building)">
                <el-button :disabled="!selectedItems.length" @click="batchDeleteBuilding" icon="ti-trash" round size="mini"
                           type="danger">
                    {{$t('general.actions.delete')}}
                </el-button>
            </template>
        </heading>
        <list-table
            :fetchMore="fetchMore"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="header"
            :items="items"
            :loading="{state: loading}"
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
            DeleteBuildingModal
        },
        data() {
            return {
                assignManagersVisible: false,
                deleteBuildingVisible: false,
                processAssignment: false,
                managersForm: {},
                toAssignList: '',
                states:{},
                propertyManagers:{},
                toAssign: [],
                isLoadingFilters: false,
                quarters: {},
                remoteLoading: false,
                delBuildingStatus: -1, // 0: unit, 1: request, 2: both
                header: [{
                    label: 'general.address',
                    withMultipleProps: true,
                    props: ['address_row', 'address_zip']
                }, {
                    label: 'models.building.units',
                    withMultipleProps: true,
                    withLinks: true,
                    route: {
                        name: 'adminBuildingUnits',
                        paramsKeys: {
                            model: 'building',
                            props: ['id']
                        }
                    },
                    props: ['units_count']
                }, {
                    label: 'general.residents',
                    withUsers: true,
                    count: 'residentscount',
                    prop: 'residents'
                }, {
                    label: 'models.building.managers',
                    withUsers: true,
                    prop: 'managers',
                    count: 'managerscount'
                }, {
                    label: 'general.requests',
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
                }, {
                    width: 150,
                    actions: [{
                        type: '',
                        icon: 'ti-pencil',
                        title: 'general.actions.edit',
                        onClick: this.edit,
                        editUrl: 'adminBuildingsEdit',
                        permissions: [
                            this.$permissions.update.building
                        ]
                    }]
                }]
            };
        },
        async created(){
            this.isLoadingFilters = true;
            const states = await this.axios.get('states?filters=true')
            this.states = states.data.data;

            const quarters = await this.axios.get('quarters')
            this.quarters = quarters.data.data.data;

            const propertyManagers = await this.axios.get('propertyManagers?get_all=true')
            this.propertyManagers = propertyManagers.data.data.map((propertyManager) => {
                return {
                    id: propertyManager.id,
                    name: propertyManager.user.name
                }
            });
            this.isLoadingFilters = false;
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
                    },
                    {
                        name: this.$t('general.filters.states'),
                        type: 'select',
                        key: 'state_id',
                        data: this.states
                    },
                    {
                        name: this.$t('general.filters.quarters'),
                        type: 'select',
                        key: 'quarter_id',
                        data: this.quarters
                    },
                    {
                        name: this.$t('general.filters.property_managers'),
                        type: 'select',
                        key: 'manager_id',
                        data: this.propertyManagers
                    },
                    {
                        name: this.$t('general.filters.request_status'),
                        type: 'select',
                        key: 'request_status',
                        data: this.prepareRequestFilters("status")
                    }
                ];
            },
            
        },
        methods: {
            ...mapActions(['getPropertyManagers', 'assignManagerToBuilding', 'deleteBuildingWithIds', 'checkUnitRequestWidthIds']),
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
        }
    };
</script>