<template>
    <el-upload
            v-if="type === 'link'"
            :action="''"
            :show-file-list="false"
            :before-upload="beforeAvatarUpload"
            :http-request="imageUpload"
    >
        <el-link>{{$t('general.actions.upload_img')}}</el-link>
    </el-upload>
    <el-upload
            v-else
            class="avatar-box__btn"
            :action="''"
            :show-file-list="false"
            :before-upload="beforeAvatarUpload"
            :http-request="imageUpload"
    >
        <i class="el-icon-camera-solid"></i>
    </el-upload>
</template>

<script>
    import {displayError} from "../helpers/messages";
    import UploadMixin from '../mixins/uploadMixin';

    export default {
        name: "UploadAvatar",
        mixins: [UploadMixin],
        props: {
            type: {
                type: String,
                default: ''
            },
        },
        methods: {
            beforeAvatarUpload(file) {
                const isJPG = file.type === 'image/jpeg';
                const isPNG = file.type === 'image/png';
                const isLt5M = file.size / 1024 / 1024 < 5;

                if (!isJPG && !isPNG) {
                    displayError({
                        success: false,
                        message: 'Avatar picture must be JPG/PNG format!'
                    });
                }
                if (!isLt5M) {
                    displayError({
                        success: false,
                        message: 'Avatar picture size can not exceed 5MB!'
                    });
                }
                return (isJPG || isPNG) && isLt5M;
            },
            imageUpload(e) {
                this.base64(e.file, (dataUrl) => {
                    this.$emit("imageUploaded", dataUrl);
                });
                this.$emit("file", e.file);
            }
        }
    }
</script>
