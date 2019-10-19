<template>
    <div class="list-latest-table">
        <el-table
            :data="items"
            :element-loading-background="loading.background"
            :element-loading-spinner="loading.icon"
            :element-loading-text="$t(loading.text)"
            :empty-text="emptyText"
            @selection-change="handleSelectionChange"
            v-loading="loading.state"
            :height="height"
        >
            <el-table-column
                :key="column.prop"
                :label="column.type == 'actions' ? '' : $t(column.label)"
                :width="column.width"
                :min-width="column.minWidth"
                v-for="column in header"
            >
                <template slot-scope="scope">
                    <span v-if="column.type == 'plain'" :style="column.style">
                        {{ scope.row[column.prop] }}
                    </span>

                    <div v-if="column.type == 'multi-props'">
                        <component
                            :class="[{'listing-link': column.withLinks}, `multi-text-${prop}`]"
                            :is="column.withLinks ? 'router-link':'div'"
                            :key="prop" 
                            :to="buildRouteObject(column.route, scope.row)"
                            v-for="prop in column.props"
                        >
                            {{scope.row[prop]}}
                        </component>
                    </div>
               
                    <div v-if="column.type == 'listing-details'" class="listing-details">
                        <div class="image" v-if="scope.row['image_url']" :style="{backgroundImage: `url(${scope.row['image_url']})`}"></div>
                        <div class="image" v-else :style="{backgroundImage: `url(${listingDefaultImg})`}"></div>
                        <div class="text">
                            <div class="title">
                                {{ scope.row['title'] }}
                            </div>
                            <div class="date">
                                {{ scope.row['created_at'] }}
                            </div>
                        </div>
                    </div>
                    <div v-if="column.type == 'resident-details'" class="user-details">
                        <div class="image">
                            <table-avatar :src="scope.row['image_url']" :name="scope.row['name']" :size="33" />
                        </div>
                        <div class="title">
                            {{ scope.row['name'] }}
                        </div>
                    </div>
                    <div v-if="column.type == 'pinboard-title'" class="user-details">
                        <!-- <div class="image" v-if="scope.row['image_url']" :style="{backgroundImage: `url(${scope.row['image_url']})`}"></div>
                        <div class="image" v-else :style="{backgroundImage: `url(${userMaleDefaultImg})`}"></div> -->
                        <div class="image">
                            <table-avatar :src="scope.row['user'].avatar" :name="scope.row['user'].name" :size="33" />
                        </div>
                        <div v-if="scope.row['content']" class="text">
                            <div class="title">
                                {{ scope.row['content'] }}
                            </div>
                        </div>
                    </div>
                   
                    <div v-if="column.type == 'users'" class="avatars-wrapper">
                        <span :key="index" v-for="(user, index) in scope.row[column.prop]" v-if="index<2">
                            <el-tooltip
                                :content="user.first_name ? `${user.first_name} ${user.last_name}`: (user.user ? `${user.user.name}`:`${user.name}`)"
                                class="item"
                                effect="light" placement="top"
                            >
                                <template v-if="user.user">
                                    <avatar 
                                        :size="28"
                                        :username="user.first_name ? `${user.first_name} ${user.last_name}`: (user.user ? `${user.user.name}`:`${user.name}`)"
                                        backgroundColor="rgb(205, 220, 57)"
                                        color="#fff"
                                        v-if="!user.user.avatar"
                                    />
                                    <avatar
                                        :size="28"
                                        :src="`/${user.user.avatar}`"
                                        v-else
                                    />
                                </template>
                                <template v-else>
                                    <avatar
                                        :size="28"
                                        :username="user.first_name ? `${user.first_name} ${user.last_name}`: `${user.name}`"
                                        backgroundColor="rgb(205, 220, 57)"
                                        color="#fff"
                                        v-if="!user.avatar"
                                    />
                                    <avatar :size="28" :src="`/${user.avatar}`" v-else></avatar>
                                </template>
                            </el-tooltip>

                        </span>
                        <avatar
                            class="avatar-count" :size="28" :username="`+ ${scope.row[column.prop].length - 2}`"
                            color="#fff"
                            v-if="scope.row[column.prop].length>2"
                        />
                    </div>

                    
                    <div v-if="column.type == 'counts'" class="avatars-wrapper square-avatars">        
                         <request-count :countsData="items[scope.$index]"></request-count>
                    </div>
                    <el-tag
                        v-if="column.type == 'tag'"
                        :class="`tag-${scope.row[column.classSuffix]}`"
                        :size="column.size" class="btn-badge"
                    >
                        {{ $t(scope.row[column.prop]) }}
                    </el-tag>
                    <div v-if="column.type == 'icon-circle'">
                        <i :class="`icon-dot-circled ${iconCircleClass(scope.row['status'])}`"></i>
                        {{ $t(scope.row[column.prop]) }}
                    </div>

                    <el-select
                         v-if="column.type == 'select'"
                        class="select-icon"
                        :class="column.class" @change="column.select.onChange(scope.row)"
                        v-model="scope.row[column.prop]"
                    >
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

                    <div v-if="column.type == 'actions'">
                        <span
                        :key="action.title"
                        class="btn-wrap"
                        v-for="action in column.actions">
                        <template
                            v-if="(!action.permissions || ( action.permissions && $can(action.permissions))) && (!action.hidden || (action.hidden && !action.hidden(scope.row)))"
                        >
                            <template v-if="action.editUrl">
                                <router-link :to="{
                                    name: action.editUrl,  
                                    params: { 
                                        type:$constants.pinboard.type[scope.row['type']], 
                                        id:scope.row['id'] }
                                    }" 
                                    class="el-menu-item-link">            
                                                              
                                        <el-button
                                            :style="action.style"
                                            :type="action.type"
                                            size="mini"
                                        >
                                            <i :class="action.icon ? action.icon : 'ti-search'"></i>
                                            <span>{{ $t(action.title) }}</span>
                                        </el-button>  
                                </router-link>      
                            </template>
                        </template>
                    </span>
                    </div>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script>
    // TODO - add transition to do things smoothly
    import {Avatar} from 'vue-avatar'
    import uuid from 'uuid/v1'
    import RequestCount from 'components/RequestCount.vue';
    import tableAvatar from 'components/Avatar';

    export default {
        name: 'ListLatestTable',
        components: {
            Avatar,
            RequestCount,
            'table-avatar': tableAvatar
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
            loading: {
                type: Object,
                default: () => ({
                    state: false,
                    text: 'general.loading',
                    icon: 'el-icon-loading',
                    background: 'rgba(0, 0, 0, 0.8)'
                })
            },
            height: {
                default: () => (undefined)
            },
        },
        data() {
            return {
                uuid,
                selectedItems: [],
                userMaleDefaultImg: require('img/male.png'),
                userFemaleDefaultImg: require('img/female.png'),
                listingDefaultImg: require('img/latest-listing-default.png'),

            }
        },
        computed: {
            emptyText() {
                return this.loading.state ? this.$t('please_wait') : this.$t('general.no_data_available');
            }
        },
        methods: {
            onSizeChange(newSize) {
                this.updatePage(1, newSize);
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
            iconCircleClass(status) {
                const classObject = {
                    '1': 'icon-success',
                    '2': 'icon-danger'
                };

                return classObject[status];
            }
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

    
    .el-button {
        border-radius: 20px;
        padding: 8.65px 15px;
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
    }

    .mt30 {
        margin-top: 30px;
    }

    .mb30 {
        margin-bottom: 20px;
    }

    .avatars-wrapper {
        display: flex;

        & > span {
            margin-right: 2px;
        }

        .vue-avatar--wrapper {
            font-size: 12px !important;
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
    .el-menu-item-link i{
        margin: 0 0;
    }
    .el-table td:last-child, .el-table th:last-child{
        text-align: center;
    }
    .list-latest-table {
        .el-table .el-table__body-wrapper {
            box-shadow: none;
        }
        .el-table {
            border-bottom: 1px solid #EBEEF5;
        }
        text-align: center;
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
        .listing-details {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding-top: 5px;
            padding-bottom: 5px;
            width: 100%;

            .image {
                border: 1px solid #e2e5ea;
                border-radius: 7px;
                width: 55px;
                height: 55px;
                min-width: 55px;
                min-height: 55px;
                margin-right: 20px;
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
                .date {
                    font-size: 0.75rem;
                    color: #919bac;
                }
            }
        }

        .el-tag {
            color: #606266;
            border: 1px solid #dcdfe6;
            background-color: #fff;
            text-align: center;

            &.tag-1 {
                color: #409eff;
                background: #ecf5ff;
                border-color: #b3d8ff;
            }
            &.tag-2 {
                color: #67c23a;
                background: #f0f9eb;
                border-color: #c2e7b0;
            }
            &.tag-3 {
                color: #909399;
                background: #f4f4f5;
                border-color: #d3d4d6;
            }
            &.tag-4 {
                color: #e6a23c;
                background: #fdf6ec;
                border-color: #f5dab1;
            }
            &.tag-5 {
                color: #f56c6c;
                background: #fef0f0;
                border-color: #fbc4c4;
            }

            &.tag-active {
                color: #5fad64;
                background: #f0f9eb;
                border-color: #b2d7c0;
            }
            &.tag-not_active {
                color: #dd6161;;
                background: #fef0f0;
                border-color: #ebb4b4;
            }
        }
    }
</style>
