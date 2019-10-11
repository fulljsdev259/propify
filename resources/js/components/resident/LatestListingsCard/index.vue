<template>
    <ui-card class="p-latest-listings-card" shadow="always">
        <template #header>
            <i class="icon-basket"></i> {{$t('resident.latest_listings')}}
            <el-button type="text" @click="$router.push({name: 'residentListing'})">{{$t('resident.actions.view_all')}}</el-button>
        </template>
        <div v-if="loading">{{$t('general.loading')}}</div>
        <div class="placeholder" v-else-if="!loading && !listings">
            <img class="image" :src="require('img/5c9d48f15dd1a.png')" />
            <div class="content">
                <div class="title">No requests available yet.</div>
                <div class="description">Et aut cum ut earum. Et aperiam ut possimus explicabo. Modi dolores in odit id fuga maxime aperiam dolor.</div>
            </div>
        </div>
        <listing-card v-for="listing in listings" :key="listing.id" :data="listing" v-else :show-action="false"/>
    </ui-card>
</template>

<script>
    import {mapState} from 'vuex'

    export default {
        name: 'p-latest-listings-card',
        props: {
            limit: {
                type: Number,
                default: 4
            }
        },
        data () {
            return {
                loading: false
            }
        },
        computed: {
            ...mapState('newListings', {
                listings: ({data}) => data.slice(this.limit)
            })
        },
        async mounted () {
            //if (!this.listings.length) {
                this.loading = true

                await this.$store.dispatch('newListings/reset');
                await this.$store.dispatch('newListings/get', {
                    sortedBy: 'desc',
                    orderBy: 'created_at',
                    per_page: this.limit
                })
                this.loading = false
            //}
        }
    }
</script>

<style lang="sass" scoped>
    .p-latest-listings-card
        /deep/ .ui-card__header
            .el-button
                padding: 0
                margin-left: auto

                /deep/ [class*=icon] + span
                    margin-left: 5px

        > /deep/ .ui-card__body
            display: inline-block
            width: 100%

            .ui-card
                width: calc(50% - 30px)
                display: inline-block
                margin-bottom: 24px

                &:nth-child(odd)
                    margin-right: 24px

                &:nth-last-child(2), &:nth-last-child(1)
                    margin-bottom: 0
</style>