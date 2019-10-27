<template>
    <div class="latest-listings dashboard-table">
        <div class="link-container">
            <router-link :to="{name: 'adminListings'}">
                <span class="title">{{ $t('dashboard.listing.go_to_listing') }} </span>
                <i class="icon-right icon"/>
            </router-link>
        </div>
        <list-table
            :header="header"
            :items="latestListings"
            :loading="{state: loading}"
            :withSearch="false"
            :withCheckSelection="false"
            @selectionChanged="selectionChanged"
        >
        </list-table>
    </div>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import DashboardListMixin from 'mixins/DashboardListMixin';
    
    const mixin = DashboardListMixin();

    export default {
        mixins: [mixin],
        props: {
          type: {
            type: String
          },
        },
        data() {
            return {
                header: [{
                    type: 'listing-details',
                    label: 'general.actions.view',
                    props: ['title', 'created_at', 'image_url'],
                    minWidth: '300px'
                }, {
                    type: 'tag',
                    minWidth: '100px',
                    label: 'models.listing.type.label',
                    prop: 'type_label',
                    classSuffix: 'type'
                }, {
                    type: 'plain',
                    label: 'models.listing.visibility.label',
                    prop: 'visibility_label'
                }, {
                    type: 'plain',
                    label: 'models.listing.price',
                    prop: 'price',
                    style: {
                        color: '#5CC279'
                    }
                }, {
                    type: 'actions',
                    label: 'dashboard.actions',
                    width: '130px',
                    actions: [ 
                        {
                            type: 'default',
                            title: 'general.actions.edit',
                            editUrl: 'adminListingsEdit',
                            permissions: [
                                this.$permissions.update.listing
                            ]
                        }
                    ]
                }],
                listing: {},
            };
        },
        computed: {
            ...mapGetters(['latestListings']),
        },
        methods: {
            ...mapActions(['getListings']),
            async fetchData() {
                const listings = await this.getListings({
                    per_page: 5
                });
                this.items = listings.data.data
            },
         },
        created() {
            this.fetchData();
        }
    }
</script>
