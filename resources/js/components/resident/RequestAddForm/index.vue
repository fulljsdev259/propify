<template>
    <el-form ref="form" class="request-add" :model="model" label-position="top" :rules="validationRules" v-loading="loading">
        
        <el-form-item prop="relation_id" :label="$t('resident.relation')" v-if="relations.length > 1">
            <el-select v-model="model.relation_id" 
                        :placeholder="$t('resident.placeholder.relation')"
                        class="custom-select"
                        filterable>
                <el-option v-for="relation in relations" 
                            :key="relation.id" 
                            :label="relation.building_room_floor_unit" 
                            :value="relation.id" />
            </el-select>
        </el-form-item>

        <el-form-item prop="category_id" :label="$t('resident.category')">
            <el-select v-model="model.category_id" 
                        :placeholder="$t('resident.placeholder.category')"
                        filterable
                        @change="changeCategory">
                <el-option
                    :key="category.id"
                    :label="$t(`models.request.category_list.${category.name}`)"
                    :value="category.id"
                    v-for="category in categories">
                </el-option>
            </el-select>
        </el-form-item>

        <el-form-item prop="sub_category_id" :label="$t('resident.defect_location')" v-if="this.showSubCategory == true">
            <el-select v-model="model.sub_category_id" 
                        :placeholder="$t('resident.placeholder.defect_location')"
                        filterable
                        @change="changeSubCategory">
                <el-option
                    :key="category.id"
                    :label="$t(`models.request.sub_category.${category.name}`)"
                    :value="category.id"
                    v-for="category in sub_categories">
                </el-option>
            </el-select>
        </el-form-item>

        <el-form-item :label="$t('models.request.category_options.range')" 
                    v-if="this.showSubCategory == true && this.showLocation == true && this.showRoom == false">
            <el-select :disabled="$can($permissions.update.serviceRequest)"
                        :placeholder="$t(`general.placeholders.select`)"
                        filterable
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
                    v-if="this.showSubCategory == true && this.showRoom == true && this.showLocation == false">
            <el-select :disabled="$can($permissions.update.serviceRequest)"
                        :placeholder="$t(`general.placeholders.select`)"
                        filterable
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
        <!-- <el-form-item prop="priority" :label="$t('resident.priority')">
                    <el-select :placeholder="$t('resident.placeholder.priority')" filterable v-model="model.priority">
                        <el-option v-for="priority in priorities" :key="priority.value" :label="$t(`models.request.priority.${priority.label}`)" :value="priority.value" />
                    </el-select>
                </el-form-item> -->
        <el-form-item prop="title" :label="$t('resident.title')">
            <el-input v-model="model.title" />
        </el-form-item>
        <el-form-item prop="description" :label="$t('resident.description')" class="full-width">
            <el-input type="textarea" ref="description" v-model="model.description" :autosize="{minRows: 4, maxRows: 16}" />
        </el-form-item>
        <!-- <el-form-item prop="visibility" :label="$t('resident.visibility')">
            <el-select v-model="model.visibility" :placeholder="$t('resident.choose_visibility')">
                <el-option :key="k" :label="$t(`models.request.visibility.${visibility}`)" :value="parseInt(k)" v-for="(visibility, k) in $constants.requests.visibility">
                </el-option>
            </el-select>
        </el-form-item> -->
        <el-form-item class="switcher full-width" prop="is_public" v-if="this.showSubCategory == true">
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
        <el-form-item class="submitBtnDiv" v-if="showSubmit">
            <el-button class="submit is-round" icon="ti-save" type="primary" :disabled="loading" @click="submit">{{$t('resident.actions.save')}}</el-button>
        </el-form-item>
    </el-form>
</template>

<script>
    import {MEDIA_UPLOAD_MAX_SIZE} from '@/config'
    import {displaySuccess, displayError} from 'helpers/messages'
    import PQueue from 'p-queue'
    import globalFunction from "helpers/globalFunction";

    export default {
        name: 'p-request-add-form',
        mixins: [
            globalFunction
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
        data () {
            return {
                model: {
                    relation_id: '',
                    title: '',
                    category_id: '',
                    priority: '',
                    visibility: '',
                    description: '',
                    media: [],
                    sub_category_id: null,
                    location: '',
                    room: '',
                    capture_phase: '',
                    component: '',
                    keyword: '',
                    keywords: [],
                },
                validationRules: {
                    relation_id: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.relation')})
                    },
                    title: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.title')})
                    },
                    category_id: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.category')})
                    },
                    sub_category_id: {
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('resident.defect_location')})
                    },
                    // priority: {
                    //     required: true,
                    //     message: this.$t('validation.required',{attribute: this.$t('resident.priority')})
                    // },                                        
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
                sub_categories: [],
                address: {},
                building_locations: [],
                apartment_rooms: [],
                relations: [],
                mediaUploadMaxSize: MEDIA_UPLOAD_MAX_SIZE,
                showSubCategory: false,
                showLocation: false,
                showRoom: false,
                request_id: null,
                audit_id: null,
                default_relation_id: ''
            }
        },
        methods: {
            changeCategory() {
                let children = this.$constants.requests.categories_data.parent_child[this.model.category_id]
                this.showSubCategory = children.length > 0 ? true : false;
                let p_category = this.categories.find(category => { return category.id == this.model.category_id});
                if(this.showSubCategory) {
                    this.sub_categories = p_category ? p_category.sub_categories : [];
                }

                this.$refs.form.clearValidate('title')
                this.$refs.form.clearValidate('description')
            },
            changeSubCategory() {
                const subcategory = this.sub_categories.find(category => {
                    return category.id == this.model.sub_category_id;
                });

                this.model.room = '';
                this.model.location = '';
                this.showLocation = false;
                this.showRoom = false;

                if(subcategory.room == 1) {
                    this.showRoom = true;
                }
                else if(subcategory.location == 1) {
                    this.showLocation = true;
                }
                
                this.$refs.form.clearValidate('title')
                this.$refs.form.clearValidate('description')
            },
            getLanguageI18n() {

                this.building_locations = Object.entries(this.$constants.requests.location).map(([value, label]) => ({value: +value, name: this.$t(`models.request.location.${label}`)}))
                this.apartment_rooms = Object.entries(this.$constants.requests.room).map(([value, label]) => ({value: +value, name: this.$t(`models.request.room.${label}`)}))
                
                this.relations = this.$store.getters.loggedInUser.resident.relations.filter( relation => relation.status == 1)

                this.relations.forEach(relation => {
                    relation.building_room_floor_unit = this.getSelectOptionOfRelation(relation)
                    return relation
                })
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

                            // params.category = params.category_id
                            // params.sub_category = params.sub_category_id

                            params.media = this.model.media.map(item => item.file.src)

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

                            this.showSubCategory = this.showLocation = this.showRoom = false
                            
                            this.$refs.form.resetFields()

                            if(this.relations.find(item => item.id == this.default_relation_id)) {
                                this.model.relation_id = this.$store.getters.loggedInUser.resident.default_relation_id
                            }
                            else if(this.relations.length == 1) {
                                this.model.relation_id = this.relations[0].id
                            }
                            this.$emit('update:visible', false)
                            
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
            //this.priorities = Object.entries(this.$constants.requests.priority).map(([value, label]) => ({value: +value, label}));
            this.relations = this.$store.getters.loggedInUser.resident.relations.filter( relation => relation.status == 1)

            this.relations.forEach(relation => {
                relation.building_room_floor_unit = this.getSelectOptionOfRelation(relation)
                return relation
            })

            this.default_relation_id = this.$store.getters.loggedInUser.resident.default_relation_id
            
            if(this.relations.find(item => item.id == this.default_relation_id)) {
                this.model.relation_id = this.$store.getters.loggedInUser.resident.default_relation_id
            }
            else if(this.relations.length == 1) {
                this.model.relation_id = this.relations[0].id
            }
            
            try {
                this.categories = this.$constants.requests.categories_data.tree
                
                if(this.model.category_id)
                {
                    let p_category = this.categories.find(category => {
                        return category.id === this.model.category_id;
                    });

                    this.sub_categories = p_category ? p_category.sub_categories : [];
                }

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
        @media screen and (max-height: 414px) {
            display: block !important;
        }
        .el-form-item {
            @media screen and (max-height: 414px) {
                width: 49.5% !important;
                display: inline-block;
            }
            &.full-width {
                width: 100% !important;
            }
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

                // /deep/ .el-input__suffix {
                //     width: 41px;
                //     margin-right: -8px;

                //     .el-input__suffix-inner {
                //         height: 40px;
                //         width: 100%;
                //         display: block;
                //     }
                //     .el-input__icon {
                //         width: 100%;
                //     }
                // }
            }
            :global(.el-input__inner),
            :global(.el-textarea__inner) {
                background-color: transparentize(#fff, .44);
            }

            .custom-select {
                display: block;
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
        }

        .submitBtnDiv {
            // position: absolute;
            width: 100%;
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
