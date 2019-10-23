<template>
    <div class="audit">
        <el-col class="filter-col" v-if="showFilter">
            <el-divider :content-position="filterPosition">
                    <el-popover
                    popper-class="popover-filter"
                    placement="bottom"
                    width="200"
                    trigger="click">
                        <el-button type="success" icon="icon-filter" size="mini" slot="reference" plain round>{{$t('resident.filters')}}</el-button>
                        <filters ref="filters" :data="filters.data" :schema="filters.schema" @changed="filtersChanged" @update:data="filterReset" />
                        <el-button size="mini" icon="icon-eraser" @click="filterReset" type="success">{{$t('resident.reset_filters')}}</el-button>
                  </el-popover>
            </el-divider>
        </el-col>
        <placeholder :src="require('img/5ce8f4e279cb2.png')" v-if="isEmpty">
            {{$t('resident.no_data.activity')}}
            <small>{{$t('resident.no_data_info.activity')}}</small>
        </placeholder>
            <el-timeline v-else>
                <template v-for="(audit,id) in list">                               
                    <el-timeline-item  :key="audit.id" :timestamp="`${audit.user.name} • ${formatDatetime(audit.updated_at)}`">
                        <span>{{audit.statement}}</span>
                    </el-timeline-item>
                </template>
                <el-timeline-item v-if="loading">
                    {{$t('resident.loading')}}
                </el-timeline-item>
                <div v-if="meta.current_page < meta.last_page">
                    <el-button @click="loadMore" size="mini" style="margin-top: 15px" type="text">{{$t('general.load_more')}}</el-button>
                </div>
            </el-timeline>
    </div>
</template>

<script>
    import Placeholder from 'components/Placeholder'
    import {format} from 'date-fns'
    import queryString from 'query-string'
    import FormatDateTimeMixin from 'mixins/formatDateTimeMixin'
    import { EventBus } from '../../../event-bus.js';
    import sFilter from './filters.json';
    export default {
        mixins: [FormatDateTimeMixin],

        props: {
            id: {
                type: Number
            },
            filterPosition: {
                default: 'right',
                type: String,
                validator: filterPosition => ['left', 'right'].includes(filterPosition)
            },
            showFilter: Boolean,
            type: {
                type: String,
                validator: type => ['pinboard', 'listing', 'request', 'unit', 'quarter', 'building', 'manager', 'resident', 'provider'].includes(type)
            }
        },
        components: {
            Placeholder
        },
        data () {
            const filterSchema = [];

            const filterData = {
                event: null,
                auditable_type: null,
            };
            return {
                list: [],
                meta: {},
                filters: {
                    schema: filterSchema,
                    data: filterData
                },
                sFilter: sFilter,
                categories: [],
                loading: true,                
            }
        },
        methods: {
            async filterReset(){
                let schema_children = [];
                let filter_name = '';
                this.filters = {
                    schema: [],
                    data: {
                        event: null,
                        auditable_type: null,
                    }
                };
                schema_children.push({
                            type: 'el-option',
                            props: {
                                label: 'resident.all',
                                value: null
                            }
                        });                  
                if(this.type){                     
                    // If there is type then only show event options
                    // Get type options from translation files
                    filter_name = 'event'
                    const filter_event_translations = this.sFilter[this.type];                    
                    const filter_event_options = Object.values(filter_event_translations).map((value,key) => {                        
                        // Push to schema array
                        schema_children.push({
                            type: 'el-option',
                            props: {
                                label: `general.components.common.audit.filter.general.${value}`,
                                value: value
                            }
                        })
                    });                    
                }else{                    
                    // If there is no type prop on audit component then show type select
                    // Get filter translations from file
                    filter_name = 'auditable_type'
                    const filter_type_translations = this.$t(`general.components.common.audit.filter.type`);
                    const filter_type_options = Object.keys(filter_type_translations).map((key, index) => {
                    schema_children.push({
                        type: 'el-option',
                        props: {
                            label: filter_type_translations[key],
                            value: key
                            }
                        })
                    });
                }
                this.filters.schema.push({
                    type: 'el-select',
                    title: 'resident.type',
                    name: filter_name,
                    props: {
                        size: 'mini'
                    },
                    children: schema_children
                })
                },
                async filtersChanged (filters) {
                    // If type filter is set search for second select
                    if(filters.auditable_type && filters.auditable_type != ''){
                        let schema_children = [];
                        const filter_event_translations = this.$t(`general.components.common.audit.filter.${filters.auditable_type}`);
                        const filter_event_options = Object.keys(filter_event_translations).map((key, index) => {
                            // Push to schema array
                            schema_children.push({
                                type: 'el-option',
                                props: {
                                    label: filter_event_translations[key],
                                    value: key
                                }
                            })
                        });
                        //remove previous select if exists
                        this.filters.schema.splice(1,1)
                        //remove any set event data in filter
                        if(!Object.keys(filter_event_translations).includes(this.filters.data.event))
                        {
                        this.filters.data.event = null
                        }
                        this.filters.schema.push({
                            type: 'el-select',
                            title: 'Event type',
                            name: 'event',
                            props: {
                                size: 'mini'
                            },
                            children: schema_children
                        });
                    }else{
                        this.filters.schema.splice(1,1)
                        if(this.filters.schema.findIndex(x => x.name == 'type') != -1){
                            this.filters.data.event = null
                        }
                    }
                    this.list = undefined
                    this.meta.current_page = undefined
                    await this.fetch();
            },
            async fetch (page = 1,params) {
                // Get current page and last page of the displayed audits
                this.loading = true                

                const auditable_type = this.type ? this.type : this.filters.data.auditable_type
                // Fetch audits
                try {
                    const resp = await this.axios.get('audits?' + queryString.stringify({
                        sortedBy: 'desc',
                        orderBy: 'created_at',
                        page,
                        per_page: 5,
                        auditable_id: this.id,
                        auditable_type: auditable_type,
                        event: this.filters.data.event,
                        ...params,
                    }))  
                    this.meta = _.omit(resp.data.data, 'data');                  
                    if(!resp.data.data.data) {
                        this.list = []
                    }
                    else{                        
                        if (page === 1) {                            
                            this.list = resp.data.data.data;
                        } else {                            
                            this.list.push(...resp.data.data.data);
                        }
                        EventBus.$emit('audit-get-counted', resp.data.data.total);                
                    }
                }catch (e) {
                    this.list = []
                    console.log(e);
                } finally {
                    this.loading = false;
                }                                             
            },
            loadMore() {
                if (this.meta.current_page < this.meta.last_page) {
                    this.fetch(this.meta.current_page + 1);
                }                
            }
        },
        computed: {
            isEmpty () {
                return !this.loading && !Object.keys(this.list).length
            }
        },
        async mounted () {
            // const {data:{data}} = await this.axios.get('requestCategories/tree?get_all=true');
            // // Get filter options from translation file and add the to filter object

            // const flattenCategories = categories => categories.reduce((obj, category) => {
            //     obj[category.id] = category.name_en.toLowerCase().replace(/ /g,"_");

            //     if (category.categories) {
            //         obj = {...obj, ...flattenCategories(category.categories)}

            //         delete category.categories;
            //     }
            //     return obj
            // }, {})

            // this.categories = flattenCategories(data)
            this.categories = this.$constants.requests.categories_data.tree
            await this.filterReset();
        }
    }
</script>

<style lang="scss" scoped>
    .audit {
        height: 100%;
        display: flex;
        flex-direction: column;
        .placeholder {
            font-size: 16px;
            small {
                color: darken(#fff, 28%);
            }
        }
        .el-divider__text.is-left{
            left: 0;
            padding:0;
        }
        .el-divider__text.is-right{
            right: 0;
            padding:0;
        }
        .filter-col{
            padding-left: 0 !important;
            padding-right: 0 !important;
            padding-bottom: 20px;

        }
        .el-timeline {
            padding: 0 0 0 1px;
            height: 100%;
            overflow-y: auto;
            overflow-x: hidden;
            .audit-timestamp{
                font-size:11px;
                color:#9e9e9e;
            }
        }

}
</style>
<style lang="scss">
.popover-filter {
    .el-button [class*="icon-"] + span{
        margin-left: 5px;
    }
    .el-button{
        width: 100%;
        margin-top: 10px;
    }
}
</style>