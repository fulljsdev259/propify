<template>
    <div class="ui-media-uploader">
        <uploader ref="uploader" v-bind="uploaderProps" :value="value" :input-id="`uploader-${$_uid}`" :headers="headers" :custom-action="customAction" @input="value => $emit('input', value)" @input-filter="onUploadFilter" style="display: block" />
        <draggable class="ui-media-uploader__list" tag="transition-group" :componentData="{type: 'transition', name: 'flip-list', mode: 'out-in'}" ghost-class="is-ghost" :list="value" :animation="240" :disabled="isDraggableDisabled" :move="onDraggableMove">
            <div :class="['ui-media-uploader__item', {'ui-media-uploader__item--is-draggable': isDraggable, 'ui-media-uploader__item--is-active': +file.progress && !file.success, 'ui-media-uploader__item--is-pending': !+file.progress, 'ui-media-uploader__item--is-success': file.success, 'ui-media-uploader__item--is-error': file.error}]" v-for="(file, index) in value" :key="file.id">
                <div class="ui-media-uploader__item__content">
                    <template v-if="isFileImage(file)">
                        <ui-image ref="ui-image" :src="file.file.blob" :src-list="images">
                            <div slot="error" class="error" style="color: red;">
                                <i class="el-icon-document-delete" />
                            </div>
                            <div slot="placeholder" class="placeholder el-icon-loading"></div>
                            <template #actions v-if="!file.active">
                                <div class="el-icon-zoom-in" @click="$refs['ui-image'][index] && $refs['ui-image'][index].openViewer"></div>
                                <div class="el-icon-delete" @click="$refs.uploader.remove(file)" style="color: red"></div>
                            </template>
                        </ui-image>
                    </template>
                    <template v-else>
                        <i class="ui-media-uploader__item__icon ti-file"></i>
                    </template>
                    <div class="ui-media-uploader__item__progress" v-if="file.active || file.success">
                        <el-progress :width="80" type="circle" key="progress" :stroke-width="3" :percentage="+file.progress" :status="file.success ? 'success' : file.error ? 'exception' : undefined" />
                    </div>
                </div>
            </div>
            <div class="ui-media-uploader__item ui-media-uploader__item--button" key="button" @click="selectFiles()">
                <div class="ui-media-uploader__item__content">
                    <i class="icon-plus"></i>
                </div>
            </div>
        </draggable>
        <!-- <el-button type="primary" round @click="$refs.uploader.active = true">Upload</el-button> -->
        <el-button class="ui-media-uploader__btn" icon="el-icon-upload" type="primary" round @click="startUploading()">{{$t('resident.actions.upload')}}</el-button>
        <div v-if="$refs.uploader && $refs.uploader.dropActive" class="ui-media-uploader--drop--active">
            <i class="icon-upload-cloud"></i>
            Drop your files here..
            <div class="description">
                Only the files with a certain extension are allowed.
            </div>
        </div>
    </div>
</template>

<script>
    import {API_BASE_URL} from 'config'
    // TODO: do not rely on 3rd party anymore
    import Draggable from 'vuedraggable'
    import Uploader from 'vue-upload-component'

    export default {
        name: 'ui-media-uploader',
        components: {
            Uploader,
            Draggable
        },
        props: {
            value: Array,
            id: {
                type: Number
            },
            headers: {
                type: Object,
                default: () => ({
                    'Accept': 'application/json, text/plain, */*',
                    'Content-Type': 'application/json;charset=UTF-8'
                })
            },
            action: {
                type: String,
                required: true
            },
            options: {
                type: Object,
                default: () => ({
                    drop: true,
                    draggable: true,
                    autoUpload: false
                })
            },
            type : {
                type: String,
                default: "request"
            }
        },
        data () {
            return {
                length: 0,
                uploaded_count: 0
            }
        },
        computed: {
            images () {
                return this.value.reduce((images, file) => {
                    if (this.isFileImage(file)) {
                        images.push(file.file.blob)

                        return images
                    }
                }, [])
            },
            uploaderProps () {
                const {auto, draggable, ...restProps} = this.options

                return restProps
            },
            isDraggable () {
                return this.value.length && this.options.draggable && !this.$refs.uploader.uploaded
            },
            isDraggableDisabled () {
                return !this.options.draggable || this.value.length && this.$refs.uploader.uploaded
            }
        },
        methods: {
            onUploadFilter (newFile, oldFile, prevent) {
                if (newFile) {
                    if (this.options.size) {
                        if (this.options.size < newFile.size) {
                            this.$message({
                                type: 'warning',
                                message: `Oops! Some files had the size bigger than the maximum allowed of ${$options.filters.formatBytes(this.options.size)}.`,
                                duration: 8000,
                                showClose: true
                            })

                            return prevent()
                        }
                    }

                    if (this.options.extensions) {
                        const fileExtension = newFile.type.substring(newFile.type.lastIndexOf('/') + 1)

                        this.$message.closeAll()

                        switch (this.options.extensions.constructor) {
                            case String:
                                if (!this.options.extensions.split(',').includes(fileExtension)) {
                                    this.$message({
                                        type: 'warning',
                                        messages: 'Oops! Skipping the files with the invalid extension.',
                                        duration: 8000,
                                        showClose: true
                                    })

                                    return prevent()
                                }

                                break
                            case Array:
                                if (!this.options.extensions.includes(fileExtension)) {
                                    this.$message({
                                        type: 'warning',
                                        messages: 'Oops! Skipping the files with the invalid extension.',
                                        duration: 8000,
                                        showClose: true
                                    })

                                    return prevent()
                                }

                                break
                            case RegExp:
                                if (!this.options.extensions.test(fileExtension)) {
                                    this.$message({
                                        type: 'warning',
                                        messages: 'Oops! Skipping the files with the invalid extension.',
                                        duration: 8000,
                                        showClose: true
                                    })

                                    return prevent()
                                }

                                break
                        }
                    }

                    const fileReader = new FileReader()

                    fileReader.readAsDataURL(newFile.file)
                    fileReader.onload = () => newFile.file.src = /,(.+)/.exec(fileReader.result)[1]

                    newFile.file.blob = URL.createObjectURL(newFile.file)
                }
            },
            onDraggableMove ({draggedContext}) {
                if (!draggedContext.element) {
                    return false
                }
            },
            isFileImage ({type}) {
                return type.includes('image/')
            },
            selectFiles () {
                document.getElementById(`uploader-${this.$_uid}`).click()
            },
            async customAction (file) {
                const xhr = new XMLHttpRequest()

                xhr.open('POST', this.action)
                xhr.onreadystatechange = () => {
                    switch (xhr.readyState) {
                        case 4:
                            const {message} = JSON.parse(xhr.response)

                            if (xhr.status === 200) {
                                this.$notify.success({
                                    message,
                                    offset: 64
                                })
                            } else {
                                this.$notify.error({
                                    message,
                                    offset: 64,
                                    title: 'Oops!'
                                })
                            }

                            break
                    }
                }

                return this.$refs.uploader.uploadXhr(xhr, file, JSON.stringify({
                    media: file.file.src
                }))
                .then(data => {
                    
                    this.$store.dispatch('newRequests/addMedia', {
                        id : this.id, media: data.response.data
                    })  
                    this.uploaded_count ++;
                    if(this.uploaded_count == this.length) {
                        this.clearUploader();
                    }
                })
                .catch(error => console.log(error));
            },
            startUploading () {
                this.$refs.uploader.active = true
                this.length = this.value.length
            },
            clearUploader () {
                this.$refs.uploader.clear()
                this.uploaded_count = 0
                this.length = 0
            }
        }
    }
</script>

<style lang="sass">
    .ui-media-uploader
        display: flex
        flex-direction: column
        min-height: 190px

        &__list
            flex-grow: 1
            display: grid
            grid-gap: 8px
            grid-template-columns: repeat(auto-fill, minmax(112px, 1fr))
            grid-auto-rows: min-content

            & + .el-button
                width: 100%
                bottom: 10px

        &__item
            position: relative
            padding-top: 100%
            overflow: hidden
            border-radius: 6px
            border: 1px var(--border-color-base) solid
            box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76)

            &--is-draggable
                cursor: move

            &--button
                font-weight: 600
                box-shadow: none
                border-width: 2px
                border-style: dashed
                color: var(--color-text-placeholder)
                cursor: pointer

                &:hover
                    color: var(--color-primary)
                    background-color: var(--primary-color-lighter)
                    border-color: var(--color-primary)

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

            &__progress
                background-color: transparentize(#fff, .08)
                position: absolute
                top: 0
                left: 0
                width: 100%
                height: 100%
                display: flex
                align-items: center
                justify-content: center
          
</style>