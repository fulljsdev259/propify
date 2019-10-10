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
                                    :filterable="true"
                                    :placeholder="filter.name"
                                    @change="filterChanged(filter)"
                                    class="filter-select"
                                    v-model="filterModel[filter.key]">
                                    <el-option :label="`${$t('general.placeholders.select') + ' ' + filter.name}`" value=""></el-option>
                                    <el-option
                                        :key="item.id + item.name"
                                        :label="item.name"
                                        :value="+item.id"
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
                             <el-form-item v-else-if="filter.type === filterTypes.language">
                                <select-language
                                    class="label-block"
                                    :role="'settings.language'"
                                    :activeLanguage.sync="filterModel['language']"
                                    @change="filterChanged(filter)"
                                    :isTable="true"
                                />
                            </el-form-item>
                        </template>

                    </el-col>
                </el-row>
            </el-card>
        </el-form>

        <!--        <div class="pull-right">-->
        <!--            <el-button :disabled="!selectedItems.length" @click="batchDelete" size="mini" type="danger">-->
        <!--                {{ $t('general.actions.delete')}}-->
        <!--            </el-button>-->
        <!--        </div>-->

        <el-table
            ref="tableData"
            :data="items"
            :element-loading-background="loading.background"
            :element-loading-spinner="loading.icon"
            :element-loading-text="$t(loading.text)"
            :empty-text="emptyText"
            @selection-change="handleSelectionChange"
            v-loading="loading.state">
            <el-table-column
                type="selection"
                v-if="withCheckSelection"
                width="40">
            </el-table-column>

            <el-table-column
                :key="column.prop"
                :label="$t(column.label)"
                :prop="column.prop"
                :width="column.width"
                v-for="column in headerWithoutActions"/>

            <el-table-column
                :key="column.prop"
                :label="$t(column.label)"
                :width="column.width"
                v-for="column in headerWithRoles">
                <template slot-scope="scope">
                    {{$t(`general.roles.${scope.row[column.prop][0].name}`)}}
                </template>
            </el-table-column>

            <el-table-column
                :key="column.prop"
                :label="$t(column.label)"
                :width="column.width"
                v-for="column in headerWithAvatars" class="request-table">
                
                <template slot-scope="scope">
                    <div class="avatars-wrapper">
                        <div class="user-details" v-if="scope.row['user']">
                            <div class="image">
                                <table-avatar :src="scope.row['user'].avatar" :name="scope.row['user'].name" :size="33" />
                            </div>
                            <div class="title">
                                {{ scope.row['user'].name }}
                            </div>
                        </div> 
                    </div>
                </template>
            </el-table-column>
            
            <el-table-column
                :key="column.label + key"
                :label="$t(column.label)"
                :width="column.width"
                v-for="(column, key) in headerWithMultipleProps"
            >
                <template slot-scope="scope">
                    <component :class="{'listing-link': column.withLinks}" :is="column.withLinks ? 'router-link':'div'"
                               :key="prop" :to="{name: 'adminUnits', query: { page : 1, per_page : 20, building_id : scope.row.id }}"
                               v-for="(prop, ind) in column.props" v-if="scope.row[prop]">
                        {{scope.row[prop]}}
                    </component>
                </template>
                <!-- <template slot-scope="scope">
                    <component :class="{'listing-link': column.withLinks}" :is="column.withLinks ? 'router-link':'div'"
                               :key="prop" :to="buildRouteObject(column.route, scope.row)"
                               v-for="(prop, ind) in column.props" v-if="scope.row[prop]">
                        {{scope.row[prop]}}
                    </component>
                </template> -->
            </el-table-column>

            <el-table-column
                :key="column.label"
                :label="$t(column.label)"
                :width="column.width"
                v-for="(column, key) in headerWithCollapsables">
                <template slot-scope="scope">
                    <template v-if="column.props == 'building_names'">
                    <span v-if="scope.row[column.props].length == 1">
                        {{scope.row[column.props][0].row}}
                        <br/>
                        {{scope.row[column.props][0].zip}}
                    </span>
                    <el-collapse class="table-collapsable" v-if="scope.row[column.props].length > 1">
                        <el-collapse-item>
                            <template slot="title">
                                <span>
                                    {{scope.row[column.props][0].row}}
                                    <br/>
                                    {{scope.row[column.props][0].zip}}
                                </span>
                            </template>
                            <span
                                :key="value.row + value.zip"
                                v-for="(value,v_index) in scope.row[column.props]"
                                v-if="v_index > 0"
                            >
                                {{value.row}}
                                <br/>
                                {{value.zip}}
                            </span>
                        </el-collapse-item>
                    </el-collapse>
                    </template>
                    <template v-else>
                        <span v-if="scope.row[column.props].length == 1">
                            {{scope.row[column.props][0]}}
                        </span>
                        <el-collapse class="table-collapsable" v-if="scope.row[column.props].length > 1">
                            <el-collapse-item :title="scope.row[column.props][0]">
                                <span
                                    :key="value.row + value.zip"
                                    v-for="(value,v_index) in scope.row[column.props]"
                                    v-if="v_index > 0"
                                >
                                    {{value}}
                                </span>
                            </el-collapse-item>
                        </el-collapse>
                    </template>
                </template>
                
            </el-table-column>

            <el-table-column
                :key="column.label"
                :label="$t(column.label)"
                :width="column.width"
                v-for="(column, key) in headerWithCounts">
                <template slot-scope="scope">
                    <request-count :countsData="items[scope.$index]" ></request-count>
                    <contract-count :countsData="items[scope.$index]" ></contract-count>
                </template>
            </el-table-column>

             <el-table-column
                :key="column.prop"
                :label="$t(column.label)"
                :width="column.width"
                v-for="(column, key) in headerWithUsers">
                <template slot-scope="scope">
                    <div class="avatars-wrapper">
                        <span :key="uuid()" v-for="(user) in scope.row[column.prop]">
                              <el-tooltip
                                  :content="user.first_name ? `${user.first_name} ${user.last_name}`: (user.user ? `${user.user.name}`:`${user.name}`)"
                                  class="item"
                                  effect="light" placement="top">
                                  <template v-if="user.user">
                                      <avatar :size="28"
                                              :username="user.first_name ? `${user.first_name} ${user.last_name}`: (user.user ? `${user.user.name}`:`${user.name}`)"
                                              backgroundColor="rgb(205, 220, 57)"
                                              color="#fff"
                                              v-if="!user.user.avatar"></avatar>
                                      <avatar :size="28" :src="`/${user.user.avatar}`" v-else></avatar>
                                  </template>
                                  <template v-else>
                                      <avatar :size="28"
                                              :username="user.first_name ? `${user.first_name} ${user.last_name}`: `${user.name}`"
                                              backgroundColor="rgb(205, 220, 57)"
                                              color="#fff"
                                              v-if="!user.avatar"></avatar>
                                      <avatar :size="28" :src="`/${user.avatar}`" v-else></avatar>
                                  </template>
                              </el-tooltip>

                        </span>
                        <avatar class="avatar-count" :size="28" :username="`+ ${scope.row[column.count]}`"
                                color="#fff"
                                v-if="scope.row[column.count]"></avatar>
                    </div>
                </template>
            </el-table-column>
            <el-table-column
                :key="column.prop"
                :label="$t(column.label)"
                :width="column.width"
                v-for="(column, key) in headerWithBadges">
                <template slot-scope="scope">
                    <el-button v-if="scope.row[column.prop] == 'low'" class="btn-priority-badge btn-badge" :size="column.size" round>{{ scope.row[column.prop] }}</el-button>
                    <el-button v-else-if="scope.row[column.prop] == 'normal'" plain type="warning" class="btn-priority-badge btn-badge" :size="column.size" round>{{ scope.row[column.prop] }}</el-button>
                    <el-button v-else-if="scope.row[column.prop] == 'urgent'" plain type="danger" class="btn-priority-badge btn-badge" :size="column.size" round>{{ scope.row[column.prop] }}</el-button>
                </template>
            </el-table-column>
            <el-table-column
                :key="column.prop"
                :label="$t(column.label)"
                :width="column.width"
                v-for="(column, key) in headerWithSelect">
                <template slot-scope="scope">
                    <el-select class="select-icon" :class="column.class" @change="column.select.onChange(scope.row)" v-model="scope.row[column.prop]" :style="{width: column.ShowCircleIcon != undefined? '120px': '150px'}">
                        <template slot="prefix">
                            <i class="icon-dot-circled" :class="scope.row[column.prop] == 1 ? 'icon-success':'icon-danger'"  v-if="column.ShowCircleIcon"></i>
                        </template>
                        <el-option
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                            v-for="item in column.select.data">
                            <i class="icon-dot-circled" :class="item.id == 1 ? 'icon-success':'icon-danger'"  v-if="column.ShowCircleIcon"></i> {{item.name}}
                        </el-option>
                    </el-select>
                   
                </template>
            </el-table-column>
            <el-table-column
                :key="key"
                :width="column.width ? column.width : 200"
                v-for="(column, key) in headerWithActions">
                <template slot-scope="scope">
                    <span
                        :key="action.title"
                        class="btn-wrap"
                        v-for="action in column.actions">
                        <template
                            v-if="(!action.permissions || ( action.permissions && $can(action.permissions))) && (!action.hidden || (action.hidden && !action.hidden(scope.row)))">
                            <template v-if="action.title.indexOf('edit') !== -1 && action.isTemplateEdit == undefined">
                                <router-link
                                        :to="{
                                            name: action.editUrl,
                                            params: {
                                                type:$constants.pinboard.type[scope.row['type']],
                                                id:scope.row['id']}
                                            }"
                                        class="el-menu-item-link">
                                    <el-button
                                        :style="action.style"
                                        :type="action.type"
                                        size="mini"
                                    >
                                        <i class="ti-pencil"></i>
                                        <span>{{ $t('general.actions.edit') }}</span>
                                    </el-button>
                                </router-link>      
                            </template>
                            <el-button
                                v-else
                                :style="action.style"
                                :type="action.type"
                                @click="action.onClick(scope.row)"
                                size="mini"
                            >
                                <template v-if="action.isTemplateEdit != undefined">
                                    <i class="ti-pencil"></i>
                                    <span>{{ $t('general.actions.edit') }}</span>    
                                </template>
                                <template v-else-if="action.title.indexOf('edit') !== -1">
                                    <router-link :to="{name: 'adminPropertyManagersEdit',  params: { id:scope.row['id']}}" class="el-menu-item-link">
                                        <i class="ti-pencil"></i>
                                        <span>{{ $t('general.actions.edit') }}</span>
                                    </router-link>      
                                </template>
                                <template v-else-if="action.title == 'Delete'">
                                    <i class="ti-close"></i>
                                    <span>{{$t(action.title)}}</span>    
                                </template>
                                <template v-else>
                                    {{$t(action.title)}}
                                </template>
                            </el-button>
                        </template>
                    </span>
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
    import RequestCount from 'components/RequestCount'
    import ContractCount from 'components/ContractCount'
    import tableAvatar from 'components/Avatar';
    import RequestDetailCard from 'components/RequestDetailCard';
    import SelectLanguage from 'components/SelectLanguage';
    import ListTableSelect from 'components/ListTableSelect';

    export default {
        name: 'ListTable',
        components: {
            Avatar,
            RequestCount,
            ContractCount,
            'table-avatar': tableAvatar,
            RequestDetailCard,
            SelectLanguage,
            ListTableSelect
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
                    date: 'date',
                    language: 'language'
                },
                filterModel: {},
                uuid,
                selectedItems: [],
            }
        },
        computed: {
            emptyText() {
                return this.loading.state ?  ' ' : (this.items.length > 0) ? '' : this.$t('general.no_data_available');
            },
            page() {
                return {
                    currPage: this.pagination.currPage,
                    currSize: this.pagination.currSize
                }
            },
            headerWithoutActions() {
                return this.header.reduce((acc, row) => (!row.actions
                && !row.roles
                && !row.select
                && !row.withUsers
                && !row.withAvatars
                && !row.withCounts
                && !row.withMultipleProps
                && !row.withCollapsables
                && !row.withBadgeProps
                && acc.push(row), acc), []);
            },
            headerWithRoles() {
                return this.header.reduce((acc, row) => (row.roles && acc.push(row), acc), []);
            },
            headerWithMultipleProps() {
                return this.header.reduce((acc, row) => (row.withMultipleProps && acc.push(row), acc), []);
            },
            headerWithCollapsables() {
                return this.header.reduce((acc, row) => (row.withCollapsables && acc.push(row), acc), []);
            },
            headerWithCounts() {
                return this.header.reduce((acc, row) => (row.withCounts && acc.push(row), acc), []);
            },
            headerWithAvatars() {
                return this.header.reduce((acc, row) => (row.withAvatars && acc.push(row), acc), []);
            },
            headerWithUsers() {
                return this.header.reduce((acc, row) => (row.withUsers && acc.push(row), acc), []);
            }, 
            headerWithActions() {
                return this.header.reduce((acc, row) => (row.actions && acc.push(row), acc), []);
            },
            headerWithSelect() {
                return this.header.filter((filter) => {
                    if (filter.select) {
                        if (filter.select.getter) {
                            const storeConstants = this.$store.getters['application/constants'][filter.select.getter];
                            if (storeConstants) {
                                const constants = storeConstants[filter.prop];
                                filter.select.data = Object.keys(constants).map((id) => {
                                    return {
                                        name: !filter.i18nPath ? constants[id] : this.$t(`${filter.i18nPath}.${constants[id]}`),
                                        id: parseInt(id)
                                    };
                                });
                            }


                        }
                        return filter.select;
                    }
                });
            },
            headerWithBadges() {
                return this.header.reduce((acc, row) => (row.withBadgeProps && acc.push(row), acc), []);
            },
            filterColSize() {
                return 4;
            }
        },
        methods: {
            rowClicked(row) {
                this.$refs.tableData.toggleRowExpansion(row);
            },
            selectChanged(e, row, column) {
                row[column.prop] = e;
                column.select.onChange(row);
            },
            clearSearch() {
                this.search = '';
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
                /*if(this.$route.name=='adminUsers') {
                    query = {roles: ['administrator'], ...query};
                    if(query.role)
                        delete query.roles;
                    else
                        delete query.role;
                }*/

                this.$router.replace({name: this.$route.name, query, params});
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
                if (filter.type === this.filterTypes.select || filter.type == this.filterTypes.language) {
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

                if (filter.type == this.filterTypes.language) {
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
                        this.filterModel = {};

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

            _.each(this.filters, (filter) => {
                let queryFilterValue = this.$route.query[filter.key];
                
                const dateReg = /^\d{2}([./-])\d{2}\1\d{4}$/;
                const value = queryFilterValue && queryFilterValue.toString().match(dateReg) ? queryFilterValue : parseInt(queryFilterValue);
                this.$set(this.filterModel, filter.key, value);
                if(filter.key == "search")
                    this.filterModel[filter.key] = queryFilterValue;
                

                if (!this.filterModel[filter.key]) {
                    delete this.filterModel[filter.key];
                }

                if (this.filterModel[filter.key] || (!filter.parentKey && filter.fetch)) {
                    this.filterChanged(filter, true);
                }
            });
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
    .el-divider.el-divider--horizontal {
        width: 90%;
        margin: 0 auto !important;
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

    .el-table {
        background: none;
        padding: 2px;

        &:before {
            display: none;
        }

        :global(.el-table__body-wrapper) {
            box-shadow: 0 1px 3px transparentize(#000, .88),
            0 1px 2px transparentize(#000, .76);
            border-radius: 4px;
        }

        :global(th),
        :global(td) {
            background: none;
        }

        :global(th) {
            background: none;
            border-bottom-style: none;
        }

        :global(tr) {
            background: none;

            :global(th) {
                padding: 12px 4px;
            }

            :global(td) {
                padding: 4px;
            }
        }

        :global(.el-table__empty-block) {
            background-color: #fff;
        }

        :global(.el-table__body) {
            :global(tr) {
                :global(td) {
                    background-color: #fff;
                }

                &:hover :global(td) {
                    background-color: #f5f7fa;
                }

                &:last-of-type :global(td) {
                    border-bottom-style: none;
                }
            }
        }

        :global(.el-loading-mask) {
            top: 47px;
            margin: 2px;
            border-radius: 6px;
        }

        :global(.table-collapsable) {
            border: none;

            :global(.el-collapse-item__header) {
                line-height: 23px;
                background: none;
            }

            :global(.el-collapse-item__wrap) {
                background: none;
                padding-bottom: 0;

                span {
                    display: block;
                    line-height: 25px;
                    border-top: 1px solid var(--color-text-placeholder);
                }
            }
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
    .btn-wrap {
       
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
    .label-block .el-form-item__label {
        display: block;
        float: none;
        text-align: left;
    }
    .el-form-item__content .selected {
        #languageform.el-input__inner {
            padding-left: 40px;
        }
        #languageflag.flag-icon {
            padding-left: 20px;
        }
    }
    .el-table th>.cell, .el-table-column--selection .cell{
        text-overflow: unset;
    }
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

        &:hover {
            color: var(--primary-color-lighter);
        }
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
