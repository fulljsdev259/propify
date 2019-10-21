<template>
<div class="dashboard-box">
    <div class="dashboard">
        <!-- <heading icon="ti-home" :title="$t('resident.my_dashboard')">
            <greeting ref="greeting" class="description" slot="description" />
        </heading> -->
        <ui-heading icon="ti-home" :title="$t('resident.my_dashboard')">
            <greeting ref="greeting" class="description" slot="description" />
        </ui-heading>
        <ui-divider />        
        <div class="row" name="fade" tag="div" ref="widgets">
            <div class="column">
                <emergency-card class="widget" />
                <weather-card class="widget" />
                <latest-property-managers-card class="widget" />
                <latest-my-neighbours-card class="widget" />
            </div>
            <div class="column">
                <requests-statistics-card class="widget" />
                <latest-requests-card class="widget" @view-detail-request="viewDetailRequest"/>
                <latest-pinboard-card class="widget" />
            </div>
            <div class="column">
                <!-- <latest-listings-card class="widget" /> -->
                <rate-card v-if="this.loggedInUser.resident.review == null"/>
            </div>
        </div>
        
        <!-- <div :class="[{[$refs.greeting.timeOfDay + '-time']: true}]" v-if="$refs.greeting"></div> -->

    </div>
    <ui-drawer :size="448" :visible.sync="visibleDrawer" :z-index="1" direction="right" docked @update:visibleDrawer="resetDataFromDrawer">
            <el-tabs type="card" v-model="activeDrawerTab" stretch v-if="openedRequest">
                <el-tab-pane name="chat" lazy>
                    <div slot="label">
                        <i class="ti-comments"></i>
                        {{$t('resident.chat')}}
                    </div>
                    <chat ref="chat" :id="openedRequest.id" type="request" height="100%" max-height="100%" />
                </el-tab-pane>
                <el-tab-pane name="media" lazy>
                    <div slot="label">
                        <i class="ti-gallery"></i>
                        {{$t('resident.media')}}
                    </div>
                    <ui-media-gallery :files="openedRequest.media.map(({url}) => url)" />
                    <ui-divider content-position="left"><i class="el-icon-upload"></i> {{$t('resident.request_upload_title')}}</ui-divider>
                    
                    <div class="upload-description">
                        <el-alert
                            :title="$t('resident.request_upload_desc')"
                            type="info"
                            show-icon
                            :closable="false"
                        >
                        </el-alert>
                        
                    </div>
                    <ui-media-uploader v-model="media" 
                                    :headers="{'Authorization': `Bearer ${authorizationToken}`, 'Accept': 'application/json, text/plain, */*', 'Content-Type': 'application/json;charset=UTF-8'}" 
                                    :action="`api/v1/requests/${openedRequest.id}/media`" 
                                    :id="openedRequest.id" 
                                    type="request"
                                    :options="{drop: true, draggable: true, multiple: true}" />

                </el-tab-pane>
                <el-tab-pane name="audit" lazy>
                    <div slot="label">
                        <i class="ti-gallery"></i>
                        Audit
                    </div>
                    <audit :id="openedRequest.id" type="request" show-filter />
                </el-tab-pane>
            </el-tabs>
        </ui-drawer>
</div>
</template>

<script>
    import Heading from 'components/Heading'
    import Greeting from 'components/Greeting'

    import WeatherCardLoader from 'components/resident/WeatherCard/Loader'
    import WeatherCardErrorFallback from 'components/resident/WeatherCard/Error'

    import LatestPropertyManagersCardLoader from 'components/resident/LatestPropertyManagersCard/Loader'
    import LatestPropertyManagersCardErrorFallback from 'components/resident/LatestPropertyManagersCard/Error'

    import RequestsStatisticsCardLoader from 'components/resident/RequestsStatisticsCard/Loader'
    import RequestsStatisticsCardErrorFallback from 'components/resident/RequestsStatisticsCard/Error'

    import LatestPinboardCardLoader from 'components/resident/LatestPinboardCard/Loader'
    import LatestPinboardCardErrorFallback from 'components/resident/LatestPinboardCard/Loader'

    import LatestRequestsCardLoader from 'components/resident/LatestRequestsCard/Loader'
    import LatestRequestsCardErrorFallback from 'components/resident/LatestRequestsCard/Loader'

    import {TweenMax, Elastic} from 'gsap'
    import {mapGetters} from 'vuex'

    export default {
        components: {
            Heading,
            Greeting,
            rateCard: () => ({
                component: import(/* webpackChunkName: "rateCard" */ 'components/resident/RateCard'),
                delay: 0,
                timeout: 8000
            }),
            latestPinboardCard: () => ({
                component: import(/* webpackChunkName: "latestPinboardCard" */ 'components/resident/LatestPinboardCard'),
                loading: LatestPinboardCardLoader,
                error: LatestPinboardCardErrorFallback,
                delay: 0,
                timeout: 8000
            }),
            latestListingsCard: () => ({
                component: import(/* webpackChunkName: "latestListingsCard" */ 'components/resident/LatestListingsCard'),
                delay: 0,
                timeout: 8000
            }),
            latestPropertyManagersCard: () => ({
                component: import(/* webpackChunkName: "latestPropertyManagersCard" */ 'components/resident/LatestPropertyManagersCard'),
                loading: LatestPropertyManagersCardLoader,
                error: LatestPropertyManagersCardErrorFallback,
                delay: 0,
                timeout: 8000
            }),
            latestRequestsCard: () => ({
                component: import(/* webpackChunkName: "latestRequestsCard" */ 'components/resident/LatestRequestsCard'),
                loading: LatestRequestsCardLoader,
                error: LatestRequestsCardErrorFallback,
                delay: 0,
                timeout: 8000
            }),
            latestMyNeighboursCard: () => ({
                component: import(/* webpackChunkName: "latestMyNeighboursCard" */ 'components/resident/LatestMyNeighboursCard'),
                delay: 0,
                timeout: 8000
            }),
            requestsStatisticsCard: () => ({
                component: import(/* webpackChunkName: "requestsStatisticsCard" */ 'components/resident/RequestsStatisticsCard'),
                loading: RequestsStatisticsCardLoader,
                error: RequestsStatisticsCardErrorFallback,
                delay: 0,
                timeout: 8000
            }),
            weatherCard: () => ({
                component: import(/* webpackChunkName: "weatherCard" */ 'components/resident/WeatherCard'),
                loading: WeatherCardLoader,
                error: WeatherCardErrorFallback,
                delay: 0,
                timeout: 8000
            }),
        },
        data () {
            return {
                loading: false,
                media: [],
                openedRequest: null,
                visibleDrawer: false,
                activeDrawerTab: 'chat',
                activeDrawerMediaTab: 0,
            }
        },
        computed: {
            ...mapGetters(['loggedInUser'])
        },
        methods: {
            resetDataFromDrawer () {
                this.activeDrawerTab = 'chat'
                this.openedRequest = null
            },
            viewDetailRequest(evt, request) {
                this.activeDrawerTab = 'chat'
                this.openedRequest = request

                this.visibleDrawer = !this.visibleDrawer
            }
        },
        watch: {
            'visibleDrawer': {
                immediate: false,
                handler (state) {
                    // TODO - auto blur container if visible is true first
                    if (!state) {
                        this.openedRequest = null
                    }
                }
            }
        },
        mounted () {
            // TweenMax.staggerFrom(, 2, {scale:0.5, opacity:0, delay:0.5, ease:Elastic.easeOut, force3D:true}, 0.2)
        }
    }
</script>

<style lang="scss" scoped>
    .dashboard {
        overflow: auto;
        padding: 16px;
        
        .heading {
            .description {
                color: darken(#fff, 40%);
            }
        }

        .el-divider {
            background: linear-gradient(to right, var(--border-base-color), transparent 56%);
        }

        &:before {
            content: '';
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background-image: url('~img/5d2212060e9f6.png');
            background-position: 0 -10em;
            background-repeat: no-repeat;
            background-attachment: fixed;
            pointer-events: none;
            z-index: -1;
            filter: opacity(.16);
        }

        .row {
            display: grid;
            grid-gap: 12px;
            // grid-template-columns: repeat(auto-fill, minmax(448px, 1fr));
            grid-template-columns: 1fr 1fr 1fr;

            .column {
                display: grid;
                grid-gap: 12px;
                grid-auto-rows: min-content;
                will-change: transform;

                .el-card,
                .ui-card {
                    background-color: transparentize(#fff, .28);
                    will-change: width, height, transform;
                }
            }
        }
    }

    @media only screen and (max-width: 1300px) {
        .dashboard {
            .row {
                grid-template-columns: 1fr 1fr;
            }
        }
    }

    @media only screen and (max-width: 676px) {
        .dashboard {
            .row {
                grid-template-columns: 1fr;
            }
        }
    }
</style>

<style lang="sass" scoped>
    .dashboard-box
        display: flex
        padding: 0 !important
        flex-direction: column
        overflow: hidden !important


        .ui-drawer
            .el-tabs
                height: 100%
                display: flex
                flex-direction: column

                /deep/ .el-tabs__header
                    margin-bottom: 0

                    /deep/ .el-tabs__nav-wrap
                        /deep/ .el-tabs__nav-scroll
                            /deep/ .el-tabs__nav
                                border: 0

                /deep/ .el-tabs__content
                    height: 100%
                    overflow-y: auto
                    display: flex
                    flex-direction: column

                    /deep/ .el-tab-pane
                        height: 100%
                        display: flex
                        flex-direction: column

                        > *
                            padding: 16px

                        .el-tabs
                            padding: 0

                        .chat
                            .comments-list
                                .vue-recycle-scroller
                                    margin-top: -16px
                                    margin-right: -16px
                                    padding-top: 16px
                                    padding-right: 16px

                        .ui-divider
                            padding: 0
                       
                        .upload-description
                            margin: 16px;
                            padding: 0

                        .ui-media-uploader 
                            flex-grow: 1
                            
                        // .ui-media-gallery
                        //     height: 100%
                        //     padding: 16px

                        // .audit
                        //     padding: 16px
            .ui-divider
                margin: 32px 16px 0 16px

                /deep/ .ui-divider__content
                    left: 0
                    z-index: 1
                    padding-left: 0
                    font-size: 16px
                    font-weight: 700
                    color: var(--color-primary)
            .content
                height: calc(100% - 32px)
                display: flex
                padding: 16px
                overflow-y: auto
                flex-direction: column
                position: relative

                .el-form
                    flex: 1
                    display: flex
                    flex-direction: column

                    /deep/ .el-input .el-input__inner,
                    /deep/ .el-textarea .el-textarea__inner
                        background-color: transparentize(#fff, .44)

                    /deep/ .el-loading-mask
                        position: fixed
</style>