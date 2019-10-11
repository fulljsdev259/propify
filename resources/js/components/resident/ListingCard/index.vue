<template>
    <ui-card shadow="hover" v-on="$listeners">
        <div class="media">
            <ui-image :src="(data.media[0] || {}).url" />
            <el-tooltip :content="$t('resident.edit_listing')" v-if="showAction && data.user_id == $store.getters.loggedInUser.id">
                <div class="edit-btn" @click.stop="$emit('edit-listing', $event, data)"><i class="ti-pencil"></i></div>
            </el-tooltip>
            <!-- <ui-image src="https://placeimg.com/640/480/any" /> -->
        </div>
        <div :class="['price', {'free': isFree}]">
            <template v-if="isFree">{{$t('resident.free')}}</template>
            <template v-else>{{data.price | price}}</template>
        </div>
        <div class="content">
            <div class="title">{{data.title}}</div>
            <div class="date">{{$t('resident.added_at')}} {{formatDatetime(data.published_at)}}</div>
            <likes type="listing" :data="data.likes" v-if="false"/>
            <like :id="data.id" type="listing" readonly>
                <div>
                    <i class="icon-picture"></i> {{data.media.length}}
                </div>
                <!-- <el-button size="mini" @click.stop="$emit('delete-listing', $event, data)" type="danger">
                    <i class="ti-close"></i>
                </el-button> -->
            </like>
            
        </div>
    </ui-card>
</template>

<script>
    import PriceFormatMixin from 'mixins/priceFormatMixin'
    import FormatDateTimeMixin from 'mixins/formatDateTimeMixin'

    export default {
        name: 'p-listing-card',
        mixins: [
            PriceFormatMixin,
            FormatDateTimeMixin
        ],
        props: {
            data: {
                type: Object,
                required: true
            },
            showAction: {
                type: Boolean,
                default: true
            }
        },
        computed: {
            isFree () {
                return !+(this.data.price || '0.00').replace(/\D/g, '') || this.data.type == (Object.values(this.$constants.listings.type).find(name => name === 'giveaway') || [])[0]
            }
        }
    }
</script>

<style lang="sass" scoped>
    .ui-card
        overflow: visible

        /deep/ .ui-card__body
            padding: 0
            position: relative

            .media
                width: 100%
                overflow: hidden
                padding-top: 100%
                position: relative
                border-radius: 12px
                // margin: -8px -8px 0 -8px
                // border: 1px var(--border-color-base) solid
                box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76)

                .ui-image
                    top: 0
                    left: 0
                    right: 0
                    bottom: 0
                    position: absolute
                    border-radius: 12px

                    &__error img
                        width: 128px

                    /deep/ .ui-image__inner
                        filter: brightness(.8)
                        transition: filter .48s

                .edit-btn
                    position: absolute
                    right: 20px
                    top: 20px
                    width: 40px
                    height: 40px
                    border-radius: 50%
                    background: var(--primary-color-lighter)
                    display: flex
                    justify-content: center
                    align-items: center
                    color: var(--primary-color)
                    display: none
                &:hover
                    .edit-btn
                        display: flex


            .price
                color: #fff
                font-size: 15px
                font-weight: 500
                padding: 4px 8px
                margin-top: -36px
                position: absolute
                border-radius: 12px
                text-transform: uppercase
                background-color: transparentize(#000, .24)
                box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76)
                left: 6px;

                &.free
                    background-color: var(--color-primary)

            .content
                padding: 12px
                line-height: 1.56

                .title
                    flex: 1
                    font-size: 16px
                    font-weight: 600
                    overflow: hidden
                    white-space: nowrap
                    text-overflow: ellipsis
                    color: var(--color-primary)

                .date
                    font-size: 13px
                    font-weight: 300
                    color: var(--color-text-placeholder)

            .like 
                /deep/ .el-button
                    margin-left: 10px
                    padding: 2px;
        &:hover
            cursor: pointer

            /deep/ .ui-card__body .media .ui-image /deep/ .ui-image__inner
                filter: brightness(1)
</style>

