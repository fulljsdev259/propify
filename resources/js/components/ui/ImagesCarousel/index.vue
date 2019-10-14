<template>
    <div class="ui-images-carousel" :style="{'height': `${this.height}px`}">
        <template v-if="images.length > 1">
            <div class="ui-images-carousel__btn ui-images-carousel__btn--prev icon-left" @click="prev"></div>
            <div class="ui-images-carousel__btn ui-images-carousel__btn--next icon-right" @click="next"></div>
        </template>
        <template v-if="images.length">
            <div :class="['ui-images-carousel__item', {'ui-images-carousel__item--active': index === activeIndex}]" v-for="(src, index) in images" :key="index" :style="{'transform': `translate3d(${getPosition(index) * 100}%, 0, 0)`}">
                <ui-image ref="ui-image" :src="src" :src-list="images" :show-delete-button="false" />
            </div>
            <div class="ui-images-carousel__indicators" v-if="showIndicator">
                <div :class="['ui-images-carousel__indicator', {'ui-images-carousel__indicator--active': index - 1 === activeIndex}]" v-for="index in images.length" :key="index" @click="activeIndex = index - 1"></div>
            </div>
            <!-- <div class="ui-images-carousel__actions">
                <div class="el-icon-zoom-in" @click="openViewer"></div>
            </div> -->
        </template>
        <ui-image v-else-if="!images.length && usePlaceholder" />
    </div>
</template>

<script>
    export default {
        name: 'ui-images-carousel',
        props: {
            images: {
                type: Array,
                default: () => ([])
            },
            height: {
                type: Number,
                default: 384
            },
            usePlaceholder: {
                type: Boolean,
                default: true
            },
            showIndicator: {
                type: Boolean,
                default: true
            }
        },
        data () {
            return {
                activeIndex: 0
            }
        },
        methods: {
            prev () {
                this.activeIndex += this.images.length - 1
                this.activeIndex %= this.images.length
            },
            next () {
                this.activeIndex++
                this.activeIndex %= this.images.length
            },
            openViewer () {
                this.$refs['ui-image'][this.activeIndex].openViewer()
            },
            getPosition (index) {
                if (index === this.activeIndex) {
                    return 0
                } else if (index === (this.activeIndex + 1) % this.images.length) {
                    return 1
                }

                return -1
            }
        }
    }
</script>

<style lang="sass">
    .ui-images-carousel
        display: flex
        align-items: center
        position: relative
        overflow: hidden

        > .ui-image
            width: 100%
            height: 100%

            /deep/ .ui-image__error img
                width: 128px

        &:hover
            .ui-images-carousel
                &__btn
                    margin: 0 16px

                &__actions
                    filter: opacity(1)

        &__btn
            z-index: 9
            color: #fff
            width: 40px
            height: 40px
            display: flex
            margin: 0 -40px
            cursor: pointer
            font-size: 18px
            border-radius: 50%
            position: absolute
            align-items: center
            filter: opacity(.64)
            justify-content: center
            transition: filter .32s, margin .32s
            background-color: transparentize(#000, .44)

            &:hover
                filter: opacity(1)

            &--prev
                left: 0

            &--next
                right: 0

        &__item
            position: absolute
            top: 0
            left: 0
            right: 0
            bottom: 0
            transition: .32s
            visibility: hidden

            &--active
                visibility: visible

            .ui-image
                height: 100%

        &__indicators
            position: absolute
            left: 0
            right: 0
            bottom: 0
            display: flex
            align-items: center
            justify-content: center
            margin: 16px
            z-index: 2

            .ui-images-carousel__indicator
                width: 10px
                height: 10px
                border-radius: 50%
                margin: 2px
                border: 1px transparentize(#fff, .44) solid
                filter: opacity(.64)
                transition: filter .32s
                cursor: pointer

                &--active, &:hover
                    background-color: #fff
                    filter: opacity(1)

        &__actions
            background-color: transparentize(#000, .5)
            position: absolute
            z-index: 1
            top: 0
            left: 0
            width: 100%
            height: 100%
            display: flex
            align-items: center
            justify-content: center
            filter: opacity(0)
            transition: filter .32s

            div
                color: #fff
                font-size: 24px
                filter: opacity(.64)
                transition: filter .32s
                cursor: pointer

                &:hover
                    filter: opacity(1)
</style>