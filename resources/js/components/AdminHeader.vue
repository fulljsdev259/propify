<template>
    <el-menu background-color="#fff" mode="horizontal">
        <el-menu-item index="prefix" v-if="hasSlot('prefix')">
            <slot name="prefix"></slot>
        </el-menu-item>
        <el-menu-item class="logo" index="logo">
            <div class="menu-icon" @click="handletoggle">
                <i class="icon-menu"></i>
            </div>
            <div class="logo-image" :style="{backgroundImage: 'url(' + logo + ')'}">
                <!-- <img :src="logo" v-show="logo"/> -->
            </div>
        </el-menu-item>
        <el-menu-item class="header-menu-links" index="content" v-if="hasSlot()">
            <slot/>
        </el-menu-item>
    </el-menu>
</template>

<script>
    export default {
        name: 'AdminHeader',
        props: {
            toggleSidebar: { type: Function },
        },
        computed: {
            logo() {
                
                if(localStorage.getItem('logo') != this.$constants.logo.logo ) {
                    localStorage.setItem('logo', this.$constants.logo.logo);
                }

                return localStorage.getItem('logo') ? `/${localStorage.getItem('logo')}` : '';
            },
        },
        mounted() {

            this.$root.$on('fetch_logo', (logo) => {
                if(localStorage.getItem('logo') != logo ) {
                    localStorage.setItem('logo', logo);
                }
            });

        },
        methods: {
            hasSlot(slot) {
                if (slot) {
                    return !!this.$slots[slot];
                }

                return !!this.$slots.default;
            },
            handletoggle() {
                this.toggleSidebar();
            }
        }
        
    }
</script>

<style lang="scss" scoped>
    .el-menu {
        &.el-menu--horizontal {
            z-index: 10;
            box-shadow: 0 1px 5px 5px transparentize(#000, .9); 
            display: flex;
            align-items: center;
            flex-shrink: 0;
            padding: 0 1em;

            > .el-menu-item {
                height: 72px;
                line-height: 40px;
                padding: 0 1em;
                background: none !important;
                border-bottom-style: none !important;
                cursor: default;

                :global(.el-button i) {
                    color: inherit;
                }

                &.logo {
                    flex: 1;
                    padding: 1em;
                    padding-left: 0px;
                    display: flex;
            
                    .logo-image {
                        width: 220px;
                        position: relative;
                        transition: 0.3s linear;
                        background-repeat: no-repeat;
                        background-position-x: 30px;
                        background-size: auto 44px;
                        img {
                            height: 100%;
                            vertical-align: baseline;
                            margin-left: 30px;
                            cursor: pointer;
                        }
                        &:before {
                            content: "";
                            position: absolute;
                            left: 10px;
                            height: 31.5px;
                            width: 1px;
                            background: #c2c2c2;
                            margin-top:6px;
                        }
                    }
                    
                    .menu-icon {
                        margin-right: 9px;
                        margin-left: 9px;
                        i {
                            color: #909399;
                            cursor: pointer;
                            &:hover {
                                color:black; 
                            }
                        }
                    }
                }
            }
        }
    }
</style>

<style lang="scss">
    .el-menu {
        &.el-menu--horizontal {

            > .el-menu-item.header-menu-links {
                display: flex !important;
                align-items: center;
                width: auto;

                font-size: 15px;

                .header-link{
                    line-height: 1;
                    text-align: center;
                    text-decoration: none;
                    color: #909399;

                    i {
                        margin-bottom: 5px;
                        margin-right: 10px;
                    }

                    &:hover {
                        color: #6AC06F;

                        i {
                            color: #6AC06F;
                        }
                    }
                }

                .dropdown-list-item-link.active{
                    background-color: var(--primary-color-lighter);
                    padding: 10px 100% 10px 10px;
                    margin-left: -10px;
                }

                .header-link.active{
                    color: #6AC06F;
                }
            }
        }
    }

    .el-menu.el-menu--horizontal{
        border-bottom: none !important;
    }

    .el-menu-item{
        
        &:hover {
            background-color: var(--primary-color) !important;
        }

        &-link{
            line-height: 1;
            text-align: center;
            text-decoration: none;
            color: #909399;

            i {
                margin-bottom: 5px;
                margin-right: 10px;
            }
        }

        .logout-button {
            display: flex;
            color: #909399;

            i {
                margin-bottom: 5px;
                margin-right: 14px;
            }

            &:hover {
                color: #6AC06F;
            }
        }
    }

    .el-menu-item-d.is-active{
        background-color: var(--primary-color-lighter) !important;
    }

    .dropdown-menu .el-submenu{
        .el-submenu__title{
            border-bottom: none !important;

            .el-submenu__icon-arrow {
                display: none !important;
            }

            .user-params {
                .user-params-wrap {
                    min-width: 65px;
                    display: flex;
                    justify-content: space-between;
                }
                .el-submenu__icon-arrow{
                    display: block !important;
                }
            }
        }

    }

    .el-menu--popup {
        min-width: 142px !important;
        left: -3px;
    }

    body > .el-menu--horizontal.profile-popper {
        right: 20px;
    }
</style>
