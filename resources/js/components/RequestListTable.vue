<template>
    <div class="list-table">
        <el-form @submit.native.prevent="" label-position="top">
            <el-input placeholder="Search..."
                      prefix-icon="el-icon-search"
                      v-if="withSearch"
                      v-model="search"
            >
                <template slot="suffix" v-if="search.length">
                    <el-button @click="clearSearch()"   
                               circle
                               icon="ti-close"
                               size="mini"/>
                </template>
            </el-input>
            <el-card :header="filtersHeader"
                     class="mb30 filters-card"
                     shadow="never"
                     v-if="this.filters.length"
                    :element-loading-background="loading.background"
                    :element-loading-spinner="loading.icon"
                    :element-loading-text="$t(loading.text)"
                    v-loading="isLoadingFilters.state"
            >
                <el-row :gutter="10">
                    <el-col :key="key" :span="filterColSize" v-for="(filter, key) in filters">
                        <template v-if="!filter.parentKey || filterModel[filter.parentKey]">
                            <el-form-item
                                v-if="filter.type === filterTypes.select && filter.data &&  filter.data.length">
                                <el-select
                                    v-if="filter.key == 'category_id' || filter.key == 'status' || filter.key == 'internal_priority'"
                                    :filterable="true"
                                    :placeholder="filter.name"
                                    @change="filterChanged(filter)"
                                    class="filter-select"
                                    multiple
                                    collapse-tags
                                    v-model="filterModel[filter.key]">
                                    <el-option :label="`${$t('general.placeholders.select')+' '+filter.name}`" value=""></el-option>
                                    <el-option
                                        :key="filter.key == 'category_id'? item.id + item.name_en : item.id + item.name "
                                        :label="filter.key == 'category_id'?item['name_'+$i18n.locale]:item.name"
                                        :value="item.id"
                                        v-for="item in filter.data">
                                    </el-option>
                                </el-select>
                                <el-select
                                    v-else
                                    :filterable="true"
                                    :placeholder="filter.name"
                                    @change="filterChanged(filter)"
                                    class="filter-select"
                                    v-model="filterModel[filter.key]">
                                    <el-option :label="`${$t('general.placeholders.select')+' '+filter.name}`" value=""></el-option>
                                    <el-option
                                        :key="item.id + item.name"
                                        :label="item.name"
                                        :value="item.id"
                                        v-for="item in filter.data">
                                    </el-option>
                                </el-select>

                               
                            </el-form-item>
                            <el-form-item
                                v-else-if="filter.type === filterTypes.text || filter.type === filterTypes.number">
                                <el-input
                                    :placeholder="filter.name"
                                    :prefix-icon="filter.icon"
                                    :type="filter.type"
                                    @change="filterChanged(filter)"
                                    v-model="filterModel[filter.key]">
                                </el-input>
                            </el-form-item>
                            <el-form-item v-else-if="filter.type === filterTypes.date">
                                <el-date-picker
                                    :format="filter.format"
                                    :placeholder="filter.name"
                                    :value-format="filter.format"
                                    @change="filterChanged(filter)"
                                    style="width: 100%"
                                    type="date"
                                    v-model="filterModel[filter.key]"
                                >
                                </el-date-picker>
                            </el-form-item>
                            <el-form-item v-else-if="filter.type === filterTypes.remoteSelect">
                                <el-select
                                    :loading="filter.remoteLoading"
                                    :placeholder="filter.name"
                                    :remote-method="data => remoteFilter(filter,data)"
                                    @change="filterChanged(filter)"
                                    filterable
                                    remote
                                    reserve-keyword
                                    style="width: 100%;"
                                    v-model="filterModel[filter.key]">
                                    <el-option
                                        :label="$t('general.all')"
                                        value=""
                                    >
                                    </el-option>
                                    <el-option
                                        :key="item.id"
                                        :label="item.name"
                                        :value="item.id"
                                        v-for="item in filter.data"/>
                                </el-select>
                            </el-form-item>
                        </template>

                    </el-col>
                    
                </el-row>
            </el-card>
        </el-form>

        <!--        <div class="pull-right">-->
        <!--            <el-button :disabled="!selectedItems.length" @click="batchDelete" size="mini" type="danger">-->
        <!--                {{$t('general.actions.delete')}}-->
        <!--            </el-button>-->
        <!--        </div>-->
        <el-popover placement="left-end" trigger="click" :width="192" style="float:right">
            <el-button slot="reference" icon="el-icon-sort" class="popover-button">{{ $t('models.request.sort') }}</el-button>
            <div class="sorters">
                <template v-for="(sorter, key) in this.Sorters">
                    <el-radio-group v-if="sorter.type=='el-radio'" v-model="filterModel[sorter.key]" @change="sortChanged(sorter)" :key="key">
                        <el-radio :label="item.key" v-for="(item, key) in sorter.data" :key="key" style="padding-top:5px">{{ $t(item.text) }}</el-radio>
                    </el-radio-group>
                    <div v-else-if="sorter.type=='el-button'" :key="key" class="sort-by">
                        <a @click="sortedBy(sorter)" style="cursor:pointer">
                            <i class="el-icon-bottom" v-if="filterModel[sorter.key]=='asc'"></i>
                            <i class="el-icon-top" v-else-if="filterModel[sorter.key]=='desc'"></i>
                            <i class="el-icon-bottom" v-else></i>
                        </a>
                    </div>
                </template>
            </div>
            <el-button type="primary" size="small" icon="el-icon-sort-up" style="width: 100%; margin-top: 8px;" @click="resetSorters">{{$t('models.request.reset_sort')}}</el-button>
        </el-popover>

        <el-table
            :data="items"
            :element-loading-background="loading.background"
            :element-loading-spinner="loading.icon"
            :element-loading-text="$t(loading.text)"
            :empty-text="emptyText"
            @selection-change="handleSelectionChange"
            v-loading="loading.state"
            @row-click="editLink">

            <el-table-column
                :key="'OneCol'"
                :width="column.width"
                v-for="(column, key) in headerWithOneCol"
            >
                <template slot-scope="scope">
                    <request-detail-card
                        :item="scope.row"
                        :loading="{state: loading}"
                        @selectionChanged="handleRequestSelectionChange"
                        @editAction="column.editAction(scope.row)"
                        @onChange="scope.row['status']=$event,column.onChange(scope.row)"
                        :categories="categories"
                    >

                    </request-detail-card>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination
            :current-page.sync="page.currPage"
            :page-size.sync="page.currSize"
            :page-sizes="pagination.pageSizes"
            :total="pagination.total"
            @current-change="onCurrentPageChange"
            @size-change="onSizeChange"
            layout="total, sizes, prev, pager, next, jumper"
            v-if="pagination.total"/>
    </div>
</template>

<script>
    // TODO - add transition to do things smoothly
    import {Avatar} from 'vue-avatar'
    import uuid from 'uuid/v1'
    import RequestCount from 'components/RequestCount.vue'
    import tableAvatar from 'components/Avatar';
    import RequestDetailCard from 'components/RequestDetailCard';
    import filters from 'components/Filters';
    import { mapActions } from 'vuex';
    
    import {ResponsiveMixin} from 'vue-responsive-components'

    export default {
        name: 'ListTable',
        mixins: [
            ResponsiveMixin
        ],
        components: {
            Avatar,
            RequestCount,
            'table-avatar': tableAvatar,
            RequestDetailCard,
            filters
        },
        props: {
            header: {
                type: Array,
                default: () => {
                    return [];
                }
            },
            items: {
                type: Array,
                default: () => {
                    return [];
                }
            },
            fetchMore: {
                type: Function,
                required: true
            },
            fetchMoreParams: {
                type: Object,
                default: () => ({})
            },
            loading: {
                type: Object,
                default: () => ({
                    state: false,
                    text: 'general.loading',
                    icon: 'el-icon-loading',
                    background: 'rgba(0, 0, 0, 0.8)'
                })
            },
            isLoadingFilters: {
                type: Object,
                default: () => ({
                    state: false,
                    text: 'general.loading',
                    icon: 'el-icon-loading',
                    background: 'rgba(0, 0, 0, 0.8)'
                })
            },
            pagination: {
                type: Object,
                default: () => ({
                    currPage: 1,
                    currSize: 10,
                    pageSizes: [10, 25, 50, 100],
                    total: 0
                })
            },
            filters: {
                type: Array,
                default: () => []
            },
            filtersHeader: {
                type: String,
                default: () => (this.$t('resident.filters'))
            },
            withSearch: {
                type: Boolean,
                default: true
            },
            withCheckSelection: {
                type: Boolean,
                default: true
            }
        },
        beforeMount() {
        },
        data() {
            return {
                search: '',
                filterTypes: {
                    select: 'select',
                    remoteSelect: 'remote-select',
                    text: 'text',
                    number: 'number',
                    date: 'date'
                },
                filterModel: {},
                uuid,
                selectedItems: [],
                Sorters: [
                    {
                        name: this.$t('filters.search'),
                        type: 'el-radio',
                        icon: 'el-icon-search',
                        key: 'orderBy',
                        data: [{
                            key: 'due_date',
                            text: 'models.request.due_date'
                        }, {
                            key: 'created_at',
                            text: 'models.request.creation_date'
                        }],
                        
                    }, {
                        type: 'el-button',
                        key: 'sortedBy',
                    }
                ],
                categories: []
            }
        },
        computed: {
            getTableClass() {
                return this.header[0].withOneCol? 'request-table': 'request-el-table';
            },
            emptyText() {
                return this.loading.state ?  ' ' : (this.items.length > 0) ? '' : this.$t('general.no_data_available');
            },
            page() {
                return {
                    currPage: this.pagination.currPage,
                    currSize: this.pagination.currSize
                }
            },
            headerWithOneCol() {
                return this.header.reduce((acc, row) => (row.withOneCol && acc.push(row), acc), []);
            },
            filterColSize() {
                return 4;
            }
        },
        methods: {
            ...mapActions(['getRequestCategoriesTree']),
            async getFilterCategories() {
                const {data: categories} = await this.getRequestCategoriesTree({get_all: true});
                
                this.categories = [];
                categories.map((category) => {
                    this.categories[category.id] = {
                        'en' : category.name_en,
                        'fr' : category.name_fr,
                        'it' : category.name_it,
                        'de' : category.name_de,
                    };
                    if(category.categories.length > 0) {
                        category.categories.map((subCategory) => {
                            this.categories[subCategory.id] = {
                                'en' : subCategory.name_en,
                                'fr' : subCategory.name_fr,
                                'it' : subCategory.name_it,
                                'de' : subCategory.name_de,
                            }
                        });
                    }
                });
            },
            clearSearch() {
                this.search = '';
            },
            sortChanged(sorter) {
                this.filterModel['sortedBy'] = 'asc';
                this.filterChanged(sorter)
            },
            sortedBy(sorter) {
                if(this.filterModel['orderBy'] == 'created_at' || this.filterModel['orderBy'] == 'due_date') {
                    if(this.filterModel[sorter.key] == undefined) {
                        this.filterModel[sorter.key] = 'asc';
                    } else if(this.filterModel[sorter.key] == 'asc') {
                        this.filterModel[sorter.key] = 'desc'
                    } else if(this.filterModel[sorter.key] == 'desc') {
                        this.filterModel[sorter.key] = 'asc'
                    } 
                    this.filterChanged(sorter);
                }
            },
            resetSorters() {
                this.Sorters.map((sorter) => {
                    this.filterModel[sorter.key] = '';
                    this.filterChanged(sorter);
                });
            },
            fetch(fetchPage, fetchPerPage) {
                fetchPerPage = 4;
                const {
                    page = fetchPage,
                    per_page = fetchPerPage,

                    ...restQueryParams
                } = this.$route.query;

                const params = {
                    page,
                    per_page,

                    ...restQueryParams,
                    ...this.fetchMoreParams
                };
                this.fetchMore(params);
            },
            syncUrl() {
                let query = {
                    ...this.$route.query,
                    page: this.page.currPage,
                    per_page: this.page.currSize,
                    ...this.filterModel
                };

                let params = this.$route.params;

                if (this.search) {
                    query = {search: this.search, ...query};
                } else if (this.withSearch) {
                    delete query.search;
                }
                for(var filter in this.filterModel) {
                    if(this.filterModel[filter] == '' && query[filter] != undefined)
                        delete query[filter];
                }

                try {
                    this.$router.replace({name: this.$route.name, query, params}).catch(err => {})
                }
                catch (err) {

                }
            },
            updatePage(page, size) {
                let {currPage, currSize} = this.page;

                currPage = page || currPage;
                currSize = size || currSize;

                this.syncUrl();
            },
            onSizeChange(newSize) {
                this.updatePage(1, newSize);
            },
            onCurrentPageChange(newPage) {
                this.updatePage(newPage);
            },
            filterChanged(filter, init = false) {
                if (filter.type === this.filterTypes.select) {
                    if (!filter.parentKey && filter.fetch && init) {
                        filter.fetch().then((resp) => {
                            filter.data = resp;
                            // TODO find a better way to update or change the logic
                            this.$forceUpdate();
                        });
                    }

                    const shouldFetchItems = this.filters.filter((f) => {
                        return f.parentKey === filter.key;
                    });

                    shouldFetchItems.forEach((f) => {
                        if (!init) {
                            this.filterModel[f.key] = '';
                            _.each(this.filters, (fl) => {
                                if (fl.parentKey === f.key) {
                                    this.filterModel[fl.key] = '';
                                }
                            });
                        }

                        if (this.filterModel[filter.key]) {
                            f.fetch().then((resp) => {
                                f.data = resp;
                                if (init) {
                                    this.$forceUpdate();
                                }
                            });
                        }
                    });
                }

                if (init && filter.type === this.filterTypes.remoteSelect && this.filterModel[filter.key]) {
                    this.remoteFilter(filter, '');
                }

                if ((!filter.parentKey && filter.fetch && init && this.filterModel[filter.key]) || !init) {
                    this.updatePage();
                }
            },
            isDisabled(select, selected, status) {
                if (select.withDisabled) {
                    if (typeof select.withDisabled === 'string') {
                        let disabledConstants = this.$store.getters[select.getter][select.withDisabled];
                        return (_.indexOf(disabledConstants[selected], status) < 0) ? true : false;
                    }
                }

                return false;
            },
            handleSelectionChange(val) {
                this.selectedItems = val;
                this.$emit('selectionChanged', this.selectedItems);
            },
            handleRequestSelectionChange(val) {
                var exist = -1;
                this.selectedItems.map((item, index) => {
                    if(item.id == val.id)
                        exist = index;
                });
                if(exist == -1) {
                    this.selectedItems.push(val)
                } else {
                    this.selectedItems.slice(exist, 1);
                }
                this.handleSelectionChange(this.selectedItems);
            },
            batchDelete() {
                this.$emit('batchDelete', this.selectedItems);
            },
            buildRouteObject(columnRoute, row) {
                if (!columnRoute || !row) {
                    return {};
                }

                const params = {};

                if (columnRoute.paramsKeys) {
                    if (columnRoute.model) {
                        params[columnRoute.model] = row;
                    }

                    if (columnRoute.paramsKeys.props) {
                        columnRoute.paramsKeys.props.forEach((prop) => {
                            params[prop] = row[prop];
                        });
                    }

                }

                return {
                    name: columnRoute.name,
                    params
                }
            },
            async remoteFilter(filter, search) {
                filter.remoteSearch = true;
                try {
                    filter.data = await filter.fetch(search);
                    this.$forceUpdate();
                } catch (e) {

                } finally {
                    filter.remoteSearch = false;
                }
            },
            editLink(row, column, cell, event) {
                if(column.label === 'Units')
                {
                    let building_id = row.id;
                    this.$router.push({ name: 'adminUnits', query: { page : 1, per_page : 20, building_id : building_id } });
                }
            }
        },
        watch: {
            search(text) {
                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }

                this.timer = setTimeout(() => this.updatePage(), 800);
            },
            "$route.query": {
                immediate: true,
                handler({page, per_page}, prevQuery) {
                    if(this.$route.name == "login") {
                        return;
                    }

                    if (!page || !per_page && prevQuery) {
                        this.page.currPage = 1;
                        this.page.currSize = 20;

                        return this.syncUrl();
                    }

                    page = parseInt(page);
                    per_page = parseInt(per_page);

                    this.page.currPage = page;
                    this.page.currSize = per_page < 1 ? this.pagination.currSize : per_page;

                    prevQuery && this.syncUrl();
                    this.fetch(this.page.currPage, this.page.currSize);
                }
            }
        },
        created() {
            if (this.$route.query.search) {
                this.search = this.$route.query.search;
            }

            _.each(this.Sorters, (sorter) => {
                if(this.$route.query[sorter.key] != undefined)
                    this.filterModel[sorter.key] = this.$route.query[sorter.key];
            });

            _.each(this.filters, (filter) => {
                const queryFilterValue = this.$route.query[filter.key];
                const dateReg = /^\d{2}([./-])\d{2}\1\d{4}$/;
                const value = queryFilterValue && queryFilterValue.match(dateReg) ? queryFilterValue : parseInt(queryFilterValue);
                this.$set(this.filterModel, filter.key, value);

                if (!this.filterModel[filter.key]) {
                    delete this.filterModel[filter.key];
                }

                if (this.filterModel[filter.key] || (!filter.parentKey && filter.fetch)) {
                    this.filterChanged(filter, true);
                }
            });
            
            this.getFilterCategories();
        }
    }
</script>

<style lang="scss" scoped>
    .avatar-count{
        min-width: 28px;
    }
    .list-table {
        padding: 20px;
    }
    .sorters {
        padding: 10px 0;
        display: flex;
        .sort-by {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
        }
    }
    .el-input {
        &.el-input--suffix {
            :global(.el-input__inner) {
                padding-right: 50px;
            }

            :global(.el-input__suffix) {
                right: 10px;
                display: flex;
                align-items: center;

                :global(.el-button) {
                    border-style: none;
                }
            }
        }
    }
    .popover-button {
        padding:0;
        border:none;
        background-color:transparent;
    }
    .el-table {
        background: none;
        padding: 0px;

        .el-table__header {
            display: none;
        }

        &:before {
            display: none;
        }
       
        :global(.el-table__body-wrapper) {
            border-radius: 4px;
        }
     
        :global(th),
        :global(td) {
            background: none;
        }

        :global(th) {
            background: none;
            border-bottom-style: none;
            display: none;
        }

        :global(tr) {
            background: none;

            :global(th) {
                padding: 12px 4px;
                border-bottom: none;
            }

            :global(td) {
                background-color: transparent;
                padding: 3px 0;
                border-bottom: none;
            
            }
        }

        :global(.el-table__empty-block) {
            background-color: #fff;
        }

        :global(.el-table__body) {
            :global(tr) {
                :global(td) {
                    background-color: transparent;
                }
                .cell {
                    padding: 2px !important;
                }

                &:hover :global(td) {
                    background-color: #f5f7fa;
                }

                &:last-of-type :global(td) {
                    border-bottom-style: none;
                }
            }
        }
        .request-card {
            margin-right: -8px;
        }

        :global(.el-loading-mask) {
            top: 47px;
            margin: 2px;
            border-radius: 6px;
        }
    }
    .el-pagination {
        display: flex;
        padding: 8px 18px;

        :global(.el-pagination__sizes) {
            flex: 1;
        }

        :global(.btn-prev),
        :global(.btn-next) {
            background: none;
        }

        :global(.el-pager) {
            :global(li) {
                background: none;
            }
        }
    }

    .mt30 {
        margin-top: 30px;
    }

    .mb30 {
        margin-bottom: 20px;
    }

    .filter-select {
        width: 100%;
    }

    .avatars-wrapper {
        display: flex;

        & > span {
            margin-right: 2px;
        }

        .vue-avatar--wrapper {
            font-size: 12px !important;
        }

        .user-details {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding-top: 5px;
            padding-bottom: 5px;
            width: 100%;

            .image {
                border-radius: 7px;
                width: 33px;
                height: 33px;
                min-width: 33px;
                min-height: 33px;
                margin-right: 15px;
                background-size: cover;
                background-position: center;
            }

            .text {
                width: calc(100% - 75px);
                .title {
                    max-width: 100%;
                    font-weight: bold;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
            }
        }
    }

    .btn-wrap:not(:first-child) {
        margin-left: 5px;
    }

    .square-avatars {
        flex-wrap: wrap;

        & > span {
            margin-bottom: 2px;

            & > div {
                position: relative;
                margin-right: -10px;
                border: 1px solid #fff;
                cursor: pointer;

                &:hover {
                    z-index: 999 !important;
                }
            }
        }
    }

    .btn-priority-badge {
        pointer-events:none;
    }

    .icon-success {
        color: #5fad64;
    }

    .icon-danger {
        color: #dd6161;
    }

</style>

<style lang="scss">
    .el-table {
        tbody {
            tr {
                td:last-child {
                    .cell {
                        padding-left: 0px !important;
                        text-align: right;
                    }
                }
            }
        }
    }

    .el-button--danger {
        margin-left: 5px !important;
    }

    .el-table__body {
        cursor: pointer;
    }

    .filters-card {
        .el-card__body {
            padding: 22px;
            padding-bottom: 0;
        }
    }

    .listing-link {
        text-decoration: none;
        color: var(--primary-color);
        font-weight: bold;
    }

    .rounded-select .el-input .el-input__prefix {
        padding-left: 3px;
        display: flex;
        align-items: center;
    }

    .rounded-select .el-input.el-input--prefix .el-input__inner {
        padding-left: 25px;
    }

    .rounded-select .el-input .el-input__inner {
        border-radius: 20px;
        height: 32px;
    }

    .rounded-select .el-input .el-input__suffix {
        top: 5px;
    }

    .rounded-select .el-input.is-focus .el-input__suffix{
        top: -3px;
    }

    .el-select-dropdown__item.selected {
        color: #606266 !important;
    }
</style>
