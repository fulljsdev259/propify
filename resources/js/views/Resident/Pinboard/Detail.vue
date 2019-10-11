<template>
    <div class="pinboard">
        <heading icon="ti-announcement" :title="$t('resident.pinboard')" :description="$t('resident.heading_info.pinboard')"/>
        <el-row :gutter="24">
            <el-col :span="16">
                <pinboard-card :data="data" v-if="data" :show-actions="false"/>
            </el-col>
            <el-col :span="8">
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
        .el-row {
            .el-col {
                &:nth-child(1) {
                    max-width: 640px;
                }
                &:nth-child(2) {
                    max-width: 448px;
                }
            }
        }
    }
</style>