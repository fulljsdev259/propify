<template>
    <div class="services-edit mb20" v-if="constants" v-loading.fullscreen.lock="loading.state">
        <heading :title="$t('models.request.edit_title')" icon="icon-chat-empty" shadow="heavy">
            <template slot="description" v-if="model.request_format">
                <div class="subtitle">{{model.request_format}}</div>
            </template>
            <el-button
                    @click="downloadPDF"
                    size="mini"
                    type="primary"
                    round
                    class="download-pdf"
            >
                {{ $t('models.request.download_pdf.title') }}
            </el-button>
            <edit-actions :saveAction="submit" :deleteAction="deleteRequest" route="adminRequests"/>
        </heading>
        <div class="crud-view" id="edit_request">
            <el-form :model="model" label-position="top" label-width="192px" ref="form">
                <el-row :gutter="20">
                    <el-col :md="12">
                        <card  :header="$t('models.request.request_details')" id="request_details">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.request.category')"
                                                  :rules="validationRules.category"
                                                  prop="category_id">
                                        <el-select
                                            :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t('models.request.placeholders.category')"
                                            class="custom-select"
                                            v-model="model.category_id"
                                            @change="changeCategory"
                                        >
                                            <el-option
                                                :key="category.id"
                                                :label="$t(`models.request.category_list.${category.name}`)"
                                                :value="category.id"
                                                v-for="category in categories">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12"
                                        v-if="this.showSubCategory == true">
                                    <el-form-item :label="$t('models.request.defect_location.label')">
                                        <el-select
                                            :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t(`general.placeholders.select`)"
                                            class="custom-select"
                                            v-model="model.sub_category_id"
                                            @change="changeSubCategory"
                                        >
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
                                        v-if="this.showSubCategory == true && this.showLiegenschaft == true">
                                    <el-form-item :label="$t('models.request.category_options.range')">
                                        <el-select 
                                            :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t(`general.placeholders.select`)"
                                            class="custom-select"
                                            v-model="model.location"
                                        >
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
                                        v-if="this.showSubCategory == true && this.showWohnung == true">
                                    <el-form-item :label="$t('models.request.category_options.room')">
                                        <el-select 
                                            :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t(`general.placeholders.select`)"
                                            class="custom-select"
                                            v-model="model.room"
                                        >
                                            <el-option
                                                :key="room.value"
                                                :value="room.value"
                                                :label="room.name"
                                                v-for="room in rooms">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12" v-if="this.showAcquisition == true">
                                    <el-form-item :label="$t('models.request.category_options.acquisition')">
                                        <el-select 
                                            :disabled="$can($permissions.update.serviceRequest)"
                                            :placeholder="$t(`general.placeholders.select`)"
                                            class="custom-select"
                                            v-model="model.capture_phase"
                                        >
                                            <el-option
                                                :key="acquisition.value"
                                                :label="acquisition.name"
                                                :value="acquisition.value"
                                                v-for="acquisition in acquisitions">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12" v-if="this.showSubCategory == true">
                                    <el-form-item :label="$t('models.request.category_options.component')">
                                        <el-input v-model="model.component"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12"
                                        v-if="model.category_id && selectedCategoryHasQualification(model.category_id)">
                                    <el-form-item :label="$t('models.request.qualification.label')"
                                                  :rules="validationRules.qualification"
                                                  prop="qualification">
                                        <el-select :disabled="$can($permissions.update.serviceRequest)"
                                                   :placeholder="$t('models.request.placeholders.qualification')"
                                                   class="custom-select"
                                                   v-model="model.qualification"
                                                   @change="changeQualification">
                                            <el-option
                                                :key="k"
                                                :label="$t(`models.request.qualification.${qualification}`)"
                                                :value="parseInt(k)"
                                                v-for="(qualification, k) in constants.requests.qualification">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12" v-if="model.category_id && selectedCategoryHasQualification(model.category_id) && this.showPayer == true">
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
                                    <el-form-item :label="$t('models.request.category_options.keywords')">
                                        <el-select
                                            v-model="model.keywords"
                                            multiple
                                            filterable
                                            allow-create
                                            default-first-option
                                            @remove-tag="deleteTag"
                                            style="display:block"
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
                            </el-row>
                            <el-row :gutter="20" class="summary-row" style="margin-bottom: 0;padding-bottom: 0;">
                                <el-col :md="8" class="summary-item" id="resident">
                                    <el-form-item v-if="model.resident">
                                        <label slot="label">
                                            {{$t('general.resident')}}
                                        </label>
                                        <router-link :to="{name: 'adminResidentsView', params: {id: model.resident.id}}"
                                                     class="resident-link">
                                            <avatar :size="30"
                                                    :src="'/' + model.resident.user.avatar"
                                                    v-if="model.resident.user.avatar"></avatar>
                                            <avatar :size="28"
                                                    :username="model.resident.user.first_name ? `${model.resident.user.first_name} ${model.resident.user.last_name}`: `${model.resident.user.name}`"
                                                    backgroundColor="rgb(205, 220, 57)"
                                                    color="#fff"
                                                    v-if="!model.resident.user.avatar"></avatar>
                                            <span>{{model.resident.first_name}} {{model.resident.last_name}}</span>
                                        </router-link>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="8" class="summary-item" id="building">
                                    <el-form-item :label="$t('general.assignment_types.building')">
                                        <strong>{{this.model.building}}</strong>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="8" class="summary-item" id="createtime">
                                    <el-form-item :label="$t('general.created_at')">
                                        <strong>{{this.model.created_at}}</strong>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20" class="summary-row">
                                <!-- <el-col :md="8" class="summary-item">
                                    <el-form-item :label="$t('models.request.priority.label')">
                                        <strong v-if="$constants.requests.priority[model.priority]">{{$t(`models.request.priority.${$constants.requests.priority[model.priority]}`)}}</strong>
                                    </el-form-item>
                                </el-col> -->
                                <!-- <el-col :md="8" class="summary-item">
                                    <el-form-item :label="$t('models.resident.contract.title')" v-if="this.model.contract">
                                        {{this.model.contract.building_id + " -- " + this.model.contract.unit_id}}
                                    </el-form-item>

                                    <el-form-item :label="$t('models.resident.contract.title')" :rules="validationRules.contract_id"
                                                v-else
                                                prop="contract_id">
                                        <el-select v-model="model.contract_id" 
                                                    :placeholder="$t('resident.placeholder.contract')"
                                                    class="custom-select">
                                            <el-option v-for="contract in dirtyContracts" 
                                                        :key="contract.id" 
                                                        :label="contract.building_room_floor_unit" 
                                                        :value="contract.id" />
                                        </el-select>
                                    </el-form-item>

                                </el-col> -->

                                <!-- <el-col :md="8" class="summary-item">
                                    <el-form-item :label="$t('models.request.visibility.label')">
                                        <strong>{{$constants.requests.visibility[model.visibility]}}</strong>
                                    </el-form-item>
                                </el-col> -->
                            </el-row>

                            <el-tabs type="card" v-model="activeTab1">

                                <el-tab-pane :label="$t('models.request.request_details')" name="request_details">
                                    <el-form-item :label="$t('models.request.prop_title')" :rules="validationRules.title"
                                                  prop="title">
                                        <el-input :disabled="$can($permissions.update.serviceRequest)" type="text"
                                                  v-model="model.title"/>
                                    </el-form-item>
                                    <el-form-item :label="$t('general.description')" :rules="validationRules.description"
                                                  prop="description"
                                                  :key="editorKey">
                                        <yimo-vue-editor
                                                :config="editorConfig"
                                                v-model="model.description"/>
                                    </el-form-item>
                                </el-tab-pane>

                                <el-tab-pane name="request_images">
                                    <span slot="label">
                                        <el-badge :value="mediaCount" :max="99" class="admin-layout">{{ $t('models.request.images') }}</el-badge>
                                    </span>
                                    <!-- <el-alert
                                        v-if="( !media || media.length == 0) && mediaCount == 0"
                                        :title="$t('models.request.no_images_message')"
                                        type="info"
                                        show-icon
                                        :closable="false"
                                    >
                                    </el-alert> -->
                                    <!-- <upload-document
                                        @fileUploaded="uploadFiles"
                                        class="drag-custom mt15"
                                        drag
                                        multiple
                                    />
                                    <div class="mt15">
                                        <request-media :data="[...model.media, ...media]" @deleteMedia="deleteMedia"
                                                       v-if="media.length || (model.media && model.media.length)"></request-media>
                                    </div> -->
                                    <span class="image-tab-title">Files</span>
                                    <ui-media-gallery :files="model.media.map(({url}) => url)" @delete-media="deleteMediaByIndex" :show-description="false"/>
                                    <span class="image-tab-title">Upload</span>
                                    <el-alert
                                        :title="$t('general.upload_all_desc')"
                                        type="info"
                                        show-icon
                                        :closable="false"
                                    >
                                    </el-alert>
                                    <media-uploader ref="media" :id="request_id" :audit_id="audit_id" type="requests" layout="grid" v-model="media" :upload-options="uploadOptions" />
                                </el-tab-pane>

                            </el-tabs>

                            <!--                            <el-form-item-->
                            <!--                                :label="$t('models.request.is_public')"-->
                            <!--                                class="switch-item"-->
                            <!--                                prop="is_public"-->
                            <!--                                style=""-->
                            <!--                            >-->
                            <!--                                <el-switch-->
                            <!--                                    :disabled="$can($permissions.update.serviceRequest)"-->
                            <!--                                    style="margin-left: 5px;"-->
                            <!--                                    v-model="model.is_public"-->
                            <!--                                >-->
                            <!--                                </el-switch>-->
                            <!--                            </el-form-item>-->
                            <!--                            <small>{{$t('models.request.public_legend')}}</small>-->
                        </card>
                        <template v-if="$can($permissions.update.serviceRequest)">
                            <card class="mt15" v-if="model.id">
                                <el-divider class="column-divider" content-position="left">
                                    {{$t('models.request.conversation')}}
                                </el-divider>
                                <el-table
                                    :data="conversations"
                                    style="width: 100%">
                                    <el-table-column
                                        :label="$t('general.name')"
                                        prop="user.name"
                                    >
                                    </el-table-column>
                                    <el-table-column
                                        width="100px"
                                    >
                                        <template slot-scope="scope">
                                            <el-button @click="openConversation(scope.row)" size="mini" type="primary">
                                                {{$t('models.request.open_conversation')}}
                                            </el-button>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </card>
                            <el-dialog
                                :visible.sync="conversationVisible"
                                width="50%">
                                <chat :id="selectedConversation.id" type="conversation"
                                      v-if="selectedConversation.id" show-templates />
                            </el-dialog>
                        </template>

                    </el-col>
                    <el-col :md="12">
                        <template v-if="$can($permissions.assign.request)">
 
                            <el-tabs class="action-tabs" type="border-card"  v-model="activeActionTab">
                                <el-tab-pane :label="$t('models.request.actions')" name="actions" >
                                    <el-row :gutter="10">                                    
                                        <el-col :md="12">
                                            <el-form-item :label="$t('models.request.status.label')"
                                                        :rules="validationRules.status"
                                                        prop="status">
                                                <el-select :placeholder="$t('models.request.placeholders.status')"
                                                        class="custom-select"
                                                        v-model="model.status">
                                                    <el-option
                                                        :key="k"
                                                        :label="$t(`models.request.status.${status}`)"
                                                        :value="parseInt(k)"
                                                        v-for="(status, k) in constants.requests.status">
                                                    </el-option>
                                                </el-select>
                                            </el-form-item>
                                        </el-col>
                                        
                                        <!-- <el-col :md="12">
                                            <el-form-item :label="$t('models.request.internal_priority.label')"
                                                        :rules="validationRules.internal_priority"
                                                        prop="internal_priority">
                                                <el-select :placeholder="$t('models.request.internal_priority.label')" class="custom-select" v-model="model.internal_priority">
                                                    <el-option
                                                        :key="k"
                                                        :label="$t(`models.request.internal_priority.${priority}`)"
                                                        :value="parseInt(k)"
                                                        v-for="(priority, k) in $constants.requests.internal_priority">
                                                    </el-option>
                                                </el-select>
                                            </el-form-item>
                                        </el-col> -->
                                        <el-col :md="12">
                                            <el-form-item :label="$t('models.request.due_date')"
                                                        class="due_date-field"
                                                        :rules="validationRules.due_date">
                                                <template slot="label" class="el-form-item__label">
                                                    {{$t('models.request.due_date')}}
                                                    <div class="reminder-box">
                                                        <label class="switcher__label">
                                                            {{$t('models.request.active_reminder_switcher')}}
                                                        </label>
                                                        <el-switch v-model="model.active_reminder"/>
                                                    </div>
                                                </template>
                                                
                                                <el-date-picker
                                                    :disabled="$can($permissions.update.serviceRequest)"
                                                    :placeholder="$t('models.request.placeholders.due_date')"
                                                    format="dd.MM.yyyy"
                                                    style="width: 100%"
                                                    type="date"
                                                    v-model="model.due_date"
                                                    :picker-options="dueDatePickerOptions"
                                                    value-format="yyyy-MM-dd"
                                                >
                                                </el-date-picker>
                                            </el-form-item>
                                            
                                        </el-col>
                                    </el-row>
                                    <el-row :gutter="10"> 
                                        <el-col :md="12" v-if="model.active_reminder">
                                            <el-form-item :label="$t('models.request.days_left')"
                                                        prop="days_left">
                                                <el-input v-model="model.days_left"></el-input>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :md="12" v-if="model.active_reminder">
                                            <el-form-item :label="$t('models.request.send_person')"
                                                        prop="person_id">
                                                <el-select
                                                    :loading="remoteLoading"
                                                    :placeholder="$t('models.request.placeholders.person')"
                                                    :remote-method="remoteSearchPersons"
                                                    filterable
                                                    remote
                                                    reserve-keyword
                                                    style="width: 100%;"
                                                    v-model="model.person_id">
                                                    <el-option
                                                        :key="person.id"
                                                        :label="person.name"
                                                        :value="person.id"
                                                        v-for="person in persons"/>
                                                </el-select>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                </el-tab-pane>
                                <el-tab-pane :label="$t('models.request.is_public')" name="is_public">
                                    <div class="switch-wrapper">
                                        <el-form-item :label="$t('models.request.public_title')" prop="is_public">
                                            <el-switch v-model="model.is_public"/>
                                        </el-form-item>
                                        <div class="switcher__desc">
                                            {{ $t('models.request.public_desc') }}
                                        </div>
                                    </div>
                                    <el-form-item class="switcher" prop="visibility" v-if="model.is_public && model.resident.building && model.resident.building.quarter_id > 0">
                                        <label class="switcher__label">
                                            {{$t('models.request.visibility_title')}}
                                            <span class="switcher__desc">{{$t('models.request.visibility_desc')}}</span>
                                        </label>
                                        <div>
                                            <el-select v-model="model.visibility">
                                                <el-option :key="k" :label="$t(`models.request.visibility.${visibility}`)" :value="parseInt(k)" v-for="(visibility, k) in $constants.requests.visibility">
                                                </el-option>
                                            </el-select>
                                        </div>
                                    </el-form-item>
                                    <div v-if="model.is_public" class="switch-wrapper">
                                        <el-form-item :label="$t('models.request.send_notification_title')" prop="send_notification">
                                            <el-switch v-model="model.send_notification"/>
                                        </el-form-item>
                                        <div class="switcher__desc">
                                            {{ $t('models.request.send_notification_desc') }}
                                        </div>
                                    </div>
                                </el-tab-pane>
                            </el-tabs>
                        
                            <card class="mt15 request" :header="$t('models.request.assignment')">
                                <assignment-by-type
                                    :resetToAssignList="resetToAssignList"
                                    :assignmentType.sync="assignmentType"
                                    :toAssign.sync="toAssign"
                                    :assignmentTypes="assignmentTypes"
                                    :assign="assignUser"
                                    :toAssignList="toAssignList"
                                    :remoteLoading="remoteLoading"
                                    :remoteSearch="remoteSearchAssignees"
                                />
                                <relation-list
                                    :actions="assigneesActions"
                                    :columns="assigneesColumns"
                                    :filterValue="model.id"
                                    fetchAction="getAssignees"
                                    filter="request_id"
                                    ref="assigneesList"
                                    v-if="model.id"
                                />
                            </card>
                        </template>
                        <!--                    v-if="(!$can($permissions.update.serviceRequest)) || ($can($permissions.update.serviceRequest) && (media.length || (model.media && model.media.length)))"-->
                        <card class="mt15" v-if="model.id" id="comments">
                            <el-tabs id="comments-card" v-model="activeTab2"  @tab-click="adjustAuditTabPadding">
                                <el-tab-pane name="comments">
                                    <span slot="label">
                                        <el-badge :value="requestCommentCount" :max="99" class="admin-layout">{{ $t('models.request.comments') }}</el-badge>
                                    </span>
                                    <chat :id="model.id" type="request" show-templates />
                                </el-tab-pane>
                                <el-tab-pane name="internal-notices">
                                    <span slot="label">
                                        <el-badge :value="noticeCommentCount" :max="99" class="admin-layout">{{ $t('models.request.internal_notices') }}</el-badge>
                                    </span>
                                    <chat :id="model.id" type="internalNotices" />
                                </el-tab-pane>
                                <el-tab-pane name="audit" style="height: 400px;overflow:auto;">
                                    <span slot="label">
                                        {{ $t('general.audits') }}
                                        <!-- <el-badge :value="auditCount" :max="99" class="admin-layout">{{ $t('general.audits') }}</el-badge> -->
                                    </span>
                                    <audit v-if="model.id" :id="model.id" type="request" showFilter/>
                                </el-tab-pane>
                            </el-tabs>
                        </card>
                    </el-col>
                </el-row>
            </el-form>
        </div>
        <ServiceDialog
            :request_id="model.id"
            :address="address"
            :conversations="conversations"
            :mailSending="mailSending"
            :managers="model.property_managers"
            :providers="model.service_providers"
            :selectedServiceRequest="selectedServiceRequest"
            :showServiceMailModal="showServiceMailModal"
            :requestData="selectedRequestData"
            @close="closeMailModal"
            @send="sendServiceMail"
            v-if="(model.service_providers && model.service_providers.length) || (model.property_managers && model.property_managers.length)"
        />

    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import RequestsMixin from 'mixins/adminRequestsMixin';
    import ServiceModalMixin from 'mixins/adminServiceModalMixin';
    import {mapActions} from 'vuex';
    import RelationList from 'components/RelationListing';
    import EditActions from 'components/EditViewActions';
    import ServiceDialog from 'components/ServiceAttachModal';
    import {displaySuccess, displayError} from "../../../helpers/messages";
    import {Avatar} from 'vue-avatar';    
    import AssignmentByType from 'components/AssignmentByType';
    import Vue from 'vue';
    import { EventBus } from '../../../event-bus.js';
    import EditorConfig from 'mixins/adminEditorConfig';

    export default {
        name: 'AdminRequestsEdit',
        mixins: [RequestsMixin({
            mode: 'edit'
        }), ServiceModalMixin({
            mode: 'edit'
        }), EditorConfig],
        components: {
            Heading,
            Card,
            ServiceDialog,
            RelationList,
            EditActions,
            Avatar,            
            AssignmentByType,
        },
        data() {
            return {
                requestCommentCount: 0,
                auditCount: 0,
                noticeCommentCount: 0,
                activeTab1: 'request_details',
                activeTab2: 'comments',
                activeActionTab: 'actions',
                conversationVisible: false,
                selectedConversation: {},
                constants: this.$constants,
                assigneesColumns: [{
                    type: 'assignProviderManagerAvatars',
                    width: 70,
                }, {
                    type: 'assigneesName',
                    prop: 'name',
                    label: 'general.name'
                }, {
                    prop: 'type',
                    label: 'models.request.user_type.label',
                    i18n: this.translateType
                }],
                assigneesActions: [{
                    width: '180px',
                    buttons: [{
                        title: 'models.request.notify',
                        tooltipMode: true,
                        type: 'success',
                        icon: 'el-icon-message',
                        view: 'request',
                        onClick: this.openNotifyProvider
                    }, {
                        title: 'general.unassign',
                        tooltipMode: true,
                        type: 'danger',
                        icon: 'el-icon-close',
                        onClick: this.notifyUnassignment
                    }]
                }],
                rolename: null,
                inputVisible: false,
            }
        },
        computed: {
            visibilities() {
                if (this.model.resident && this.model.resident.building && this.model.resident.building.quarter_id) {
                    return this.constants.requests.visibility;
                } else {
                    return Object.keys(this.constants.requests.visibility)
                        .filter(key => key != 3)
                        .reduce((obj, key) => {
                            obj[key] = this.constants.requests.visibility[key];
                            return obj;
                        }, {});
                }
            },
            selectedRequestData() {
                console.log('sub', this.model.sub_category_id)
                return {
                    resident: this.model.resident,
                    request_format: this.model.request_format,
                    category: (this.model.sub_category_id == null) ? this.$t(`models.request.category_list.${this.model.category.name}`) : this.$t(`models.request.category_list.${this.model.category.name}`) + " > " + this.$t(`models.request.sub_category.${this.model.sub_category.name}`)
                }
            },
            mediaCount() {
                if(this.model.media) {
                    return this.model.media.length;
                } else {
                    return 0;
                }
            }
        },
        async mounted() {
            this.rolename = this.$store.getters.loggedInUser.roles[0].name;
            this.$root.$on('changeLanguage', () => {
                //this.fetchCurrentRequest();
            });
            EventBus.$on('request-comment-count', request_comment_count => {
                this.requestCommentCount = request_comment_count;
            });
            EventBus.$on('request-comment-deleted', () => {
                this.requestCommentCount--;
            });
            EventBus.$on('request-comment-added', () => {
                this.requestCommentCount++;
            });
            EventBus.$on('notice-comment-count', notice_comment_count => {
                this.noticeCommentCount = notice_comment_count;
            });
            EventBus.$on('notice-comment-deleted', () => {
                this.noticeCommentCount--;
            });
            EventBus.$on('notice-comment-added', () => {
                this.noticeCommentCount++;
            });
            EventBus.$on('audit-get-counted', audit_count => {
                this.auditCount = audit_count;
            });

        },
        methods: {
            ...mapActions(['unassignAssignee', 'deleteRequest', 'downloadRequestPDF']),
            translateType(type) {
                return this.$t(`models.request.user_type.${type}`);
            },
            isDisabled(status) {
                return _.indexOf(this.constants.requests.statusByAgent[this.model.status], parseInt(status)) < 0
            },
            notifyUnassignment(provider) {
                this.$confirm(this.$t(`general.swal.confirm_change.title`), this.$t('general.swal.confirm_change.warning'), {
                    confirmButtonText: this.$t(`general.swal.confirm_change.confirm_btn_text`),
                    cancelButtonText: this.$t(`general.swal.confirm_change.cancel_btn_text`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading.status = true;
                        let resp;

                        const payload = {
                            toAssignId: provider.id
                        };

                        
                        resp = await this.unassignAssignee(payload)
                        

                        if (resp && resp.data) {
                            await this.fetchCurrentRequest();
                            this.$refs.assigneesList.fetch();
                            const detachedType = provider.uType === 1 ? 'service' : 'manager';
                            displaySuccess(resp.data);
                        }
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.loading.status = false;
                    }
                }).catch(async () => {
                    this.loading.status = false;
                });
            },
            openNotifyProvider(provider) {
                this.selectedServiceRequest = provider;
                this.showServiceMailModal = true;
            },
            openConversation(row) {
                this.selectedConversation = {};
                this.$nextTick(() => {
                    this.selectedConversation = row;
                    this.conversationVisible = true;
                })
            },
            adjustAuditTabPadding(tab){
                var active_bar = document.querySelector('#comments-card .el-tabs__active-bar');
                
                if(tab.name == 'internal-notices') {
                    setTimeout( () =>  {
                        active_bar.style.width = '120px'
                    },0)
                }
                
                if(tab.name == 'audit') {
                    setTimeout( () => { active_bar.style.transform = 'translateX(265px)' }, 0)
                }
            },
            async downloadPDF() {
                this.loading.state = true;
                try {
                    console.log('this.model.id', this.model.id)
                    const resp = await this.downloadRequestPDF({id: this.model.id});
                    if (resp && resp.data) {
                        const url = window.URL.createObjectURL(new Blob([resp.data], {type: resp.headers['content-type']}));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', resp.headers['content-disposition'].split('filename=')[1]);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(url);
                    }
                } catch (e) {
                    displayError(e)
                } finally {
                    this.loading.state = false;
                }
            }
        },
    };
</script>
<style lang="scss" scoped>
    .download-pdf {
        margin-right: 5px;
    }

    .services-edit {
        .heading {
            margin-bottom: 20px;
        }
    }

    .custom-select {
        display: block;
    }

    .resident-link {
        display: flex;
        align-items: center;
        color: var(--primary-color);
        text-decoration: none;

        &:hover {
            color: var(--primary-color-lighter);
        }

        & > span {
            margin-left: 5px;
        }
    }

    /deep/ .ql-container.ql-snow .ql-editor {
        min-height: 300px;
    }

    .ui-media-gallery {
        margin-bottom: 10px;
    }
</style>

<style lang="scss">

    .switch-item {
        display: flex;
        margin: 0;
        padding: 0;

        .el-form-item__label, .el-form-item__content {
            line-height: 20px;
        }
    }

    #tab-audit{
        padding-left:40px;
    }

    .summary-row {
        background-color: #F3F3F3;
        padding: 2%;
        margin-left: 0px !important;
        margin-right: 0px !important;
        margin-bottom: 15px;
        .el-form-item {
            margin-bottom: 0px !important;
            .el-form-item__content {
                line-height: 28px !important;
                strong {
                    color: gray;
                }
            }
        }

        &:first-child {
            margin-bottom: 0;
        }

        .summary-item {
            
            .el-form-item {
                margin-bottom: 0px !important;
                .el-form-item__content {
                    line-height: 28px !important;
                }
            }
        }
    }

    #tab-request_images {
        padding-right: 40px !important;
    }



    .el-tag + .el-tag {
        margin-left: 10px;
    }
    .button-new-tag {
        margin-left: 10px;
        height: 32px;
        line-height: 30px;
        padding-top: 0;
        padding-bottom: 0;
    }
    .input-new-tag {
        width: 90px;
        margin-left: 10px;
        vertical-align: bottom;
    }

    $min-width: 991px;
    $max-width: 1228px;
    @media only screen and (min-width: $min-width) and (max-width: $max-width) {
        #resident {
            .el-form-item {
                .el-form-item__label {
                    min-height: 50px;
                }
            }
        }
        #building {
            .el-form-item {
                .el-form-item__label {
                    min-height: 50px;
                }
            }
        }
        #createtime {
            .el-form-item {
                .el-form-item__label {
                    line-height: 25px;
                }
            }
        }
    }
    @media only screen and (max-width: $min-width) {
        #resident {
            .el-form-item {
                .el-form-item__label {
                    min-height: 40px !important;
                }
            }
        }
        #building {
            .el-form-item {
                .el-form-item__label {
                    min-height: 40px !important;
                }
            }
        }
    }

    #edit_request {
        .image-tab-title {
            display: block;
            margin-bottom: 5px;
        }
        .el-form-item {
            margin-bottom: 16px;
        }
        .el-card__body {
            padding: 16px 16px 0px 16px !important;
        }
        .request {
            .el-card__body {
                padding: 16px !important;
            }
        }
        #comments {
            .el-card__body {
                padding: 16px !important;
            }
        }
        .el-tabs--border-card {
            border-radius: 6px;
            .el-tabs__header {
                border-radius: 6px 6px 0 0;
            }
            .el-tabs__nav-wrap.is-top {
                border-radius: 6px 6px 0 0;
            }
        }
        #pane-is_public {

            .switcher {
                .el-form-item__content {
                    display: flex;
                    align-items: center;

                    & > div {
                        flex: 1;
                        justify-content: flex-end;
                        text-align: end;
                        width: 130px
                    }
                    .el-select {
                        width: 130px
                    }
                }
                &__label {
                    text-align: left;
                    line-height: 1.4em;
                    color: #606266;
                }
                &__desc {
                    margin-top: 0.5em;
                    display: block;
                    font-size: 0.9em;
                }

            }
            
        }

        .due_date-field {
            .el-form-item__label {
                width: 100%;
            }
        }

        .reminder-box {
            min-width: 100px;
            display: flex;
            float: right;
            margin-top: 5px;
            justify-content: flex-end;

            .switcher__label {
                margin-top: 5px;
                padding-right: 5px;
            }

            .el-switch {
                margin-top: 0.3em;
            }
        }
            
        .action-tabs {
            border-radius: 6px;
        }
        
    }
    
</style>
