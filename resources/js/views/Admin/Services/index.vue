<template>
    <div class="services">
        <heading :title="$t('models.service.title')" icon="icon-tools" shadow="heavy">
            <template v-if="$can($permissions.create.provider)">
                <el-button @click="add" icon="ti-plus" round size="mini" type="primary">{{$t('models.service.add_title')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.provider)">
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
            Heading
        },
        data() {
            return {
                header: [{
                    label: 'general.name',
                    withMultipleProps: true,
                    props: ['name', 'addr', 'cty']
                }, {
                    label: 'models.service.contact_details',
                    withMultipleProps: true,
                    props: ['email', 'phone']
                }, {
                    label: 'general.requests',
                    withCounts: true,
                }, {
                    width: 120,
                    actions: [{
                        
                        icon: 'ti-pencil',
                        title: 'general.actions.edit',
                        onClick: this.edit,
                        editUrl: 'adminServicesEdit',
                        permissions: [
                            this.$permissions.update.provider
                        ]
                    }]
                }],
                states: {},
                quarters: {},
                buildings: {},
                categories: {},
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
                    }, {
                        name: this.$t('general.filters.categories'),
                        type: 'select',
                        key: 'category',
                        data: this.categories,
                    },
                    // {
                    //     name: this.$t('general.filters.quarters'),
                    //     type: 'select',
                    //     key: 'quarter_id',
                    //     data: this.quarters,
                    // },
                    {
                        name: this.$t('general.filters.language'),
                        type: 'language',
                        key: 'language'
                    }
                ]
            }
        },
        methods: {
            ...mapActions(['getBuildings', 'getRequestCategoriesTree']),
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
            async getStateBuildings() {
                const buildings = await this.getBuildings({
                    get_all: true
                });

                return buildings.data;
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
            this.isLoadingFilters = true;
            const quarters = await this.axios.get('quarters')
            this.quarters = quarters.data.data.data;

            const states = await this.axios.get('states?filters=true')
            this.states = states.data.data;

            this.buildings = await this.getStateBuildings()
            this.categories = await this.getFilterCategories()
            this.isLoadingFilters = false;
        },
    }
</script>
