<template>
    <el-container class="admin-layout" direction="vertical">
        
        <el-container>
            <a-sidebar :links="links" :collapsed="isCallapsed">
                <div class="actions" slot="header-action">
                    <el-badge class="notification-badge" type="danger" :value="unreadNotifications.length" :max="9" :hidden="!unreadNotifications.length">
                        <i class="el-icon-bell"></i>
                    </el-badge>
                    <el-popover
                        placement="right"
                        width="200"
                        trigger="hover"
                    >
                        <!-- <router-link  v-if="this.user.roles[0].name != 'manager'" :to="{name: 'adminProfile'}" class="el-menu-item-link">
                            <el-dropdown-item>
                                <i class="icon-user"/>
                                {{$t('general.admin_menu.profile')}}
                            </el-dropdown-item>
                        </router-link> -->
                        <router-link  v-if="this.user.roles[0].name == 'administrator'" :to="{name: 'adminPropertyManagersEdit', params: {id: this.user.property_manager_id}}" class="el-menu-item-link">
                            <el-dropdown-item>
                                <i class="icon-user"/>
                                {{$t('general.admin_menu.profile')}}
                            </el-dropdown-item>
                        </router-link>
                        <router-link  v-else-if="this.user.roles[0].name == 'manager'" :to="{name: 'adminPropertyManagersEdit', params: {id: this.user.property_manager_id}}" class="el-menu-item-link">
                            <el-dropdown-item>
                                <i class="icon-user"/>
                                {{$t('general.admin_menu.profile')}}
                            </el-dropdown-item>
                        </router-link>
                        <router-link  v-else-if="this.user.roles[0].name == 'provider'" :to="{name: 'adminServicesEdit', params: {id: this.user.service_privider_id}}" class="el-menu-item-link">
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
                        <avatar slot="reference" :src="user.avatar" :name="user.name" :size="33" />
                    </el-popover>
                    
                </div>
            </a-sidebar>
            <el-main sticky-container>
                <v-router-transition transition="slide-left">
                    <router-view/>
                </v-router-transition>
            </el-main>
        </el-container>
        <vue-snotify></vue-snotify>
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

                isCallapsed: true,
                dropdownwidth: 0,
                currActive: '',
                requests: [],
                all_request_count: null,
                all_pending_count: null,
                all_unassigned_count: null,
                my_request_count: null,
                my_pending_count: null,
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
            role_name() {
                return this.$store.getters.loggedInUser.roles[0].name;
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
                        route: {
                            name: 'adminRequests'
                        }
                    },
                    "managerRequests": {
                        icon: 'icon-chat-empty',
                        title: this.$t('general.admin_menu.requests'),
                        permission: this.$permissions.list.request,
                        route: {
                            name: 'adminRequests'
                        }
                    },
                    // "activity": {
                    //     icon: 'icon-gauge-1',
                    //     title: this.$t('general.admin_menu.activity'),
                    //     permission: this.$permissions.list.audit,
                    //     route: {
                    //         name: 'adminActivityList'
                    //     }
                    // },
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
                    // "houseOwners": {
                    //     icon: 'icon-users',
                    //     title: this.$t('general.admin_menu.house_owners'),
                    //     permission: this.$permissions.list.propertyManager,
                    //     route: {
                    //         name: 'adminHouseOwners'
                    //     }
                    // },
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
                if (this.role_name == 'administrator') {
                   //links = Object.values(menu_items);
                   links = [
                            menu_items.dashboard,
                            menu_items.buildings,
                            menu_items.requests, 
                            // menu_items.activity,
                            menu_items.residents,
                            menu_items.propertyManagers,
                            menu_items.services,
                            menu_items.pinboard,
                            // menu_items.listings,
                       ];
                }
                else if (this.role_name == 'manager') {
                   links = [
                            menu_items.buildings,
                            menu_items.managerRequests,
                            menu_items.residents,
                            menu_items.propertyManagers,
                            menu_items.services,
                            menu_items.pinboard,
                            // menu_items.listings,
                       ];
                }
                else if (this.role_name == 'service') {
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
            ...mapActions(['updateUserSettings']),
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

            // getDropdownWidth() {
            //     this.dropdownwidth = this.$refs.prev.clientWidth;
            // },

            handlerDropdownVisibleChange() {
                let Itag = this.$el.querySelector("i[style]");
                if(!Itag) {
                    let initialItag = this.$el.querySelector('.el-icon-arrow-down');
                    initialItag.setAttribute('style', 'transform: rotateZ(180deg)');
                }
                else {
                    Itag.removeAttribute('style');
                }
            },
            async getRequestCounts() {
                const requestsCounts = await this.axios.get('requestsCounts');
                this.all_request_count = requestsCounts.data.data.all_request_count;
                this.all_pending_count = requestsCounts.data.data.all_pending_request_count;
                this.all_unassigned_count = requestsCounts.data.data.all_unassigned_request_count;
                this.my_request_count = requestsCounts.data.data.my_request_count;
                this.my_pending_count = requestsCounts.data.data.my_pending_request_count;
            }
        },
        beforeCreate() {
            document.getElementById('viewport').setAttribute('content', 'width=920, initial-scale=1.0');
        },

        mounted(){            
            // EventBus.$on('profile-username-change', () => {
            //     this.dropdownwidth = this.$refs.prev.clientWidth;
            // });

            //this.getDropdownWidth();
            
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


            EventBus.$on('avatar-update', () => {
                this.user.avatar += "?"
            });

            EventBus.$on('update-request-counts', () => {
                this.getRequestCounts()
            });

        },
        

        async created() {
            this.setLocale('de');
            this.getRequestCounts();
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
    .admin-layout {
        font-family: 'Radikal';
        :global(.heading .content .title) {
            font-family: 'Radikal Bold';
        }
        :global(.heading .el-button) {
            font-family: 'Radikal';
            margin-right: 15px !important;
            padding: 9px 15px;
        }
        :global(.heading .list-checkbox .el-button) {
            padding: 2px 2px;
            margin-right: 23px !important;;
        }
        :global(.heading .el-button.more-actions) {
            margin-right: 12px !important;
            padding: 9px 6px;
        }
        :global(.heading .menu-button) {
            margin-right: 20px;
        }
        /deep/ .heading .el-button.is-circle {
            padding: 5px 6.5px !important;
            margin-right: 0px !important;
            i.el-icon-close {
                font-size: 17px;
            }
        }
        .list-view {
            :global(.heading .el-button) {
                &:hover {
                    background-color: #f6f5f7;
                    color: var(--color-text-primary);
                    font-weight: 500;
                    box-shadow: none;
                    :global(i) {
                        font-weight: 500;
                    }
                }
            }
        }
    }

    .el-container {
        background-color: #f6f5f7;
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
                width: 26px;
                height: 26px;
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

            i {
                font-size: 32px;
                color: var(--color-info);
            }
        }
    }
</style>
<style lang="less">
    .wangEditor-container .wangEditor-txt {
        padding-top: 12px;
        line-height: 1.8;
        h1, h2, h3, h4, h5, p {
            &:first-child {
                margin-top: 0;
            }
        }
    }

    .crud-view {
        .el-card__header {
            border-bottom: 1px solid #EBEEF5 !important;
            font-size: 16px !important;
        }

        .el-dropdown .el-button {
            padding-left: 5px !important;
        }
    }

    
</style>

<style lang="scss">
    .admin-layout .el-badge {
        display: flex;
        align-items: center;
        .el-badge__content.is-fixed {
            position: static;
            transform: none;
            background-color: var(--primary-color) !important;
            margin-left: 5px;
        }
    }
    .el-menu-item-link {
        text-decoration: none;
    }
    .el-dropdown-menu {
        border-radius: 12px;
        &.el-popper {
            :global(&[x-placement^=bottom] .popper__arrow) {
                top: -12px;
                &::after {
                    margin-left: -10px;  
                }
            }
            :global(.popper__arrow) {
                border-bottom-width: 12px;
                border-left-width: 10px;
                border-right-width: 10px;
                border-top-width: 0;      
                &::after {
                    border-width: 12px;
                    border-left-width: 10px;
                    border-right-width: 10px;
                    border-top-width: 0;
                }
            }
        }
    }

    .el-input {
        .el-input__inner {
            background-color: #f6f5f7;
            border-color: transparent;
            color: var(--text-color);

            &:disabled {
               background-color: #f6f5f7 !important;
                border-color: transparent !important;
                color: var(--text-color) !important; 
            }
        }    
        &.is-disabled .el-input-group__prepend {
            background-color: #f6f5f7 !important;
            border-color: transparent !important;
            color: var(--text-color) !important;
        }
    } 
    .el-textarea {
        .el-textarea__inner {
            background-color: #f6f5f7;
            border-color: transparent;
            color: var(--text-color);
            
            &:disabled {
               background-color: #f6f5f7 !important;
                border-color: transparent !important;
                color: var(--text-color) !important; 
            }
        }
    }
    :global(.edit-details-form .el-row .el-col .el-form-item) {
        .el-input .el-input__inner, .el-input .el-textarea__inner, .el-input .el-input-group__prepend {
            background-color: var(--border-color-lighter);
            border-color: transparent;
            color: var(--color-text-regular);
        }    
        .el-button {
            background-color: var(--border-color-lighter);

            &.is-disabled {  
               background-color: #f6f5f7 !important;
            }
        }
    }
    :global(.add-form .el-row .el-col .el-form-item) {
        .el-input .el-input__inner, .el-input .el-input-group__prepend, .el-input .el-textarea__inner {
            border-color: transparent;
            background-color: #f6f5f7 !important;
        }    
    }
    :global(.edit-details-form .el-row .el-col), :global(.add-form .el-row .el-col) {
        &:first-child {
            padding-left: 0px !important;
        }
        &:last-child {
            padding-right: 0px !important;
        }
    }
    .el-select .el-input.is-disabled .el-input__inner,
    .el-input.is-disabled .el-input__icon,
    .el-input.is-disabled .el-input__inner,
    .el-button.is-disabled {
        cursor: default;
        &:hover {
            border-color: transparent;
        }
    }
    .el-row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    .el-tabs {
        margin: 0px 10px 40px !important;
        box-shadow: none !important;
    }
    .el-tabs .el-tabs__header {
        background: #f6f5f7;
        border-color: transparent !important;
    }
    .el-tabs .el-tabs__header .el-tabs__item {
        border: none;
        border-radius: 6px 6px 0 0;
    }
    .el-tabs--border-card>.el-tabs__content {
        padding: 20px;
    }
    .list-table-search {
        width: 250px;
        &.el-input {
            :global(.el-input__inner) {
                border-color: transparent;
                color: var(--color-text-regular);
                background-color: var(--background-color-base);
                height: 30px !important;
                line-height: 30px !important;
            }
            :global(.el-input__icon) {
                font-size: 16px;
                line-height: 30px;
            }
        }
    }

    .admin-layout {
        .el-button--default{
            background-color: lightgrey;
            color:var(--color-text-regular);
            border: none
        }
        .el-button--default:focus,.el-button--default:hover{
            color:var(--color-text-regular);
            background-color:lightgrey;
            border:none;
            box-shadow:0 0 5px lightgrey
        }
        .el-button--default.is-active,.el-button--default:active{
            color:var(--color-text-regular);
            background-color:lightgrey;
            border:none;
        }
        .el-button--primary{
            color:var(--color-white);
            background-color:#878810;
            border:none
        }
        .el-button--primary:focus,.el-button--primary:hover{
            color:var(--color-white);
            background-color:#878810;
            border:none;
            box-shadow:0 0 5px #878810
        }
        .el-button--primary.is-active,.el-button--primary:active{
            color:var(--color-white);
            background-color:#878810;
            border:none
        }
        .el-button--success{
            color:var(--color-white);
            background-color:#878810;
            border:none
        }
        .el-button--success:focus,.el-button--success:hover{
            color:var(--color-white);
            background-color:#878810;
            border:none;
            box-shadow:0 0 5px #878810
        }
        .el-button--success.is-active,.el-button--success:active{
            color:var(--color-white);
            background-color:#878810;
            border:none
        }
        .el-button--danger{
            background-color:#848484;
            color:white;
            border:none
        }
        .el-button--danger:focus,.el-button--danger:hover{
            background-color:#848484;
            color: var(--color-white);
            border:none;
            box-shadow: 0 0 5px #848484
        }
        .el-button--danger.is-active,.el-button--danger:active{
            background-color:#848484;
            color: var(--color-white);
            border:none;
            box-shadow: 0 0 5px #848484
        }
        .el-button--warning{
            color:#FFF;
            background-color:var(--color-warning);
            border-color:var(--color-warning)
        }
        .el-button--warning:focus,.el-button--warning:hover{
            background:#ebb563;
            border-color:#ebb563;
            color:#FFF
        }
        .el-button--warning.is-active,.el-button--warning:active{
            background:#cf9236;
            border-color:#cf9236;
            color:#FFF
        }
        .el-button--info{
            color:#FFF;
            background-color:var(--color-info);
            border-color:var(--color-info)
        }
        .el-button--info:focus,.el-button--info:hover{
            background:#a6a9ad;
            border-color:#a6a9ad;
            color:#FFF
        }
        .el-button--info.is-active,.el-button--info:active{
            background:#82848a;
            border-color:#82848a;
            color:#FFF
        }
        .el-button--transparent{
            background-color: transparent;
            border: none;
            color: var(--color-text-secondary)
        }
        /* .el-button--transparent:focus{
            background-color: transparent;
            border: none;
            color: var(--color-text-secondary);
            box-shadow: none
        } */

        /* .el-button--transparent span{
            padding-left: 5px;
        } */
        .el-button--assign, .el-button--assign.is-disabled {
            background-color: #3D3F41;
            color: var(--color-white);
        }

        .el-button--assign:focus, .el-button--assign:hover {
            background-color: var(--background-color-base);
        }
    }
</style>
