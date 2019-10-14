<template>
    <el-upload
        :action="''"
        :before-upload="beforeDocumentUpload"
        :drag="drag"
        :http-request="fileUpload"
        :multiple="multiple"
        :show-file-list="false"
        :accept="acceptType"
        class="file-uploader"
    >
        <div class="uploader-icon-div">
            <i class="icon-plus"></i>
        </div>
    </el-upload>
</template>

<script>
    import {displayError} from "../helpers/messages";
    import UploadMixin from 'mixins/uploadMixin';

    export default {
        name: "UploadContract",
        mixins: [UploadMixin],
        props: {
            multiple: {
                type: Boolean,
                default: false
            },
            drag: {
                type: Boolean,
                default: false
            },
            acceptType: {
                type: String,
                default: ""
            },
        },
        methods: {
            beforeDocumentUpload(file) {
                const isLt2M = file.size / 1024 / 1024 < 16;

                if (!isLt2M) {
                    displayError({
                        success: false,
                        message: 'Document size can not exceed 16MB!'
                    });
                }

                return isLt2M;
            },
            fileUpload(e) {
                let file = {
                    raw: e.file,
                    name: e.file.name,
                    src: ''
                };
                this.base64(e.file, (dataUrl) => {
                    file.src = dataUrl;
                    this.$emit("fileUploaded", file);
                });
            }
        }
    }
</script>

<style lang="scss">
    .upload-custom {

        display: grid;
        grid-gap: 8px;
        grid-template-columns: repeat(auto-fill, minmax(112px, 1fr));
        grid-auto-rows: -webkit-min-content;
        grid-auto-rows: min-content;
        padding-top: 10px;
        
        .el-upload {
            border: none;

            .el-upload-dragger {
                width: 100%;
                height: 0;
                padding-top: 100%;
                background: transparent;
                border-style: dashed;
                border-width: 2px;
                color: var(--color-text-placeholder);

                &:hover {
                    color: var(--color-primary);
                    background-color: var(--primary-color-lighter);
                    border-color: var(--color-primary);
                }

                .uploader-icon-div {
                    position: absolute;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    display: -webkit-box;
                    display: -ms-flexbox;
                    display: flex;
                    -webkit-box-orient: vertical;
                    -webkit-box-direction: normal;
                    -ms-flex-direction: column;
                    flex-direction: column;
                    -webkit-box-align: center;
                    -ms-flex-align: center;
                    align-items: center;
                    -webkit-box-pack: center;
                    -ms-flex-pack: center;
                    justify-content: center;


                }
            }
        }

    }
</style>
