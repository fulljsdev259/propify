<template>
    <div class="request-card">
        <div class="request-card-header clearfix">
            <el-row  :gutter="20" type="flex">
                <el-col :span="3" class="request-aside">
                   <h4>{{ item.request_format }}</h4>
                </el-col>
                <el-col :span="15" class="request-title">
                    <h4>{{ item.title }}</h4>
                </el-col>
                <el-col :span="6" class="request-tail">
                    <el-select 
                        class="select-icon rounded-select"  
                        v-model="item.status" 
                        @change="$emit('onChange', $event)"
                    >
                        <template slot="prefix">
                        </template>
                        <el-option
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                            v-for="item in selectData">
                        </el-option>
                    </el-select>
                     <router-link
                            :to="{name: 'adminRequestsEdit',  params: { id:item.id}}">
                        <el-button
                            size="mini"
                        >
                            <i class="ti-search"></i>
                            {{ $t('general.actions.edit') }}
                        </el-button>
                    </router-link>
                    <!-- <router-link
                            :to="{name: 'adminRequestsView',  params: { id:item.id}}">
                        <el-button
                            size="mini"
                        >
                            <i class="ti-search"></i>
                            {{ $t('general.actions.view') }}
                        </el-button>
                    </router-link> -->
                </el-col>
            </el-row>
        </div>
        <div class="request-card-body">
            <el-row style="margin-bottom: 24px;" :gutter="20" type="flex" class="request-footer">
                <el-col :span="1" class="request-actions">
                    <el-checkbox @change="handleSelectionChanged"></el-checkbox>
                </el-col> 
                <el-col :span="1" class="request-actions">
                    <el-tooltip v-if="item.contract.building.id" :content="$t('models.request.go_to_building')" placement="top" effect="light">
                        <router-link :to="{name: 'adminBuildingsEdit', params: {id:item.contract.building.id}}" class="listing-link">
                             <i class="icon icon-commerical-building"></i>
                        </router-link>
                    </el-tooltip>
                </el-col>
                <el-col :span="1" class="request-actions">
                    <el-tooltip :content="$t('models.request.download_pdf.title')" placement="top" effect="light">
                        <a @click="$emit('pdf-download', item.id)"><i class="icon icon-file-pdf"></i></a>
                    </el-tooltip>
                </el-col>
                <el-col :span="4">
                    <span>{{ $t('models.request.assigned_property_managers') }}</span>
                        <div class="avatars-wrapper">
                        <span :key="index" v-for="(user, index) in item.property_managers">
                                <el-tooltip
                                    v-if="index < 2"
                                    :content="user.first_name ? `${user.first_name} ${user.last_name}`: (user.user ? `${user.user.name}`:`${user.name}`)"
                                    class="item"
                                    effect="light" placement="top">
                                    <template v-if="user.user">
                                        <avatar :size="30"
                                                :username="user.first_name ? `${user.first_name} ${user.last_name}`: (user.user ? `${user.user.name}`:`${user.name}`)"
                                                backgroundColor="rgb(205, 220, 57)"
                                                color="#fff"
                                                v-if="!user.user.avatar"></avatar>
                                        <avatar :size="30" :src="`/${user.user.avatar}`" v-else></avatar>
                                    </template>
                                    <template v-else>
                                        <avatar :size="30"
                                                :username="user.first_name ? `${user.first_name} ${user.last_name}`: `${user.name}`"
                                                backgroundColor="rgb(205, 220, 57)"
                                                color="#fff"
                                                v-if="!user.avatar"></avatar>
                                        <avatar :size="30" :src="`/${user.avatar}`" v-else></avatar>
                                    </template>
                                </el-tooltip>
                        </span>
                        <avatar class="avatar-count" :size="30" :username="`+ ${item.property_managers.length-2}`"
                                color="#fff"
                                v-if="item.property_managers.length>2"></avatar>
                    </div>
                </el-col>
                <el-col :span="4">
                    <span>{{ $t('models.request.assigned_service_providers') }}</span>
                        <div class="avatars-wrapper">
                        <span :key="index" v-for="(user, index) in item.service_providers">
                                <el-tooltip
                                    v-if="index < 2"
                                    :content="user.first_name ? `${user.first_name} ${user.last_name}`: (user.user ? `${user.user.name}`:`${user.name}`)"
                                    class="item"
                                    effect="light" placement="top">
                                    <template v-if="user.user">
                                        <avatar :size="30"
                                                :username="user.first_name ? `${user.first_name} ${user.last_name}`: (user.user ? `${user.user.name}`:`${user.name}`)"
                                                backgroundColor="rgb(205, 220, 57)"
                                                color="#fff"
                                                v-if="!user.user.avatar"></avatar>
                                        <avatar :size="30" :src="`/${user.user.avatar}`" v-else></avatar>
                                    </template>
                                    <template v-else>
                                        <avatar :size="30"
                                                :username="user.first_name ? `${user.first_name} ${user.last_name}`: `${user.name}`"
                                                backgroundColor="rgb(205, 220, 57)"
                                                color="#fff"
                                                v-if="!user.avatar"></avatar>
                                        <avatar :size="30" :src="`/${user.avatar}`" v-else></avatar>
                                    </template>
                                </el-tooltip>
                        </span>
                        <avatar class="avatar-count" :size="30" :username="`+ ${item.service_providers.length-2}`"
                                color="#fff"
                                v-if="item.service_providers.length > 2"></avatar>
                    </div>
                </el-col>
                <el-col :span="4">
                    <span>{{ $t('models.request.created_by') }}</span>
                    <div class="created-by">
                        <p>
                            <el-tooltip
                                :content="item.creator.name"
                                class="item"
                                effect="light" placement="top">
                        
                                <table-avatar :src="item.creator.avatar" :name="item.creator.name" :size="30" />
                            </el-tooltip>
                        </p>
                        <div>
                            <p>{{ item.creator.name }}</p>
                            <p>{{formatDate(item.created_at)}}</p>
                        </div>
                    </div>
                </el-col> 
                <el-col :span="4" class="request-category">
                    <span>{{ $t('models.request.category') }}</span>
                    <p>{{ item.category.parent_id==null?'': categories[item.category.parentCategory.id] == undefined? '':
                        categories[item.category.parentCategory.id][$i18n.locale]+ ' > ' }}
                        {{ categories[item.category.id] == undefined? '':categories[item.category.id][$i18n.locale]}}
                    </p>
                </el-col>
                <el-col :span="4">
                </el-col>
                
            </el-row>    
        </div>
    </div>
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import RequestCount from 'components/RequestCount.vue';
    import {Avatar} from 'vue-avatar'
    import tableAvatar from 'components/Avatar';
    import globalFunction from "helpers/globalFunction";
    import {
        format, differenceInMinutes, parse, 
        differenceInCalendarDays, subHours
    } from 'date-fns';

export default {
    mixins: [globalFunction],
    props: {
        item: {
            type: Object,
            default: () => {
                return [];
            }
        },
        loading: {
            type: Object,
            default: () => ({
                state: false,
                text: this.$t('general.loading'),
                icon: 'el-icon-loading',
                background: 'rgba(0, 0, 0, 0.8)'
            })
        },
        categories: {
            type: Array,
            default: () => ([])
        }
    },
    data() {
        return {
        }
    },
    components: {
        RequestCount,
        Avatar,
        format,
        'table-avatar': tableAvatar
    },
    computed: {
        selectData() {                        
            const storeConstants = this.$constants.requests;
            if (storeConstants) {
                const constants = storeConstants['status'];
                var data = Object.keys(constants).map((id) => {
                    return {
                        name: this.$t(`models.request.status.${constants[id]}`),
                        id: parseInt(id)
                    };
                });
                return data;
            }
        }
    },
    methods: {
      
        handleSelectionChanged(val) {
            this.$emit('selectionChanged', this.item);
        },
        edit() {
            this.$emit('editAction', this.item);
        },
        formatDate (date) {
            var res = date.split(" ");
            return res[0] + ', ' +res[1].substr(0, 5);
        },
        
    },
}
</script>
<style lang="scss" scoped>
    .el-row {
        margin: 0px !important;
        .el-col {
            padding: 0px !important;
            span {
                font-size: 12px;
            }
        }
    }
    .avatars-wrapper {
        display: flex;

        .item {
            margin-right: 5px;
        }
    }
    .request-card {
        text-align: left;
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 
                    0 1px 2px rgba(0,0,0,0.24);
        margin: 4px;
        .request-card-header {
            .el-row {
                margin:-18px -20px;
            }
            .request-aside {
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .request-tail {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                .el-select {
                    width: 190px;
                }
                a {
                    margin-left: 10px;
                    .el-button {
                        border-radius: 20px;
                        padding: 8.65px 15px;
                    }
                }
            }
            .request-aside {
                padding: 0 !important;
                text-align: center;
                width: 180px !important;
            }
            .request-title {
                width: calc(80% - 270px) !important;
            }
        }
        .request-footer {
            background-color: #f2f2f2;
            border-bottom-right-radius: 5px;
            border-bottom-left-radius: 5px;
            .request-actions {
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 0px !important;

                i {
                    font-size: 20px;
                }
            }
            .request-category {
                p {
                    text-overflow: ellipsis;
                    white-space: nowrap;
                    overflow: hidden;
                }
            }
            .el-col-1 {
                width: 60px !important;
            }
            .el-col {
                border-right: 1px solid darken(#ebeef5, 10%);
                padding: 0 15px 3px !important;
                &:last-child {
                    border-right: none;
                }

                span {
                    color: darken(#ffffff, 45%);
                }
                p {
                    font-weight: 700;
                    margin: 0px;
                    padding-top: 4px;
                }
                &:nth-of-type(6) {
                    p {
                        font-size: 12px;
                        padding-top: 0px;
                        line-height: 1.4;
                    }
                    .created-by {
                        display: flex;
                        div:nth-child(2){
                            margin-left: 10px;
                        }
                    }
                } 
                
            }
        }
    }
    .listing-link {
        text-decoration: none;
        color: darken(#fff, 50%);
        font-weight: bold;
    }
</style>