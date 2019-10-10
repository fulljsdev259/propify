<template>
    <div :class="['listing', {md: el.is.md}]">
        <div class="container">
            <ui-heading icon="icon-basket" :title="$t('resident.listing')" :description="$t('resident.heading_info.listing')" />
            <ui-divider />
            <ui-card class="content" shadow="always" v-loading="loading">
                <template #header >
                    <el-popover class="filter-btn" popper-class="listing__filter-popover" placement="bottom-start" trigger="click" :width="192">
                        <el-button slot="reference" icon="el-icon-sort">{{$t('resident.filters')}}</el-button>
                        <filters ref="filters" layout="row" :data.sync="filters.data" :schema="filters.schema" @changed="onFiltersChanged" />
                        <el-button type="primary" size="small" icon="el-icon-sort-up" @click="resetFilters">{{$t('resident.reset_filters')}}</el-button>
                    </el-popover>
                    <el-input prefix-icon="el-icon-search" v-model="search" :placeholder="$t('resident.placeholder.search_listing')" clearable @clear="handleSearch" @keyup.enter.native="handleSearch" />
                    <el-button type="primary" class="search-btn" icon="el-icon-search" :disabled="loading" @click="handleSearch">{{$t('resident.actions.search')}}</el-button>
                    <el-button type="primary" class="add-listing-btn" icon="icon-plus" @click="addListing()">{{$t('resident.add_listing')}}</el-button>
                    <el-button type="primary" class="mobile-search-btn" icon="el-icon-search" :disabled="loading" @click="handleSearch"></el-button>
                    <el-button type="primary" class="mobile-add-listing-btn" icon="icon-plus" @click="addListing()"></el-button>
                </template>
                <template v-if="loading">
                    <loader v-for="idx in 5" :key="idx" />
                </template>
                <div class="placeholder" v-else-if="!loading && !listings.data.length">
                    <img class="image" :src="require('img/5ca7dde590fa1.png')" />
                    <div class="title">{{$t('resident.no_data.listing')}}</div>
                    <div class="description">{{$t('resident.no_data_info.listing')}}</div>
                </div>
                <template v-else>
                    <listing-card v-for="listing in listings.data"
                                :key="listing.id"
                                :data="listing"
                                @click="openListingDetailsDialog(listing)"
                                @delete-listing="deleteListing"
                                @edit-listing="editListing"/>
                </template>
                <el-pagination slot="footer" :layout="pagination.layout" :current-page="pagination.current" :page-size="pagination.size" :page-sizes="pagination.sizes" :total="listings.total" @size-change="onSizeChange" @current-change="onCurrentPageChange" background />
            </ui-card>
            <el-dialog :custom-class="`listing__opened-listing-dialog ${el.is.md ? 'listing__opened-listing-md-dialog' : ''}`" :visible.sync="visibleDialog" :before-close="onListingDetailsDialogClose" :show-close="true" append-to-body>
                <listing-details :data="openedListing" v-if="openedListing" />
            </el-dialog>
        </div>
        <ui-drawer :size="448" :visible.sync="visibleDrawer" :z-index="1" direction="right" docked>
            <ui-divider content-position="left" v-if="editingListing"><i class="ti-pencil"></i> {{$t('resident.edit_listing')}}</ui-divider>
            <ui-divider content-position="left" v-else>{{$t('resident.add_listing')}}</ui-divider>
            <div class="content">
                <listing-edit-form :data="editingListing" @delete-listing="deleteListing" v-if="editingListing"/>
                <listing-add-form :visible.sync="visibleDrawer" v-else/>
            </div>
        </ui-drawer>
    </div>
</template>

<script>
    import Loader from 'components/resident/ListingCard/Loader'
    import {ResponsiveMixin} from 'vue-responsive-components'
    import {mapState} from 'vuex'

    const DEFAULT_PAGINATION_LAYOUT = 'total, sizes, prev, pager, next, jumper'
    const MINIMAL_PAGINATION_LAYOUT = 'prev, pager, next'

    export default {
        mixins: [
            ResponsiveMixin
        ],
        components: {
            Loader
        },
        data () {
            return {
                loading: false,
                search: undefined,
                openedListing: null,
                editingListing: null,
                visibleDrawer: false,
                visibleDialog: false,
                pagination: {
                    current: 1,
                    size: 15,
                    sizes: [15, 25, 50, 100],
                    layout: DEFAULT_PAGINATION_LAYOUT
                },
                filters: {
                    schema: [{
                        type: 'el-switch',
                        title: 'resident.my_offerings',
                        name: 'user_id',
                        props: {
                            activeValue: this.$store.getters.loggedInUser.id.toString(),
                            inactiveValue: null
                        }
                    }, {
                        type: 'el-select',
                        title: 'resident.type',
                        name: 'type',
                        props: {
                            size: 'small'
                        },
                        children: [{
                            type: 'el-option',
                            props: {
                                label: 'resident.all',
                                value: null
                            }
                        }].concat(Object.entries(this.$constants.listings.type).map(([value, label]) => ({
                            type: 'el-option',
                            props: {label: `models.listing.type.${label}`, value}
                        })))
                    }],
                    data: {
                        type: null,
                        user_id: null
                    }
                }
            }
        },
        computed: {
            ...mapState('newListings', {
                listings: state => state
            }),

            breakpoints () {
                return {
                    md: el => {
                        if (el.width <= 1024) {
                            this.pagination.layout = MINIMAL_PAGINATION_LAYOUT

                            return true
                        } else {
                            this.pagination.layout = DEFAULT_PAGINATION_LAYOUT
                        }
                    },
                    sm: el => el.width <= 768
                }
            }
        },
        methods: {
            async get(params = {
                search: this.search,
                page: this.pagination.current,
                per_page: this.pagination.size
            }) {
                if (this.loading && this.listings.data.length) {
                    return
                }

                this.loading = true

                this.pagination.current = +params.page
                this.pagination.size = +params.per_page

                if ('search' in params && !params.search) {
                    delete params.search
                }

                this.$router.replace({query: params, name: this.$route.name})

                await this.$store.dispatch('newListings/get', {
                    per_page: 5,
                    sortedBy: 'desc',
                    orderBy: 'created_at',
                    ...params
                })

                this.loading = false
                this.$el.scrollTop = 0
            },
            async handleSearch (value) {
                const {page, per_page, search, ...rest} = this.$route.query

                await this.get({page, per_page, search: this.search, ...rest})
            },
            async onFiltersChanged (filters) {
                await this.get({
                    page: 1,
                    per_page: this.pagination.size,
                    search: this.$route.query.search,
                    ...filters
                })
            },
            async onSizeChange (per_page) {
                await this.get({page: 1, per_page})
            },
            async onCurrentPageChange (page) {
                await this.get({page, per_page: this.pagination.size})
            },
            resetFilters () {
                this.$refs.filters.reset()
            },
            openListingDetailsDialog (listing) {
                this.openedListing = listing
                this.visibleDialog = true
            },
            onListingDetailsDialogClose (done) {
                this.openedListing = null

                done()
            },
            addListing() {
                this.editingListing = null
                this.visibleDrawer = true
            },
            editListing(evt, listing) {
                this.editingListing = listing
                this.visibleDrawer = true
            },
            deleteListing(evt, listing) {
                this.$confirm(this.$t(`general.swal.delete_listing.text`), this.$t(`general.swal.delete_listing.title`), {
                    type: 'warning'
                }).then(() => {
                    this.$store.dispatch('newListings/delete', {id: listing.id})
                    this.editingListing = null
                    this.visibleDrawer = false
                }).catch(() => {
                });
                
            }
        },
        created () {
            const {search, ...rest} = this.$route.query

            if (search) {
                this.search = search
            }

            Object.entries(rest).forEach(([param, value]) => {
                if (this.filters.data.hasOwnProperty(param)) {
                    this.filters.data[param] = value
                }
            })
        },
        async mounted () {
            const {page = this.pagination.current, per_page = this.pagination.size, search, ...rest} = this.$route.query

            await this.get({page, per_page, search, ...rest})
        },
        watch: {
            'visibleDrawer': {
                immediate: false,
                handler (state) {
                    // TODO - auto blur container if visible is true first
                    if (!state) {
                        this.editingListing = null
                    }
                }
            }
        },
    }
</script>

<style lang="sass">
    .listing__opened-listing-dialog
        width: 80%
        max-width: 1024px
        overflow: hidden
        border-radius: 12px

        &.listing__opened-listing-md-dialog .el-dialog__body .listing-details
            grid-template-columns: 1fr

            .ui-images-carousel
                height: 384px !important

        .el-dialog__header
            display: none

        .el-dialog__body
            padding: 0

    .listing__filter-popover
        padding: 16px
        border-radius: 12px
        box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76)

        .el-button
            width: 100%
            margin-top: 8px
    @media only screen and (max-width: 676px)
        .listing__opened-listing-dialog
            width: 100%
            margin-top: 0 !important

            .el-dialog__header
                display: block
                position: absolute
                z-index: 1
                right: 0
</style>

<style lang="sass" scoped>
    .listing
        display: flex
        padding: 0 !important
        flex-direction: column
        overflow: hidden !important

        &:before
            content: ''
            top: 0
            left: 0
            z-index: -1
            width: 100%
            height: 100%
            position: fixed
            filter: opacity(.08)
            pointer-events: none
            background-repeat: no-repeat
            background-attachment: fixed
            background-position: top left
            // background-image: url('~img/5c3a1aefbaf4e.png')

        &.md .container .content /deep/ .ui-card__footer .el-pagination
            justify-content: center

        .container
            height: 100%
            padding: 16px
            overflow-y: auto

            .content
                background-color: transparentize(#fff, .28) !important

                /deep/ .ui-card__header
                    display: flex
                    align-items: center

                    .el-input 
                        width: 300px

                    .el-button
                        margin: 0

                    > *:not(:last-child)
                        margin-right: 8px

                /deep/ > .ui-card__body
                    display: grid
                    grid-gap: 24px
                    grid-template-rows: auto auto auto
                    grid-template-columns: repeat(auto-fill, minmax(256px, 1fr))

                    .placeholder
                        grid-column: 1 / -1
                        grid-row-end: 3
                        display: flex
                        flex-direction: column
                        align-items: center

                        .image
                            width: 320px
                            margin-bottom: 16px

                        .title
                            font-size: 28px
                            font-weight: 800
                            color: var(--color-primary)

                        .description
                            font-size: 16px
                            color: var(--color-text-placeholder)

                    .ui-card
                        background-color: transparent

                /deep/ .ui-card__footer
                    .el-pagination
                        display: flex
                        align-items: center

                        /deep/ .el-pagination__sizes
                            flex: 1

        .ui-drawer
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
                i
                    padding-right: 0

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

                    /deep/ .ui-divider__content
                        left: 0
                        z-index: 1
                        padding-left: 0
                        font-size: 16px
                        font-weight: 700
                        color: var(--color-primary)
        .mobile-search-btn
            display: none
        .mobile-add-listing-btn
            display: none

    @media only screen and (max-width: 676px)
        .listing
            /deep/ .ui-heading__content__description
                display: none
            .filter-btn
                display: none
            .search-btn
                display: none
            .add-listing-btn
                display: none
            .mobile-search-btn
                display: block
            .mobile-add-listing-btn
                display: block
</style>