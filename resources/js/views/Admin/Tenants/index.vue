<template>
    <div class="tenants">
        <heading :title="$t('pages.tenant.title')" icon="icon-group" shadow="heavy">
            <template v-if="$can($permissions.create.resident)">
                <el-button @click="add" icon="ti-plus" round size="mini" type="primary">{{$t('models.tenant.add')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.resident)">
                <el-button :disabled="!selectedItems.length" @click="batchDeleteWithIds" icon="ti-trash" round size="mini"
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
            get: 'getTenants',
            delete: 'deleteTenantWithIds'
        },
        getters: {
            items: 'tenants',
            pagination: 'tenantsMeta'
        }
    });

    export default {
        name: 'AdminTenants',
        mixins: [mixin, getFilterStates, getFilterQuarters],
        components: {
            Heading
        },
        data() {
            return {
                header: [{
                    label: 'general.id',
                    prop: 'id',
                    width: 64
                }, {
                    label: 'general.name',
                    withAvatars: true,
                    props: ['user']
                }, {
                    label: 'models.tenant.contact_info_card',
                    withMultipleProps: true,
                    props: ['user_email', 'private_phone']
                }, {
                    label: 'models.tenant.building.name',
                    withCollapsables: true,
                    width: 200,
                    props: ['building_names']
                }, {
                    label: 'models.tenant.unit.name',
                    withCollapsables: true,
                    width: 150,
                    props: ['unit_names']
                }, {
                    label: 'models.tenant.contract.title',
                    withCounts: true,
                    width: 150,
                    counts: [
                        {
                            prop: 'total_rent_contracts_count',
                            background: '#bbb',
                            color: '#fff',
                            label: this.$t('models.tenant.status.total')
                        }, {
                            prop: 'active_rent_contracts_count',
                            background: '#5fad64',
                            color: '#fff',
                            label: this.$t('models.tenant.status.active')
                        }, {
                            prop: 'inactive_rent_contracts_count',
                            background: '#dd6161',
                            color: '#fff',
                            label: this.$t('models.tenant.status.not_active')
                        }
                    ]
                }, {
                    label: 'models.tenant.status.label',
                    prop: 'status',
                    i18nPath: 'models.tenant.status',
                    class: 'rounded-select',
                    ShowCircleIcon: true,
                    width: 150,
                    select: {
                        icon: 'ti-pencil',
                        data: [],
                        getter: "tenants",
                        onChange: this.listingSelectChangedNotify
                    }
                }/*, {
                    width: 120,
                    actions: [{
                        icon: 'ti-pencil',
                        type: 'success',
                        title: this.$t('general.actions.edit'),
                        onClick: this.edit,
                        permissions: [
                            this.$permissions.update.tenant
                        ]
                    }]
                }*/, {
                    width: 80,
                    actions: [{
                        type: '',
                        icon: 'ti-pencil',
                        title: 'models.tenant.view',
                        onClick: this.view,
                        permissions: [
                            this.$permissions.view.resident
                        ]
                    }]
                }],
                buildings:{},
                units:{},
                states:{},
                quarters:{},
                isLoadingFilters: false,
            };
        },
        async created(){
            this.isLoadingFilters = true;
            const quarters = await this.axios.get('quarters')
            this.quarters = quarters.data.data.data;

            const states = await this.axios.get('states?filters=true')
            this.states = states.data.data;

            this.buildings = await this.getStateBuildings()
            this.units = await this.getBuildingUnits();
            this.isLoadingFilters = false;
        },
        methods: {
            ...mapActions(['getBuildings', 'getUnits', 'changeTenantStatus']),
            add() {
                this.$router.push({
                    name: 'adminTenantsAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminTenantsEdit',
                    params: {
                        id
                    }
                });
            },
            view({id}) {
                this.$router.push({
                    name: 'adminTenantsView',
                    params: {
                        id
                    }
                });
            },
            async getStateBuildings() {
                const buildings = await this.getBuildings({
                    get_all: true
                });

                return buildings.data;
            },
            async getBuildingUnits() {
                const units = await this.getUnits({
                    get_all: true
                });

                return units.data;
            },
            listingSelectChangedNotify(row) {
                this.$confirm(this.$t(`general.swal.confirmChange.title`), this.$t('general.swal.confirmChange.warning'), {
                    confirmButtonText: this.$t(`general.swal.confirmChange.confirmBtnText`),
                    cancelButtonText: this.$t(`general.swal.confirmChange.cancelBtnText`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading = true;
                        const resp = await this.changeTenantStatus({id: row.id, status: row.status});
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
                return Object.keys(this.tenantConstants[property]).map((id) => {
                    return {
                        id: parseInt(id),
                        name: this.$t(`models.tenant.${property}.${this.tenantConstants[property][id]}`)
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
                tenantConstants(state) {
                    return state.constants.tenants;
                },
                requestConstants(state) {
                    return state.constants.requests;
                }
            }),
            filters() {
                return [
                    {
                        name: this.$t('filters.search'),
                        type: 'text',
                        icon: 'el-icon-search',
                        key: 'search'
                    }, {
                        name: this.$t('filters.states'),
                        type: 'select',
                        key: 'state_id',
                        data: this.states,
                    }, {
                        name: this.$t('filters.buildings'),
                        type: 'select',
                        key: 'building_id',
                        data: this.buildings,
                    }, {
                        name: this.$t('filters.units'),
                        type: 'select',
                        key: 'unit_id',
                        data: this.units,
                    }, {
                        name: this.$t('filters.quarters'),
                        type: 'select',
                        key: 'quarter_id',
                        data: this.quarters,
                    }, {
                        name: this.$t('filters.requestStatus'),
                        type: 'select',
                        key: 'request_status',
                        data: this.prepareRequestFilters("status")
                    }, {
                        name: this.$t('filters.status'),
                        type: 'select',
                        key: 'status',
                        data: this.prepareFilters('status'),
                    },
                    {
                        name: this.$t('filters.language'),
                        type: 'language',
                        key: 'language'
                    }
                ]
            }
        }
    };
</script>
