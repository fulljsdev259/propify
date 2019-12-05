<template>
    <div class="list-table">
        <el-form @submit.native.prevent="" label-position="top">
            <el-input
                clearable 
                placeholder="Search..."
                prefix-icon="el-icon-search"
                v-if="withSearch"
                v-model="search"
            >
                <template slot="suffix" v-if="search.length">
                    <el-button 
                        @click="clearSearch()"   
                        circle
                        icon="ti-close"
                        size="mini"
                    />
                </template>
            </el-input>
            <div class="sub-menu" key="sub-menu">
                <a
                    :key="link.title + key"
                    v-for="(link, key) in subMenu"
                    v-if="$can(link.permission) || !link.permission"
                    :class="{'is-active': link.route.name == $route.name}"
                    @click="makeFilterQuery(link.route.name)"
                >
                    <span class="title">{{ link.title }}</span>
                </a>
            </div>
            <el-card 
                v-if="this.filters.length"
                :header="filtersHeader"
                class="mb30 filters-card"
                :class="{'filter-right': subMenu.length}"
                shadow="never"
                :element-loading-background="loading.background"
                :element-loading-spinner="loading.icon"
                :element-loading-text="$t(loading.text)"
                v-loading="isLoadingFilters.state"
            >
                <el-row :gutter="10">
                    <el-col :key="`${key}key${filter.data && filter.data.length}`" :span="filterColSize" v-for="(filter, key) in filters" v-if="filter.hidden === undefined || showFilters">
                        <template v-if="!filter.parentKey || filterModel[filter.parentKey]">
                            <el-form-item
                                v-if="filter.type === filterTypes.select && filter.data">
                        
                                <list-filter-select
                                    :type="filter.key"
                                    :name="filter.name"
                                    :data="filter.data"
                                    :searchBox="filter.searchBox"
                                    :selectedOptions="filterModel[filter.key]"
                                    @select-changed="handleSelectChange($event, filter)"
                                >
                                </list-filter-select>

                            </el-form-item>
                            <!-- <el-form-item
                                v-if="filter.type === filterTypes.text || filter.type === filterTypes.number">
                                 <el-input
                                    v-if="filter.key == 'search'"
                                    :placeholder="filter.name"
                                    :suffix-icon="filter.icon"
                                    :type="filter.type"
                                    class="list-table-search"
                                    @change="filterChanged(filter)"
                                    v-model="filterModel[filter.key]">
                                </el-input>
                                <el-input
                                    v-else
                                    clearable
                                    :placeholder="filter.name"
                                    :prefix-icon="filter.icon"
                                    :type="filter.type"
                                    class="list-table-search"
                                    @change="filterChanged(filter)"
                                    v-model="filterModel[filter.key]">
                                </el-input>
                            </el-form-item> -->
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
                            <el-form-item v-else-if="filter.type === filterTypes.daterange">
                                <el-date-picker
                                    v-model="dateRange"
                                    type="daterange"
                                    align="right"
                                    unlink-panels
                                    :range-separator="$t('general.date_range.range_separator')"
                                    :start-placeholder="filter.name_from"
                                    :end-placeholder="filter.name_to"
                                    format="dd.MM.yyyy"
                                    value-format="dd.MM.yyyy"
                                    :picker-options="pickerOptions"
                                    popper-class="custom-picker-panel"
                                    @change="dateRangeChanged(filter)"
                                >
                                </el-date-picker>
                            </el-form-item>
                             <el-form-item v-else-if="filter.type === filterTypes.toggle">
                                <el-button
                                    class="toggle-filter-button"
                                    @click="toggleFilters"
                                >
                                    {{ !showFilters? $t('general.filters.more_filters'):$t('general.filters.less_filters') }}
                                </el-button>
                            </el-form-item>
                             <el-form-item v-else-if="filter.type === filterTypes.popover">
                                <el-popover placement="bottom-end" trigger="click" :width="192" style="float:right">
                                    <el-button slot="reference" class="filter-button">{{ filter.name }}</el-button>
                                    <!-- <el-button class="popover-button" @click="visibleSaveDialog=true">{{ $t('general.actions.save') }}</el-button> -->
                                </el-popover>
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
            @row-click="handleRowClick"
            @row-dblclick="handleRowDblClick"
            @selection-change="handleSelectionChange"
            v-loading="loading.state">

            <el-table-column
                :key="'header' + index"
                :label="$t(column.label)"
                :width="column.width"
                :min-width="column.minWidth"
                :class-name="column.withRequestUsers ? 'request-users' : ''"
                :align="column.align"
                v-for="(column, index) in computedHeader">
                
                <template slot-scope="scope">
                    <div v-if="column.withAvatars" class="avatars-wrapper">
                        <div class="user-details" v-if="scope.row['user']">
                            <div class="image">
                                <table-avatar :src="scope.row['user'].avatar" :name="scope.row['user'].name" :size="33" />
                            </div>
                            <div class="title">
                                {{ scope.row['user'].name }}
                            </div>
                        </div> 
                    </div> 
                    <div v-else-if="column.withAvatarsAndProps" class="avatar-with-multiprops">
                        <table-avatar :src="scope.row['user'].avatar" :name="scope.row['user'].name" :size="33" />
                        <div class="avatar-info">
                            <component :class="{'listing-link': column.withLinks}" :is="column.withLinks ? 'router-link':'div'"
                                    :key="prop" :to="{name: 'adminUnits', query: { page : 1, per_page : 20, building_id : scope.row.id }}"
                                    v-for="(prop, ind) in column.props" v-if="scope.row[prop]">
                                {{scope.row[prop]}}
                            </component>
                        </div>
                    </div>
                    <span v-else-if="column.roles">
                        {{ $t(`general.roles.${$constants.propertyManager.type[scope.row[column.prop]]}`) }}
                    </span>
                    <div v-else-if="column.withMultipleProps">
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
                    </div>
                    <div v-else-if="column.withCollapsables">
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
                    </div>
                    <div v-else-if="column.withCounts">
                        <request-count :countsData="items[scope.$index]" ></request-count>
                    </div>
                    <div v-else-if="column.withRelationCounts">
                        <relation-count :countsData="items[scope.$index]" ></relation-count>
                    </div>
                    <div v-else-if="column.withMultiplePropsString">
                        <span v-for="prop in column.props">{{scope.row[prop]}} </span>
                    </div>
                    <div v-else-if="column.withServiceCategory">
                        {{$t(`models.service.category.${$constants.serviceProviders.category[scope.row[column.prop]]}`)}}
                    </div>
                    <div v-else-if="column.withInternalQuarterIds">
                        <div class="internal-quarter-wrapper">
                            <span class="internal-quarter" v-for="item in scope.row[column.prop].slice(0, 3)">{{item}}</span>
                            <span v-if="scope.row[column.prop].length > 3" class="internal-quarter internal-quarter_count">+{{scope.row[column.prop].length - 3}}</span>
                        </div>
                    </div>
                    <div v-else-if="column.withStatusSign">
                        <el-tooltip
                            :content="`${$t(`models.unit.status.${$constants.relations.status[scope.row[column.prop]]}`)}`"
                            class="item"
                            effect="light" placement="top"
                        >
                            <avatar 
                                :background-color="$constants.status_colorcode[scope.row[column.prop]]"
                                :initials="''"
                                :size="30"
                                :style="{'z-index': (800 - index)}"
                                :username="''"
                            />
                        </el-tooltip>
                    </div>
                    <div v-else-if="column.withPMStatusSign">
                        <el-tooltip
                            :content="`${$t(`general.status.${$constants.propertyManager.status[scope.row[column.prop]]}`)}`"
                            class="item"
                            effect="light" placement="top"
                        >
                            <avatar 
                                :background-color="$constants.status_colorcode[scope.row[column.prop]]"
                                :initials="''"
                                :size="30"
                                :style="{'z-index': (800 - index)}"
                                :username="''"
                            />
                        </el-tooltip>
                    </div>
                    <div v-else-if="column.withResidentStatusSign">
                        <el-tooltip
                            :content="`${$t(`models.resident.relation.status.${$constants.relations.status[scope.row[column.prop]]}`)}`"
                            class="item"
                            effect="light" placement="top"
                        >
                            <avatar 
                                :background-color="$constants.status_colorcode[scope.row[column.prop]]"
                                :initials="''"
                                :size="30"
                                :style="{'z-index': (800 - index)}"
                                :username="''"
                            />
                        </el-tooltip>
                    </div>
                    <div v-else-if="column.withTranslatedFloor">
                        {{getTranslatedFloorOfUnit(scope.row)}}
                    </div>
                    <div v-else-if="column.withStatus">
                        <div class="avatars-wrapper">
                            <span :key="index" v-for="(status, index) in $constants.relations.status">
                                <el-tooltip
                                    :content="`${$t(`models.unit.status.${status}`)}: ${scope.row[`${status}_units_count`]}`"
                                    class="item"
                                    effect="light" placement="top"
                                >
                                    <avatar 
                                        :background-color="$constants.relations.status_colorcode[index]"
                                        color="#fff"
                                        :initials="`${scope.row[`${status}_units_count`]}`"
                                        :size="30"
                                        :style="{'z-index': (800 - index)}"
                                        :username="`${scope.row[`${status}_units_count`]}`"
                                    />
                                </el-tooltip>
                            </span>
                        </div>
                    </div>
                    <div v-else-if="column.withResidentTypes">
                        {{ showResidentTypes(scope.row[column.prop]) }}
                    </div>
                    <div v-else-if="column.withReuqestIDAndTitle">
                        <div class="request-format">
                            <strong>{{scope.row.request_format}}</strong>                    
                        </div>
                        <span>{{scope.row.title}}</span>
                    </div>
                    <div v-else-if="column.withRequestStatusSign">
                        <el-tooltip
                            :content="`${$t(`models.request.status.${$constants.requests.status[scope.row[column.prop]]}`)}`"
                            class="item"
                            effect="light" placement="top"
                        >
                            <avatar 
                                :background-color="$constants.requests.status_colorcode[scope.row[column.prop]]"
                                :initials="''"
                                :size="30"
                                :style="{'z-index': (800 - index)}"
                                :username="''"
                            />
                        </el-tooltip>
                    </div>
                    <div v-else-if="column.withRequestCreator">
                        <div>
                            <strong>{{scope.row.creator.name}}</strong>                    
                        </div>
                        <span>{{(scope.row.created_at.split(" "))[0]}}</span>
                    </div>
                    <div v-else-if="column.i18n">
                        {{column.i18n(scope.row[column.prop])}}
                    </div>
                    <div v-else-if="column.withRequestVisible">
                        <span class="icon-container">
                             <i class="icon-eye"></i>
                        </span>
                    </div>
                    <div v-else-if="column.withRequestAppendix">
                        <span class="icon-container">
                             <i class="icon-doc-text"></i>
                        </span>
                    </div>
                    <div v-else-if="column.withIcon">
                        <span class="icon-container">
                             <i class="el-icon-document"></i>
                        </span>
                    </div>
                    <div v-else-if="column.withUsers">
                        <div class="avatars-wrapper">
                            <span :key="uuid()" v-if="index<2" v-for="(user, index) in scope.row[column.prop]">
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
                            <avatar class="avatar-count" :size="28" :username="`+ ${scope.row[column.prop].length-2}`"
                                    color="#fff"
                                    v-if="scope.row[column.prop].length>2"></avatar>
                        </div>
                    </div>
                    <div v-else-if="column.withRequestUsers">
                        <div class="avatars-wrapper">
                            <span :key="uuid()" v-if="index<2" v-for="(user, index) in scope.row[column.prop]">
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
                            <avatar class="avatar-count" :size="28" :username="`+ ${scope.row[column.prop].length-2}`"
                                    color="#fff"
                                    v-if="scope.row[column.prop].length>2"></avatar>
                            <div class="quick-assign-avatar"> 
                            <el-dropdown placement="bottom" trigger="click">
                                <el-button size="mini" class="more-actions" >
                                    <i class="el-icon-user"></i>
                                </el-button>
                                
                                <el-dropdown-menu slot="dropdown" class="quick-assign-dropdown" :visible-change="handleVisibleChange">
                                    
                                    <el-dropdown-item
                                            command="quick-assign"
                                    >
                                        <div class="header-box">
                                            <strong>{{$t(column.label)}}</strong>
                                            <i class="el-icon el-icon-close"></i>
                                        </div>
                                        <!-- <multi-select
                                            :type="quarterFilter.key"
                                            :name="quarterFilter.name"
                                            :data="quarterFilter.data"
                                            :maxSelect="1"
                                            :showMultiTag="true"
                                            :selectedOptions="[model.quarter_id]"
                                            @select-changed="handleSelectChange($event, 'quarter')"
                                        >
                                        </multi-select> -->
                                        <div class="content-box">
                                            <el-select
                                                    :loading="remoteLoading"
                                                    :placeholder="$t('models.request.name_or_email')"
                                                    :remote-method="search =>remoteSearchAssignees(search, scope.row.id)"
                                                    filterable
                                                    remote
                                                    reserve-keyword
                                                    @change="val => handleQuickAssign(scope.row.id, val)"
                                                    v-model="assignee">
                                                <el-option
                                                        :key="assignee.id"
                                                        :label="assignee.name"
                                                        :value="assignee.id"
                                                        v-for="assignee in assignees"/>
                                            </el-select>

                                            <span>{{$t('models.request.or')}}</span>
                                            
                                            <el-button @click="() => handleAssignMe(scope.row.id)">
                                                {{$t('models.request.assign_me')}}
                                            </el-button>
                                        </div>
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        
                            
                        </div>
                        </div>
                        
                        
                    </div>
                    <template v-else-if="column.withBadges">
                        <el-button v-if="scope.row[column.prop] == 'low'" class="btn-priority-badge btn-badge" :size="column.size" round>{{ scope.row[column.prop] }}</el-button>
                        <el-button v-else-if="scope.row[column.prop] == 'normal'" plain type="warning" class="btn-priority-badge btn-badge" :size="column.size" round>{{ scope.row[column.prop] }}</el-button>
                        <el-button v-else-if="scope.row[column.prop] == 'urgent'" plain type="danger" class="btn-priority-badge btn-badge" :size="column.size" round>{{ scope.row[column.prop] }}</el-button>
                    </template>
                    <template v-else-if="column.select">
                        <el-select class="select-icon" :class="column.class" @change="column.select.onChange(scope.row)" v-model="scope.row[column.prop]" :style="{width: '100%', maxWidth: column.ShowCircleIcon != undefined? '120px': '150px'}">
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
                    <template v-else-if="column.actions">
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
                                            <i class="ti-search"></i>
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
                                        <i class="ti-search"></i>
                                        <span>{{ $t('general.actions.edit') }}</span>    
                                    </template>
                                    <template v-else-if="action.title.indexOf('edit') !== -1">
                                        <router-link :to="{name: 'adminPropertyManagersEdit',  params: { id:scope.row['id']}}" class="el-menu-item-link">
                                            <i class="ti-search"></i>
                                            <span>{{ $t('general.actions.edit') }}</span>
                                        </router-link>      
                                    </template>
                                    <template v-else-if="action.title == 'Delete'">
                                        <i class="ti-close"></i>
                                        <span>{{$t(action.title)}}</span>    
                                    </template>
                                    <template v-else>
                                        <i class="ti-search"></i>
                                        <span>{{ $t(action.title) }}</span>
                                    </template>
                                </el-button>
                            </template>
                        </span>
                    </template>
                    <span v-else>
                        {{ _.get(scope.row, column.prop) }}
                    </span>
                    
                </template>
            </el-table-column>

            <el-table-column
                type="selection"
                v-if="withCheckSelection"
                width="60">
            </el-table-column>
        </el-table>
        <el-pagination
            :class="{'sticky-bottom': items.length < page.currSize}"
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
    import RelationCount from 'components/RelationCount'
    import tableAvatar from 'components/Avatar';
    import RequestDetailCard from 'components/RequestDetailCard';
    import SelectLanguage from 'components/SelectLanguage';
    import ListFilterSelect from 'components/Select';
    import FormatDateTimeMixin from 'mixins/formatDateTimeMixin';
    import globalFunction from "helpers/globalFunction";
    import {displayError, displaySuccess} from "helpers/messages";
    import MultiSelect from 'components/Select';
    import {mapGetters, mapActions} from 'vuex';

    export default {
        name: 'ListTable',
        components: {
            Avatar,
            RequestCount,
            RelationCount,
            'table-avatar': tableAvatar,
            RequestDetailCard,
            SelectLanguage,
            ListFilterSelect,
            FormatDateTimeMixin,
            MultiSelect
        },
        mixins: [globalFunction],
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
            },
            searchText: {
                type: String,
                default: () => ''
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
                    daterange: 'daterange',
                    language: 'language',
                    role: 'role',
                    toggle: 'toggle',
                    popover: 'popover',
                },
                filterModel: {},
                uuid,
                selectedItems: [],
                subMenu: [],
                dateRange: '',
                showFilters: false,
                assignees: [],
                assignee: '',
                remoteLoading: false,
                isVisible: false,
            }
        },
        computed: {
            ...mapGetters({
                user: 'loggedInUser',
            }),
            emptyText() {
                return this.loading.state ?  ' ' : (this.items.length > 0) ? '' : this.$t('general.no_data_available');
            },
            page() {
                return {
                    currPage: this.pagination.currPage,
                    currSize: this.pagination.currSize
                }
            },
            computedHeader() {
                this.header.forEach((filter) => {
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
                    }
                });
                return this.header;
            },
            filterColSize() {
                return 4;
            },
            pickerOptions: function() {
                const lastWeek = {
                    text: this.$t('general.date_range.last_week'),
                    onClick(picker) {
                    let end = subDays(new Date(), 7);
                    const start = startOfWeek(end);
                    end = lastDayOfWeek(end);

                    picker.$emit('pick', [start, end]);
                    }
                };
                const last7Days = {
                    text: this.$t('general.date_range.last_7_days'),
                    onClick(picker) {
                    const end = new Date();
                    const start = new Date();
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                    picker.$emit('pick', [start, end]);
                    }
                };
                const last14Days = {
                    text: this.$t('general.date_range.last_14_days'),
                    onClick(picker) {
                    const end = new Date();
                    const start = new Date();
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 14);
                    picker.$emit('pick', [start, end]);
                    }
                };
                const last30Days = {
                    text: this.$t('general.date_range.last_30_days'),
                    onClick(picker) {
                    const end = new Date();
                    const start = new Date();
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                    picker.$emit('pick', [start, end]);
                    }
                };
                const lastMonth = {
                    text: this.$t('general.date_range.last_month'),
                    onClick(picker) {
                    let end = subMonths(new Date(), 1);
                    const start = startOfMonth(end);
                    end = lastDayOfMonth(end);

                    picker.$emit('pick', [start, end]);
                    }
                };
                const last3Months = {
                    text: this.$t('general.date_range.last_3_months'),
                    onClick(picker) {
                    const end = new Date();
                    const start = new Date();
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                    picker.$emit('pick', [start, end]);
                    }
                };
                const last6Months = {
                    text: this.$t('general.date_range.last_6_months'),
                    onClick(picker) {
                    const end = new Date();
                    const start = new Date();
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 183);
                    picker.$emit('pick', [start, end]);
                    }
                };
                const lastYear = {
                    text: this.$t('general.date_range.last_year'),
                    onClick(picker) {
                    const end = new Date();
                    const start = new Date();
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 365);
                    picker.$emit('pick', [start, end]);
                    }
                };
                const last2Years = {
                    text: this.$t('general.date_range.last_2_years'),
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 730);
                        picker.$emit('pick', [start, end]);
                    }
                }
                
                return {
                    shortcuts: [last7Days, last14Days, last30Days, lastWeek, lastMonth, last3Months]
                };
            },
        },
        methods: {
            ...mapActions([ 
                'getAllAdminsForRequest',
                'assignUsersToRequest'
            ]),
            makeFilterQuery(pathName) {
                let query = {};
                let quarter_ids = localStorage.getItem('quarter_ids');
                let building_ids = localStorage.getItem('building_ids');

                if(quarter_ids !== undefined && quarter_ids) {
                    quarter_ids = JSON.parse(quarter_ids);
                } else 
                    quarter_ids = null;
                if(building_ids !== undefined && building_ids)
                    building_ids = JSON.parse(building_ids);
                else
                    building_ids = null;

                if((pathName == 'adminBuildings' || pathName == 'adminUnits') && quarter_ids !== null) 
                    query.quarter_ids = quarter_ids;
                if(pathName == 'adminUnits' && building_ids !== null)
                    query.building_id = building_ids;

                this.$router.push({
                    name: pathName,
                    query: query
                });
            },
            handleVisibleChange(isVisible) {
                this.isVisible = isVisible
            },
            async handleQuickAssign(request_id, assignee_id) {
                let assignee = this.assignees.find(item => item.id == assignee_id)
                let user_params = [{user_id: assignee_id, role: assignee.roles[0].name}]

                let resp = await this.assignUsersToRequest({
                            id: request_id,
                            user_params: user_params
                        });
                console.log(resp)
                if (resp && resp.data) {
                    displaySuccess(resp) 
                    
                    let current_index = -1

                    this.items.map((item, index) => {
                        if(item.id == resp.data.id)
                            current_index = index
                    })
                    
                    if(current_index != -1) {
                        this.$emit('update-row', current_index, resp.data)
                    }
                    
                    this.assignees = []
                    this.assignee = ''
                }
            },
            async handleAssignMe(request_id) {
                let loggedinUser = this.$store.getters.loggedInUser
                let user_params = [{user_id: loggedinUser.id, role: loggedinUser.roles[0].name}]

                let resp = await this.assignUsersToRequest({
                            id: request_id,
                            user_params: user_params
                        });

                if (resp && resp.data) {
                    displaySuccess(resp)
                    let current_index = -1

                    this.items.map((item, index) => {
                        if(item.id == resp.data.id)
                            current_index = index
                    })
                    
                    if(current_index != -1) {
                        this.$emit('update-row', current_index, resp.data)
                    }
                        
                    this.assignees = []
                    this.assignee = ''
                }
            },
            toggleFilters() {
                this.showFilters = !this.showFilters;
            },
            rowClicked(row) {
                this.$refs.tableData.toggleRowExpansion(row);
            },
            handleRowClick(row) {
                this.$refs.tableData.toggleRowSelection(row);
            },
            handleRowDblClick(row) {
                let currRouteName = this.$route.name;
                let name = '';
                name = `${currRouteName}Edit`;
                if(name == 'adminPinboardEdit') {
                    this.$router.push({
                        name: name,
                        params: {
                            id: row.id,
                            type: this.$constants.pinboard.type[row.type],
                        }
                    });
                }
                else {
                    this.$router.push({
                        name: name,
                        params: {
                            id: row.id,
                        }
                    });
                }
            },
            selectChanged(e, row, column) {
                row[column.prop] = e;
                column.select.onChange(row);
            },
            handleSelectChange(val, filter) {
                this.filterModel[filter.key] = val;
                this.filterChanged(filter);
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
                    query = {...query, search: this.search};
                } else if (this.withSearch) {
                    delete query.search;
                }
                for(var filter in this.filterModel) {
                    if((this.filterModel[filter] == '' || this.filterModel[filter] == null) && (query[filter] != undefined || query[filter] == null))
                    {
                        delete query[filter];
                        
                    }     
                }
                /*if(this.$route.name=='adminUsers') {
                    query = {roles: ['administrator'], ...query};
                    if(query.role)
                        delete query.roles;
                    else
                        delete query.role;
                }*/
                
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
            showResidentTypes(types) {
                if(types.constructor === Array){
                    let translatedTypes = types.map(type => this.$t(`models.resident.relation.type.${this.$constants.relations.type[type]}`))
                    return translatedTypes.join(', ')
                }
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

                if(this.filterModel[filter.key] == '')
                    this.filterModel[filter.key] = null
                    
                if ((!filter.parentKey && filter.fetch && init && this.filterModel[filter.key]) || !init) {
                    this.updatePage();
                }

                if (filter.type == this.filterTypes.language) {
                    this.updatePage();
                }
            },
            dateRangeChanged(filter) {
                if(this.dateRange) {
                    this.filterModel[filter.key_from] = this.dateRange[0]
                    this.filterModel[filter.key_to] = this.dateRange[1]
                }
                else {
                    this.filterModel[filter.key_from] = ''
                    this.filterModel[filter.key_to] = ''
                }
                this.updatePage();
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
            },
            async remoteSearchAssignees(search, request_id) {
                if (search === '') {
                    this.assignees = [];
                } else {
                    this.remoteLoading = true;

                    try {
                        const data = await this.getAllAdminsForRequest({request_id: request_id, is_get_function: true, search})

                        this.assignees = data;

                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
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
            },
        },
        created() {
            if (this.$route.query.search) {
                this.search = this.$route.query.search;
            }

            _.each(this.filters, (filter) => {
                let queryFilterValue = this.$route.query[filter.key];
                
                const dateReg = /^\d{2}([./-])\d{2}\1\d{4}$/;
                let value;

                if((filter.key !== 'search') && queryFilterValue !== undefined && !Array.isArray(queryFilterValue))
                    value = [queryFilterValue];
                else
                    value = queryFilterValue;
                    
                if(!Array.isArray(value))
                    value = queryFilterValue && ( queryFilterValue.match(dateReg) || filter.key == 'search') ? queryFilterValue : parseInt(queryFilterValue); // due to parseInt 0007 becomes 7
                else if(filter.key !== 'cities')
                    for(let i = 0; i < value.length; i ++)
                        value[i] = parseInt(value[i]);

                this.$set(this.filterModel, filter.key, value);
                if(filter.key == "search") {
                    this.$set(this.filterModel, 'search', queryFilterValue);
                }

                if (!this.filterModel[filter.key]) {
                    delete this.filterModel[filter.key];
                }

                if (this.filterModel[filter.key] || (!filter.parentKey && filter.fetch)) {
                    this.filterChanged(filter, true);
                }
            });

            this.subMenu = this.$route.params.subMenu;
            if(this.subMenu != undefined && this.subMenu.children != undefined) {
                this.subMenu = this.subMenu.children;
                localStorage.setItem('subMenu', JSON.stringify(this.subMenu));
            } else {
                this.subMenu = JSON.parse(localStorage.getItem('subMenu'));
                if(!this.subMenu)
                    this.subMenu = [];
                else {
                    let result = false;
                    this.subMenu.forEach((item) => {
                        if(item.route.name == this.$route.name)
                            result = true;
                    });
                    if(!result) {
                        localStorage.setItem('subMenu', null);
                        this.subMenu = [];
                    }
                }
                this.$forceUpdate();
            }
            this.search = this.searchText;
        },
        updated() {
            if(this.search !== this.searchText) {
                this.$set(this.filterModel, 'search', this.searchText);
                this.filterChanged(this.filters[0]);
            }
        }
    }
</script>

<style lang="scss" scoped>
    .list-table {
        position: relative;
        :global(.el-card.filter-right .el-card__body) {
            float: right;
        }
        .el-row {
            &.filter-right {
                float: right;
            }
        }
        .icon-container {
            font-size: 24px;
        }
        .sticky-bottom {
            position: fixed;
            bottom: 0;
            width: 92%;
        }
        .sub-menu {
            position: absolute;
            top: 20px;
            left: 40px;
            z-index: 999;
            font-size: 18px;
            a {
                margin-right: 25px;
                text-decoration: none;
                padding: 0 5px 18px;
                color: var(--color-text-secondary);
                position: relative;
                font-family: 'Radikal Thin';
                cursor: pointer;
                &:hover, &.is-active {
                    color: var(--color-text-primary);
                    font-weight: 900px;
                    font-family: 'Radikal';
                    font-size: 17.2px;
                    &::after {
                        content: '';
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        width: 100%;
                        height: 5px;
                        background-color: var(--color-primary);
                        border-radius: 12px 12px 0 0;
                    }
                }
            }
        }
        .custom-select {
            :global(.el-button) {
                font-family: inherit;
            }
        }

        .request-format {
            color:var(--primary-color);
        }

        .toggle-filter-button {
            border-radius: 6px;
            padding: 13px 15px;
            background-color: #f6f5f7;
        }

    }

    .quick-assign-dropdown {
        .el-dropdown-menu__item {
            padding-right: 5px;

            &:not(.is-disabled):hover {
                background-color: transparent
            }

            .header-box {
                display: flex;
                color : var(--color-text-regular);


                i{
                    flex-grow: 1;
                    display: flex;
                    justify-content: flex-end;
                    align-items: top;
                    &:hover {
                        color : var(--primary-color);
                    }
                }
            }

            .content-box {
                display: flex;
                padding-right: 20px;

                > span {
                    padding: 0 10px;
                    color : var(--color-text-regular);
                }

                > .el-select {
                    border-radius: 10px;
                    /deep/ div, /deep/ input {
                        border-radius: 10px;
                    }
                }

                > .el-button {
                    border-radius: 10px;
                }
            }
        }
        
    }
    .remote-select {
        width: 100%;
        :global(input) {
            padding-right: 15px;
        }
        :global(span.el-input__suffix) {
            display: none;
        }
    }
    :global(.el-date-editor) {
        :global(.el-input__inner) {
            padding-right: 15px !important;
        }
    }
    .el-col {
        width: auto !important;
        height: 62px;
    }
    .avatar-count{
        min-width: 28px;
        margin-right: 2px;
    }
    .el-divider.el-divider--horizontal {
        width: 90%;
        margin: 0 auto !important;
    }

    .el-button {
        font-family: inherit;
    }
    .popover-button {
        width: 100%;
        border: none;
        background-color: var(--border-color-base);
    }
    .filter-button {
        height: 40px;
        background-color: #f6f5f7;
    }
    
    .el-input {
        font-family: inherit;
        &.el-input--suffix {
            :global(.el-input__inner) {
                padding-right: 30px;
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

        &:before {
            display: none;
        }

        :global(.el-table__body-wrapper) {
            box-shadow: 0 1px 3px transparentize(#000, .88),
            0 1px 2px transparentize(#000, .76);
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
                color: var(--color-text-primary);
                &:first-of-type {
                    padding-left: 32px;
                }
            }

            :global(td) {
                padding: 20px 4px;
                &:first-of-type {
                    padding-left: 36px;
                }
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
                    background-color: var(--background-color-base);
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

            :global(.el-collapse-item__content) {
                padding-bottom: 0;
            }
        }

        
    }

    .el-pagination {
        display: flex;
        padding: 8px 18px;

        :global(.el-pagination__sizes) {
            flex: 1;
        }

        :global(.el-select .el-input) {
            width: 120px;
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

    .request-users {
        
        .quick-assign-avatar {
            display: none;

            .el-dropdown {
                width : 30px;
                height: 30px;
                border-radius: 50%;
                border: 1px dashed black; 
            }
            .more-actions {
                padding: 0;
                background: transparent;
                
                width: 100%;
                height: 0;
                padding-top: 100%;
                position: relative;

                /deep/ span {
                    position: absolute;
                    font-size: 18px;
                    top: 6px;
                    left: 5.5px;
                }
            }
            
            
        }

        &:hover {

            // .avatars-wrapper {
            //     display: none;
            // }
            .quick-assign-avatar {
                display: flex;
            }
        }
    }

    .avatar-with-multiprops {
        display: flex;
        align-items: center;
        .avatar-info {
            margin-left: 15px;
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
                margin-right: 0px;
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

    .internal-quarter-wrapper {
        display: flex;
    }
    .internal-quarter {
        margin: 1px 2px 1px 0;
        display: inline-flex;
        padding: 0 4px;
        border-radius: 4px;
        background: #f0f0f0;
        white-space: nowrap;
        &_count {
            margin-right: 0;
            color: #3f4245;
            background: #cecfce;
            font-weight: 600;
        }
    }

</style>

<style lang="scss">
    .list-table {
        .el-checkbox__input {
            .el-checkbox__inner {
                height: 20px;
                width: 20px;
                border-radius: 6px;

                &::after {
                    height: 10px;
                    top: 2px;
                    left: 8px;
                }
            }

            &.is-indeterminate .el-checkbox__inner::before {
                top: 8px;
                height: 3px;
            }
        }
    }
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
        th.el-table-column--selection.is-leaf {
            padding-left: 0px;
            .cell {
                padding-left: 0px;
                text-align: left;
            }
        }
        tbody {
            tr {
                td:last-child:not(.is-left) {
                    padding-left: 0px;
                    .cell {
                        padding-left: 0px !important;
                        text-align: left;
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
        margin-bottom: 0 !important;
        border: none !important;
        border-radius: 0;
        padding-right: 15px;
        .el-card__body {
            padding: 0 20px;
            .el-form-item {
                margin-bottom: 0;
            }
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
