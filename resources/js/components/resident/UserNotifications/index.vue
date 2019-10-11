<template>
    <div class="notifications" v-infinite-scroll="getNotifications" infinite-scroll-disabled="true">
        <template v-if="loading.notifications || notifications.data.length">
            <el-divider content-position="right">
                <el-button size="small" :disabled="!unreadNotifications.length" :loading="loading.markAll" icon="icon-th-list" round @click="markAll()">{{$t('resident.mark_all_as_read')}}</el-button>
            </el-divider>
            <el-timeline>
                <el-timeline-item :class="{'is-unread': !notification.read_at}" v-for="notification in notifications.data" :key="notification.id" size="large" placement="top" hide-timestamp>
                    <div class="user">
                        <ui-avatar :src="notification.avatar" :name="notification.data.user_name" :size="24" />
                        <!-- <div class="ui-avatar">
                            <i v-if="notification.data.post_type == 1" class="icon icon-megaphone-1"></i>
                            <i v-else-if="notification.data.post_type == 2" class="icon icon-chat-empty"></i>
                        </div> -->
                        {{notification.data.user_name}} • <div class="date">{{ago(notification.created_at, $i18n.locale)}}</div>
                    </div>
                    <div class="content">{{notification.data.fragment}}</div>
                    <el-dropdown size="small" @command="onDropdownCommand" trigger="click">
                        <el-button type="text" icon="icon-ellipsis-vert" class="el-dropdown-link" />
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item :icon="notification.read_at ? 'icon-eye-off' : 'icon-eye'" :command="notification">Mark as {{notification.read_at ? 'unread' : 'read'}}</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </el-timeline-item>
                <el-timeline-item v-if="loading.notifications">
                    {{$t('general.loading')}}
                </el-timeline-item>
            </el-timeline>
        </template>
        <div class="placeholder" v-else-if="!loading.notifications && !notifications.data.length">
            <img :src="require('img/4as334da63xc.png')" />
            <div class="content">
                <div class="title">{{$t('resident.no_data.notification')}}</div>
                <div class="description">{{$t('resident.no_data_info.notification')}}</div>
            </div>
        </div>
    </div>
</template>

<script>
    // TODO - find reason why it makes two calls (duplicated keys)
    import AgoMixin from 'mixins/agoMixin'
    import {mapState, mapGetters} from 'vuex'

    export default {
        name: 'p-user-notifications',
        mixins: [AgoMixin],
        data () {
            return {
                loading: {
                    notifications: false,
                    markAll: false,
                    mark: {}
                }
            }
        },
        computed: {
            ...mapState(['notifications']),
            ...mapGetters('notifications', {
                unreadNotifications: 'unread'
            })
        },
        methods: {
            async getNotifications () {
                if (this.loading.notifications) {
                    return
                }

                const {current_page, last_page} = this.notifications

                if (this.notifications.data.length && current_page && last_page && current_page === last_page) {
                    return
                }

                let page = current_page || 0

                page++

                this.loading.notifications = true
                await this.$store.dispatch('notifications/get', {
                    page,
                    per_page: 50,
                    sortedBy: 'desc',
                    orderBy: 'created_at'
                })

                this.loading.notifications = false
            },
            async mark ({id}) {
                this.loading.mark[id] = true

                await this.$store.dispatch('notifications/mark', {id})

                this.loading.mark[id] = false
            },
            markAll () {
                this.unreadNotifications.forEach(async ({id}) => {
                    this.loading.markAll = true

                    await this.$store.dispatch('notifications/mark', {id})

                    this.loading.markAll = false
                })
            },
            async onDropdownCommand (command) {
                if (typeof command === 'object') {
                    await this.mark(command)
                }
            },
            getTypeColor (type) {
                return {
                    'announcement_pinbord_published': '#BF55EC'
                }[type]
            }
        },
        mounted () {
            this.getNotifications();
        }
    }
</script>


<style lang="scss" scoped>
    .notifications {
        .placeholder {
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            justify-content: center;

            img {
                width: 240px;
            }

            .content {

                .title {
                    font-size: 21px;
                    font-weight: 800;
                }

                .description {
                    font-size: 13px;
                    color: var(--color-text-placeholder);
                }
            }
        }

        .el-divider {
            margin: 16px 0 32px 0;

            :global(.el-divider__text.is-right) {
                right: 0;
                padding: 0;
            }
        }

        .el-timeline {
            margin: 0;
            padding: 0;

            :global(.el-timeline-item) {
                padding: 2px 0;

                &.is-unread {
                    :global(.el-timeline-item__wrapper) {
                        background-color: var(--primary-color-lighter);

                        :global(.el-timeline-item__content) {
                            .el-dropdown {
                                visibility: visible;
                            }
                        }
                    }
                }

                &:not(.is-unread) {
                    :global(.el-timeline-item__wrapper) {
                        :global(.el-timeline-item__content) {
                            .el-dropdown {
                                visibility: hidden;
                            }
                        }
                    }
                }

                &:hover {
                    :global(.el-timeline-item__wrapper) {
                        :global(.el-timeline-item__content) {
                            .el-dropdown {
                                visibility: visible;
                                opacity: 1;
                            }
                        }
                    }
                }

                &:not(.is-unread):hover {
                    :global(.el-timeline-item__wrapper) {
                        background-color: darken(#fff, 6%);
                        cursor: pointer;
                    }
                }

                :global(.el-timeline-item__wrapper) {
                    top: -13px;
                    padding: 8px;
                    margin-left: 16px;
                    border-radius: 12px;
                    transition: .16s background-color;

                    :global(.el-timeline-item__content) {
                        display: flex;
                        flex-wrap: wrap;
                        align-items: center;

                        .user {
                            width: 100%;
                            font-size: 13px;
                            font-weight: 600;
                            color: var(--color-primary);
                            display: flex;
                            align-items: center;

                            .ui-avatar {
                                margin-right: 8px;
                                i {
                                    font-size: 23px;
                                    border-radius: 50%;
                                }
                            }

                            .date {
                                font-weight: 500;
                                font-size: 12px;
                                margin-left: 4px;
                            }
                        }

                        .content {
                            flex: 1;
                            padding-left: 32px;
                            white-space: pre-warp;
                        }

                        .el-dropdown {
                            position: absolute;
                            top: 0;
                            right: 0;
                            margin: 12px;

                            .el-dropdown-link {
                                padding: 0;
                            }
                        }
                    }
                }
            }
        }
    }
</style>