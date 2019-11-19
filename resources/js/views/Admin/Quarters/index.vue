<template>
    <div class="quarters">
        <heading :title="$t('models.quarter.title')" icon="icon-share" shadow="heavy" class="padding-right-300">
            <template>
                <list-field-filter :fields="header" @field-changed="fields=$event"  @order-changed="header=$event"></list-field-filter>
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
            <template v-if="$can($permissions.delete.quarter)">
                <el-button 
                    :disabled="!selectedItems.length" 
                    @click="batchDeleteWithIds" 
                    icon="ti-trash" 
                    size="mini"
                    class="transparent-button"
                >
                    {{$t('general.actions.delete')}}
                </el-button>
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
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import ListTableMixin from 'mixins/ListTableMixin';
    import {mapActions} from 'vuex';


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
            Heading
        },
        mixins: [mixin],
        data() {
            return {
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
                    label: 'models.quarter.buildings_count',
                    prop: 'buildings_count'
                }, {
                    label: 'models.quarter.total_units_count',
                    prop: 'total_units_count'
                }, 
                // {
                //     width: 150,
                //     actions: [{
                //         type: '',
                //         icon: 'ti-search',
                //         title: 'general.actions.edit',
                //         editUrl: 'adminQuartersEdit',
                //         onClick: this.edit,
                //         permissions: [
                //             this.$permissions.update.quarter
                //         ]
                //     }]
                // }
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
                    },{
                        name: this.$t('models.quarter.project_ort'),
                        type: 'select',
                        key: 'city_id',
                        data: this.cities,
                    },{
                        name: this.$t('models.quarter.type'),
                        type: 'select',
                        key: 'type_id',
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
            computedItems() {
                if(this.items != undefined)
                    this.items.forEach((item) => {
                        item.types = this.$t(`models.quarter.types.${this.$constants.quarters.type[item.types]}`);
                    });
                return this.items;
            }
        },
        methods: {
            ...mapActions(['getBuildings']),
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
