<template>
    <div class="services-edit" v-loading.fullscreen.lock="loading.state">
        <heading :title="$t('models.request.add_title')" icon="icon-chat-empty" shadow="heavy">
            <add-actions :saveAction="submit" route="adminRequests" editRoute="adminRequestsEdit"/>
        </heading>
        <el-row :gutter="20" class="crud-view" id="add_request">
            <el-col :md="12">
                <el-form :model="model" label-position="top" :rules="validationRules" ref="form">
                <card >
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
                                        :label="$t(`models.request.category_list.${category.name}`)"
                                        :value="category.id"
                                        v-for="category in categories">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" v-if="this.showSubCategory == true">
                            <el-form-item :label="$t('models.request.defect_location.label')" 
                                        :rules="validationRules.sub_category"
                                        prop="sub_category_id">
                                <el-select :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t(`general.placeholders.select`)"
                                            class="custom-select"
                                            v-model="model.sub_category_id"
                                            @change="changeSubCategory">
                                    <el-option
                                        :key="category.id"
                                        :label="$t(`models.request.sub_category.${category.name}`)"
                                        :value="category.id"
                                        v-for="category in sub_categories">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" 
                                v-if="this.showSubCategory == true && this.showLocation == true && this.showRoom == false">
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
                                v-if="this.showSubCategory == true && this.showRoom == true && this.showLocation == false">
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
                        <el-col :md="12" v-if="this.showCapturePhase == true">
                            <el-form-item :label="$t('models.request.category_options.capture_phase')">
                                <el-select :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t(`general.placeholders.select`)"
                                            class="custom-select"
                                            v-model="model.capture_phase">
                                    <el-option
                                            :key="phase.value"
                                            :label="phase.name"
                                            :value="phase.value"
                                            v-for="phase in capture_phases">
                                        </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" v-if="this.showComponent == true">
                            <el-form-item :label="$t('models.request.category_options.component')">
                                <el-input v-model="model.component"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12"
                                v-if="this.showAction == true">
                            <el-form-item :label="$t('models.request.action.label')"
                                            prop="action">
                                <el-select :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t('models.request.placeholders.action')"
                                            class="custom-select"
                                            v-model="model.action">
                                    <el-option
                                            :key="action.value"
                                            :label="action.name"
                                            :value="action.value"
                                            v-for="action in actions">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" v-if="this.showCostImpact == true">
                                    <el-form-item :label="$t('models.request.cost_impact.label')"
                                            prop="cost_impact">
                                <el-select :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t('models.request.placeholders.cost_impact')"
                                            class="custom-select"
                                            v-model="model.cost_impact"
                                            @change="changeCostImpact">
                                    <el-option
                                            :key="cost_impact.value"
                                            :label="cost_impact.name"
                                            :value="cost_impact.value"
                                            v-for="cost_impact in cost_impacts">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" v-if="this.showPercent == true">
                            <el-form-item 
                                :label="$t('models.request.category_options.payer_percent')"
                                :rules="validationRules.percentage"
                                prop="percentage">
                                <el-input 
                                    type="number"
                                    v-model="model.percentage" 
                                >
                                    <template slot="prepend">%</template>
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" v-if="this.showQualification == true">
                            <el-form-item :label="$t('models.request.category_options.qualification_category')">
                                <el-select 
                                    :disabled="$can($permissions.update.serviceRequest)"
                                    :placeholder="$t(`general.placeholders.select`)"
                                    class="custom-select"
                                    v-model="model.qualification_category"
                                >
                                    <el-option
                                        :key="qualification.value"
                                        :label="qualification.name"
                                        :value="qualification.value"
                                        v-for="qualification in qualification_categories">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <!-- <el-col :md="6" v-if="this.showPayer == true">
                            <el-form-item 
                                :label="$t('models.request.category_options.payer_percent')"
                                :rules="validationRules.payer_percent"
                                prop="payer_percent">
                                <el-input 
                                    type="number"
                                    v-model="model.payer_percent" 
                                >
                                    <template slot="prepend">%</template>
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :md="6" v-if="this.showPayer == true">
                            <el-form-item 
                                :label="$t('models.request.category_options.payer_amount')"
                                :rules="validationRules.payer_amount"
                                prop="payer_amount">
                                <el-input 
                                    type="number"
                                    v-model="model.payer_amount" 
                                >
                                    <template slot="prepend">CHF</template>
                                </el-input>
                            </el-form-item>
                        </el-col> -->
                        <!-- <el-col :md="12">
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
                        </el-col> -->
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
                            <!-- <el-form-item :label="$t('models.request.public_desc')"
                                        prop="is_public"
                            >
                                <el-switch v-model="model.is_public" @change="changePublic"/>
                            </el-form-item> -->
                            <div class="switch-wrapper">
                                <el-form-item :label="$t('models.request.public_title')" prop="is_public">
                                    <el-switch v-model="model.is_public" @change="changePublic"/>
                                </el-form-item>
                                <div class="switcher__desc">
                                    {{ $t('models.request.public_desc') }}
                                </div>
                            </div>
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
                            <div class="switch-wrapper">
                                <el-form-item :label="$t('models.request.send_notification_title')" prop="send_notification">
                                    <el-switch v-model="model.send_notification"/>
                                </el-form-item>
                                <div class="switcher__desc">
                                    {{ $t('models.request.send_notification_desc') }}
                                </div>
                            </div>
                            <!-- <el-form-item :label="$t('models.request.send_notification_desc')"
                                        prop="send_notification"
                            >
                                <el-switch v-model="model.send_notification"/>
                            </el-form-item> -->
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
                        <!-- <el-col :md="12" v-if="model.resident">
                            <el-form-item>
                                <label slot="label">
                                    {{$t('general.resident')}}
                                </label>
                                <router-link :to="{name: 'adminResidentsEdit', params: {id: model.resident.id}}">
                                    {{model.resident.first_name}} {{model.resident.last_name}}
                                </router-link>
                            </el-form-item>
                        </el-col> -->
                        <el-col :md="12">
                            <el-form-item :label="$t('general.resident')" :rules="validationRules.resident_id" prop="resident_id">
                                <el-select
                                    :loading="remoteLoading"
                                    :placeholder="$t('models.request.placeholders.resident')"
                                    :remote-method="remoteSearchResidents"
                                    filterable 
                                    remote
                                    reserve-keyword
                                    style="width: 100%;"
                                    @change="changeResident"
                                    v-model="model.resident_id">
                                    <el-option
                                        :key="resident.id"
                                        :label="resident.name"
                                        :value="resident.id"
                                        v-for="resident in residents"/>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="24" v-if="model.resident_id && relations.length > 1">
                            <el-form-item :label="$t('models.resident.relation.title')" :rules="validationRules.relation_id"
                                              prop="relation_id">
                                <el-select v-model="model.relation_id" 
                                            :placeholder="$t('resident.placeholder.relation')"
                                            class="custom-select resident-select">
                                    <el-option v-for="relation in relations" 
                                                :key="relation.id" 
                                                :label="relation.building_room_floor_unit" 
                                                :value="relation.id" >
                                            <!-- <span class="status-icon" :style="{ background: constants.relations.status_colorcode[relation.status], border: '2px solid ' + getLightenDarkenColor(constants.relations.status_colorcode[relation.status], 200) + '59' }" >&nbsp;</span> -->
                                            <span class="status-icon" :style="{ background: constants.relations.status_colorcode[relation.status], border: '2px solid #ffffffe7'}" >&nbsp;</span>
                                            <!-- <span><i class="icon-dot-circled" :class="[constants.relations.status[relation.status] === 'active' ? 'icon-success' : 'icon-danger']"></i></span> -->
                                            <span>{{ relation.building_room_floor_unit }}</span>
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="24">
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
    import globalFunction from "helpers/globalFunction";
    import {mapActions, mapGetters} from 'vuex';

    export default {
        name: 'AdminRequestsEdit',
        mixins: [RequestsMixin({
            mode: 'add'
        }), EditorConfig, globalFunction],
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
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
        },
        methods: {
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
            // this.model['priority'] = '';
            // this.validationRules['priority'] = [{
            //     required: true,
            //     message: this.$t('validation.general.required')
            // }];
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

    /deep/ i.icon-success {
        color: #5fad64;
    }
    /deep/ i.icon-danger {
        color: #dd6161;
    }
</style>
<style lang="scss">
    .label-block .el-form-item__label {
        display: block;
        float: none;
        text-align: left;
    }

    #add_request {

        .el-form-item__content {
            .el-input.el-input-group {
                .el-input-group__prepend {
                    padding: 2px 8px 0;
                    font-weight: 600;
                }
            }
        }

        .el-select__tags {
            //top: 70% !important;
        }

        .switch-wrapper {
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

        }
        
    }

    .status-icon {
        width: 13px;
        height: 13px;
        border-radius: 50%;
        display: block;
        margin-right: 5px;
    }

    li.el-select-dropdown__item {
        display: flex;
        align-items: center;
    }
</style>
<style>
    /* .el-button > i {
        margin-right: 5px;
    } */
</style>
