<template>
    <div class="residents list-view">
        <heading :title="$t('general.resident')" 
                    icon="icon-group" 
                    shadow="heavy" 
                    :searchBar="true"
                    @search-change="search=$event">
            <template v-if="$can($permissions.create.resident)">
                <el-button 
                    @click="add" 
                    icon="ti-plus" 
                    size="mini"
                    class="el-button--transparent"
                >
                    {{$t('models.resident.add')}}
                </el-button>
            </template>
            <template>
                <list-field-filter :fields="header" @field-changed="fields=$event" @order-changed="header=$event"></list-field-filter>
            </template>
            <!-- <template v-if="$can($permissions.delete.resident)">
                <el-button 
                    :disabled="!selectedItems.length" 
                    @click="batchDeleteWithIds" 
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
                                v-if="$can($permissions.delete.resident)"
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
                :searchText="search"
                @selectionChanged="selectionChanged"
        >
        </list-table>
    </div>
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import Heading from 'components/Heading';
    import ListTableMixin from 'mixins/ListTableMixin';
    import getFilterStates from 'mixins/methods/getFilterStates';
    import getFilterQuarters from 'mixins/methods/getFilterQuarters';

    const mixin = ListTableMixin({
        actions: {
            get: 'getResidents',
            delete: 'deleteResidentWithIds'
        },
        getters: {
            items: 'residents',
            pagination: 'residentsMeta'
        }
    });

    export default {
        name: 'AdminResidents',
        mixins: [mixin, getFilterStates, getFilterQuarters],
        components: {
            Heading
        },
        data() {
            return {
                header: [{
                    label: 'general.filters.status',
                    withResidentStatusSign: true,
                    prop: 'status',
                    width: 130
                }, {
                    label: 'general.first_name',
                    prop: 'first_name',
                    width: 200
                }, {
                    label: 'general.last_name',
                    prop: 'last_name',
                    width: 200
                }, {
                    label: 'general.email',
                    prop: 'user.email',
                }, {
                    label: 'general.mobile',
                    prop: 'mobile_phone',
                }, {
                    label: 'models.resident.business_relation',
                    withResidentTypes: true,
                    prop: 'types',
                }/*, {
                    label: 'general.id',
                    prop: 'id',
                    width: 100
                }, {
                    label: 'general.name',
                    withAvatars: true,
                    props: ['user']
                }, {
                    label: 'models.resident.contact_info_card',
                    withMultipleProps: true,
                    props: ['user_email', 'private_phone']
                }*//*, {
                    label: 'models.resident.relation.title',
                    withCounts: true,
                    counts: [
                        {
                            prop: 'total_relations_count',
                            background: '#bbb',
                            color: '#fff',
                            label: this.$t('models.resident.status.total')
                        }, {
                            prop: 'active_relations_count',
                            background: '#5fad64',
                            color: '#fff',
                            label: this.$t('models.resident.status.active')
                        }, {
                            prop: 'inactive_relations_count',
                            background: '#dd6161',
                            color: '#fff',
                            label: this.$t('models.resident.status.not_active')
                        }
                    ]
                }*/, {
                    label: 'general.request_status',
                    withCounts: true,
                    width: 230,
                    prop: 'request_count'
                },/*, {
                    label: 'models.resident.status.label',
                    prop: 'status',
                    i18nPath: 'models.resident.status',
                    class: 'rounded-select',
                    ShowCircleIcon: true,
                    select: {
                        icon: 'ti-pencil',
                        data: [],
                        getter: "residents",
                        onChange: this.listingSelectChangedNotify
                    }
                }*//*, {
                    width: 120,
                    actions: [{
                        icon: 'ti-pencil',
                        type: 'success',
                        title: this.$t('general.actions.edit'),
                        onClick: this.edit,
                        permissions: [
                            this.$permissions.update.resident
                        ]
                    }]
                }*//*, {
                    width: 130,
                    actions: [{
                        type: '',
                        icon: 'ti-pencil',
                        title: 'models.resident.view',
                        onClick: this.view,
                        permissions: [
                            this.$permissions.view.resident
                        ]
                    }]
                }*/],
                buildings:[],
                units:[],
                states:[],
                quarters:[],
                isLoadingFilters: false,
                search: '',
            };
        },
        async created(){
            this.isLoadingFilters = true;

            const states = await this.axios.get('states?filters=true')
            this.states = states.data.data;

            this.isLoadingFilters = false;

            this.buildings = await this.fetchRemoteBuildings();
            this.units = await this.fetchRemoteUnits();
            this.quarters = await this.fetchRemoteQuarters();

        },
        methods: {
            ...mapActions(['getBuildings', 'getQuarters', 'getUnits', 'changeResidentStatus']),
            handleMenuClick(command) {
                if(command == 'delete')
                    this.batchDeleteWithIds();
            },
            add() {
                this.$router.push({
                    name: 'adminResidentsAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminResidentsEdit',
                    params: {
                        id
                    }
                });
            },
            view({id}) {
                this.$router.push({
                    name: 'adminResidentsView',
                    params: {
                        id
                    }
                });
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
            async fetchRemoteUnits(search = '') {
                const buildings = await this.getUnits({get_all: true, search});

                return buildings.data
            },
            listingSelectChangedNotify(row) {
                this.$confirm(this.$t(`general.swal.confirm_change.title`), this.$t('general.swal.confirm_change.warning'), {
                    confirmButtonText: this.$t(`general.swal.confirm_change.confirm_btn_text`),
                    cancelButtonText: this.$t(`general.swal.confirm_change.cancel_btn_text`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading = true;
                        const resp = await this.changeResidentStatus({id: row.id, status: row.status});
                        await this.fetchMore();
                        displaySuccess(resp);
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.loading = false;
                    }
                }).catch(async () => {
                    this.loading = true;
                    await this.fetchMore();
                    this.loading = false;
                });
            },
            prepareFilters(property) {
                return Object.keys(this.residentConstants[property]).map((id) => {
                    return {
                        id: parseInt(id),
                        name: this.$t(`models.resident.${property}.${this.residentConstants[property][id]}`)
                    };
                });
            },
            prepareRelationFilters(property) {
                return Object.keys(this.relationConstants[property]).map((id) => {
                    return {
                        id: parseInt(id),
                        name: this.$t(`models.resident.relation.${property}.${this.relationConstants[property][id]}`)
                    };
                });
            },
            prepareRequestFilters(property) {
                return Object.keys(this.requestConstants[property]).map((id) => {
                    return {
                        id: parseInt(id),
                        name: this.$t(`models.request.${property}.${this.requestConstants[property][id]}`)
                    };
                });
            },
        },
        computed: {
            ...mapState("application", {
                residentConstants(state) {
                    return state.constants.residents;
                },
                requestConstants(state) {
                    return state.constants.requests;
                },
                relationConstants(state) {
                    return state.constants.relations;
                },
            }),
            filters() {
                return [
                    {
                        name: this.$t('general.placeholders.search'),
                        type: 'text',
                        icon: 'el-icon-search',
                        key: 'search'
                    }, {
                        name: this.$t('general.filters.states'),
                        type: 'select',
                        key: 'state_id',
                        data: this.states,
                    }, {
                        name: this.$t('general.filters.buildings'),
                        type: 'select',
                        key: 'building_id',
                        data: this.buildings,
                        remoteLoading: false,
                        fetch: this.fetchRemoteBuildings
                    }, {
                        name: this.$t('general.filters.units'),
                        type: 'select',
                        key: 'unit_id',
                        data: this.units,
                        remoteLoading: false,
                        fetch: this.fetchRemoteUnits
                    }, {
                        name: this.$t('general.filters.quarters'),
                        type: 'select',
                        key: 'quarter_id',
                        data: this.quarters,
                        remoteLoading: false,
                        fetch: this.fetchRemoteQuarters
                    }, {
                        name: this.$t('general.filters.request_status'),
                        type: 'select',
                        key: 'request_status',
                        data: this.prepareRequestFilters("status")
                    }, {
                        name: this.$t('general.filters.status'),
                        type: 'select',
                        key: 'status',
                        data: this.prepareFilters('status'),
                    },
                    /*{
                        name: this.$t('general.filters.language'),
                        type: 'select',
                        key: 'language',
                        data: []
                    },*/
                    {
                        name: this.$t('general.filters.type'),
                        type: 'select',
                        key: 'type',
                        data: this.prepareRelationFilters('type'),
                    }
                ]
            }
        }
    };
</script>
