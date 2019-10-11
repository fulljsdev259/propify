<template>
    <el-form ref="form" class="request-add" :model="model" label-position="top" :rules="validationRules" v-loading="loading">
        <el-row type="flex" :gutter="16">
            <el-col>
                <el-form-item prop="category_id" :label="$t('resident.category')" required>
                    <el-select v-model="model.category_id" 
                                :placeholder="$t('resident.placeholder.category')"
                                @change="showSubcategory">
                        <el-option v-for="category in categories" 
                                    :key="category.id" 
                                    :label="category['name_'+$i18n.locale]" 
                                    :value="category.id" />
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col v-if="this.showsubcategory == true">
                <el-form-item prop="defect" :label="$t('resident.defect_location')" required>
                    <el-select v-model="model.defect" 
                                :placeholder="$t('resident.placeholder.defect_location')"
                                @change="showLocationOrRoom">
                        <el-option v-for="category in defect_subcategories" 
                                    :key="category.id" 
                                    :label="category['name_'+$i18n.locale]" 
                                    :value="category.id" />
                    </el-select>
                </el-form-item>
            </el-col>

        </el-row>
        <el-form-item :label="$t('models.request.category_options.range')" 
                    v-if="this.showsubcategory == true && this.showLiegenschaft == true && this.showWohnung == false">
            <el-select :disabled="$can($permissions.update.serviceRequest)"
                        :placeholder="$t(`general.placeholders.select`)"
                        class="custom-select"
                        v-model="model.location">
                <el-option
                    :key="location.value"
                    :label="location.name"
                    :value="location.value"
                    v-for="location in building_locations">
                </el-option>
            </el-select>
        </el-form-item>
        <el-form-item :label="$t('models.request.category_options.room')"
                    v-if="this.showsubcategory == true && this.showWohnung == true && this.showLiegenschaft == false">
            <el-select :disabled="$can($permissions.update.serviceRequest)"
                        :placeholder="$t(`general.placeholders.select`)"
                        class="custom-select"
                        v-model="model.room">
                <el-option
                    :key="room.value"
                    :label="room.name"
                    :value="room.value"
                    v-for="room in apartment_rooms">
                </el-option>
            </el-select>
        </el-form-item>
        <el-form-item prop="priority" :label="$t('resident.priority')" required>
                    <el-select :placeholder="$t('resident.placeholder.priority')" v-model="model.priority">
                        <el-option v-for="priority in priorities" :key="priority.value" :label="$t(`models.request.priority.${priority.label}`)" :value="priority.value" />
                    </el-select>
                </el-form-item>
        <el-form-item prop="title" :label="$t('resident.title')" required>
            <el-input v-model="model.title" />
        </el-form-item>
        <el-form-item prop="description" :label="$t('resident.description')" required>
            <el-input type="textarea" ref="description" v-model="model.description" :autosize="{minRows: 4, maxRows: 16}" />
        </el-form-item>
        <!-- <el-form-item prop="visibility" :label="$t('resident.visibility')" required>
            <el-select v-model="model.visibility" :placeholder="$t('resident.choose_visibility')">
                <el-option :key="k" :label="$t(`models.request.visibility.${visibility}`)" :value="parseInt(k)" v-for="(visibility, k) in $constants.requests.visibility">
                </el-option>
            </el-select>
        </el-form-item> -->
        <el-form-item class="switcher" prop="is_public" v-if="this.showsubcategory == true">
            <label class="switcher__label" >
                {{$t('resident.request_public_title')}}
                <span class="switcher__desc">{{$t('resident.request_public_desc')}}</span>
            </label>
            <el-switch v-model="model.is_public"/>
        </el-form-item>

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
        <media-uploader ref="media" :id="request_id" :audit_id="audit_id" type="requests" layout="grid" v-model="model.media" :upload-options="uploadOptions" />

        <!-- <media-upload ref="upload" v-model="model.media" :size="mediaUploadMaxSize" :allowed-types="['image/jpg', 'image/jpeg', 'image/png', 'application/pdf']" :cols="4" /> -->
        <el-form-item class="submitBtnDiv" v-if="showSubmit" style="grid-column: span 6">
            <el-button class="submit is-round" icon="ti-save" type="primary" :disabled="loading" @click="submit">{{$t('resident.actions.save')}}</el-button>
        </el-form-item>
    </el-form>
</template>

<script>
    import {MEDIA_UPLOAD_MAX_SIZE} from '@/config'
    import MediaUpload from 'components/MediaUpload'
    import ServicesTypes from 'mixins/methods/servicesTypes'
    import {displaySuccess, displayError} from 'helpers/messages'
    import PQueue from 'p-queue'

    export default {
        name: 'p-request-add-form',
        mixins: [
            ServicesTypes,
        ],
        props: {
            visible: {
                type: Boolean,
                default: false
            },
            showSubmit: {
                type: Boolean,
                default: true
            }
        },
        components: {
            MediaUpload
        },
        data () {
            return {
                model: {
                    title: '',
                    category_id: '',
                    priority: '',
                    visibility: '',
                    description: '',
                    media: [],
                    defect:'',
                    location: '',
                    room: '',
                    capture_phase: '',
                    component: '',
                    keyword: '',
                    keywords: [],
                    payer: '',
                },
                validationRules: {
                    title: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.title')})
                    },
                    category_id: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.category')})
                    },
                    priority: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.priority')})
                    },                                        
                    description: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.description')})
                    }                    
                },
                uploadOptions: {
                    drop: true,
                    multiple: true,
                    draggable: true,
                    hideUploadButton: true,
                    extensions: 'png,jpg,jpeg',
                    hideSelectFilesButton: false
                },
                categories: [],
                priorities: [],
                loading: false,
                defect_subcategories: [],
                address: {},
                building_locations: [],
                apartment_rooms: [],

                mediaUploadMaxSize: MEDIA_UPLOAD_MAX_SIZE,
                showsubcategory: false,
                showpayer: false,
                showUmgebung: false,
                showLiegenschaft: false,
                showacquisition: false,
                showWohnung: false,
                request_id: null,
                audit_id: null
            }
        },
        methods: {
            showSubcategory() {
                this.showsubcategory = this.model.category_id == 1 ? true : false;
                this.showpayer = this.model.qualification == 5 ? true : false;
            },
            showLocationOrRoom() {
                const subcategory = this.defect_subcategories.find(category => {
                    return category.id == this.model.defect;
                });

                this.model.room = '';
                this.model.location = '';
                this.showLiegenschaft = false;
                this.showUmgebung = false;
                this.showWohnung = false;

                if(subcategory.room == 1) {
                    this.showWohnung = true;
                }
                else if(subcategory.location == 1) {
                    this.showLiegenschaft = true;
                }
                else if(subcategory.location == 0 && subcategory.room == 0) {
                    this.showUmgebung = true;
                }
            },
            getLanguageI18n() {

                this.building_locations = Object.entries(this.$constants.requests.location).map(([value, label]) => ({value: +value, name: this.$t(`models.request.location.${label}`)}))
                this.apartment_rooms = Object.entries(this.$constants.requests.room).map(([value, label]) => ({value: +value, name: this.$t(`models.request.room.${label}`)}))
                
            },
            submit () {
                this.$refs.form.validate(async valid => {
                    if (valid) {
                        try {
                            this.loading = true
                            this.model.visibility = 1
                            if(this.model.is_public == true)
                                this.model.visibility = 2
                            const {media, ...params} = this.model

                            //const data = await this.$store.dispatch('createRequest', params)

                            if(params.category_id == 1)
                                params.category_id = this.model.defect;
                            

                            const resp = await this.$store.dispatch('newRequests/create', params);
                            
                            displaySuccess(resp.message)

                            
                            if (resp && resp.data) {                            
                                if (this.model.media.length) {
                                // TODO - make await for this   
                                    this.request_id = resp.data.id;            
                                    this.audit_id = resp.data.audit_id;
                                    this.$refs.media.startUploading();
                                }
                            }
                            // const {id} = resp.data
                            // if (media.length) {
                            //     const queue = new PQueue({concurrency: 1})

                            //     media.forEach(
                            //         ({file}) => queue.add(
                            //             async () => { 
                            //                 const mediaResp = await this.$store.dispatch('newRequests/uploadMedia', {
                            //                     id, media: file.src,
                            //                     merge_in_audit: resp.data.audit_id
                            //                 }) 
                            //             }
                            //         )
                            //     )

                            //     await queue.onIdle()
                            // }

                            this.$emit('update:visible', false)
                            this.$refs.form.resetFields()
                            //this.$refs.upload.clear()
                        } catch (err) {
                            displayError(err);
                        } finally {
                            this.loading = false
                        }
                    }
                })
            }
        },
        async mounted () {
            this.priorities = Object.entries(this.$constants.requests.priority).map(([value, label]) => ({value: +value, label}));
            try {
                const {data} = await this.$store.dispatch('getRequestCategoriesTree', {get_all: true})

                this.categories = data.filter(category => {
                    return category.parent_id !== 1;
                });
                
                let defect_cat = data.find(category => {
                    return category.id === 1;
                });
                this.defect_subcategories = defect_cat.categories;

            } catch (err) {
                displayError(err)
            }
            
        },
        watch: {
            "$i18n.locale": {
                immediate: true,
                handler(val) {
                    this.getLanguageI18n();
                }
            }
        },
    };
</script>

<style lang="scss" scoped>
    .request-add.el-form {
        .el-form-item {
            margin-bottom: 0px;

            &.is-error {
                margin-bottom: 10px;
            }

                        
            &.switcher {
                padding-top: 10px;

                /deep/ .el-form-item__content {
                    display: flex;
                    
                    .switcher__label {
                        text-align: left;
                        line-height: 1.4em;
                        color: #606266;
                    }
                    .switcher__desc {
                        margin-top: 0.5em;
                        display: block;
                        font-size: 0.9em;
                    }

                    /deep/ & > div {
                        flex: 1;
                        justify-content: flex-end;
                        text-align: end;
                    }
                }
                    
                /deep/ .el-switch {
                    margin-left: auto;
                }
                
            }


            /deep/ .el-form-item__label {
                padding: 0;
            }

            .el-select {
                width: 100%;
            }
            :global(.el-input__inner),
            :global(.el-textarea__inner) {
                background-color: transparentize(#fff, .44);
            }
        }
        > .el-form-item:last-child :global(.el-form-item__content) {
            display: flex;
            align-items: center;
            small {
                margin-left: 12px;
                line-height: 1.48;
                word-break: break-word;
                color: darken(#fff, 40%);
            }
        }
        .el-divider {
            margin: 16px 0;
            &:last-of-type {
                margin-bottom: 0;
            }
        }

        .ui-divider {
            margin-top: 30px;
        }
        
        .upload-description {
            padding: 0;

            .el-alert {
                align-items: flex-start;
                padding-right: 0;

                .el-alert__icon {
                    padding-top: 2px;
                }
            }
        }

        .submitBtnDiv {
            // position: absolute;
            width: 100%;
            grid-column: span 6 / auto;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            justify-content: flex-end;
            margin-bottom: 30px;

        }
        .el-button.submit {
            margin-top: 1em;
            width: 100%;
            /deep/ i {
                padding-right: 5px;
            }
        }

        .switcher-form-item {
            :global(.el-form-item__content) {
                display: flex;
                align-items: center;

                .el-switch {
                    flex: 1;
                    justify-content: flex-end;
                }
            }
        }

    }

</style>
