<template>
    <ui-card shadow="always">
        <template #header>
            <i class="icon-megaphone-1"></i> {{$t('resident.latest_pinboard')}}
            <el-button type="text" @click="$router.push({name: 'residentPinboard'})">{{$t('resident.actions.view_all')}}</el-button>
        </template>
        <loader v-if="loading" />
        <div class="placeholder" v-else-if="!loading && !pinboard.length">
            <img class="image" :src="require('img/5c9d6e6c73f70.png')" />
            <div class="content">
                <div class="title">{{$t('resident.no_data.pinboard')}}</div>
                <div class="description">{{$t('resident.no_data_info.pinboard')}}</div>
            </div>
        </div>
        <el-collapse :value="0" accordion v-else>
            <el-collapse-item v-for="(pinboard, index) in pinboard" :key="pinboard.id" :name="index">
                <template #title>
                    <ui-avatar :src="pinboard.user.avatar" :size="36" :name="pinboard.user.name" />
                    <div class="content">
                        <div class="user">
                            {{pinboard.user.name}}
                        </div>
                        <like :id="pinboard.id" type="pinboard" readonly>
                            <div>
                                <i class="icon-chat"></i> {{pinboard.comments_count}}
                            </div>
                            <div>
                                <i class="icon-picture"></i> {{pinboard.media.length}}
                            </div>
                            <div class="time">                                                      
                                {{ago(pinboard.published_at, $i18n.locale)}}
                            </div>
                        </like>
                    </div>
                </template>
                <div class="content" v-html="pinboard.content"></div>
                <el-button size="mini" icon="icon-right-1" plain round @click="$router.push({name: 'residentPinboard',  params: { id: pinboard.id } });">{{$t('resident.actions.view')}}</el-button>
            </el-collapse-item>
        </el-collapse>
    </ui-card>
</template>

<script>
    import Loader from './Loader'
    import AgoMixin from 'mixins/agoMixin'
    import {mapState} from 'vuex'
    import {EXTRA_LOADING_SECONDS} from 'config'

    export default {
        name: 'p-latest-pinboard-card',
        mixins: [AgoMixin],
        props: {
            limit: {
                type: Number,
                default: 6
            }
        },
        components: {
            Loader
        },
        data () {
            return {
                loading: false,
                timeout: null
            }
        },
        computed: {
            ...mapState('newPinboard', {
                pinboard: ({data}) => data.slice(this.limit)
            })
        },
        async mounted () {
            //if (!this.pinboard.length) {
                this.loading = true
                
                await this.$store.dispatch('newPinboard/reset');
                await this.$store.dispatch('newPinboard/get', {
                    feed: 1,
                    sortedBy: 'desc',
                    per_page: this.limit,
                    orderBy: 'created_at',
                    status: +Object.entries(this.$constants.pinboard.status).find(([key, name]) => name === 'published')[0]
                })

                this.timeout = setTimeout(() => this.loading = false, EXTRA_LOADING_SECONDS)
            //}
        },
        beforeDestroy () {
            clearTimeout(this.timeout)
        }
    }
</script>

<style lang="sass" scoped>
    .ui-card
        /deep/ .ui-card__header
            .el-button
                padding: 0
                margin-left: auto

                /deep/ [class*=icon] + span
                    margin-left: 5px

        /deep/ .ui-card__body
            .el-collapse
                margin: -16px
                border-style: none

                .el-collapse-item
                    
                    &:last-child /deep/ .el-collapse-item__header
                        border-bottom-style: none

                    /deep/ .el-collapse-item__header
                        height: 56px
                        cursor: pointer
                        padding: 0 16px
                        line-height: 56px
                        background: transparent

                        .content
                            flex: auto
                            display: flex
                            overflow: hidden
                            margin-left: 16px
                            line-height: 1.48
                            flex-direction: column
                            justify-content: center

                            .user
                                min-width: 0
                                font-size: 15px
                                overflow: hidden
                                font-weight: 600
                                white-space: nowrap
                                text-overflow: ellipsis
                                color: var(--color-primary)


                            .like
                                font-size: 13px
                                font-weight: 300
                                color: var(--color-text-placeholder)

                    /deep/ .el-collapse-item__wrap
                        background: transparent

                        /deep/ .el-collapse-item__content
                            color: var(--color-text-secondary)
                            padding: 0 16px 16px 68px
                            font-size: 14px

                            &:after
                                content: ''
                                clear: both
                                display: table

                            .content
                                white-space: pre-wrap

                            .el-button
                                float: right
</style>