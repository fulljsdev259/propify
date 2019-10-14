<template>
    <div class="listings">
        <heading :title="$t('models.listing.title')" icon="icon-basket" shadow="heavy">
            <!--<el-button @click="add" icon="ti-plus" round type="primary">-->
            <!--{{$t('models.listing.add')}}-->
            <!--</el-button>-->
            <template v-if="$can($permissions.delete.listing)">
                <el-button :disabled="!selectedItems.length" @click="batchDeleteWithIds" icon="ti-trash" round size="mini"
                           type="danger">
                    {{$t('general.actions.delete')}}
                </el-button>
            </template>
        </heading>
        <list-table
            :fetchMore="fetchMore"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="header"
            :items="formattedItems"
            :loading="{state: loading}"
            :isLoadingFilters="{state: isLoadingFilters}"
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            @selectionChanged="selectionChanged"
        >
        </list-table>
        <el-dialog :title="$t('general.actions.view')" :visible.sync="listingDetailsVisible">
            <listing-details :listing="listing"></listing-details>
            <el-button @click="changeListingStatus(listing.id, listingConstants.published)"
                       type="success"
                       v-if="listing.status != listingConstants.published"
            >
                {{$t('models.listing.publish')}}
            </el-button>
            <el-button @click="changeListingStatus(listing.id, listingConstants.unpublished)"
                       type="danger"
                       v-else
            >
                {{$t('models.listing.unpublish')}}
            </el-button>
        </el-dialog>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import {mapActions} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import ListTableMixin from 'mixins/ListTableMixin';
    import ListingDetails from "components/ListingDetails";
    import getFilterQuarters from 'mixins/methods/getFilterQuarters';
    
    const mixin = ListTableMixin({
        actions: {
            get: 'getListings',
            delete: 'deleteListingWithIds'
        },
        getters: {
            items: 'listings',
            pagination: 'listingsMeta'
        }
    });

    export default {
        components: {
            Heading,
            ListingDetails
        },
        mixins: [mixin, getFilterQuarters],
        data() {
            return {
                header: [{
                    label: 'models.listing.listing_title',
                    prop: 'title'
                }, {
                    label: 'general.email',
                    prop: 'user.email'
                }, {
                    label: 'models.listing.type.label',
                    prop: 'formatted_type_label'
                }, {
                    label: 'models.listing.visibility.label',
                    prop: 'formatted_visibility_label'
                }, {
                    label: 'models.listing.status.label',
                    prop: 'formatted_status_label'
                }, {
                    // width: 170,
                    width: 120,
                    actions: [
                        // {
                        //     type: 'primary',
                        //     title: this.$t('general.actions.view'),
                        //     onClick: this.show,
                        //     permissions: [
                        //         this.$permissions.view.listing
                        //     ]
                        // }, 
                        {
                            type: '',
                            title: 'general.actions.edit',
                            onClick: this.edit,
                            editUrl: 'adminListingsEdit',
                            permissions: [
                                this.$permissions.update.listing
                            ]
                        }
                    ]
                }],
                listing: {},
                listingDetailsVisible: false,
                residents: {},
                quarters: {},
                isLoadingFilters: false,
            };
        },
        computed: {
            formattedItems() {
                const storeConstants = this.$constants.listings;
                return this.items.map((listing) => {
                    listing.formatted_status_label = this.$t(`models.listing.status.${storeConstants.status[listing.status]}`);
                    listing.formatted_visibility_label = this.$t(`models.listing.visibility.${storeConstants.visibility[listing.visibility]}`);
                    listing.formatted_type_label = this.$t(`models.listing.type.${storeConstants.type[listing.type]}`);
                    return listing
                });
            },
            filters() {
                return [
                    {
                        name: this.$t('general.placeholders.search'),
                        type: 'text',
                        icon: 'el-icon-search',
                        key: 'search'
                    },
                    {
                        name: this.$t('models.listing.status.label'),
                        type: 'select',
                        key: 'status',
                        data: this.prepareFilters("status"),
                    },
                    {
                        name: this.$t('models.listing.type.label'),
                        type: 'select',
                        key: 'type',
                        data: this.prepareFilters("type"),
                    },
                    {
                        name: this.$t('general.filters.quarters'),
                        type: 'select',
                        key: 'quarter_id',
                        data: this.quarters
                    },
                    {
                        name: this.$t('general.filters.resident'),
                        type: 'remote-select',
                        key: 'resident_id',
                        data: this.residents,
                        remoteLoading: false,
                        fetch: this.fetchRemoteResidents
                    }
                ];
            },
            listingConstants() {
                return this.$constants.listings;
            },

        },
        methods: {
            ...mapActions(['changeListingPublish', 'getBuildings', 'getResidents']),
            async getFilterBuildings() {
                this.loading = true;
                const buildings = await this.getBuildings({
                    get_all: true
                });
                this.loading = false;

                return buildings.data;
            },
            async fetchRemoteResidents(search) {
                const residents = await this.getResidents({get_all: true, search});

                return residents.data.map((resident) => {
                    return {
                        name: `${resident.first_name} ${resident.last_name}`,
                        id: resident.id
                    };
                });
            },
            add() {
                this.$router.push({
                    name: 'adminListingsAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminListingsEdit',
                    params: {
                        id
                    }
                });
            },
            show(listing) {
                this.listing = listing;
                this.listingDetailsVisible = true;
            },
            changeListingStatus(id, status) {
                this.changeListingPublish({id, status}).then((resp) => {
                    this.getListings();
                    this.listingDetailsVisible = false;
                    displaySuccess(resp);
                }).catch((error) => {
                    displayError(error);
                });
            },
            prepareFilters(property) {
                return Object.keys(this.listingConstants[property]).map((id) => {
                    return {
                        id: parseInt(id),
                        name: this.$t(`models.listing.${property}.${this.listingConstants[property][id].toLowerCase()}`)
                    };
                });
            },
        },
        async created(){
            this.isLoadingFilters = true;
            this.residents = await this.fetchRemoteResidents();
            const quarters = await this.axios.get('quarters')
            this.quarters = quarters.data.data.data;
            this.isLoadingFilters = false;
        }
    }
</script>
