<template>
    <div :class="['media', {[`media-${layout}-layout`]: true}]">
        <uploader ref="uploader" class="media-uploader" v-bind="uploaderProps" :value="value" :input-id="`upload-${$_uid}`" :headers="headers" :custom-action="customAction" @input="value => $emit('input', value)" @input-filter="onUploadFilter" />
        <gallery :images="galleryImages" :index="galleryIndex" :options="galleryOptions" />
        <draggable class="media-list" tag="transition-group" :componentData="{type: 'transition', name: 'flip-list', mode: 'out-in'}" ghost-class="is-ghost" :list="value" :handle="draggableHandler" :animation="240" :disabled="isDraggableDisabled" :move="onDraggableMove">
            <div :class="['media-item', {'is-draggable': uploadOptions.draggable && value.length && !$refs.uploader.uploaded}, $refs.uploader.active && {'is-active': +file.progress && !file.success, 'is-pending': !+file.progress}, {'is-success': file.success, 'is-failed': file.error}]" v-for="(file, idx) in value" :key="file.id" :style="{'transition-delay': `calc(0.16 * ${idx}s)`}">
                <div class="media-content">
                    <div class="icon-menu media-draggable-handler" v-if="canShowListDraggableHandler(file)"></div>
                    <ui-image class="media-image" :src="file.file.blob" :alt="file.name" v-if="isFileImage(file)" @click="isListLayout ? previewFile(file, idx) : undefined" v-once>
                        <!-- <div slot="error" style="color: red;">
                            <div class="icon-file-image"></div>
                        </div>
                        <div slot="placeholder" class="el-icon-loading"></div> -->
                    </ui-image>
                    <!-- <el-image class="media-image" :src="file.file.blob" :alt="file.name" v-if="isFileImage(file)" @click="isListLayout ? previewFile(file, idx) : undefined" v-once>
                        <div slot="error" style="color: red;">
                            <div class="icon-file-image"></div>
                        </div>
                        <div slot="placeholder" class="el-icon-loading"></div>
                    </el-image> -->
                    <div class="media-icon icon-doc" @click="previewFile(file, idx)" v-else></div>
                    <div class="media-filename" v-if="isListLayout || !isFileImage(file)">
                        {{file.name}}
                        <div class="media-filesize">
                            {{file.size | formatBytes}}
                        </div>
                    </div>
                    <transition-group class="media-progress" tag="div" name="fade" v-if="canShowProgress(file)">
                        <el-progress :width="80" :type="progressType" key="progress" :stroke-width="3" :percentage="+file.progress" :status="getProgressStatus(file)" />
                        <div key="speed" class="media-progress-speed" v-if="canShowProgressSpeed(file)">
                            {{file.speed | formatBytes}}/s
                        </div>
                    </transition-group>
                    <div class="media-actions" v-if="!file.active">
                        <el-button circle plain icon="el-icon-zoom-in" size="mini" @click="previewFile(file, idx)" v-if="isGridLayout && canFileBePreviewed(file)" />
                        <el-button circle plain type="danger" icon="el-icon-delete" size="mini" @click="removeFile(file)" v-if="!file.success" />
                    </div>
                </div>
            </div>
            <el-button-group slot="footer" key="footer" v-if="canShowButtonGroup">
                <el-button class="media-trigger" icon="icon-plus" @click="selectFiles()" v-if="!uploadOptions.hideSelectFilesButton">
                    <template v-if="uploadOptions.drop">
                        {{$t('general.components.common.media.buttons.select_files.with_drop')}}
                    </template>
                    <template v-else>
                        {{$t('general.components.common.media.buttons.select_files.without_drop')}}
                    </template>
                </el-button>
                <el-button type="primary" icon="icon-upload-cloud" @click="startUploading()" v-if="canShowUploadButton">
                    {{$t('general.components.common.media.buttons.upload')}}
                </el-button>
            </el-button-group>
            <template slot="footer" v-else-if="isGridLayout">
                <el-button key="media-trigger" class="media-upload-trigger" @click="selectFiles()" v-if="!uploadOptions.hideSelectFilesButton">
                    <div class="icon-plus"></div>
                    <!-- <template v-if="uploadOptions.drop">
                        {{$t('general.components.common.media.buttons.select_files.with_drop')}}
                    </template>
                    <template v-else>
                        {{$t('general.components.common.media.buttons.select_files.without_drop')}}
                    </template> -->
                </el-button>
                <el-button key="media-upload" type="primary" icon="icon-upload-cloud" @click="startUploading()" v-if="canShowUploadButton">
                    {{$t('general.components.common.media.buttons.upload')}}
                </el-button>
            </template>
        </draggable>
        <div v-if="$refs.uploader && $refs.uploader.dropActive" class="media-drop-active">
            <i class="icon-upload-cloud"></i>
            {{$t('general.components.common.media.drop_active.title')}}
            <div class="description">
                {{$t('general.components.common.media.drop_active.description')}}
            </div>
        </div>
    </div>
</template>

<script>
    import {API_BASE_URL} from '@/config'
    import Gallery from './../../MediaGallery'
    import Draggable from 'vuedraggable'
    import Uploader from 'vue-upload-component'
    import {displaySuccess, displayError} from 'helpers/messages'
    import store from 'store'

    export default {
        props: {
            value: Array,
            id: {
                type: Number
            },
            audit_id: {
                type: Number
            },
            type: {
                type: String,
                validator: type => ['pinboard', 'requests', 'listings'].includes(type),
                required: true
            },
            layout: {
                type: String,
                default: 'grid',
                validator: layout => ['grid', 'list'].includes(layout)
            },
            galleryOptions: {
                type: Object,
                default: () => ({})
            },
            uploadOptions: {
                type: Object,
                default: () => ({
                    drop: true,
                    auto: false,
                    clear: false,
                    draggable: true,
                    hideSelectFilesButton: false,
                    hideUploadButton: false
                })
            }
        },
        components: {
            Gallery,
            Uploader,
            Draggable
        },
        filters: {
            formatBytes (bytes) {
                const i = Math.floor(Math.log(bytes) / Math.log(1024))
                const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']

                return (bytes / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + sizes[i]
            }
        },
        data () {
            return {
                galleryIndex: null,
                length: 0,
                uploaded_count: 0
            }
        },
        methods: {
            selectFiles () {
                document.getElementById(`upload-${this.$_uid}`).click()
            },
            isFileImage ({type}) {
                return type.includes('image/')
            },
            previewFile (file, idx) {
                console.log('prevFile called')
                if (this.isFileImage(file)) {
                    this.galleryIndex = idx
                    console.log('is image file')
                } else if (this.canFileBePreviewed(file)) {
                    window.open(file.file.blob)
                    console.log('can file be previewed')
                } else {
                    this.$message.warning(this.$t('general.components.common.media.messages.preview'), {
                        duration: 2400
                    })
                }
            },
            canFileBePreviewed (file) {
                return this.isFileImage(file) || ['application/pdf'].includes(file.type)
            },
            onUploadFilter (newFile, oldFile, prevent) {
                if (newFile) {
                    if (this.uploadOptions.size) {
                        if (this.uploadOptions.size < newFile.size) {
                            this.$message({
                                type: 'warning',
                                message: this.$t('general.components.common.media.messages.size', {
                                    bytes: this.$options.filters.formatBytes(this.uploadOptions.size)
                                }),
                                duration: 8000,
                                showClose: true
                            })

                            return prevent()
                        }
                    }

                    if (this.uploadOptions.extensions) {
                        const fileExtension = newFile.type.substring(newFile.type.lastIndexOf('/') + 1)

                        this.$message.closeAll()
                        switch (this.uploadOptions.extensions.constructor) {
                            case String:
                                if (!this.uploadOptions.extensions.split(',').includes(fileExtension)) {
                                    this.$message({
                                        type: 'warning',
                                        message: this.$t('general.components.common.media.messages.extensions'),
                                        duration: 8000,
                                        showClose: true
                                    })

                                    return prevent()
                                }

                                break
                            case Array:
                                if (!this.uploadOptions.extensions.includes(fileExtension)) {
                                    this.$message({
                                        type: 'warning',
                                        message: this.$t('general.components.common.media.messages.extensions'),
                                        duration: 8000,
                                        showClose: true
                                    })

                                    return prevent()
                                }

                                break
                            case RegExp:
                                if (!this.uploadOptions.extensions.test(fileExtension)) {
                                    this.$message({
                                        type: 'warning',
                                        message: this.$t('general.components.common.media.messages.extensions'),
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
            clearUploader () {
                this.$refs.uploader.clear()
                this.uploaded_count = 0
                this.length = 0
            },
            async customAction (file) {
                const xhr = new XMLHttpRequest()

                xhr.open('POST', `${API_BASE_URL}/${this.type}/${this.id}/media`.replace(/([^:]\/)\/+/g, "$1"))
                xhr.onreadystatechange = () => {
                    switch (xhr.readyState) {
                        case 4:
                            const {message} = JSON.parse(xhr.response)

                            if (xhr.status === 200) {
                                // this.$notify.success({
                                //     message,
                                //     offset: 64
                                // })
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
                    media: file.file.src,
                    merge_in_audit: this.audit_id
                }))
                .then(data => {
                    if(this.type == "pinboard") {
                        this.$store.dispatch('newPinboard/addMedia', {
                            id : this.id, media: data.response.data
                        })  
                    }
                    else if(this.type == "requests") {
                        this.$store.dispatch('newRequests/addMedia', {
                            id : this.id, media: data.response.data
                        })  
                    }
                    else if(this.type == "listings") {
                        this.$store.dispatch('newListings/addMedia', {
                            id : this.id, media: data.response.data
                        })  
                    }

                    this.uploaded_count ++;
                    if(this.uploaded_count == this.length) {
                        this.$root.$emit('media-upload-finished');
                        this.clearUploader();
                    }
                })
                .catch(error => console.log(error));
            },
            getProgressStatus ({success, error}) {
                return success ? 'success' : error ? 'exception' : undefined
            },
            startUploading () {
                this.$refs.uploader.active = true
                this.length = this.value.length
            },
            canShowProgress ({active, success}) {
                return active || success
            },
            removeFile (file) {
                this.$refs.uploader.remove(file)
            },
            canShowProgressSpeed ({active, progress}) {
                return this.isListLayout && active && +progress
            },
            canShowListDraggableHandler ({active, success, error}) {
                return this.isListLayout && this.uploadOptions.draggable
            },
            onDraggableMove ({draggedContext}, originalEvent) {
                if (!draggedContext.element) {
                    return false
                }
            }
        },
        computed: {
            headers () {
                let selectedLocale = store.state.application.locale || 'de';
                return {
                    'Accept': 'application/json, text/plain, */*',
                    'Content-Type': 'application/json;charset=UTF-8',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`, 
                    'Localization': selectedLocale
                }
            },
            uploaderProps () {
                const {auto, clear, draggable, hideSelectFilesButton, hideUploadButton, ...restProps} = this.uploadOptions

                return restProps
            },
            galleryImages () {
                return this.value.filter(file => this.isFileImage(file)).map(({file}) => file.blob)
            },
            isListLayout () {
                return this.layout === 'list'
            },
            isGridLayout () {
                return this.layout === 'grid'
            },
            isDraggableDisabled () {
                return !this.uploadOptions.draggable || this.value.length && this.$refs.uploader.uploaded
            },
            canShowSelectFilesButton () {
                return !this.uploadOptions.hideSelectFilesButton
            },
            canShowUploadButton () {
                return !this.uploadOptions.auto && !this.uploadOptions.hideUploadButton
            },
            canShowButtonGroup () {
                return this.isListLayout && (this.canShowSelectFilesButton || this.canShowUploadButton)
            },
            progressType () {
                return this.isListLayout ? 'line' : this.isGridLayout ? 'circle' : undefined
            },
            draggableHandler () {
                return this.isListLayout ? '.media-draggable-handler' : undefined
            },
        },
        mounted () {
            
            if (this.uploadOptions.auto || this.uploadOptions.clear) {
                this.$watch(() => this.value, media => {
                    if (media.length) {
                        if (this.id && this.uploadOptions.auto) {
                            this.$refs.uploader.active = true

                            this.$message({
                                type: 'info',
                                message: this.$t('general.components.common.media.messages.uploading'),
                                duration: 8000,
                                showClose: true
                            })
                        }

                        if (this.uploadOptions.clear) {
                            if (this.$refs.uploader.uploaded) {
                                this.$refs.uploader.clear()

                                this.$message({
                                    type: 'success',
                                    message: this.$t('general.components.common.media.messages.uploaded'),
                                    duration: 8000,
                                    showClose: true
                                })
                            }
                        }
                    }
                })
            }
        }
    }
</script>

<style lang="scss" scoped>
    .media {
        width: 100%;
        padding-top: 10px;
        padding-bottom: 10px;

        &.media-list-layout {
            .media-list {
                .media-item {
                    padding: 8px;

                    &.is-success .media-content {
                        .media-draggable-handler {
                            color: lighten(#67c23a, 20%);
                        }

                        .media-filename {
                            .media-filesize {
                                color: lighten(#67c23a, 20%);
                            }
                        }
                    }

                    &.is-failed .media-content {
                        .media-draggable-handler {
                            color: lighten(#f56c6c, 16%);
                        }

                        .media-filename {
                            .media-filesize {
                                color: lighten(#f56c6c, 16%);
                            }
                        }
                    }

                    &.is-draggable .media-content .media-draggable-handler {
                        cursor: move;
                    }

                    .media-content {
                        display: flex;
                        flex-wrap: wrap;
                        align-items: center;

                        .media-draggable-handler {
                            font-size: 12px;
                            color: darken(#DCDFE6, 4%);
                            margin-right: 8px;
                        }

                        .media-icon,
                        .media-image {
                            width: 32px;
                            height: 32px;
                            border: 1px darken(#fff, 6%) solid;
                            border-radius: 6px;
                            margin-right: 8px;
                            box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
                        }

                        .media-image,
                        .media-icon {
                            &:hover {
                                cursor: zoom-in;
                            }
                        }

                        .media-image {
                            filter: opacity(.8);
                            transition: filter .24s;

                            &:hover {
                                cursor: zoom-in;
                            }
                        }

                        .media-icon {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }

                        .media-filename {
                            flex: 1;
                        }

                        .media-progress {
                            width: 100%;
                            margin-left: 62px;

                            .media-progress-speed {
                                font-size: 12px;
                                color: darken(#fff, 48%);
                            }
                        }

                        .media-actions {
                            visibility: hidden;
                        }
                    }

                    &:not(:last-of-type) {
                        margin-bottom: 8px;
                    }

                    &:hover {
                        .media-content {
                            .media-actions {
                                visibility: visible;
                            }
                        }

                        &:not(.is-success):not(.is-failed) {
                            background-color: lighten(#DCDFE6, 8%);
                        }
                    }
                }

                .el-button-group {
                    width: 100%;
                    display: flex;

                    .el-button {
                        &:nth-child(1) {
                            flex: 1;
                            border-width: 2px;
                            border-style: dashed;
                            color: darken(#DCDFE6, 6%);
                        }

                        &:nth-child(2) {
                            margin-left: -1px;
                        }

                        &:not(.is-circle) :global([class^="icon-"]) {
                            margin-right: 8px;
                        }
                    }
                }

                .media-upload-trigger:focus, .media-upload-trigger:hover {
                    color: var(--primary-color);
                    border-color: var(--primary-color);
                    background-color: var(--primary-color-lighter);
                }
            }
        }

        &.media-grid-layout {
            .media-list {
                display: grid;
                grid-gap: 8px;
                grid-template-columns: repeat(auto-fill, minmax(112px, 1fr));

                .media-item {
                    position: relative;
                    padding-top: 100%;

                    &.is-draggable .media-content {
                        cursor: move;
                    }

                    .media-content {
                        position: absolute;
                        top: 0;
                        left: 0;
                        bottom: 0;
                        right: 0;
                        width: 100%;
                        height: 100%;
                        cursor: pointer;
                        box-sizing: border-box;
                        overflow: hidden;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        text-align: center;
                        border-radius: 4px;
                        word-break: break-word;
                        hyphens: auto;

                        .media-icon {
                            font-size: 24px;
                        }

                        .media-filename {
                            width: 90%;
                            padding: 4px;
                        }

                        .media-progress {
                            background-color: transparentize(#fff, .08);
                            position: absolute;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }

                        .media-actions {
                            background-color: transparentize(#000, .56);
                            position: absolute;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            opacity: 0;
                            transition: opacity .32s cubic-bezier(.17,.67,1,1.23);

                            .el-button {
                                width: auto;
                                font-size: 16px;
                                padding: 0;
                                border-style: none;
                                background-color: transparent;
                                filter: opacity(.72);
                                transition: filter .24s;

                                &:not(:last-of-type) {
                                    color: #fff;
                                }

                                &:hover {
                                    filter: opacity(1);
                                }
                            }
                        }

                        &:hover .media-actions {
                            opacity: 1;
                        }
                    }
                }

                > .el-button {
                    &:nth-of-type(1) {
                        position: relative;
                        padding: 0;
                        padding-top: 100%;
                        border-width: 2px;
                        border-style: dashed;

                        :global(span) {
                            font-size: 12px;
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            color: darken(#DCDFE6, 6%);
                            position: absolute;
                            top: 0;
                            left: 0;
                            bottom: 0;
                            right: 0;
                            width: 100%;
                            height: 100%;
                            white-space: normal;
                            line-height: 1.24;

                            :global(i) {
                                font-size: 18px;
                            }
                        }
                    }

                    &:nth-of-type(2) {
                        margin-left: 0;
                        grid-column: 1 / -1;
                    }

                    &.media-upload-trigger:focus, &.media-upload-trigger:hover {
                        color: var(--primary-color);
                        border-color: var(--primary-color);
                        background-color: var(--primary-color-lighter);

                        /deep/ span {
                            color: var(--primary-color);
                        }
                    }
                }
            }
        }

        .media-uploader {
            display: block;
        }

        .media-list {
            .media-item {
                border-radius: 6px;
                box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
                transition-property: color, filter;
                transition-duration: .24s;
                filter: opacity(1);

                &:not(.is-ghost) {
                    border: 1px lighten(#DCDFE6, 6%) solid;
                }

                &.is-ghost {
                    border: 2px #DCDFE6 dashed;
                }

                &.is-pending {
                    filter: opacity(.16);
                    pointer-events: none;
                }

                &.is-success {
                    background-color: #f0f9eb;
                    border-color: darken(#f0f9eb, 4%);
                    color: #67c23a;
                }

                &.is-failed {
                    background-color: #fef0f0;
                    border-color: darken(#fef0f0, 4%);
                    color: #f56c6c;
                }

                .media-content {
                    .media-image {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }

                    .media-filename {
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;

                        .media-filesize {
                            font-size: 10px;
                            color: darken(#DCDFE6, 4%);
                            display: block;
                        }
                    }
                }
            }
        }

        .media-drop-active {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            padding: 12px;
            z-index: 9;
            background-color: transparentize(#fff, .08);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: lighten(#000, 28%);
            font-size: 22px;
            line-height: 1.24;
            border: 2px #6AC06F dashed;
            box-sizing: border-box;
            text-align: center;

            i {
                color: #6AC06F;
                font-size: 56px;
                margin: 4px;
            }

            .description {
                font-size: 12px;
                color: darken(#DCDFE6, 48%);
            }
        }
    }
</style>