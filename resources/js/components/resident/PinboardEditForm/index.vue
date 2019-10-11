<template>
    <el-form ref="form" :model="model" :rules="validationRules" label-position="top" v-loading="loading">

        
        <el-row type="flex" :gutter="16">
            <el-col>
                <el-form-item prop="content" :label="$t('resident.content')" style="grid-column: span 6">
                    <el-input type="textarea" resize="none" v-model="model.content" :autosize="{minRows: 4, maxRows: 16}" />
                </el-form-item>
            </el-col>
        </el-row>

        <ui-divider content-position="left"><i class="icon-file-image"></i> {{$t('resident.images')}}</ui-divider>
        <ui-media-gallery :files="data.media.map(({url}) => url)" />
        
        <ui-divider content-position="left"><i class="el-icon-upload"></i> {{$t('resident.request_upload_title')}}</ui-divider>
        <media-uploader ref="media" :id="pinboard_id" type="pinboard" layout="grid" v-model="model.media" :upload-options="uploadOptions" />

        <div class="submitBtnDiv" v-if="!hideSubmit">
            <el-row type="flex" :gutter="16" >
                <el-col>
                    <el-form-item v-if="!hideSubmit">
                        <el-button class="submit is-round" icon="ti-save" type="primary" :disabled="loading" @click="submit">{{$t('resident.actions.save')}}</el-button>
                    </el-form-item>
                </el-col>
            </el-row>
        </div>
        
    </el-form>
</template>

<script>
    import {displaySuccess, displayError} from 'helpers/messages'

    export default {
        name: 'p-pinboard-edit-form',
        props: {
            hideSubmit: {
                type: Boolean,
                default: false
            },
            data: {
                type: Object
            },
            visible: {
                type: Boolean,
                default: false
            }
        },
        data () {
            return {
                types: [],
                loading: false,
                uploadOptions: {
                    drop: true,
                    multiple: true,
                    draggable: true,
                    hideUploadButton: true,
                    extensions: 'png,jpg,jpeg',
                    hideSelectFilesButton: false
                },
                pinboard_id: null,
                model: {
                    media: [],
                    content: null,   
                },
                validationRules: {
                    content: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.content')})
                    }
                }
            }
        },
        methods: {
            submit () {
                this.$refs.form.validate(async valid => {
                    if (valid) {
                        this.loading = true;

                        const {media, ...params} = this.model

                        params.id = this.data.id

                        this.data.content = this.model.content
                        const resp = await this.$store.dispatch('newPinboard/update', this.data);
                        
                        if (resp && resp.data) {                            
                            if (this.model.media.length) {
                            // TODO - make await for this   
                                this.pinboard_id = this.data.id;
                                this.$refs.media.startUploading();
                                this.$root.$on('media-upload-finished', () => this.$emit('update:visible', false));
                            }
                        }

                        this.loading = false
                        
                        if(!this.model.media.length)
                            this.$emit('update:visible', false);
//                        this.$refs.form.resetFields()
                    }
                })
            },
            
        },
        created () {
            this.model.content = this.data.content;
        }
    };
</script>

<style lang="sass" scoped>
    .el-form
        display: flex
        flex-direction: column

        .el-form-item
            margin-bottom: 16px

            &:last-child
                margin-bottom: 0

            /deep/ .el-form-item__label
                padding: 0
                line-height: 32px

            /deep/ .el-form-item__content
                .el-input.el-input-group
                    /deep/ .el-input-group__prepend
                        padding: 8px

                .el-button, .el-select
                    width: 100%

                .el-button i
                    padding-right: 5px
</style>