<template>
    <div class="services list-view">
        <heading :title="$t('models.service.title')"
                 icon="icon-tools"
                 shadow="heavy"
                 :searchBar="true"
                 @search-change="search=$event">
            <template v-if="$can($permissions.create.provider)">
                <el-button
                        @click="add"
                        icon="ti-plus"
                        size="mini"
                        class="el-button--transparent"
                >
                    {{$t('models.service.add_title')}}
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
<!--                        <el-dropdown-item-->
<!--                                v-if="$can($permissions.assign.manager)"-->
<!--                                icon="ti-user"-->
<!--                                command="assign"-->
<!--                                :disabled="!selectedItems.length"-->
<!--                        >-->
<!--                            {{$t('models.building.assign_managers')}}-->
<!--                        </el-dropdown-item>-->
                        <el-dropdown-item
                                v-if="$can($permissions.delete.provider)"
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
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            :searchText="search"
            @selectionChanged="selectionChanged"
        >
        </list-table>
    </div>
</template>

<script>
    import {mapActions} from 'vuex';
    import Heading from 'components/Heading';
    import ListTableMixin from 'mixins/ListTableMixin';
    import getFilterStates from 'mixins/methods/getFilterStates';
    import getFilterQuarters from 'mixins/methods/getFilterQuarters';
    import PrepareCategories from 'mixins/methods/prepareCategories';


    const mixin = ListTableMixin({
        actions: {
            get: 'getServices',
            delete: 'deleteServiceWithIds'
        },
        getters: {
            items: 'services',
            pagination: 'servicesMeta'
        }
    });

    export default {
        name: 'AdminServices',
        mixins: [mixin, getFilterStates, getFilterQuarters, PrepareCategories],
        components: {
            Heading,
        },
        data() {
            return {
                header: [{
                    label: 'general.filters.status',
                    withPMStatusSign: true,
                    prop: 'status',
                    width: 100,
                }, {
                    label: 'models.service.company_name',
                    prop: 'company_name'
                }, {
                    label: 'general.name',
                    withMultiplePropsString: true,
                    props: ['first_name', 'last_name']
                }, {
                    label: 'general.email',
                    prop: 'email'
                }, {
                    label: 'general.mobile_phone',
                    prop: 'mobile_phone'
                }, {
                    label: 'general.function',
                    withServiceCategory: true,
                    prop: 'category'
                }, {
                    label: 'resident.building',
                    withInternalQuarterIds: true,
                    prop: 'internal_quarter_ids'
                }, {
                    label: 'models.building.request_status',
                    withCounts: true,
                    width: 230,
                    counts: [{
                        prop: 'requests_new_count',
                        background: this.$constants.relations.status_colorcode[1],
                        color: '#fff',
                        label: this.$t('models.request.status.new')
                    }, {
                        prop: 'requests_in_processing_count',
                        background: this.$constants.relations.status_colorcode[2],
                        color: '#fff',
                        label: this.$t('models.request.status.in_processing')
                    }, {
                        prop: 'requests_pending_count',
                        background: this.$constants.relations.status_colorcode[3],
                        color: '#fff',
                        label: this.$t('models.request.status.pending')
                    }, {
                        prop: 'requests_done_count',
                        background: this.$constants.relations.status_colorcode[4],
                        color: '#fff',
                        label: this.$t('models.request.status.done')
                    }, {
                        prop: 'requests_warranty_claim_count',
                        background: this.$constants.relations.status_colorcode[5],
                        color: '#fff',
                        label: this.$t('models.request.status.warranty_claim')
                    }, {
                        prop: 'requests_archived_count',
                        background: this.$constants.relations.status_colorcode[6],
                        color: '#fff',
                        label: this.$t('models.request.status.archived')
                    }
                    ]
                }],
                states: [],
                quarters: [],
                buildings: [],
                categories: [],
                search: '',
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
                        name: this.$t('general.function'),
                        type: 'select',
                        key: 'category',
                        data: this.categories,
                    }, {
                        name: this.$t('general.filters.status'),
                        type: 'select',
                        key: 'status',
                        data: this.statuses,
                    }
                    // {
                    //     name: this.$t('general.filters.quarters'),
                    //     type: 'select',
                    //     key: 'quarter_id',
                    //     data: this.quarters,
                    // },
                ]
            },
        },
        methods: {
            ...mapActions(['getBuildings']),
            handleMenuClick(command) {
                if(command == 'assign')
                    this.batchAssignManagers();
                else if(command == 'delete')
                    this.batchDeleteWithIds();
            },
            add() {
                this.$router.push({
                    name: 'adminServicesAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminServicesEdit',
                    params: {
                        id
                    }
                });
            },
            async fetchRemoteBuildings(search = '') {
                let buildings = await this.getBuildings({get_all: true, search});
                buildings.data.map(building => {
                    building.name = building.address ? building.address.street + ' ' + building.address.house_num : ''
                })
                return buildings.data
            },
            async getFilterCategories() {
                let categories = [];

                await _.forEach(this.$constants.serviceProviders.category, (value, index) => {
                    categories.push({
                        name: this.$t(`models.service.category.${value}`),
                        id: index
                    });
                });

                return categories;
            },
        },
        async created(){
            const quarters = await this.axios.get('quarters')
            this.quarters = quarters.data.data.data;

            const states = await this.axios.get('states?filters=true')
            this.states = states.data.data;

            this.categories = await this.getFilterCategories()

            this.statuses = Object.keys(this.$constants.serviceProviders.status).map((id) => {
                return {
                    id: parseInt(id),
                    name: this.$t(`general.status.${this.$constants.serviceProviders.status[id]}`)
                };
            });

            this.buildings = await this.fetchRemoteBuildings();
        },
    }
</script>
