<template>
    <div class="audit" v-infinite-scroll="fetch">
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
            No activity available for now!
            <small>All available activities will appear here in chronological order.</small>
        </placeholder>
            <el-timeline v-else>
                <template v-for="(audit, date) in audits.data">
                    <el-timeline-item v-for="(content, index) in audit.content" :key="audit.id+'-'+index" :timestamp="`${audit.userName} • ${formatDatetime(date)}`">
                        <span v-html="content">{{content}}</span>
                    </el-timeline-item>
                </template>
                <el-timeline-item v-if="loading">
                    {{$t('general.loading')}}
                </el-timeline-item>
            </el-timeline>
    </div>
</template>

<script>
    import Placeholder from 'components/Placeholder'
    import {format} from 'date-fns'
    import queryString from 'query-string'
    import FormatDateTimeMixin from 'mixins/formatDateTimeMixin'
    import Filters from 'components/Filters';
    import { EventBus } from '../event-bus.js';

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
                validator: type => ['pinboard', 'listing', 'request'].includes(type)
            }
        },
        components: {
            Placeholder,
            Filters
        },
        data () {
            const filterSchema = [];

            const filterData = {
                event: null,
                auditable_type: null,
            };
            return {
                audits: {
                    data: {}
                },
                filters: {
                    schema: filterSchema,
                    data: filterData
                },
                categories: [],
                loading: true
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
                                label: this.$t('resident.all'),
                                value: null
                            }
                        });
                if(this.type){
                    // If there is type then only show event options
                    // Get type options from translation files
                    filter_name = 'event'
                    const filter_event_translations = this.$t(`general.components.common.audit.filter.${this.type}`);
                    
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
                    title: this.$t('resident.type'),
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
                    this.audits.data = undefined
                    this.audits.current_page = undefined
                    await this.fetch();
            },
            async fetch (params) {              
                // Get current page and last page of the displayed audits
                const {
                    current_page,
                    last_page
                } = this.audits

                // If current page and last page are set, and current page is the last page then don't fetch the next audits
                if (current_page && last_page &&
                    current_page == last_page) {
                    return;
                }

                // Display loading in timeline
                this.loading = true

                let page = current_page || 0
                page++
                const auditable_type = this.type ? this.type : this.filters.data.auditable_type
                // Fetch audits
                const {data:{data}} = await this.axios.get('audits?' + queryString.stringify({
                    sortedBy: 'desc',
                    orderBy: 'created_at',
                    page,
                    per_page: 25,
                    auditable_id: this.id,
                    auditable_type: auditable_type,
                    event: this.filters.data.event,
                    ...params,
                }))
                
                try{

                // Extract audits from response
                
                let constant_variables = {}
                switch (auditable_type) {
                    case 'request': constant_variables = this.$constants.requests
                    break;
                    case 'pinboard': constant_variables = this.$constants.pinboard;
                    break;
                    case 'listing': constant_variables = this.$constants.listings;
                    break;
                }
                const translation_with_id = this.id ? 'withId': 'withNoId'
                const audits = data.data.reduce((obj, current, idx) => {
                    let audit_replacer = [];
                    let content = [];
                    const translated_auditable_type = this.$t(`general.components.common.audit.type.${current.auditable_type}`);
                    switch(current.event){
                        //  If audit event is updated
                        case 'updated':
                            //  Build new values array for type
                            Object.values(current.new_values).map((new_value, new_idx) => {
                                audit_replacer[Object.keys(current.new_values)[new_idx]] = []
                                const type = Object.keys(current.new_values)[new_idx];
                                if(type in constant_variables){
                                    audit_replacer[type]['new'] = this.$t(`models.${auditable_type}.${type}.${constant_variables[type][new_value]}`)
                                }else{
                                    switch (type) {
                                        case 'category_id':
                                            // audit_replacer[type]['new'] = this.$t(`models.${auditable_type}.category_options.${this.categories[new_value]}`);
                                            audit_replacer[type]['new'] = this.categories[new_value].name;
                                        break;
                                        case 'due_date':
                                            audit_replacer[type]['new'] = this.formatDatetime(new_value);
                                        break;
                                        case 'published_at':
                                            audit_replacer[type]['new'] = this.formatDatetime(new_value);
                                        break;
                                        default: audit_replacer[type]['new'] = new_value
                                    }
                                }
                                audit_replacer[type]['new'] = audit_replacer[type]['new']

                            })
                            //  Build old values array for type
                            Object.values(current.old_values).map((old_value, old_idx) => {
                                const type = Object.keys(current.old_values)[old_idx];
                                if(type in constant_variables){
                                    audit_replacer[type]['old'] = this.$t(`models.${auditable_type}.${type}.${constant_variables[type][old_value]}`)
                                }else{
                                    switch (type) {
                                        case 'category_id':
                                        //    audit_replacer[type]['old'] = this.$t(`models.${auditable_type}.category_options.${this.categories[old_value]}`);
                                            audit_replacer[type]['old'] = this.categories[old_value].name;
                                        break;
                                        case 'due_date':
                                            audit_replacer[type]['old'] = this.formatDatetime(old_value);
                                        break;
                                        default: audit_replacer[type]['old'] = old_value
                                    }
                                }
                                audit_replacer[type]['old'] = audit_replacer[type]['old']
                            })

                            //  For each type find the content text located in the translation file, 
                            //  then replace the old and new values
                            
                            content = Object.values(audit_replacer).map(( current_line, index) => {
                                return this.$t(`general.components.common.audit.content.${translation_with_id}.${current.auditable_type}.updated.${Object.keys(audit_replacer)[index]}`,{old: current_line.old, new: current_line.new, auditable_id: current.auditable_id, auditable_type: translated_auditable_type})
                            })

                            //  Build audit object
                            obj[current.created_at] = {id:current.id, event:current.event, content:content, userName:current.user.name}
                        break;
                        case 'created':
                            content[0] = this.$t(`general.components.common.audit.content.${translation_with_id}.${current.auditable_type}.created`,{userName: current.user.name, auditable_id: current.auditable_id, auditable_type: translated_auditable_type})
                            obj[current.created_at] = {id:current.id, event:current.event, content:content, userName:current.user.name}
                        break;
                        case 'user_assigned':
                            content[0] = this.$t(`general.components.common.audit.content.${translation_with_id}.${current.auditable_type}.user_assigned`,{userName: current.new_values.user_name, auditable_id: current.auditable_id, auditable_type: translated_auditable_type})
                            obj[current.created_at] = {id:current.id, event:current.event, content:content, userName:current.user.name}
                        break;
                        case 'provider_assigned':
                            content[0] = this.$t(`general.components.common.audit.content.${translation_with_id}.${current.auditable_type}.provider_assigned`,{providerName: current.new_values.service_provider_name, auditable_id: current.auditable_id, auditable_type: translated_auditable_type})
                            obj[current.created_at] = {id:current.id, event:current.event, content:content, userName:current.user.name}
                        break;
                        case 'provider_unassigned':
                            content[0] = this.$t(`general.components.common.audit.content.${translation_with_id}.${current.auditable_type}.provider_unassigned`,{providerName: current.old_values.service_provider_name, auditable_id: current.auditable_id, auditable_type: translated_auditable_type})
                            obj[current.created_at] = {id:current.id, event:current.event, content:content, userName:current.user.name}
                        break;
                        case 'manager_assigned':
                            content[0] = this.$t(`general.components.common.audit.content.${translation_with_id}.${current.auditable_type}.manager_assigned`,{propertyManagerFirstName: current.new_values.property_manager_first_name,propertyManagerLastName: current.new_values.property_manager_last_name, auditable_id: current.auditable_id, auditable_type: translated_auditable_type})
                            obj[current.created_at] = {id:current.id, event:current.event, content:content, userName:current.user.name}
                        break;
                        case 'manager_unassigned':
                            content[0] = this.$t(`general.components.common.audit.content.${translation_with_id}.${current.auditable_type}.manager_unassigned`,{propertyManagerFirstName: current.old_values.property_manager_first_name,propertyManagerLastName: current.old_values.property_manager_last_name, auditable_id: current.auditable_id, auditable_type: translated_auditable_type})
                            obj[current.created_at] = {id:current.id, event:current.event, content:content, userName:current.user.name}
                        break;
                        default:
                            content[0] = this.$t(`general.components.common.audit.content.${translation_with_id}.${current.auditable_type}.${current.event}`,{ auditable_id: current.auditable_id, auditable_type: translated_auditable_type})
                             obj[current.created_at] = {id:current.id, event:current.event, content:content, userName:current.user.name}
                        break;
                    }
                    return obj
                },{})

                // Delete fetched audits from object so they don't merge with the procesed ones 
                delete data.data

                // Concatenate to the existing audits and also update the extra data like current page and last page that come from the request
                this.audits = {data: {...this.audits.data, ...audits}, ...data}
                EventBus.$emit('audit-get-counted', this.audits.total);
                }catch (err) {
                    this.$message.error(err, {
                        offset: 88
                    })
                }finally {
                    this.loading = false
                }
            }
        },
        computed: {
            isEmpty () {
                return !this.loading && !Object.keys(this.audits.data).length
            }
        },
        async mounted () {
            // const {data:{data}} = await this.axios.get('requestCategories/tree?get_all=true');
            // // Get filter options from translation file and add the to filter object
          
            // const flattenCategories = categories => categories.reduce((obj, category) => {
            //     obj[category.id] = category
            //     obj[category.id].linked_name = category.name_en.toLowerCase().replace(/ /g,"_");
                
            //     obj[category.id].name = category['name_'+ this.$i18n.locale]
            //     //obj[category.id].name = this.$i18n.locale ? category.name_en : category.name_de


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
            word-break: break-word;
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