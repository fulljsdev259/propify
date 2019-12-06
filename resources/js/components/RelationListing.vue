<template>
    <div class="listing">
        <el-table
            :data="list"
            :show-header="showHeader"
            style="width: 100%"
            @row-dblclick="handleRowDblClick"
            >
            <div slot="empty">
                <el-alert                                     
                    :title="$t('general.no_data_available')"
                    type="info"
                    show-icon
                    :closable="false"
                    class="no_data_box"
                >
                </el-alert>
            </div>
            <el-table-column
                :key="column.prop"
                :width="column.width"
                :align="column.align"
                :label="$t(column.label)"
                :prop="column.prop"
                v-for="column in columns"
                v-if="!column.i18n"
            >
                <template slot-scope="scope">
                    <div v-if="column.type === 'requestIcon'">
                        <span class="request-icon icon-chat-empty"></span>
                    </div>
                    <div v-else-if="column.type === 'requestTitleWithDesc'">
                        <div class="request-title">
                            <span class="normal">
                                #{{scope.row.request_format}}
                            </span> 
                            <span class="black">{{$t(`models.request.category_list.${scope.row.category.name}`)}}:</span> 
                            {{scope.row.title}}          
                        </div>
                        <div class="request-title">
                            {{$t(`general.on`)}} {{scope.row.created_at.slice(0, 10)}}, {{scope.row.created_at.slice(10, -3)}}
                        </div>
                        <!-- <div class="category">
                            <span>
                                {{$t(`models.request.category_list.${scope.row.category.name}`)}}
                            </span>
                            <span v-if="scope.row.sub_category">
                                &nbsp;>&nbsp; {{$t(`models.request.sub_category.${scope.row.sub_category.name}`)}}
                            </span>
                        </div> -->
                    </div>

                    <div v-else-if="column.type === 'requestStatus'">
                        <el-tooltip
                            :content="`${$t(`models.request.status.${$constants.requests.status[scope.row.status]}`)}`"
                            class="item"
                            effect="light" placement="top"
                        >
                            <span class="status-icon" :style="{ background: $constants.requests.status_colorcode[scope.row.status], border: '2px solid #ffffffe7'}" >&nbsp;</span>
                            <!-- <avatar 
                                :background-color="$constants.requests.status_colorcode[scope.row.status]"
                                :initials="''"
                                :size="30"
                                :style="{'z-index': (800 - index)}"
                                :username="''"
                            /> -->
                        </el-tooltip>
                    </div>

                    <div v-else-if="column.type === 'requestResidentAvatar'">
                        <el-tooltip
                                v-if="column.prop"
                                :content="`${scope.row[column.prop].first_name} ${scope.row[column.prop].last_name}`"
                                class="item"
                                effect="light" placement="top"
                        >
                            <avatar :size="30"
                                    :src="'/' + scope.row[column.prop].user.avatar"
                                    v-if="scope.row[column.prop].user.avatar"></avatar>
                            <avatar :size="28"
                                    backgroundColor="rgb(205, 220, 57)"
                                    color="#fff"
                                    :username="scope.row[column.prop].user.first_name ? `${scope.row[column.prop].user.first_name} ${scope.row[column.prop].user.last_name}`: `${scope.row[column.prop].user.name}`"
                                    v-if="!scope.row[column.prop].user.avatar"></avatar>
                        </el-tooltip>
                        <el-tooltip v-else
                                :content="`${scope.row.first_name} ${scope.row.last_name}`"
                                class="item"
                                effect="light" placement="top"
                        >
                            <avatar :size="30"
                                    :src="'/' + scope.row.user.avatar"
                                    v-if="scope.row.user.avatar"></avatar>
                            <avatar :size="28"
                                    backgroundColor="rgb(205, 220, 57)"
                                    color="#fff"
                                    :username="scope.row.user.first_name ? `${scope.row.user.first_name} ${scope.row.user.last_name}`: `${scope.row.user.name}`"
                                    v-if="!scope.row.user.avatar"></avatar>
                        </el-tooltip>
                    </div>

                    <div v-else-if="column.type === 'residentAvatarWithType'">
                        <el-tooltip v-if="column.prop"
                                :content="`${scope.row[column.prop].first_name} ${scope.row[column.prop].last_name}`"
                                class="item"
                                effect="light" placement="top"
                        >
                            <avatar :size="30"
                                    :src="'/' + scope.row[column.prop].user.avatar"
                                    v-if="scope.row[column.prop].user.avatar"></avatar>
                            <avatar :size="28"
                                    backgroundColor="rgb(205, 220, 57)"
                                    color="#fff"
                                    :username="scope.row[column.prop].user.first_name ? `${scope.row[column.prop].user.first_name} ${scope.row[column.prop].user.last_name}`: `${scope.row[column.prop].user.name}`"
                                    v-if="!scope.row[column.prop].user.avatar"></avatar>
                        </el-tooltip>
                        <el-tooltip v-else
                                :content="`${scope.row.first_name} ${scope.row.last_name}`"
                                class="item"
                                effect="light" placement="top"
                        >
                            <avatar :size="30"
                                    :src="'/' + scope.row.user.avatar"
                                    v-if="scope.row.user.avatar"></avatar>
                            <avatar :size="28"
                                    backgroundColor="rgb(205, 220, 57)"
                                    color="#fff"
                                    :username="scope.row.user.first_name ? `${scope.row.user.first_name} ${scope.row.user.last_name}`: `${scope.row.user.name}`"
                                    v-if="!scope.row.user.avatar"></avatar>
                        </el-tooltip>
                        <span>{{column.translate(scope.row.types)}}</span>
                    </div>

                    <div v-else-if="column.type === 'residentNameAndType'" class="normal">
                        {{scope.row.name}}
                        <div class="type">{{column.translate(scope.row.types)}}</div>
                    </div>

                    <div v-else-if="column.type === 'residentRelation'">
                        <relation-count :countsData="scope.row" ></relation-count>
                    </div>

                    <div v-else-if="column.type === 'residentMediaName'">
                        <div class="request-title normal">
                            <a v-if="scope.row.url" :href="scope.row.url" target="_blank"><strong>{{scope.row.name}}</strong></a>                      
                        </div>
                    </div>

                    <div v-else-if="column.type === 'buildingResidentAvatars'" class="avatars-wrapper">
                        <span class="resident-item" :key="uuid()" v-for="(resident) in scope.row[column.prop].slice(0, column.propLimit)">
                              <el-tooltip
                                      :content="resident.first_name ? `${resident.first_name} ${resident.last_name}`: (resident.user ? `${resident.user.name}`:`${resident.name}`)"
                                      class="item"
                                      effect="light" placement="top">
                                  <template v-if="resident.user">
                                      <avatar :size="28"
                                              :username="resident.first_name ? `${resident.first_name} ${resident.last_name}`: (resident.user ? `${resident.user.name}`:`${resident.name}`)"
                                              backgroundColor="rgb(205, 220, 57)"
                                              color="#fff"
                                              v-if="!resident.user.avatar"></avatar>
                                      <avatar :size="28" :src="`/${resident.user.avatar}`" v-else></avatar>
                                  </template>
                                  <template v-else>
                                      <avatar :size="28"
                                              :username="resident.first_name ? `${resident.first_name} ${resident.last_name}`: `${resident.name}`"
                                              backgroundColor="rgb(205, 220, 57)"
                                              color="#fff"
                                              v-if="!resident.avatar"></avatar>
                                      <avatar :size="28" :src="`/${resident.avatar}`" v-else></avatar>
                                  </template>
                              </el-tooltip>
                        </span>
                        <avatar class="avatar-count" :size="28" :username="`+ ${scope.row[column.count]}`"
                                color="#fff"
                                v-if="scope.row[column.count]"></avatar>
                    </div>

                    <div v-else-if="column.type === 'assignProviderManagerAvatars'">
                        <!-- <el-tooltip
                                :content="`${scope.row.name}`"
                                class="item"
                                effect="light" placement="top"
                        > -->
                            <avatar :size="30"
                                    :src="'/' + scope.row.avatar"
                                    v-if="scope.row.avatar"></avatar>
                            <avatar :size="28"
                                    backgroundColor="rgb(205, 220, 57)"
                                    color="#fff"
                                    :username="scope.row.name"
                                    v-if="!scope.row.avatar"></avatar>
                        <!-- </el-tooltip> -->
                    </div>

                    <div v-else-if="column.type === 'assignProviderManagerFunctions'">
                        {{scope.row.type == "provider" ? $t(`models.service.category.${$constants.serviceProviders.category[scope.row.function]}`)  : ''}}
                        {{scope.row.type == "manager" ? $t(`general.roles.${scope.row.role}`) : ''}}
                    </div>

                    <div v-else-if="column.type === 'unitResidentAvatar'">
                        <div class="avatars-wrapper">
                            <span :key="uuid()" v-for="(resident) in scope.row.activeResidents">
                                <el-tooltip
                                    :content="resident.user.name"
                                    class="item"
                                    effect="light" placement="top">
                                        <avatar :size="28"
                                                :username="resident.user.name"
                                                backgroundColor="rgb(205, 220, 57)"
                                                color="#fff"
                                                v-if="!resident.user.avatar"></avatar>
                                        <avatar :size="28" :src="`/${resident.user.avatar}`" v-else></avatar>
                                </el-tooltip>
                            </span>
                            <avatar class="avatar-count" :size="28" :username="`+ ${scope.row.activeResidentsCount}`"
                                    color="#fff"
                                    v-if="scope.row.activeResidentsCount"></avatar>
                        </div>
                        
                    </div>
                    <div v-else-if="column.type === 'residentStatusSign'">
                        <el-tooltip
                            :content="`${$t(`models.resident.relation.status.${$constants.relations.status[scope.row[column.prop]]}`)}`"
                            class="item"
                            effect="light" placement="top"
                        >
                            <avatar 
                                :background-color="$constants.status_colorcode[scope.row[column.prop]]"
                                :initials="''"
                                :size="30"
                                :username="''"
                            />
                        </el-tooltip>
                    </div>
                    <div v-else-if="column.type === 'assigneesName'" class="normal">
                        {{scope.row.name}}
                    </div>
                    <div v-else-if="column.type === 'companyName'" class="normal">
                        {{scope.row.company_name}}
                    </div>
                    <div v-else-if="column.type === 'buildingHouseName'" class="normal">           
                        <!-- {{scope.row.address ? scope.row.address.street + ' ' + scope.row.address.house_num : ''}}                 -->
                        {{scope.row.address ? scope.row.address.house_num : ''}}
                    </div>
                    <div v-else-if="column.type === 'buildingTypes'" class="normal">           
                        <!-- {{scope.row.address ? scope.row.address.street + ' ' + scope.row.address.house_num : ''}}                 -->
                        {{ scope.row.types ? (  scope.row.types.map(type => $t(`models.quarter.types.${$constants.buildings.type[type]}`)) ).join(", ") : ''}}
                    </div>
                    <div v-else-if="column.type === 'residentName'" class="normal"> 
                        {{scope.row.name}}
                    </div>
                    <div v-else-if="column.type === 'serviceName'" class="normal">                     
                        {{scope.row.name}}                  
                    </div>
                    <div v-else-if="column.type === 'multiProp'">
                        <span v-for="item in column.prop.split(' ')">
                            {{scope.row[item]}}
                        </span>
                    </div>
                    <div v-else>
                        {{scope.row[column.prop]}}
                    </div>
                </template>
            </el-table-column>
            <el-table-column
                :key="column.prop"
                :label="$t(column.label)"
                :align="column.align"
                v-for="column in columns"
                v-if="column.i18n"
            >
                <template slot-scope="scope">
                    <span v-if="column.withBadge">
                        <i :class="`icon-dot-circled ${column.withBadge(scope.row[column.prop])}`"></i>
                        {{column.i18n(scope.row[column.prop])}}
                    </span>
                    <template v-else>
                        {{column.i18n(scope.row[column.prop])}}
                    </template>
                </template>
            </el-table-column>
            <el-table-column
                :key="index"
                :width="action.width"
                align="right"
                class-name="action-buttons"
                v-for="(action, index) in actions"
            >
                <template slot-scope="scope">
                    <el-button
                        :icon="button.icon"
                        :key="button.title"
                        :style="button.style"
                        :type="button.type"
                        :class="button.class"
                        @click="button.onClick(scope.row)"
                        size="mini"
                        round
                        v-for="button in action.buttons"
                        v-if="!button.tooltipMode">
                        &nbsp;{{$t(button.title)}}
                    </el-button>
                    <el-tooltip
                        :content="$t(button.title)"
                        :key="button.title"
                        class="item" effect="light" placement="top-end"
                        v-for="button in action.buttons"
                        v-if="button.tooltipMode">
                        <el-button
                            :icon="button.icon"
                            :style="button.style"
                            :type="button.type"
                            :class="button.class"
                            @click="button.onClick(scope.row)"
                            v-if="button.view == 'request' && scope.row.type == 'provider' && scope.row.sent_email == false"
                            size="mini"
                            round
                        >
                        </el-button>
                        <el-button
                            :icon="button.icon"
                            :style="button.style"
                            :type="button.type"
                            @click="button.onClick(scope.row)"
                            v-else-if="button.view != 'request' && ((button.view == 'building') || (scope.row.type != 'user' && scope.row.type != 'manager') || button.type == 'danger')"
                            size="mini"
                            round
                        >
                        </el-button>
                    </el-tooltip>
                     <el-dropdown size="small" trigger="click" placement="bottom-end" @command="changeCommand" v-if="action.dropdowns">
                        <el-tooltip ref="visibility-button-tooltip" :content="$t('general.actions.label')">
                            <el-button type="text" class="el-dropdown-link">
                                <i class="icon-ellipsis-vert"></i>
                            </el-button>
                        </el-tooltip>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item v-for="(item, index) in action.dropdowns" :key="item.key" :command="item.key + ' ' + scope.$index" :divided="!! index">
                                {{$t(`${item.title}`)}}
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </template>
            </el-table-column>
            <!-- <el-table-column
                :key="index"
                :width="action.width"
                align="right"
                v-for="(action, index) in actions"
            >
                <template slot-scope="scope">
                     <el-dropdown size="small" trigger="click" placement="bottom-end" @command="changeCommand" v-if="action.dropdowns">
                        <el-tooltip ref="visibility-button-tooltip" :content="$t('general.actions.label')">
                            <el-button type="text" class="el-dropdown-link">
                                <i class="icon-ellipsis-vert"></i>
                            </el-button>
                        </el-tooltip>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item v-for="(item, index) in action.dropdowns" :key="item.key" :command="item.key + ' ' + scope.$index" :divided="!! index">
                                {{$t(`${item.title}`)}}
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </template>
            </el-table-column> -->
        </el-table>
        <div v-if="meta.current_page < meta.last_page">
            <el-button @click="loadMore" size="mini" style="margin-top: 15px" type="text">{{$t('general.load_more')}}</el-button>
        </div>
    </div>
</template>

<script>
    import {Avatar} from 'vue-avatar'
    import uuid from 'uuid/v1'
    import RelationCount from 'components/RelationCount'

    export default {
        components: {
            Avatar,
            RelationCount
        },
        props: {
            filter: {
                type: [String, Boolean],
                required: true
            },
            filterValue: {
                type: [Number, Boolean],
                required: true
            },
            fetchAction: {
                type: [String, Boolean],
                required: true
            },
            columns: {
                type: Array,
                default() {
                    return [];
                }
            },
            fetchStatus: {
                type: Boolean,
                default: () => true
            },
            actions: {
                type: Array,
                default() {
                    return [];
                }
            },
            addedAssigmentList: {
                type: Array
            },
            showHeader: {
                type: Boolean,
                default: false
            },
        },
        data() {
            return {
                list: [],
                meta: {},
                loading: false,
                uuid,
            }
        },
        async created() {
            if (!this.fetchStatus) {
                this.list = this.addedAssigmentList;
            } else {
                await this.fetch();
            }
        },
        async mounted() {
            if (!this.fetchStatus) {
                this.$root.$on('changeLanguage', () => this.fetch());
            }
        },
        watch: {
            addedAssigmentList: {
                immediate: true,
                handler() {
                    this.list = this.addedAssigmentList
                }
            }
        },
        methods: {
            async fetch(page = 1) {
                if (!this.fetchStatus) return;
                this.loading = true;
                try {
                    const resp = await this.$store.dispatch(this.fetchAction, {
                        [this.filter]: this.filterValue,
                        per_page: 5,
                        page
                    });


                    this.meta = _.omit(resp.data, 'data');
                    if(!resp.data) {
                        this.list = []
                        if(this.fetchAction == "getResidentMedia") {
                            if(page == 1)
                                this.list = resp
                            else
                                this.list.push(...resp)
                        }
                    }
                    else if (page === 1) {
                        
                        if(this.fetchAction == "getBuildings") {
                            resp.data.data.forEach(item => {
                                item.residents = item.relations.map(relation => relation.resident)
                                item.residents_count = item.residents.length > 2 ? (item.residents.length - 2) : 0;
                            })
                        }
                        
                        this.list = resp.data.data;
                        if(this.fetchAction == 'getUnits' || this.fetchAction == 'getUnitsWithResidents') {
                            this.unitsTypeLabelMap();
                        }
                        
                    } else {
                        if(this.fetchAction == "getBuildings") {
                            resp.data.data.forEach(item => {
                                item.residents = item.relations.map(relation => relation.resident)
                                item.residents_count = item.residents.length > 2 ? (item.residents.length - 2) : 0;
                            })
                        }
                        this.list.push(...resp.data.data);
                        if(this.fetchAction == 'getUnits' || this.fetchAction == 'getUnitsWithResidents') {
                            this.unitsTypeLabelMap();
                        }
                    }
                } catch (e) {
                    this.list = []
                    console.log(e);
                } finally {
                    this.loading = false;
                }
            },
            unitsTypeLabelMap() {
                this.list.map((unit) => {
                    if(unit.type == 1)
                        unit.typeLabel = this.$t('models.unit.type.apartment');
                    else
                        unit.typeLabel = this.$t('models.unit.type.business');
                })
            },
            loadMore() {
                if (this.fetchStatus && this.meta.current_page < this.meta.last_page) {
                    this.fetch(this.meta.current_page + 1);
                }
            },
            changeCommand(val) {
                var res = val.split(" ");
                this.$emit(res[0], res[1])
            },
            handleRowDblClick(item) {
                let name = '';
                let id = item.id;
                this.columns.forEach((column) => {
                    if(column.type === 'requestTitleWithDesc')
                        name = 'adminRequestsEdit';
                    else if(column.type === 'requestResidentAvatar')
                        name = 'adminResidentsEdit';
                    else if(column.type === 'residentAvatarWithType')
                        name = 'adminResidentsEdit';
                    else if(column.type === 'residentNameAndType')
                        name = 'adminResidentsEdit';
                    else if(column.type === 'assigneesName') {
                        if(item.type === 'manager')
                            name = 'adminPropertyManagersEdit';
                        else if(item.type === 'provider')
                            name = 'adminServicesEdit';
                        else if(item.type === 'user')
                            name = 'adminUsersEdit';
                        else if(item.type === 'quarter')
                            name = 'adminQuartersEdit';
                        else if(item.type === 'building')
                            name = 'adminBuildingsEdit';

                        id = item.edit_id;
                    }
                    else if(column.type === 'buildingHouseName')
                        name = 'adminBuildingsEdit';
                    else if(column.type === 'residentName')
                        name = 'adminResidentsEdit';
                    else if(column.type === 'serviceName')
                        name = 'adminServicesEdit';
                    else if(column.type === 'unitColumn')
                        name = 'adminUnitsEdit';
                });
                if(name !== '') {
                    this.$router.push({
                        name: name,
                        params: {id: id}
                    })
                }
            }
        }
    }
</script>
<style lang="scss">
    .el-table .cell, .el-table th div{
        overflow: hidden;
        text-overflow: unset;
    }

    .action-buttons {
        .cell {
            text-align: right;
            float: right;
        }
        
    }
</style>
<style lang="scss" scoped>    
    .listing {
        :global(.el-table__body-wrapper) {
            :global(table) {
                display: block;
                max-height: 270px;
                overflow-y: auto;
                &::-webkit-scrollbar{
                    width: 8px;
                }
                &::-webkit-scrollbar-thumb{
                    background-color: var(--color-text-placeholder);
                    border: 1px solid transparent;
                    border-radius: 11px;
                    background-clip: content-box;
                }
                &::-webkit-scrollbar * {
                    background: transparent;
                }
            }
        }
        // :global(.el-table__header-wrapper) {
        //     display: none;
        // }
    }
    .el-button--default {
        border-radius: 20px;
        height: 29px;
    }
    .request-title {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
    .category {
        color: #909399;
    }
    .avatars-wrapper {
        display: inline-flex;
        .resident-item,
        .avatar-count{
            &:not(:first-child) {
                margin-left: 2px;
            }
        }
    }
    .resident-link {
        display: inline-block;
        text-decoration: none;
    }
    .resident-item {
        display: inline-block;
    }
    .badge {
        color: #fff;
        display: flex;
        width: 100px;
        font-size: 12px;
        justify-content: center;
        border-radius: 25px;
    }
    .icon-success {
        color: #5fad64;
    }
    .icon-danger {
         color: #dd6161;
    }
    .request {
        .listing {
            .normal {
                a {
                    text-decoration: none;
                    color:var(--primary-color);

                    &:hover {
                        color:var(--primary-color-lighter);
                    }
                }

                
            }
        }
    }
    .normal {
        a {
            text-decoration: none;
            color:var(--primary-color);

            &:hover {
                color:var(--primary-color-lighter);
            }
        }

        .type {
            color: var(--color-text-regular);
        }
    }

    .request-icon {
        font-size: 30px;
    }
    .request-title {
        .normal {
            color:var(--primary-color);
        }

        .black {
            color:black;
        }
    }
    .status-icon {
        width: 13px;
        height: 13px;
        border-radius: 50%;
        display: block;
    }
</style>
