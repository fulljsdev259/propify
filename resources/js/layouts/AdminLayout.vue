<template>
    <el-container class="admin-layout" direction="vertical">
        <a-header :toggleSidebar="toggleSidebar">
            <div class="header-link">
                <div v-bind:class="[{ active: showMenu }, language]">
                    <div class="language-iconBorder" @click="toggleShow" v-click-outside="hideMenu">
                        <div class="language-checked-img">
                            <span v-bind:class="selectedFlag"></span>
                        </div>
                    </div>
                    <div class="language-check-box">
                        <div class="language-check-box-title">
                            {{$t('general.choose_language')}}
                        </div>
                        <div class="language-check-box-body">
                            <ul class="language-check-box-body-item" v-for='language in this.languages' :key="language.symbol" @click='itemClicked(language.symbol, language.flag)'>
                                <li>
                                    <span v-bind:class="language.flag"></span>
                                    <p>{{language.name}}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <el-badge class="notification-badge" type="danger" :value="unreadNotifications.length" :max="9" :hidden="!unreadNotifications.length">
                <el-button icon="icon-bell-alt" circle />
            </el-badge>
            <div id="dropdown" class="dropdown-menu" ref="prev">
                <el-dropdown trigger="click" @visible-change="handlerDropdownVisibleChange">
                    <div>
                        <avatar :src="user.avatar" :name="user.name" :size="33" />
                        <span class="el-dropdown-link">
                            {{user.name}}<i class="el-icon-arrow-down el-icon--right"></i>
                        </span>
                        
                        <el-dropdown-menu slot="dropdown" :style="dropmenuwidth" @click.native="removeMenuActive">
                                <router-link  v-if="this.user.roles[0].name != 'manager'" :to="{name: 'adminProfile'}" class="el-menu-item-link">
                                    <el-dropdown-item>
                                        <i class="icon-user"/>
                                        {{$t('general.admin_menu.profile')}}
                                    </el-dropdown-item>
                                </router-link>
                                <router-link  v-if="this.user.roles[0].name == 'manager'" :to="{name: 'adminPropertyManagersEdit', params: {id: this.user.property_manager_id}}" class="el-menu-item-link">
                                    <el-dropdown-item>
                                        <i class="icon-user"/>
                                        {{$t('general.admin_menu.profile')}}
                                    </el-dropdown-item>
                                </router-link>
                                <template v-if="$can($permissions.view.settings) && this.user.roles[0].name != 'manager'">
                                    <router-link :to="{name: 'adminSettings'}" class="el-menu-item-link">
                                        <el-dropdown-item>
                                            <i class="icon-cog"/>
                                            {{$t('general.admin_menu.settings')}}
                                        </el-dropdown-item>
                                    </router-link>
                                </template>
                                <el-dropdown-item id="logout" @click.native="handleLogout">
                                    <i class="icon-logout"/>
                                    {{$t('general.admin_menu.logout')}}
                                </el-dropdown-item>
                        </el-dropdown-menu>
                    </div>
                </el-dropdown>
            </div>
        </a-header>
        <el-container>
            <a-sidebar :links="links" :collapsed="isCallapsed">
            </a-sidebar>
            <el-main sticky-container>
                <v-router-transition transition="slide-left">
                    <router-view/>
                </v-router-transition>
                <a-footer />
            </el-main>
        </el-container>
    </el-container>
</template>

<script>
    import AHeader from 'components/AdminHeader';
    import ASidebar from 'components/AdminSidebar';
    import AFooter from 'components/AdminFooter';
    import Avatar from 'components/Avatar';
    import VRouterTransition from 'v-router-transition';
    import {mapActions, mapState} from "vuex";

    import { EventBus } from '../event-bus.js';
    import Vue from 'vue';

    Vue.directive('click-outside', {
        bind: function (el, binding, vnode) {
            el.clickOutsideEvent = function (event) {
                if (!(el == event.target || el.contains(event.target))) {
                    vnode.context[binding.expression](event);
                }
            };
            document.body.addEventListener('click', el.clickOutsideEvent)
        },
        unbind: function (el) {
            document.body.removeEventListener('click', el.clickOutsideEvent)
        },
    });
    

    export default {
        name: 'AdminLayout',
        components: {
            AHeader,
            ASidebar,
            AFooter,
            Avatar,
            VRouterTransition
        },

        data() {
            return {
                fullScreenText: 'Enter fullscreen mode',
                showMenu: false,
                language: "language",
                activeLanguage: 'Piano',                

                activeIndex: '1',
                activeIndex2: '1',

                languages: [],

                isCallapsed: false,
                dropdownwidth: 0,
                currActive: '',
                requests: [],
                all_request_count: null,
                all_pending_count: null,
                all_unassigned_count: null,
                my_request_count: null,
                my_pending_count: null,
                rolename: null,
                unreadNotifications: 0
            }
        },

        computed: {
            ...mapState({
                user: ({users}) => users.loggedInUser
            }),
            ...mapState('application', {
                locale: ({locale}) => locale
            }),
            selectedFlag: {
                get: function () {
                    if(this.$store.state.application.locale){
                        if(this.$store.state.application.locale === 'en'){
                            return `flag-icon flag-icon-us`;
                        }else {
                            return  `flag-icon flag-icon-${this.$store.state.application.locale}`;
                        }                    
                    } else {                                        
                        if(this.user.settings.language === 'en'){
                            return  `flag-icon flag-icon-us`;
                        }else {
                            return `flag-icon flag-icon-${this.user.settings.language}`;
                        }
                    }
                },
                set: function (newValue) {
                    this.value = newValue;
                }                
            },
            links() {
                let links = [];
                let menu_items = {
                    "dashboard": {
                        icon: 'icon-chart-bar',
                        title: this.$t('general.admin_menu.dashboard'),
                        route: {
                            name: 'adminDashboard'
                        }
                    },
                    "buildings": {
                        icon: 'icon-commerical-building',
                        title: this.$t('general.admin_menu.buildings'),
                        permission: this.$permissions.list.user,
                        children: [
                        {
                            title: this.$t('general.admin_menu.quarters'),
                            permission: this.$permissions.list.quarter,
                            route: {
                                name: 'adminQuarters'
                            }
                        }, {
                            title: this.$t('general.admin_menu.all_buildings'),
                            permission: this.$permissions.list.building,
                            route: {
                                name: 'adminBuildings'
                            }
                        }, {
                            title: this.$t('general.admin_menu.units'),
                            permission: this.$permissions.list.unit,
                            route: {
                                name: 'adminUnits'
                            }
                        }]
                    },
                    "requests": {
                        icon: 'icon-chat-empty',
                        title: this.$t('general.admin_menu.requests'),
                        permission: this.$permissions.list.request,
                        children: [{
                            title: this.$t('general.admin_menu.all_requests'),
                            permission: this.$permissions.list.request,
                            value: this.all_request_count,
                            route: {
                                name: 'adminRequests'
                            }
                        },  {
                            title: this.$t('general.admin_menu.my_requests'),
                            permission: this.$permissions.list.request,
                            value: this.my_request_count,
                            route: {
                                name: 'adminMyRequests'
                            }
                        },  {
                            title: this.$t('general.admin_menu.my_pending_requests'),
                            permission: this.$permissions.list.request,
                            value: this.my_pending_count,
                            route: {
                                name: 'adminMypendingRequests'
                            }
                        },  {
                            title: this.$t('general.admin_menu.not_assigned'),
                            permission: this.$permissions.list.request,
                            value: this.all_unassigned_count,
                            route: {
                                name: 'adminUnassignedRequests'
                            }
                        },  {
                            title: this.$t('general.admin_menu.all_pending_requests'),
                            permission: this.$permissions.list.request,
                            value: this.all_pending_count,
                            route: {
                                name: 'adminAllpendingRequests'
                            }
                        }]
                    },
                    "activity": {
                        icon: 'icon-gauge-1',
                        title: this.$t('general.admin_menu.activity'),
                        permission: this.$permissions.list.audit,
                        route: {
                            name: 'adminRequestsActivity'
                        }
                    },
                    "residents": {
                        title: this.$t('general.admin_menu.residents'),
                        icon: 'icon-group',
                        permission: this.$permissions.list.resident,
                        route: {
                            name: 'adminResidents'
                        }
                    },
                    "propertyManagers": {
                        icon: 'icon-users',
                        title: this.$t('general.admin_menu.property_managers'),
                        permission: this.$permissions.list.propertyManager,
                        route: {
                            name: 'adminPropertyManagers'
                        }
                    },
                    "houseOwners": {
                        icon: 'icon-users',
                        title: this.$t('general.admin_menu.house_owners'),
                        permission: this.$permissions.list.propertyManager,
                        route: {
                            name: 'adminHouseOwners'
                        }
                    },
                    "services": {
                        icon: 'icon-tools',
                        title: this.$t('general.admin_menu.services'),
                        permission: this.$permissions.list.provider,
                        route: {
                            name: 'adminServices'
                        }
                    },
                    "pinboard": {
                        title: this.$t('general.admin_menu.pinboard'),
                        icon: 'icon-megaphone-1',
                        permission: this.$permissions.list.pinboard,
                        route: {
                            name: 'adminPinboard'
                        }
                    },
                    // "listings": {
                    //     title: this.$t('general.admin_menu.listings'),
                    //     icon: 'icon-basket',
                    //     permission: this.$permissions.list.listing,
                    //     route: {
                    //         name: 'adminListings'
                    //     }
                    // },
                    // "admins": {
                    //     icon: 'icon-user',
                    //     title: this.$t('general.admin_menu.admins'),
                    //     permission: this.$permissions.list.user,
                    //     route: {
                    //         name: 'adminUsers'
                    //     }
                    // }
                }                
                if (this.rolename == 'administrator') {
                   links = Object.values(menu_items);
                }
                else if (this.rolename == 'manager') {
                   links = [
                            menu_items.buildings,
                            menu_items.requests, 
                            menu_items.activity,
                            menu_items.residents,
                            menu_items.propertyManagers,
                            menu_items.services,
                            menu_items.pinboard,
                            // menu_items.listings,
                       ];
                }
                else if (this.rolename == 'service') {
                     links = [                            
                            menu_items.requests,                             
                       ];
                }
                return links;
            },
            dropmenuwidth () {
                return `width: ${this.dropdownwidth + 12}px;`
            }
        },

        methods: {
            ...mapActions(['logout']),
            ...mapActions(['updateUserSettings', 'getRequestCategoriesTree']),
            ...mapActions('application', ['setLocale']),
            toggleFullscreen() {
                if (document.fullscreenElement) {
                    this.fullScreenText = 'Enter fullscreen mode';

                    document.exitFullscreen();
                } else {
                    this.fullScreenText = 'Exit fullscreen mode';

                    document.documentElement.requestFullscreen();
                }
            },

            toggleSidebar() {
                this.isCallapsed = !this.isCallapsed;
            },

            handleLogout() {
                // this.$confirm(this.$t('general.swal.logout_confirm'), this.$t('general.swal.delete.title'), {
                //     type: 'warning'
                // }).then(() => {
                //     this.logout()
                //         .then(() => {
                //             this.$router.push({name: 'login'});
                //         })
                //         .catch(err => {
                //             displayError(err);
                //         });
                // }).catch(() => {

                // });
                this.logout()
                    .then(() => {
                        this.$router.push({name: 'login'});
                    })
                    .catch(err => {
                        displayError(err);
                    });
            },

            removeMenuActive() {
                while( this.$el.querySelector('.content .is-active') != null) 
                {
                    this.$el.querySelector('.content .is-active').classList.remove('is-active');
                }
            },

            toggleShow: function() {
                this.showMenu = !this.showMenu;
            },

            hideMenu: function() {
                this.showMenu = false;
            },

            itemClicked: function(item, flag) {
                // this.toggleShow();
                this.onClick(item, flag);
                //this.$router.push({ name: 'adminBuildings' });
                //this.$router.push({ path: `/` })
                //this.$router.push({ path: `/admin/buildings` })
                
            },

            changeLanguage: function(language) {
                this.activeLanguage = language;
            },

            onClick(language, flag){                
                this.setLocale(language);
                this.selectedFlag = flag;
                this.$root.$emit('changeLanguage');

                this.toggleShow();

                // this.saveLangParamsInLocalStorage();
            },            

            /*saveLangParamsInLocalStorage(){
                localStorage.setItem('locale', this.$i18n.locale);
                localStorage.setItem('selectedFlag', this.selectedFlag);
            },*/

            getDropdownWidth() {
                this.dropdownwidth = this.$refs.prev.clientWidth;
            },

            handlerDropdownVisibleChange() {
                let Itag = this.$el.querySelector("i[style]");
                if(!Itag) {
                    let initialItag = this.$el.querySelector('.el-icon-arrow-down');
                    initialItag.setAttribute('style', 'transform: rotateZ(180deg)');
                }
                else {
                    Itag.removeAttribute('style');
                }
            }
        },
        beforeCreate() {
            document.getElementById('viewport').setAttribute('content', 'width=920, initial-scale=1.0');
        },

        mounted(){            
            EventBus.$on('profile-username-change', () => {
                this.dropdownwidth = this.$refs.prev.clientWidth;
            });

            this.getDropdownWidth();
            
            let languagesObject = this.$constants.app.languages;
            let languagesArray = Object.keys(languagesObject).map(function(key) {
                return [String(key), languagesObject[key]];
            });

            this.languages = languagesArray.map(item => { 
                let flag_class = 'flag-icon flag-icon-';
                let flag = flag_class + item[0];
                if( item[0] == 'en')
                {
                    flag = flag_class + 'us'
                }
                return {
                    name: item[1],
                    symbol: item[0],
                    flag: flag
                }
            });

            this.rolename = this.$store.getters.loggedInUser.roles[0].name;
            
        },
        
        async created(){            
            const requestsCounts = await this.axios.get('requestsCounts');
            this.all_request_count = requestsCounts.data.data.all_request_count;
            this.all_pending_count = requestsCounts.data.data.all_pending_request_count;
            this.all_unassigned_count = requestsCounts.data.data.all_unassigned_request_count;
            this.my_request_count = requestsCounts.data.data.my_request_count;
            this.my_pending_count = requestsCounts.data.data.my_pending_request_count;
        }


    }
</script>
<style lang="scss" scoped>
    .el-button {
        padding: 0px !important;
        width: 100%;
        + .el-button {
            margin-left: 0px !important;
        }
    }
    .el-button--text {
        color: #909399 !important;
        width: 100%;
        text-align: left;
    }
    .el-dropdown-menu {
        margin: 16px 8px 16px 0px !important;
        .el-dropdown-menu__item {
            padding: 0px 12px !important;
            text-align: left;
            color: #909399;

            i {
                margin-right: 10px;
            }
            &:hover {
                color: #909399;
            }
        }
    }
</style>

<style lang="scss" scoped>

    .el-container {
        background-color: #F2F4F9;
        height: 100%;

        .el-main {
            padding: 0;
            height: 100%;
            overflow: hidden;
            flex-basis: 0;
            overflow-y: auto;

            display: flex;
            flex-direction: column;
            justify-content: space-between;

            .el-breadcrumb {
                background-color: #fff;
                padding: 1em;
                margin: -30px;
                margin-bottom: 2em;
                box-shadow: 0 1px 3px transparentize(#000, .88),
                0 1px 2px transparentize(#000, .76);
                position: relative;
                top: -2px;
            }

            // causes a bug
            // > * {
            //     height: 100% !important;
            // }

        }

        .user-params{
            display: flex;
            align-items: center;
            position: relative;
            width: 100%;

            &-img{
                width: 33px;
                height: 33px;
                border: solid #c2c2c2 1px;
                border-radius: 50%;
            }

            &-wrap{
                display: flex;
                align-items: center;
                padding-left: 15px;

                &-icon{
                    position: static;
                    margin-top: 0;
                    margin-left: 10px;
                }
            }

            &-name{
                display: flex;
                width: auto;

                &-rotateIcon{
                    transform: rotate(180deg);
                }
            }
        }
        .dropdown-menu {
            min-width: 118px;
            cursor: pointer;
            .avatar {
                margin-right: 3%;
                background-color: rgb(205, 220, 57)!important;
                color: white !important;
            }
            .el-dropdown-link {
                color: #909399;
                .el-icon-arrow-down {
                    font-size: 12px;
                    transition: .4s;
                }
            }
        }

        .language {
            position: relative;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 40px;

            &:after{
                content: "";
                position: absolute;
                right: -21px;
                height: 90%;
                width: 1px;
                background: #c2c2c2;;
            }


            &.active{
                background: #ececec;


                .language-check-box{
                    top: 45px;
                    pointer-events: auto;
                    opacity: 1;
                }
            }

            &-iconBorder{
                width: 35px;
                height: 35px;
                border-radius: 50%;
                background: #eee;
                display: flex;
                justify-content: center;
                align-items: center;
                transition: 0.2s ease-in;
                cursor: pointer;

                &:hover{
                    background: #B4B4B4;
                }
            }

            .language-checked-img{
                width: 25px;
                height: 25px;
                border-radius: 50%;
                overflow: hidden;
                position: relative;

                span{
                    width: 102%;
                    height: 102%;
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    object-fit: cover;
                    display: block;
                    background-size: cover;
                }
            }

            .language-check-box{
                position: absolute;
                top: 25px;
                left: -70px;
                z-index: 5;
                background: white;
                box-shadow: 0 2px 5px rgba(34,34,34,.4);
                border-radius: .4rem;
                overflow: hidden;
                opacity: 0;
                pointer-events: none;
                transition: .2s;

                .language-check-box-title{
                    cursor: default;
                    padding: 15px 30px;
                    background: #525560;
                    color: #fff;
                }

                .language-check-box-body-item{
                    padding: 0;
                    margin: 0;
                    cursor: pointer;
                    li{
                        display: flex;
                        justify-content: flex-start;
                        align-items: center;
                        padding: 10px 20px;
                        transition: .4s;


                        &:hover{
                            background-color: var(--primary-color-lighter);
                        }

                        span{
                            margin: 0 20px 0 0 ;
                        }

                        p{
                            margin: 0;
                            color: #303133;
                        }
                    }
                }
            }
        }

        .notification-badge {
            width: 35px;
            height: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 20px;

            /deep/ .el-button {
                height: 100%;
                width: 100%;
            }
        }
    }
</style>
<style lang="less">
    .crud-view {
        .el-card__header {
            border-bottom: 1px solid #EBEEF5 !important;
            font-size: 16px !important;
        }
    }
    .el-button--primary:focus, .el-button--primary:hover {
        background: var(--primary-color-lighter);
        border-color: var(--primary-color-lighter);
        color: var(--primary-color);
    }
    
    .el-button--default:hover {
        background: var(--primary-color-lighter);
        border-color: var(--primary-color-lighter);
        color: var(--primary-color);
    }
    .el-button--default:active {
        background: var(--primary-color-lighter);
        border-color: var(--primary-color-lighter);
        color: var(--primary-color);
    } 
</style>

<style lang="scss">
    .admin-layout .el-badge__content.is-fixed {
        top: 19px;
        right: -5px;
        background-color: var(--primary-color) !important;
        margin-left: 5px;
    }
</style>