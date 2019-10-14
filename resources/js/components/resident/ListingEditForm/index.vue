<template>
    <el-form ref="form" :model="model" :rules="validationRules" label-position="top" v-loading="loading">
        <el-form-item prop="title" :label="$t('resident.title')">
            <el-input v-model="model.title" />
        </el-form-item>
        <el-row type="flex" :gutter="16">
            <el-col>
                <el-form-item prop="type" :label="$t('resident.type')">
                    <el-select v-model="model.type">
                        <el-option v-for="category in types" :key="category.value" :label="$t(`models.listing.type.${category.label}`)" :value="category.value" />
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col>
                <el-form-item prop="visibility" :label="$t('resident.visibility')">
                    <el-select v-model="model.visibility">
                        <el-option v-for="visibility in visibilities" :key="visibility.value" :label="$t(`models.listing.visibility.${visibility.label}`)" :value="visibility.value" />
                    </el-select>
                </el-form-item>
            </el-col>
        </el-row>

        <el-form-item prop="price" :label="$t('resident.price')" v-if="isPriceVisible">
            <div style="display: flex">
                <el-input v-model="model.price.integer">
                    <template slot="prepend">CHF</template>
                </el-input>
                <div style="padding: 4px">.</div>
                <el-input v-model="model.price.decimals" :maxlength=2 style="width: 50%" />
            </div>
        </el-form-item>
        <el-form-item prop="content" :label="$t('resident.content')">
            <el-input type="textarea" resize="none" v-model="model.content" :autosize="{minRows: 4, maxRows: 16}" />
        </el-form-item>
        <el-row type="flex" :gutter="16">
            <el-col>
                <el-form-item prop="resident_name" :label="$t('resident.contact_name')">
                    <el-input v-model="model.resident_name" />
                </el-form-item>
            </el-col>
            <el-col>
                <el-form-item prop="resident_phone" :label="$t('resident.contact_phone')">
                    <el-input v-model="model.resident_phone" />
                </el-form-item>
            </el-col>
        </el-row>
       
        <ui-divider content-position="left"><i class="icon-file-image"></i> {{$t('resident.images')}}</ui-divider>
        <ui-media-gallery :files="data.media.map(({url}) => url)" />
        
        
        <ui-divider content-position="left"><i class="el-icon-upload"></i> {{$t('resident.request_upload_title')}}</ui-divider>
        
        <div class="upload-description">
            <el-alert
                :title="$t('resident.request_upload_desc')"
                type="info"
                show-icon
                :closable="false"
            >
            </el-alert>
        </div>
        <media-uploader ref="media" :id="listing_id" type="listings" layout="grid" v-model="model.media" :upload-options="uploadOptions" />
        <div class="submitBtnDiv" v-if="!hideSubmit">
            <el-row type="flex" :gutter="16" >
                <el-col>
                    <el-form-item>
                        <el-button class="submit is-round" icon="ti-save" type="primary" :disabled="loading" @click="submit">{{$t('resident.actions.save')}}</el-button>
                    </el-form-item>
                </el-col>
                <el-col>
                    <el-form-item>
                        <el-button class="is-round" icon="ti-trash" type="danger" :disabled="loading" @click.stop="$emit('delete-listing', $event, data)">{{$t('general.actions.delete')}}</el-button>
                    </el-form-item>
                </el-col>
            </el-row>
        </div>
    </el-form>
</template>

<script>
    import {displaySuccess, displayError} from 'helpers/messages'

    export default {
        name: 'p-listing-edit-form',
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
                listing_id: null,
                model: {
                    media: [],
                    type: null,
                    title: null,
                    price: {
                        integer: '0',
                        decimals: '00'
                    },
                    content: null,
                    visibility: null,
                    resident_name: null,
                    resident_phone: null,       
                },
                validationRules: {
                    type: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.type')})
                    },
                    title: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.title')})
                    },
                    price: {
                        required: true,
                        validator: this.priceValidator
                    },
                    content: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.content')})
                    },
                    visibility: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.visibility')})
                    },
                    resident_name: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.contact_name')})
                    },
                    resident_phone: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.contact_phone')})
                    }
                }
            }
        },
        methods: {
            submit () {
                this.$refs.form.validate(async valid => {
                    if (valid) {
                        this.loading = true;

                        const {price, media, resident_name, resident_phone, ...params} = this.model

                        params.price = `${price.integer}.${price.decimals}`
                        params.contact = `${resident_name} - ${resident_phone}`
                        params.id = this.data.id

                        const resp = await this.$store.dispatch('newListings/update', params);
                        
                        if (resp && resp.data) {                            
                            if (this.model.media.length) {
                            // TODO - make await for this   
                                this.listing_id = this.data.id;
                                this.$refs.media.startUploading();
                                this.$root.$on('media-upload-finished', () => this.$emit('update:visible', false));
                            }
                        }
                        

                        this.loading = false
                        this.$refs.form.resetFields()
                        if(!this.model.media.length)
                            this.$emit('update:visible', false);
                        // this.$refs.media.clearUploader()
                    }
                })
            },
            priceValidator (rule, value, callback) {
                const integer = +(value.integer || undefined)
                const decimals = +(value.decimals || undefined)

                if (!isNaN(integer) &&
                    !isNaN(decimals) &&
                    integer % 1 === 0 &&
                    decimals % 1 === 0 &&
                    decimals >= 0 && decimals <= 99 &&
                    integer >= 0 && integer <= Number.MAX_SAFE_INTEGER
                ) {
                    callback()
                } else {
                    callback(new Error('The price is invalid'))
                }
            }
        },
        computed: {
            isPriceVisible () {
                return this.model.type != (Object.values(this.$constants.listings.type).find(name => name === 'giveaway') || [])[0]
            }
        },
        created () {
            this.model.title = this.data.title;
            this.model.type = this.data.type;
            this.model.visibility = this.data.visibility;
            this.model.content = this.data.content;

            let contacts = this.data.contact.split(" - ");
            this.model.resident_name = contacts[0]
            this.model.resident_phone = contacts[1]

            this.model.price.integer = this.data.price.split(".")[0]
            this.model.price.decimals = this.data.price.split(".")[1]
            this.types = Object.entries(this.$constants.listings.type).map(([value, label]) => ({value: +value, label}))
            this.visibilities = Object.entries(this.$constants.listings.visibility).map(([value, label]) => ({value: +value, label}))

        }
    };
</script>

<style lang="sass" scoped>
    .el-form
        display: flex
        flex-direction: column

        .el-form-item
            //margin-bottom: 16px
            margin-bottom: 0

            &.is-error
                margin-bottom: 10px;

            &:last-child
                margin-bottom: 0

                &.is-error
                    margin-bottom: 20px;

            /deep/ .el-form-item__label
                padding: 0
                //line-height: 32px

            /deep/ .el-form-item__content
                .el-input.el-input-group
                    /deep/ .el-input-group__prepend
                        padding: 8px

                .el-button, .el-select
                    width: 100%

                .el-button i
                    padding-right: 5px

        .ui-media-gallery 
            margin-top: 8px


        .ui-divider
            margin-top: 30px

        .upload-description
            padding: 0

        

        .submitBtnDiv 
            // position: absolute
            width: 100%
            display: flex
            flex-direction: column
            flex-grow: 1
            justify-content: flex-end
            

            .el-col
                align-items: flex-end
                display: flex

                .el-form-item
                    width: 100%
        
        .el-button.submit 
            margin-top: 1em
            width: 100%
            /deep/ i 
                padding-right: 5px
</style>