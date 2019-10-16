<template>
    <div class="services-edit">
        <heading :title="$t('models.request.add_title')" icon="icon-chat-empty" shadow="heavy">
            <add-actions :saveAction="submit" route="adminRequests" editRoute="adminRequestsEdit"/>
        </heading>
        <el-row :gutter="20" class="crud-view" id="add_request">
            <el-col :md="12">
                <el-form :model="model" :rules="validationRules" ref="form">
                <card :loading="loading">
                    <el-row :gutter="20">
                        <el-col :md="12">
                            <el-form-item :label="$t('models.request.category')"
                                            :rules="validationRules.category"
                                            prop="category_id">
                                <el-select :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t('models.request.placeholders.category')"
                                            class="custom-select"
                                            v-model="model.category_id"
                                            @change="changeCategory">
                                    <el-option
                                        :key="category.id"
                                        :label="category['name_'+$i18n.locale]"
                                        :value="category.id"
                                        v-for="category in categories">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" v-if="this.showsubcategory == true">
                            <el-form-item :label="$t('models.request.defect_location.label')">
                                <el-select :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t(`general.placeholders.select`)"
                                            class="custom-select"
                                            v-model="model.defect"
                                            @change="changeSubCategory">
                                    <el-option
                                        :key="category.id"
                                        :label="category['name_'+ $i18n.locale]"
                                        :value="category.id"
                                        v-for="category in defect_subcategories">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" 
                                v-if="this.showsubcategory == true && this.showLiegenschaft == true && this.showWohnung == false">
                            <el-form-item :label="$t('models.request.category_options.range')">
                                <el-select :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t(`general.placeholders.select`)"
                                            class="custom-select"
                                            v-model="model.location">
                                    <el-option
                                        :key="location.value"
                                        :label="location.name"
                                        :value="location.value"
                                        v-for="location in locations">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12"
                                v-if="this.showsubcategory == true && this.showWohnung == true && this.showLiegenschaft == false">
                            <el-form-item :label="$t('models.request.category_options.room')">
                                <el-select :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t(`general.placeholders.select`)"
                                            class="custom-select"
                                            v-model="model.room">
                                    <el-option
                                        :key="room.value"
                                        :label="room.name"
                                        :value="room.value"
                                        v-for="room in rooms">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" v-if="this.showacquisition == true">
                            <el-form-item :label="$t('models.request.category_options.acquisition')">
                                <el-select :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t(`general.placeholders.select`)"
                                            class="custom-select"
                                            v-model="model.capture_phase">
                                    <el-option
                                        :key="acquisition.value"
                                        :label="acquisition.name"
                                        :value="acquisition.value"
                                        v-for="acquisition in acquisitions">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12">
                            <el-form-item :label="$t('models.request.category_options.component')">
                                <el-input v-model="model.component"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12"
                                v-if="model.category_id && selectedCategoryHasQualification(model.category_id)">
                            <el-form-item :label="$t('models.request.qualification.label')"
                                        :rules="validationRules.qualification"
                                        prop="qualification">
                                <el-select :placeholder="$t('models.request.placeholders.qualification')"
                                        class="custom-select"
                                        v-model="model.qualification"
                                        @change="changeQualification">
                                    <el-option
                                        :key="k"
                                        :label="$t(`models.request.qualification.${qualification}`)"
                                        :value="parseInt(k)"
                                        v-for="(qualification, k) in $constants.requests.qualification">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12"
                                v-if="model.category_id && selectedCategoryHasQualification(model.category_id) && this.showpayer == true">
                            <el-form-item :label="$t('models.request.category_options.cost')">
                                <el-select :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t(`general.placeholders.select`)"
                                            class="custom-select"
                                            v-model="model.payer">
                                    <el-option
                                        :key="cost.value"
                                        :label="cost.name"
                                        :value="cost.value"
                                        v-for="cost in costs">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12">
                            <el-form-item :label="$t('models.request.priority.label')" :rules="validationRules.priority"
                                      prop="priority">
                                <el-select :placeholder="$t('models.request.priority.label')" class="custom-select"
                                        v-model="model.priority">
                                    <el-option
                                        :key="k"
                                        :label="$t(`models.request.priority.${priority}`)"
                                        :value="parseInt(k)"
                                        v-for="(priority, k) in $constants.requests.priority">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12">
                            <el-form-item :label="$t('models.request.status.label')" :rules="validationRules.status"
                                        prop="status">
                                <el-select :placeholder="$t('models.request.placeholders.status')" class="custom-select"
                                        v-model="model.status">
                                    <el-option
                                        :key="k"
                                        :label="$t(`models.request.status.${status}`)"
                                        :value="parseInt(k)"
                                        v-for="(status, k) in $constants.requests.status">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="24">
                            <el-form-item :label="$t('models.request.public_desc')"
                                        prop="is_public"
                            >
                                <el-switch v-model="model.is_public" @change="changePublic"/>
                            </el-form-item>
                        </el-col>
                        <el-col :md="24" v-if="model.is_public">
                            <el-form-item :label="$t('models.request.visibility.label')"
                                        :rules="validationRules.visibility"
                                        prop="visibility"
                            >
                                <el-select
                                    :placeholder="$t('models.request.placeholders.visibility')"
                                    class="custom-select"
                                    v-model="model.visibility">
                                    <el-option
                                        :key="k"
                                        :label="$t(`models.request.visibility.${visibility}`)"
                                        :value="parseInt(k)"
                                        v-for="(visibility, k) in $constants.requests.visibility">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="24" v-if="model.is_public">
                            <el-form-item :label="$t('models.request.send_notification_desc')"
                                        prop="send_notification"
                            >
                                <el-switch v-model="model.send_notification"/>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12">
                            <el-form-item :label="$t('models.request.due_date')" :rules="validationRules.due_date"
                                        prop="due_date">
                                <el-date-picker
                                    :placeholder="$t('models.request.placeholders.due_date')"
                                    format="dd.MM.yyyy"
                                    style="width: 100%"
                                    type="date"
                                    v-model="model.due_date"
                                    value-format="yyyy-MM-dd"
                                    :picker-options="dueDatePickerOptions"
                                >
                                </el-date-picker>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" v-if="model.resident">
                            <el-form-item>
                                <label slot="label">
                                    {{$t('general.resident')}}
                                </label>
                                <router-link :to="{name: 'adminResidentsEdit', params: {id: model.resident.id}}">
                                    {{model.resident.first_name}} {{model.resident.last_name}}
                                </router-link>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" v-else>
                            <el-form-item :label="$t('general.resident')" :rules="validationRules.resident_id" prop="resident_id">
                                <el-select
                                    :loading="remoteLoading"
                                    :placeholder="$t('models.request.placeholders.resident')"
                                    :remote-method="remoteSearchResidents"
                                    filterable 
                                    remote
                                    reserve-keyword
                                    style="width: 100%;"
                                    v-model="model.resident_id">
                                    <el-option
                                        :key="resident.id"
                                        :label="resident.name"
                                        :value="resident.id"
                                        v-for="resident in residents"/>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12">
                            <el-form-item :label="$t('models.request.prop_title')" :rules="validationRules.title"
                                        prop="title">
                                <el-input type="text" v-model="model.title"/>
                            </el-form-item>
                        </el-col>
                        <el-col :md="24">
                            <el-form-item class="label-block" :label="$t('general.description')" :rules="validationRules.description"
                                          prop="description"
                                          :key="editorKey">
                                <yimo-vue-editor
                                        :config="editorConfig"
                                        v-model="model.description"/>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12">
                            <el-form-item :label="$t('models.request.category_options.keywords')">
                                <el-select
                                    v-model="model.keywords"
                                    multiple
                                    filterable
                                    allow-create
                                    default-first-option
                                    @remove-tag="deleteTag"
                                    class="custom-select"
                                    @change="changeTags"
                                    >
                                    <el-option
                                        v-for="item in tags"
                                        :key="item.id"
                                        :label="item.name"
                                        :value="item.name">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <!--                        <el-form-item :label="$t('models.request.is_public')"-->
                        <!--                                      prop="is_public">-->
                        <!--                            <el-switch-->
                        <!--                                v-model="model.is_public"-->
                        <!--                            >-->
                        <!--                            </el-switch>-->
                        <!--                        </el-form-item>-->
                        </el-row>
                    </card>
                </el-form>
            </el-col>
            <el-col :md="12">
                <card :header="$t('models.request.images')">
                    <!-- <upload-document @fileUploaded="uploadFiles" class="drag-custom" drag multiple/>
                    <div class="mt15">
                        <request-media :data="media" @deleteMedia="deleteMedia" v-if="media.length"></request-media>
                    </div> -->
                    <el-alert
                        :title="$t('general.upload_all_desc')"
                        type="info"
                        show-icon
                        :closable="false"
                    >
                    </el-alert>
                    <media-uploader ref="media" :id="request_id" :audit_id="audit_id" type="requests" layout="grid" v-model="media" :upload-options="uploadOptions" />
                </card>
            </el-col>
        </el-row>

    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import RequestsMixin from 'mixins/adminRequestsMixin';
    import {displayError} from "helpers/messages";
    import AddActions from 'components/EditViewActions';
    import EditorConfig from 'mixins/adminEditorConfig';
    import { mapActions } from 'vuex';

    export default {
        name: 'AdminRequestsEdit',
        mixins: [RequestsMixin({
            mode: 'add'
        }), EditorConfig],
        components: {
            Heading,
            Card,
            AddActions,
        },
        data() {
            return {
                couldSaveWithService: false,
            }
        },
        methods: {
            ...mapActions(['getRequestCategoriesTree']),
            async saveWithService(serviceAttachModel) {
                try {
                    const resp = await this.saveRequest();

                    if (resp.data.id) {
                        this.model.id = resp.data.id;
                        await this.sendServiceMail(serviceAttachModel);
                        this.setSelectedServiceRequest({});
                    }

                } catch (err) {
                    displayError(err);
                } finally {
                    this.loading.state = false;
                }
            },
            changePublic(val) {
                if(val == true)
                    this.model.visibility = 1;
            },
        },
        async created(){
            this.model['priority'] = '';
            this.validationRules['priority'] = [{
                required: true,
                message: this.$t('validation.general.required')
            }];
        }
        
    };
</script>

<style lang="scss" scoped>
    .services-edit {
        .heading {
            margin-bottom: 20px;
        }
    }

    .custom-select {
        display: block;
    }
</style>
<style lang="scss">
    .label-block .el-form-item__label {
        display: block;
        float: none;
        text-align: left;
    }

    #add_request {
        .el-input__suffix {
            height: 120px !important;
        }
        .el-select__tags {
            top: 70% !important;
        }
    }

</style>
<style>
    .el-button > i {
        margin-right: 5px;
    }
</style>
