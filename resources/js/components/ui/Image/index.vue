<template>
    <div class="ui-image">
        <slot name="placeholder" v-if="loading">
            <div class="ui-image__placeholder">
                {{$t('general.loading')}}
            </div>
        </slot>
        <slot name="error" v-else-if="error">
            <div class="ui-image__error">
                <img :src="require('img/5c98a90bb5c05.png')" width="56" />
            </div>
        </slot>
        <transition name="fade" appear mode="out-in">
            <img :src="src" class="ui-image__inner" v-bind="$attrs" v-on="$listeners" v-show="!error && !loading" />
        </transition>
        <div class="ui-image__actions" v-if="srcList.length">
            <slot name="actions">
                <div class="el-icon-zoom-in" @click="openViewer"></div>
                <div class="icon-trash-empty" @click="$emit('delete-image', index)"></div>
            </slot>
        </div>
        <ui-viewer :current-image="src" :images="srcList" :z-index="zIndex" v-if="showViewer && srcList.length" @close="closeViewer" />
    </div>
</template>

<script>
    import Viewer from './Viewer'

    export default {
        name: 'ui-image',
        components: {
            'ui-viewer': Viewer
        },
        props: {
            src: String,
            lazy: Boolean,
            srcList: {
                type: Array,
                default: () => []
            },
            autoPreview: {
                type: Boolean,
                default: true
            },
            zIndex: {
                type: Number,
                default: 999999
            },
            index: {
                type: Number
            }
        },
        data () {
            return {
                error: false,
                loading: true,
                showViewer: false
            }
        },
        methods: {
            openViewer () {
                this.showViewer = true
            },
            closeViewer () {
                this.showViewer = false
            },
            onClickHandler () {
                if (this.autoPreview) {
                    this.openViewer()
                }
            },
            handleImageLoad () {
                this.loading = true
                this.error = false

                const image = new Image()

                image.onload = () => {
                    this.imageWidth = this.width
                    this.imageHeight = this.height

                    // setTimeout(() => this.loading = false, 99999)
                    this.loading = false
                }

                image.onerror = () => {
                    this.error = true
                    this.loading = false
                }

                image.src = this.src
            }
        },
        watch: {
            src: {
                immediate: true,
                handler () {
                    this.handleImageLoad()
                }
            }
        }
    }
</script>

<style lang="sass">
    .ui-image
        display: flex
        overflow: hidden
        position: relative

        &:hover .ui-image__actions
            filter: opacity(1)

        &__error,
        &__placeholder,
        &__inner
            width: 100%
            height: 100%

        &__error, &__placeholder
            display: flex
            align-items: center
            flex-direction: column
            justify-content: center
            background-color: darken(#fff, 1%)

        &__inner
            object-fit: cover

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
                font-size: 18px
                margin: 4px
                filter: opacity(.64)
                transition: filter .32s
                cursor: pointer

                &:hover
                    filter: opacity(1)

            .icon-trash-empty
                color: var(--color-danger)
                border-color: #fbc4c4
                &:hover
                    color: white

        .el-icon-loading
            height: 15px
            position: absolute
            top: 40%
            left: 45%
</style>