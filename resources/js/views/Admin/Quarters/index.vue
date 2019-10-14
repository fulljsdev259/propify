<template>
    <div class="quarters">
        <heading :title="$t('models.quarter.title')" icon="icon-share" shadow="heavy">
            <template v-if="$can($permissions.create.quarter)">
                <el-button @click="add" icon="ti-plus" round size="mini" type="primary">
                    {{$t('models.quarter.add')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.quarter)">
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
                i18nName: 'quarter',
                header: [{
                    label: 'general.name',
                    width: 230,
                    prop: 'name'
                }, 
                // {
                //     label: 'models.quarter.count_of_buildings',
                //     prop: 'count_of_buildings'
                // }, 
                {
                    label: 'models.quarter.buildings_count',
                    prop: 'buildings_count'
                }, {
                    label: 'models.quarter.total_units_count',
                    prop: 'total_units_count'
                }, {
                    label: 'models.quarter.occupied_units_count',
                    prop: 'occupied_units_count'
                }, {
                    label: 'models.quarter.active_residents_count',
                    prop: 'active_residents_count'
                }, {
                    width: 120,
                    actions: [{
                        type: '',
                        icon: 'ti-pencil',
                        title: 'general.actions.edit',
                        editUrl: 'adminQuartersEdit',
                        onClick: this.edit,
                        permissions: [
                            this.$permissions.update.quarter
                        ]
                    }]
                }],
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
                }
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
                        name: this.$t('general.filters.states'),
                        type: 'select',
                        key: 'state_id',
                        data: this.states,
                    }
                ]
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
        },
        created() {
            this.getState();
        },
        mounted() {
            this.$root.$on('changeLanguage', () => {
                this.getState();
            });
        },
    }
</script>
