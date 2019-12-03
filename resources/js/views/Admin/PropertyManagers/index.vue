<template>
    <div class="property-managers list-view">
        <heading :title="$t('models.property_manager.title')" 
                icon="icon-users" 
                shadow="heavy" 
                :searchBar="true">
            <template v-if="$can($permissions.create.propertyManager)">            
                <el-button 
                    @click="add" 
                    icon="ti-plus" 
                    size="mini"
                    class="el-button--transparent"
                >
                    {{$t('models.property_manager.add')}}
                </el-button>
            </template>
            <template>
                <list-field-filter :fields="header" @field-changed="fields=$event" @order-changed="header=$event"></list-field-filter>
            </template>
            
            <!-- <template v-if="$can($permissions.delete.propertyManager)">
                <el-button 
                    :disabled="!selectedItems.length" 
                    @click="openDeleteWithReassignment" 
                    icon="ti-trash"
                    size="mini"
                    class="el-button--transparent"
                >
                    {{$t('general.actions.delete')}}
                </el-button>
            </template> -->
            <template>
                <el-dropdown placement="bottom" trigger="click" @command="handleMenuClick">
                    <el-button size="mini" class="el-button--transparent more-actions">
                        <i class="el-icon-more" style="transform: rotate(90deg)"></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item
                                v-if="$can($permissions.delete.propertyManager)"
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
            :isLoadingFilters="{state: isLoadingFilters}"
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            @selectionChanged="selectionChanged"
        >
        </list-table>
        <el-dialog  class="delete_width_reassign_modal" 
                    :close-on-click-modal="false" :title="$t('models.property_manager.delete_with_reassign_modal.title')"
                    :visible.sync="assignManagersVisible"
                    v-loading="processAssignment" width="30%">
            <el-row>
                <el-col :span="24">
                    <p class="description">{{$t('models.property_manager.delete_with_reassign_modal.description')}}</p>
                    <el-select
                        :loading="remoteLoading"
                        :placeholder="$t('general.placeholders.search')"
                        :remote-method="remoteSearchManagers"
                        class="custom-remote-select"
                        filterable
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
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="24">
                    <el-button 
                        :disabled="!toAssign"
                        @click="batchDelete(true)" 
                        size="mini" 
                        type="primary">
                        {{$t('models.property_manager.delete_with_reassign_modal.title')}}
                    </el-button>
                </el-col>
            </el-row> 
            <span class="dialog-footer" slot="footer">
                <el-button @click="closeModal" size="mini">{{$t('models.building.cancel')}}</el-button>                
                <el-button @click="batchDelete(false)" size="mini" type="danger">{{$t('models.property_manager.delete_without_reassign')}}</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import RequestDetailCard from 'components/RequestDetailCard';
    import ListTableMixin from 'mixins/ListTableMixin';
    import {displayError, displaySuccess} from "helpers/messages";
    import {mapActions} from 'vuex';
    import getFilterQuarters from 'mixins/methods/getFilterQuarters';
    import PropertyManagersMixin from 'mixins/adminPropertyManagersMixin';

    const mixin = ListTableMixin({
        actions: {
            get: 'getPropertyManagers',
            delete: 'deletePropertyManager'
        },
        getters: {
            items: 'propertyManagers',
            pagination: 'propertyManagersMeta'
        }
    });

    export default {
        name: 'AdminPropertyManagers',
        mixins: [mixin, getFilterQuarters, PropertyManagersMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            RequestDetailCard
        },
        data() {
            return {
                header: [{
                    label: 'general.filters.status',
                    withPMStatusSign: true,
                    prop: 'status',
                    width: 130
                }, /*{
                    label: 'general.name',
                    prop: 'user.name'
                }, */{
                    label: 'general.name',
                    withAvatars: true,
                    props: ['user']
                }, {
                    label: 'general.email',
                    prop: 'user.email'
                }, {
                    label: 'general.mobile',
                    prop: 'mobile_phone'
                }, {
                    label: 'general.roles.label',
                    prop: 'type',
                    roles: true
                }, {
                    label: 'resident.building',
                    withInternalQuarterIds: true,
                    prop: 'internal_quarter_ids'
                }, {
                    label: 'general.request_status',
                    withCounts: true,
                }, 
                /*{
                    width: 150,
                    actions: [{
                        type: '',
                        icon: 'ti-search',
                        title: 'general.actions.edit',
                        editUrl: 'adminPropertyManagersEdit',
                        onClick: this.edit,
                        permissions: [
                            this.$permissions.update.propertyManager
                        ]
                    }]
                }*/],
                assignManagersVisible: false,
                processAssignment: false,
                toAssignList: '',
                toAssign: '',
                remoteLoading: false,
                quarters: [],
                buildings:[],
                isLoadingFilters: false,
            }
        },
        computed: {
            filters() {
                return [
                    {
                        name: this.$t('general.placeholders.search'),
                        type: 'text',
                        icon: 'el-icon-search',
                        key: 'search'
                    },
                    {
                        name: this.$t('general.filters.quarters'),
                        type: 'select',
                        key: 'quarter_id',
                        data: this.quarters,
                        remoteLoading: false,
                        fetch: this.fetchRemoteQuarters
                    },
                    {
                        name: this.$t('general.filters.buildings'),
                        type: 'select',
                        key: 'building_id',
                        data: this.buildings,
                        remoteLoading: false,
                        fetch: this.fetchRemoteBuildings
                    },
                    {
                        name: this.$t('general.function'),
                        type: 'select',
                        key: 'role',
                        data: this.roles
                    },

                ];
            }
        },
        methods: {
            ...mapActions(["remoteSearchManagers", "batchDeletePropertyManagers", "getBuildings", "getIDsAssignmentsCount"]),
            handleMenuClick(command) {
                if(command == 'delete')
                    this.openDeleteWithReassignment();
            },
            async getFilterBuildings() {
                let buildings = await this.getBuildings({
                    get_all: true
                });

                buildings.data.map(building => {
                    building.name = building.address ? building.address.street + ' ' + building.address.house_num : ''
                })

                return buildings.data;
            },
            add() {
                this.$router.push({
                    name: 'adminPropertyManagersAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminPropertyManagersEdit',
                    params: {
                        id
                    }
                });
            },
            async openDeleteWithReassignment() {
                try {
                    const resp = await this.getIDsAssignmentsCount({
                        ids:_.map(this.selectedItems, 'id')
                    });

                    if(resp.data > 0) {
                        this.assignManagersVisible = true;
                    }else {
                        this.$confirm('Are you sure you want to delete?', 'Confirm?', {
                            confirmButtonText: 'OK',
                            cancelButtonText: 'Cancel',
                            type: 'warning',
                            roundButton: true
                        }).then(() => {
                            this.batchDelete(false);
                        }).catch(() => {
                        });
                    }
                } catch (e) {
                    displayError(e);
                }   
            },
            async batchDelete(withReassign) {
                try {                    
                    const resp = await this.batchDeletePropertyManagers({
                        managerIds: _.map(this.selectedItems, 'id'),
                        assignee: (this.toAssign && withReassign) ? this.toAssign : 0
                    });

                    if (resp) {
                        displaySuccess(resp);
                        this.closeModal();
                        this.fetchMore();
                    }
                } catch (e) {
                    displayError(e);
                }
            },
            closeModal() {
                this.assignManagersVisible = false;
                this.toAssign = '';
                this.toAssignList = [];
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
            async remoteSearchManagers(search) {
                if (search === '') {
                    this.resetToAssignList();
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getPropertyManagers({
                            get_all: true,
                            search,
                            disableCommit: true
                        });

                        this.toAssignList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            async getRoles() {
                this.roles = [];
                for(let role in this.$constants.propertyManager.type) {
                    this.roles.push({
                        id: role,
                        name: this.$t(`general.roles.${this.$constants.propertyManager.type[role]}`),
                    })
                }
            }
            
        },
        async created(){
            this.quarters = await this.fetchRemoteQuarters();
            this.buildings = await this.fetchRemoteBuildings();
        },
        mounted() {
        }
    }
</script>

<style lang="scss" scoped>
    .delete_width_reassign_modal {

        /deep/ .el-dialog__body {
            word-break: break-word;
        }

        .el-row {
            margin-bottom: 20px;
            &:last-child {
            margin-bottom: 0;
            }
        }

        .description {
            margin-top: 0;
        }
    }
</style>
