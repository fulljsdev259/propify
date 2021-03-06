<template>
    <div class="ui-media-gallery">
        <!-- <slot name="before" /> -->
        <div class="ui-media-gallery__item" v-for="(file, index) in files" :key="file">
        
            <div class="ui-media-gallery__item__content">
                <template v-if="isFileImage(file)">
                    <ui-image ref="ui-image" :src="file" :src-list="files" :index="index" @delete-image="deleteImage">
                        <div slot="error" class="error" style="color: red;">
                            <i class="el-icon-document-delete" />
                        </div>
                        <div slot="placeholder" class="placeholder el-icon-loading"></div>
                    </ui-image>
                </template>
                <template v-else-if="isFilePDF(file)">
                    <i class="ui-media-gallery__item__content__icon icon-file-pdf" />
                    <div class="file__actions">
                        <div class="el-icon-zoom-in" @click="openFile(file)"></div>
                        <div class="icon-trash-empty" @click="deleteImage(index)"></div>
                    </div>
                </template>
                <template v-else-if="isFileDoc(file)">
                    <i class="ui-media-gallery__item__content__icon icon-doc-text-1" />
                    <div class="file__actions">
                        <div class="el-icon-zoom-in" @click="openFile(file)"></div>
                        <div class="icon-trash-empty" @click="deleteImage(index)"></div>
                    </div>
                </template>
                <template v-else-if="isFileExcel(file)">
                    <i class="ui-media-gallery__item__content__icon icon-file-excel" />
                    <div class="file__actions">
                        <div class="el-icon-zoom-in" @click="openFile(file)"></div>
                        <div class="icon-trash-empty" @click="deleteImage(index)"></div>
                    </div>
                </template>
                <template v-else>
                    <i class="ui-media-gallery__item__content__icon ti-file" />
                </template>
            </div>
        </div>
        <div class="ui-media-gallery__placeholder" v-if="!files.length && usePlaceholder">
            <img class="ui-media-gallery__placeholder__image" :src="require('img/5c98a90bb5c05.png')" />
            <div class="ui-media-gallery__placeholder__title">
                {{$t('resident.no_data.media')}}
            </div>
            <div class="ui-media-gallery__placeholder__description" v-if="showDescription">
                {{$t('resident.media_info')}}
            </div>
        </div>
        <slot name="after" />
    </div>
</template>

<script>
    export default {
        name: 'ui-media-gallery',
        props: {
            files: {
                type: Array,
                default: () => ([])
            },
            usePlaceholder: {
                type: Boolean,
                default: true
            },
            showDescription: {
                type: Boolean,
                default: true
            }
        },
        methods: {
            isFileImage (file) {

                const extension = file.split('.').pop()

                return ['jpg', 'jpeg', 'gif', 'bmp', 'png'].includes(extension)
            },
            isFilePDF (file) {
                const extension = file.split('.').pop()

                return ['pdf'].includes(extension)
            },
            isFileDoc (file) {
                const extension = file.split('.').pop()

                return ['doc', 'docx'].includes(extension)
            },
            isFileExcel (file) {
                const extension = file.split('.').pop()

                return ['xls', 'xlsx'].includes(extension)
            },
            openViewer (index) {
                this.$refs['ui-image'][index].openViewer()
            },
            openFile (file) {
                window.open(file, "_blank")
            },
            deleteImage (index) {
                this.$emit('delete-media', index)
            }
        },
    }
</script>

<style lang="sass">
    .ui-media-gallery
        display: grid
        grid-gap: 8px
        grid-template-columns: repeat(auto-fill, minmax(112px, 1fr))
        grid-auto-rows: min-content
        box-sizing: border-box

        &__item
            position: relative
            padding-top: 100%
            overflow: hidden
            border-radius: 6px
            border: 1px var(--border-color-base) solid
            box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76)

            &__content
                position: absolute
                top: 0
                right: 0
                bottom: 0
                left: 0
                width: 100%
                height: 100%
                display: flex
                flex-direction: column
                align-items: center
                justify-content: center

                .ui-image
                    width: 100%
                    height: 100%

                .file__actions
                    background-color: rgba(0,0,0,0.5)
                    position: absolute
                    width: 100%
                    height: 100%
                    display: flex
                    align-items: center
                    justify-content: center
                    transition: filter .32s, -webkit-filter .32s
                    filter: opacity(0);

                    &:hover
                        filter: opacity(1)

                    div
                        color: #fff
                        font-size: 18px
                        margin: 4px
                        -webkit-filter: opacity(0.64)
                        filter: opacity(0.64)
                        -webkit-transition: -webkit-filter .32s
                        transition: -webkit-filter .32s
                        transition: filter .32s
                        transition: filter .32s, -webkit-filter .32s
                        cursor: pointer
                        filter: opacity(0.64);

                        &:hover
                            filter: opacity(1)
                    
                    .icon-trash-empty
                        color: var(--color-danger)
                        border-color: #fbc4c4

                        &:hover
                            color: white

        &__placeholder
            grid-column: 1 / -1
            display: flex
            text-align: center
            align-items: center
            flex-direction: column
            justify-content: center
            width: 100%

            &__image
                width: 128px

            &__title
                font-size: 20px
                font-weight: 800
                color: var(--color-primary)

            &__description
                font-size: 14px
                font-weight: 600
                word-break: break-word
                color: var(--color-text-placeholder)
                
</style>