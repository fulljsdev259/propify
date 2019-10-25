<template>
    <ui-card shadow="always" v-if="requests.length">
        <template #header>
            <i class="icon-chat-empty"></i> {{$t('resident.latest_public_requests')}}
            <el-button type="text" @click="$router.push({name: 'residentRequests'})">{{$t('resident.actions.view_all')}}</el-button>
        </template>
        <loader v-if="loading" />
        <div class="placeholder" v-else-if="!loading && !requests.length">
            <img class="image" :src="require('img/5c9d48f15dd1a.png')" />
            <div class="content">
                <div class="title">No requests available yet.</div>
                <div class="description">Et aut cum ut earum. Et aperiam ut possimus explicabo. Modi dolores in odit id fuga maxime aperiam dolor.</div>
            </div>
        </div>
        <el-collapse :value="0" accordion v-else>
            <el-collapse-item v-for="(request, idx) in requests" :key="request.id" :name="idx">
                <template #title>
                    <ui-avatar :src="request.resident.user.avatar" :size="36" :name="request.resident.user.name" />
                    <div class="content">
                        <div class="category">{{ request.category.name }}</div>
                        <div class="title">
                            {{request.title}}
                        </div>
                    </div>
                </template>
                <div class="p-description" v-html="request.description"></div>
                <el-button size="mini" icon="icon-right-1" plain round @click="$emit('view-detail-request', $event, request)">{{$t('resident.actions.view')}}</el-button>
            </el-collapse-item>
        </el-collapse>
    </ui-card>
</template>

<script>
    import Loader from './Loader'
    import {EXTRA_LOADING_SECONDS} from 'config'
    import {displaySuccess, displayError} from 'helpers/messages'
    import {mapState} from 'vuex'

    export default {
        name: 'p-latest-requests-card',
        components: {
            Loader
        },
        props: {
            limit: {
                type: Number,
                default: 4
            }
        },
        data () {
            return {
                timeout: null,
                loading: false
            }
        },
        computed: {
            ...mapState('newRequests', {
                requests: ({data}) => data.slice(this.limit)
            })
        },
        async mounted () {
            
            //if (!this.requests.length) {
                this.loading = true;

                await this.$store.dispatch('newRequests/reset');    
                await this.$store.dispatch('newRequests/get', {
                    sortedBy: 'desc',
                    orderBy: 'created_at',
                    per_page: this.limit
                })

                this.timeout = setTimeout(() => this.loading = false, EXTRA_LOADING_SECONDS)
            //}
        },
        beforeDestroy () {
            clearTimeout(this.timeout)
        }
    }
</script>

<style lang="scss" scoped>
    .ui-card {
        :global(.ui-card__header) {
            position: relative;
            .el-button {
                padding: 0;
                margin-left: auto;

                :global([class*=icon] + span) {
                    margin-left: 5px;
                }
            }
        }

        :global(.ui-card__body) {
            .el-collapse {
                margin: -16px;
                border-style: none;

                .el-collapse-item {

                    &:last-child :global(.el-collapse-item__header) {
                        border-bottom-style: none;
                    }

                    :global(.el-collapse-item__header) {
                        background: transparent;
                        height: 56px;
                        line-height: 56px;
                        padding: 0 16px;
                        cursor: pointer;

                        .content {
                            flex: auto;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            line-height: 1.48;
                            overflow: hidden;
                            margin-left: 16px;

                            .category,
                            .title {
                                overflow: hidden;
                                min-width: 0;
                                text-overflow: ellipsis;
                                white-space: nowrap;
                            }

                            .category {
                                font-size: 14px;
                                font-weight: 300;
                                color: var(--color-text-placeholder);
                            }

                            .title {
                                font-size: 15px;
                                font-weight: 600;
                                color: var(--color-primary);
                            }
                        }
                    }

                    :global(.el-collapse-item__wrap) {
                        background: transparent;

                        :global(.el-collapse-item__content) {
                            color: var(--color-text-secondary);
                            padding: 0 16px 16px 68px;

                            &:after {
                                content: '';
                                display: table;
                                clear: both;
                            }

                            .description {
                                white-space: pre-wrap;
                            }

                            .el-button {
                                float: right;
                            }
                        }
                    }
                }
            }
        }
    }
</style>

<style lang="scss" scoped>
    .el-card {
        :global(.el-card__header) {
            height: 64px;
            font-size: 20px;
            font-weight: 600;
            color: var(--primary-color);
            flex: auto;
            overflow: hidden;
            min-width: 0;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: flex;
            align-items: center;

            i {
                width: 32px;
                height: 32px;
                border: 2px var(--primary-color) solid;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 8px;
            }

            .el-button {
                margin-left: auto;

                :global([class*=icon]) + :global(span) {
                    margin-left: 5px;
                }
            }
        }

        :global(.el-card__body) {
            .el-collapse {
                margin: -16px;
                border-style: none;

                .el-collapse-item {
                    &:hover {
                        background-color: var(--primary-color-lighter);
                    }

                    :global(.el-collapse-item__header) {
                        background: transparent;
                        height: 56px;
                        line-height: 56px;
                        padding: 0 16px;
                        cursor: pointer;

                        .content {
                            flex: auto;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            line-height: 1.48;
                            overflow: hidden;
                            margin-left: 16px;

                            .category,
                            .title {
                                overflow: hidden;
                                min-width: 0;
                                text-overflow: ellipsis;
                                white-space: nowrap;
                            }

                            .category {
                                font-size: 14px;
                                font-weight: 300;
                                color: var(--color-text-placeholder)
                            }

                            .title {
                                font-size: 15px;
                                font-weight: 600;
                                color: var(--color-primary);
                            }
                        }
                    }

                    :global(.el-collapse-item__wrap) {
                        background: transparent;

                        :global(.el-collapse-item__content) {
                            color: var(--color-text-secondary);
                            padding: 0 16px 16px 68px;

                            &:after {
                                content: '';
                                display: table;
                                clear: both;
                            }

                            .description {
                                white-space: pre-wrap;
                            }

                            .el-button {
                                float: right;
                            }
                        }
                    }
                    &:last-child :global(.el-collapse-item__header) {
                        border-bottom-style: none;
                    }
                }
            }
        }
    }
</style>