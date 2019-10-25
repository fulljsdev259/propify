<template>
    <div class="pinboard">
        <heading icon="ti-announcement" :title="$t('resident.pinboard')" :description="$t('resident.heading_info.pinboard')"/>
        <el-row :gutter="16">
            <el-col :xl="11" :lg="11" :md="12" :sm="12" :xs="24">
                <pinboard-card :data="data" v-if="data" :show-actions="false"/>
            </el-col>
            <el-col :xl="8" :lg="8" :md="12" :sm="12" :xs="24">
                <rss-feed title="Blick.ch Pinboard"/>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import PinboardCard from 'components/resident/PinboardCard'
    import Heading from 'components/Heading'
    import RssFeed from 'components/resident/RSSFeed'
    import {displaySuccess, displayError} from 'helpers/messages'

    export default {
        components: {
            PinboardCard,
            Heading,
            RssFeed
        },
        data () {
            return {
                data: null
            }
        },
        async created () {
            try {
                const id = Number(this.$route.params.id)
                
                await this.$store.dispatch('newPinboard/getById', {id})
                this.data = this.$store.getters['newPinboard/getById'](id)

            } catch (err) {
                this.$router.replace({name: 'residentPinboards'})
            }
        }
    }
</script>

<style lang="scss" scoped>
    .pinboard {
        :global(.heading) {
            margin-bottom: 1em;
        }
        @media screen and (min-width: 667px) {
            .el-col-xs-24 {
                width:50% !important;
            }
        }
        @media screen and (max-width: 666px) {
            .el-col:nth-of-type(2) {
                margin-top: 16px;
            }
        }
    }
</style>