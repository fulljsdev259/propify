<template>
    <div :class="['pinboard-box']">
        <div :class="['pinboard']">
            <ui-heading icon="icon-megaphone-1" :title="$t('resident.pinboard')" :description="$t('resident.heading_info.pinboard')" />
            
                <ui-divider />
            <div class="container">
                
                <div class="content">
                    <pinboard-add-card />
                    <el-divider content-position="left">
                        <el-button @click="refreshPage" size="small" icon="icon-arrows-ccw" plain round>{{$t('resident.refresh')}}</el-button>
                        <!-- <el-popover popper-class="pinboard-filter" placement="bottom-end" trigger="click" :width="192">
                            <el-button size="small" slot="reference" icon="el-icon-sort" round>{{$t('resident.filters')}}</el-button>
                            <filters ref="filters" layout="row" :data="filters.data" :schema="filters.schema" @changed="onFiltersChanged" />
                            <el-button type="primary" size="mini" icon="el-icon-sort-up" @click="resetFilters">{{$t('resident.reset_filters')}}</el-button>
                        </el-popover> -->
                    </el-divider>
                    <dynamic-scroller ref="dynamic-scroller" heightField="height" :items="filteredPinboards" :min-item-size="131" v-if="!loading && filteredPinboards.length">
                        <!-- <template #before v-if="loading && !filteredPinboards.length">
                            <loader v-for="idx in 5" :key="idx" />
                        </template> -->
                        <template v-slot="{item, index, active}">
                            <dynamic-scroller-item :item="item" :active="active" :data-index="index" :size-dependencies="[item]" page-mode v-if="!loading">
                                <pinboard-new-resident-card :data="item" v-if="$constants.pinboard.type[item.type] === 'new_neighbour' && item.user_id != $store.getters.loggedInUser.id"/>
                                <pinboard-card :data="item" @edit-pinboard="editPinboard" @delete-pinboard="deletePinboard" v-else-if="$constants.pinboard.type[item.type] !== 'new_neighbour'" @update-dynamic-scroller="force_scroller_update()"/>
                            </dynamic-scroller-item>
                        </template>
                        <!-- <template #after v-if="loading && !filteredPinboards.length">
                            <loader />
                        </template> -->
                    </dynamic-scroller>
                </div>
                <!-- <rss-feed class="rss-feed" title="Blick.ch Pinboard" /> -->
            </div>
            
        </div>
        <ui-drawer :size="448" :visible.sync="visibleDrawer" :z-index="1" direction="right" docked>
            <ui-divider content-position="left" v-if="editingPinboard"><i class="ti-pencil"></i> {{$t('resident.edit_pinboard')}}</ui-divider>
            <div class="content">
                <pinboard-edit-form :data="editingPinboard" v-if="editingPinboard" :visible.sync="visibleDrawer"/>
            </div>
        </ui-drawer>
        
    </div>
</template>

<script>
    import Loader from 'components/resident/PinboardCard/Loader'
    import {mapState} from 'vuex'

    import PinboardCardLoader from 'components/resident/PinboardCard/Loader'
    import PinboardCardErrorFallback from 'components/resident/PinboardCard/Error'

    import PinboardNewResidentCardLoader from 'components/resident/PinboardNewResidentCard/Loader'
    import PinboardNewResidentCardFallbackError from 'components/resident/PinboardNewResidentCard/Error'

    export default {
        components: {
            Loader,
            pinboardAddCard: () => ({
                component: import(/* webpackChunkName: "pinboardAddCard" */ 'components/resident/PinboardAddCard'),
                delay: 0,
                timeout: 8000
            }),
            pinboardCard: () => ({
                component: import(/* webpackChunkName: "pinboardCard" */ 'components/resident/PinboardCard'),
                error: PinboardCardErrorFallback,
                delay: 0,
                timeout: 8000
            }),
            pinboardNewResidentCard: () => ({
                component: import(/* webpackChunkName: "pinboardCard" */ 'components/resident/PinboardNewResidentCard'),
                // loading: PinboardNewResidentCardLoader,
                error: PinboardNewResidentCardFallbackError,
                delay: 0,
                timeout: 8000
            }),
            rssFeed: () => ({
                component: import(/* webpackChunkName: "rssFeed" */ 'components/resident/RSSFeed'),
                delay: 0,
                timeout: 8000
            })
        },
        data () {
            return {
                loading: false,
                filters: {
                    schema: [{
                        type: 'el-select',
                        title: 'Category',
                        name: 'category',
                        props: {
                            size: 'mini',
                            clearable: false,
                            defaultFirstOption: true
                        },
                        children: [{
                            type: 'el-option',
                            props: {
                                label: this.$t('resident.all'),
                                value: null
                            }
                        }, {
                            type: 'el-option',
                            props: {
                                label: this.$t('resident.my_pinboard'),
                                value: 1
                            }
                        }, {
                            type: 'el-option',
                            props: {
                                label: this.$t('resident.from_neighbourhood'),
                                value: 2
                            }
                        }, {
                            type: 'el-option',
                            props: {
                                label: this.$t('resident.from_quarter'),
                                value: 3
                            }
                        }]
                    }],
                    data: {
                        category: null
                    }
                },
                filterCategory: null,
                editingPinboard: null,
                visibleDrawer: false,
                deleteModalVisible: false,
                delPinboardStatus: -1
            }
        },
        methods: {
            onResize() {
                force_scroller_update();
            },
            force_scroller_update() {
                 this.$refs['dynamic-scroller'].forceUpdate();
            },
            async getPinboards (params = {}) {
                if (this.loading && this.pinboard.data.length) {
                    return
                }

                const {current_page, last_page} = this.pinboard

                if (this.pinboard.data.length && current_page === last_page) {
                    return
                }

                let page = current_page || 0

                page++

//                this.$refs['dynamic-scroller'].forceUpdate()
                this.loading = true

                await this.$store.dispatch('newPinboard/get', {
                    page,
                    per_page: 25,
                    sortedBy: 'desc',
                    orderBy: 'created_at',
                    ...params
                })

                this.loading = false
            },
            async onFiltersChanged (filters) {
                this.filterCategory = filters.category;
            },
            resetFilters () {
                this.$refs.filters.reset()
                this.filterCategory = null;
            },
            refreshPage () {
                this.getPinboards();
                this.resetFilters ()
            },
            async deletePinboard(event, data) {
                const resp = await this.$confirm(this.$t(`general.swal.delete_listing.text`), this.$t(`general.swal.delete_listing.title`), {
                    type: 'warning'
                }).then(() => {
                    this.$store.dispatch('newPinboard/delete', data)
                }).catch(() => {
                });
            },
            editPinboard(event, data) {
               this.editingPinboard = data;
               this.visibleDrawer = true;
            }
        },
        watch: {
            'visibleDrawer': {
                immediate: false,
                handler (state) {
                    // TODO - auto blur container if visible is true first
                    if (!state) {
                        this.editingPinboard = null
                    }
                }
            }
        },
        computed: {
            ...mapState('newPinboard', {
                pinboard: state => state
            }),
            filteredPinboards() {
                // if(this.$refs['dynamic-scroller'])
                //     this.$refs['dynamic-scroller'].forceUpdate()
                return this.pinboard.data.filter( pinboard => { return this.filterCategory == null || pinboard.category == this.filterCategory})
            }
        },
        async mounted() {
            await this.$store.dispatch('newPinboard/reset')
            await this.$store.dispatch('comments/reset')
            await this.getPinboards()
        }
    }
</script>

<style lang="scss" scoped>
    .pinboard-filter {
        // .el-divider {
        //     .el-divider__text {
        //         width: 100%;
        //         padding: 16px;
        //         background-color: transparent;
        //         padding: 0;
        //         left: 0;
        //         display: flex;
        //         align-items: center;
        //         box-sizing: border-box;
        //     }

        //     .el-button {
        //         width: 100%;
        //         margin-top: 16px;
        //     }
        // }
    }
</style>


<style lang="scss" scoped>
    .layout .container .content .view {
        padding: 0;
    }

    .pinboard {
        width: 100%;
        height: 100%;
        padding: 16px;
        overflow-y: auto;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }
    
    .container {
        display: grid;
        grid-gap: 12px;
        grid-template-columns: minmax(min-content, 640px) minmax(auto, 480px);

        &:before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            background-image: url('~img/51177185_23843277688790167_2069589399565238272_n.png');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: top left;
            width: 100%;
            height: 100%;
            opacity: .16;
            pointer-events: none;
        }

        .heading {
            & + .el-divider {
                grid-column: 1 / -1;
            }
        }

        .el-card {
            background-color: transparentize(#fff, .28);
            align-self: baseline;
            z-index: 1;
        }

        .content {
            > .el-card {
                & + .el-divider {
                    :global(.el-divider__text) {
                        width: 100%;
                        padding: 16px;
                        background-color: transparent;
                        left: 0;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        box-sizing: border-box;
                    }
                }
            }

            .vue-recycle-scroller {
                overflow: hidden;

                :global(.vue-recycle-scroller__item-wrapper) {
                    overflow: visible;

                    :global(.vue-recycle-scroller__item-view) > div {
                        padding-top: 6px;
                        padding-bottom: 6px;
                    }
                }
            }
        }

        .el-divider--horizontal {
            margin: 24px 0;
        }
    }

    @media screen and (max-width: 676px) {

        .pinboard .container {
            grid-template-columns: auto;
        }
        .rss-feed {
            display: none;
        }
    }
</style>

<style lang="sass" scoped>
    .pinboard-box
        /deep/ .ui-drawer
            display: flex
            flex-direction: column

            &:before
                content: ''
                position: fixed
                top: 0
                left: 0
                width: 100%
                height: 100%
                // background-image: url('~img/5d619aede1e3c.png')
                background-repeat: no-repeat
                background-position: top center
                width: 100%
                height: 100%
                filter: opacity(0.08)
                pointer-events: none

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
                height: 100%
                display: flex
                padding: 16px
                overflow-y: auto
                flex-direction: column

                .el-form
                    flex: 1

                    /deep/ .el-input .el-input__inner,
                    /deep/ .el-textarea .el-textarea__inner
                        background-color: transparentize(#fff, .44)

                    /deep/ .el-loading-mask
                        position: fixed
                    
                    .ui-media-gallery
                        margin-top: 8px

                    .ui-divider
                        margin-top: 30px
                        margin-left: 0
                        margin-bottom: 16px
    @media only screen and (max-width: 676px)
        .pinboard
            /deep/ .ui-heading__content__description
                display: none
</style>

