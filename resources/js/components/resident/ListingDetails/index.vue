<template>
    <div class="listing-details">
        <ui-images-carousel :images="data.media.map(({url}) => url)" />
        <el-tabs value="overview" stretch>
            <el-tab-pane name="overview">
                <div slot="label">
                    <i class="icon-th"></i> {{$t('resident.overview')}}
                </div>
                <div class="type">{{this.$t(`models.listing.type.${$constants.listings.type[data.type]}`)}}</div>
                <div class="title">{{data.title}}</div>
                <div class="datetime">{{$t('resident.added_at')}} {{formatDatetime(data.published_at)}}</div>
                <ui-divider />
                <div class="price">
                    <template v-if="isFree">{{$t('resident.free')}}</template>
                    <template v-else>
                        {{$t('resident.price')}}
                        <div class="value">
                            {{data.price}}CHF
                        </div>
                    </template>
                </div>
                <ui-divider />
                <read-more class="description" :text="data.content" :max-chars="512" :more-str="$t('resident.read_more')" :less-str="$t('resident.read_less')" />
                <like :id="data.id" type="listing" />
                <ui-divider />
                <div class="contact" v-if="showContactInformations">
                    {{data.contact}}
                </div>
                <template v-else>
                    <el-button type="primary" round @click="showContactInformations = true">{{$t('resident.get_in_touch')}}</el-button>
                    <div class="hint">{{$t('resident.get_in_touch_info')}}</div>
                </template>
            </el-tab-pane>
            <el-tab-pane name="comments" lazy>
                <div slot="label">
                    <i class="icon-chat-empty"></i> {{$t('resident.comments')}}
                </div>
                <chat :id="data.id" type="listing" size="100%" max-size="512px" />
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
    import MediaGalleryCarousel from 'components/MediaGalleryCarousel'
    import FormatDateTimeMixin from 'mixins/formatDateTimeMixin'

    export default {
        mixins: [FormatDateTimeMixin],
        components: {
            MediaGalleryCarousel
        },
        props: {
            data: {
                type: Object,
                required: true
            }
        },
        data () {
            return {
                showContactInformations: false
            }
        },
        computed: {
            isFree () {
                return !+(this.data.price || '0.00').replace(/\D/g, '') || this.data.type == (Object.entries(this.$constants.listings.type).find(([_, name]) => name === 'giveaway') || [])[0]
            }
        }
    }
</script>

<style lang="sass" scoped>
    .listing-details
        display: grid
        grid-template-columns: 1fr minmax(auto, 384px)

        .ui-images-carousel
            height: 100% !important

        .el-tabs
            padding: 16px
            box-shadow: 0 24px 40px transparentize(#000, .68), 0 15px 12px transparentize(#000, .76)
            z-index: 1
            min-height: 450px
            
            /deep/ .el-tabs__content
                #pane-overview
                    .type
                        color: var(--color-primary)
                        font-size: 16px
                        font-weight: 500
                        text-transform: uppercase

                    .title
                        color: var(--color-primary)
                        font-size: 24px
                        font-weight: 800

                    .datetime
                        color: var(--color-text-placeholder)

                    .price
                        color: var(--color-primary)
                        font-size: 18px
                        font-weight: 600
                        text-transform: uppeercase

                        .value
                            font-size: 24px
                            font-weight: 700

                    .description
                        color: var(--color-text-secondary)

                    .contact
                        font-weight: 600
                        color: var(--color-text-secondary)
                        background-color: darken(#fff, 4%)
                        padding: 8px
                        border-radius: 6px
                        text-align: center

                    > .el-button
                        width: 100%

                    .hint
                        color: var(--color-text-placeholder)
                        padding: 8px
                        font-size: 12px
                        text-align: center
</style>