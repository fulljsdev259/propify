<template>
    <aside class="el-menu-aside">
        <el-menu :default-active="currActive" :unique-opened="true" class="el-menu-vertical-demo" :collapse="collapsed">
            <li class="slot" index="slot" v-if="hasSlot">
                <slot/>
            </li>

            <ul class="content">
                <router-link 
                    :key="link.title"
                    v-for="(link, key) in links"
                    v-if="!link.children && ($can(link.permission) || !link.permission)"
                    :to="{name: link.route.name}">
                    <el-menu-item
                            :class="{nested: link.nestedItem }"
                            :index="link.title"
                            >
                            <i :class="[link.icon, 'icon']"/>
                            <span class="title" v-if="!collapsed">{{ link.title }}</span>
                        <span class="title" slot="title" v-if="collapsed">{{ link.title }}</span>
                    </el-menu-item>
                </router-link>
                <el-submenu :index="link.title" v-else-if="($can(link.permission) || !link.permission)">
                    <template slot="title">
                        <i :class="[link.icon, 'icon']"/>
                        <span class="title" slot="title">{{ link.title }}</span>
                    </template>
                    <router-link 
                        :key="child.title"
                        v-for="(child, childKey) in link.children"
                        :to="child.route">
                        <el-menu-item
                                :index="child.title"
                                >
                                <i :class="['icon-right-open', 'icon']"/>
                                <span class="title">{{ child.title }}</span>
                            <el-badge :value="child.value" class="item" type="primary"></el-badge>
                        </el-menu-item>
                    </router-link>
                </el-submenu>
            </ul>
        </el-menu>
    </aside>
</template>

<script>
    import {displaySuccess, displayError} from 'helpers/messages';

    export default {
        name: 'AdminSidebar',
        props: {
            links: {
                type: Array,
                default: []
            },
            collapsed: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                currActive: '',
            }
        },
        methods: {
            async handleLink(ev, key, {route, action, children, icon}) {
                //this.currActive = key.toString();
                !children && route && this.$router.push(route);

                /*if (!children && !!icon) {
                    const element1 = document.body.querySelector('.el-submenu.is-opened');

                    if (element1) {
                        element1.classList.remove('is-opened');
                        element1.removeAttribute('aria-expanded');
                        element1.querySelector('ul').style.display = 'none';
                    }
                }*/
                if (action) {
                    if (action.showConfirmation) {
                        try {

                            if (action && this.$confirm(this.$t('general.swal.delete.text'), this.$t('general.swal.delete.title'), {
                                confirmButtonText: 'OK',
                                cancelButtonText: 'Cancel',
                                type: 'warning',
                                roundButton: true
                            })) {
                                await this.$store.dispatch(action.name);
                            }
                        } catch (error) {
                            displayError(error)
                        }
                    } else {

                        try {
                            await this.$store.dispatch(action.name);
                        } catch (error) {
                            displayError(error)
                        }
                        
                    }
                }
            }
        },
        computed: {
            hasSlot() {
                return !!this.$slots.default;
            }
        },
        watch: {
            links() {
                const routeName = this.$route.name;

                this.links.map(link => {
                    if (link.route && link.route.name == routeName) {
                        this.currActive = link.title;
                    } else if (link.children) {
                        let dActive = '';
                        link.children.map(child => {
                            if (child.route && child.route.name == routeName) {
                                this.currActive = child.title;
                            }
                        });
                    }
                }); 
            },
            "$route.query": {
                immediate: true,
                handler({page, per_page}, prevQuery) {
                    const routeName = this.$route.name;
                    this.links.map(link => {
                        if (link.route && link.route.name == routeName) {
                            this.currActive = link.title;
                        } else if (link.children) {
                            let dActive = '';
                            link.children.map(child => {
                                if (child.route && child.route.name == routeName) {
                                    this.currActive = child.title;
                                }
                            });
                        }
                    }); 
                }
            }
        },
    }
</script>
<style lang="scss" scoped>
    /deep/ .el-submenu {
        /deep/ .el-submenu__title {
            .el-icon-arrow-right {
                display: none !important;
            }
        }
        .el-badge {
            float: right;
            display: block;
        }
    }
    /deep/ .el-menu-item.is-active, .el-menu-item:hover {
        font-weight: bolder;
        background-color: var(--primary-color) !important;
        color: white;

        i {
            color: white;
        }
    }
    /deep/ .el-submenu__title:hover {
        background-color: var(--primary-color) !important;
        color: white;

        i {
            color: white;
        }
    }
</style>


<style lang="scss" scoped>
    .el-menu-vertical-demo:not(.el-menu--collapse) {
        width: 256px;
    }
    .el-menu--collapse {
        .el-menu-item {
            will-change: transform;
            span.title {
                display: none;
            }
        }
        .el-submenu {
            .el-submenu__title {
                span.title {
                    display: none;
                }
            }
        }
    }
    .el-menu--vertical {
        .el-menu--popup {
            a {
                color: #303133 !important;
                text-decoration: none;
            }
        }
    }
    .el-menu {
        width: 56px;
        display: flex;
        flex-direction: column;
        border-right: none !important;

        &-aside {
            background: #fff;
        }

        .content {
            padding: 0;
            // overflow: auto;

            a {
                color: #303133;
                text-decoration: none;
            }

            .is-active:not(.el-submenu) {

                > a {
                    font-weight: bold;
                }
            }

            .el-menu-item,
            :global(.el-submenu__title) {
                will-change: transform;
                .icon {
                    vertical-align: middle;
                    margin-right: 5px;
                    width: 24px;
                    text-align: center;
                    font-size: 18px;
                }

                &.nested {
                    padding: 0 40px !important;
                }
            }

            .icon-right-open {
                &.icon {
                    font-size: 14px;
                    margin-right: 0px;
                }
            }

            .el-submenu {
                .el-menu-item {
                    padding: 0px 21px 0px 40px !important;
                }
            }
        }
    }

    .el-submenu :global(.el-menu) {
        background-color: darken(#fff, 2.4%);
    }
</style>
