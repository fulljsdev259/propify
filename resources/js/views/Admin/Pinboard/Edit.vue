<template>
    <div id="pinboard-edit-view" class="units-edit mb20">
        <heading :title="$t('models.pinboard.edit_title')" icon="icon-megaphone-1" shadow="heavy" style="margin-bottom: 20px;">
            <edit-actions :saveAction="submit" :deleteAction="deletePinboard" route="adminPinboard"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-form :model="model" label-position="top" label-width="192px" ref="form">
                <el-col :md="12">                
                    <el-card :header="$t('models.property_manager.details_card')" :loading="loading" class="mb20">
                        <el-row :gutter="20" class="mb20">
                            <el-col :lg="8">
                                <el-form-item :label="$t('models.pinboard.type.label')">
                                    <!-- <el-select style="display: block" v-model="model.type">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.pinboard.type.${type}`)"
                                            :value="parseInt(key)"
                                            v-for="(type, key) in pinboardConstants.type">
                                        </el-option>
                                    </el-select> -->
                                    <el-select style="display: block" v-model="model.type">
                                        <el-option
                                            :label="$t(`models.pinboard.type.post`)"
                                            :value="1"
                                        >
                                        </el-option>
                                        <el-option
                                            :label="$t(`models.pinboard.type.announcement`)"
                                            :value="3"
                                        >
                                        </el-option>
                                        <el-option
                                            :label="$t(`models.pinboard.type.article`)"
                                            :value="4"
                                            v-if="rolename == 'administrator'"
                                        >
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :lg="8" v-if="this.model.type != 3">
                                <el-form-item :label="$t('models.pinboard.status.label')">
                                    <el-select style="display: block" v-model="model.status">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.pinboard.status.${status}`)"
                                            :value="parseInt(key)"
                                            v-for="(status, key) in pinboardConstants.status">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :lg="16" v-if="this.model.type == 3">
                                <el-row :gutter="20">
                                    <el-col :lg="model.sub_type == 3 ? 12 : 24">
                                        <el-form-item :label="$t('models.pinboard.sub_type.label')">
                                            <el-select style="display: block" v-model="model.sub_type" @change="changeSubType">
                                                <el-option
                                                        :key="key"
                                                        :label="$t(`models.pinboard.sub_type.${subtype}`)"
                                                        :value="parseInt(key)"
                                                        v-for="(subtype, key) in pinboardConstants.sub_type[3]">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :lg="12" v-if="model.sub_type == 3">
                                        <el-form-item :label="$t('models.pinboard.category.label')">
                                            <el-select style="display: block" v-model="model.category">
                                                <el-option
                                                        :key="key"
                                                        :label="$t(`models.pinboard.category.${category}`)"
                                                        :value="parseInt(key)"
                                                        v-for="(category, key) in pinboardConstants.category">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                            </el-col>
                            <el-col :lg="8" v-else>
                                <el-form-item :label="$t('models.pinboard.visibility.label')">
                                    <el-select style="display: block" v-model="model.visibility">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.pinboard.visibility.${visibility}`)"
                                            :value="parseInt(key)"
                                            v-for="(visibility, key) in pinboardConstants.visibility">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                        </el-row>

                        <el-tabs type="card" v-if="this.model.type == 3" v-model="activeTab1">
                            <el-tab-pane :label="$t('general.actions.view')" name="details">
                                <el-form-item :label="$t('models.pinboard.title_label')" :rules="validationRules.title"
                                            prop="title">
                                    <el-input type="text" v-model="model.title"></el-input>
                                </el-form-item>
                                <el-form-item :label="$t('general.content')" :rules="validationRules.content"
                                              prop="content"
                                              :key="editorKey">
                                    <yimo-vue-editor
                                            :config="editorConfig"
                                            v-model="model.content"/>
                                </el-form-item>
                                <!-- <el-form-item v-if="this.model.type == 3 && this.model.sub_type == 3">
                                    <label>{{$t('models.pinboard.category_default_image_label')}}</label>
                                    <el-switch v-model="model.category_image"/>
                                    <el-row :gutter="20">
                                        <img
                                            src="~img/announcement_category/1.png"
                                            class="user-image"
                                            v-if="this.model.category == 1"
                                            width="50%" 
                                            height="50%"/>
                                        <img
                                            src="~img/announcement_category/2.png"
                                            class="user-image"
                                            v-else-if="this.model.category == 2"
                                            width="50%" 
                                            height="50%"/>
                                        <img
                                            src="~img/announcement_category/3.png"
                                            class="user-image"
                                            v-else-if="this.model.category == 3"
                                            width="50%" 
                                            height="50%"/>
                                        <img
                                            src="~img/announcement_category/4.png"
                                            class="user-image"
                                            v-else-if="this.model.category == 4"
                                            width="50%" 
                                            height="50%"/>
                                        <img
                                            src="~img/announcement_category/5.png"
                                            class="user-image"
                                            v-else-if="this.model.category == 5"
                                            width="50%" 
                                            height="50%"/>  
                                    </el-row>  
                                </el-form-item>  -->
                                <!-- <el-form-item :label="model.type == 3 ? $t('models.pinboard.attachments') : $t('models.pinboard.images')">
                                    <upload-document @fileUploaded="uploadFiles" class="drag-custom" drag multiple />   
                                    <div class="mt15" v-if="media.length || (model.media && model.media.length)">
                                        <request-media :data="[...model.media, ...media]" @deleteMedia="deleteMedia"
                                                       v-if="media.length || (model.media && model.media.length)"></request-media>
                                    </div>
                                </el-form-item> -->
                                <ui-media-gallery :files="model.media.map(({url}) => url)" @delete-media="deleteMediaByIndex" :show-description="false"/>
                                <el-alert
                                    :title="$t('general.upload_all_desc')"
                                    type="info"
                                    show-icon
                                    :closable="false"
                                >
                                </el-alert>
                                <media-uploader ref="media" :id="pinboard_id" :audit_id="audit_id" type="requests" layout="grid" v-model="media" :upload-options="uploadOptions" />
                                
                            </el-tab-pane>
                            <el-tab-pane name="comments">
                                <span slot="label">
                                    <el-badge :value="pinboardCommentCount" :max="99" class="admin-layout">{{ $t('models.pinboard.comments') }}</el-badge>
                                </span>
                                <chat class="edit-pinboard-chat" :id="model.id" size="480px" type="pinboard"/>
                            </el-tab-pane>
                        </el-tabs>
                        
                        <template v-if="this.model.type != 3">
                            <el-form-item :label="$t('general.content')" :rules="validationRules.content"
                                          prop="content"
                                          :key="editorKey">
                                <yimo-vue-editor
                                        :config="editorConfig"
                                        v-model="model.content"/>
                            </el-form-item>
                            <el-form-item :label="$t('models.pinboard.images')"
                            >
                                <!-- <upload-document @fileUploaded="uploadFiles" class="drag-custom" drag multiple/>
                                <div class="mt15" v-if="media.length || (model.media && model.media.length)">
                                    <request-media :data="[...model.media, ...media]" @deleteMedia="deleteMedia"
                                                       v-if="media.length || (model.media && model.media.length)"></request-media>
                                </div> -->
                                <ui-media-gallery :files="model.media.map(({url}) => url)" @delete-media="deleteMediaByIndex" :show-description="false"/>
                                <el-alert
                                    :title="$t('general.upload_all_desc')"
                                    type="info"
                                    show-icon
                                    :closable="false"
                                >
                                </el-alert>
                                <media-uploader ref="media" :id="pinboard_id" :audit_id="audit_id" type="requests" layout="grid" v-model="media" :upload-options="uploadOptions" />
                            </el-form-item>
                        </template>                        
                    </el-card>

                    <el-card :loading="loading" v-if="this.model.type != 3 && (!model.resident)">
                        <div slot="header" class="clearfix">
                            <span>{{$t('general.assignment')}}</span>
                        </div>
                        <assignment-by-type
                            :resetToAssignList="resetToAssignList"
                            :assignmentType.sync="assignmentType"
                            :toAssign.sync="toAssign"
                            :assignmentTypes="assignmentTypes"
                            :assign="attachBuilding"
                            :toAssignList="toAssignList"
                            :remoteLoading="remoteLoading"
                            :remoteSearch="remoteSearchBuildings"
                        />
                        <relation-list
                            :actions="assignmentsActions"
                            :columns="assignmentsColumns"
                            :filterValue="model.id"
                            fetchAction="getPinboardAssignments"
                            filter="pinboard_id"
                            ref="assignmentsList"
                            v-if="model.id"
                        />

                    </el-card>                
                </el-col>
                <el-col :md="12">
                    <el-card :loading="loading" class="contact-info-card">
                        <el-row  :gutter="30" class="contact-info-card-row">
                            <el-col class="contact-info-card-col" :md="8">
                                <div class="contact-info-title">
                                    <span class="custom-label">
                                        <i class="icon-user"></i>&nbsp;{{$t('general.user')}}
                                    </span>
                                </div>
                                <div class="contact-info-content">
                                    <span v-if="model.user">
                                        <router-link :to="{name: 'adminUsersEdit', params: {id: model.user.id}}" class="resident-link">
                                            <avatar :size="30"
                                                    :src="'/' + model.user.avatar"
                                                    v-if="model.user.avatar"></avatar>
                                            <avatar :size="28"
                                                    :username="model.user.first_name ? `${model.user.first_name} ${model.user.last_name}`: `${model.user.name}`"
                                                    backgroundColor="rgb(205, 220, 57)"
                                                    color="#fff"
                                                    v-if="!model.user.avatar"></avatar>
                                            <span>{{model.user.name}}</span>
                                        </router-link>
                                    </span>
                                </div>
                            </el-col>                            
                            <el-col class="contact-info-card-col" :md="8">
                                <div class="contact-info-title">
                                    <span class="custom-label">
                                        <i class="icon-paper-plane"></i>&nbsp;{{$t('models.pinboard.published_at')}}
                                    </span>
                                </div>
                                <div class="contact-info-content" v-if="model.published_at">
                                    <span class="custom-value">
                                        {{this.formatDatetime(model.published_at)}}
                                    </span>
                                </div>
                                <div class="contact-info-content" v-else>
                                    <span class="custom-value">-</span>
                                </div>
                            </el-col>
                            <el-col class="contact-info-card-col" :md="8">
                                <div class="contact-info-title">
                                    <span class="custom-label">
                                        <i class="icon-chat"></i>&nbsp;{{$t('models.pinboard.comments')}}
                                    </span>
                                </div>
                                <div class="contact-info-content">
                                    <span class="custom-value">
                                        {{model.comments_count}}
                                    </span>
                                </div>
                            </el-col>
                        </el-row>     
                        <el-row  :gutter="30" class="contact-info-card-row">
                            <el-col class="contact-info-card-col" :md="8">
                                <div class="contact-info-title">
                                    <span class="custom-label">
                                        <i class="icon-eye"></i>&nbsp;{{$t('models.pinboard.views')}}
                                    </span>
                                </div>
                                <div class="contact-info-content">
                                    <span class="custom-value">
                                        {{model.views}}
                                    </span>
                                </div>
                            </el-col>
                            <el-col class="contact-info-card-col" :md="8">
                                <div class="contact-info-title">
                                    <span class="custom-label">
                                        <i class="icon-thumbs-up"></i>&nbsp;{{$t('models.pinboard.likes')}}
                                    </span>
                                </div>
                                <div class="contact-info-content">
                                    <span class="custom-value">
                                        {{model.likes_count}}
                                    </span>   
                                </div> 
                            </el-col>
                            <el-col v-if="model.type == 3" class="contact-info-card-col" :md="8">
                                <div class="contact-info-title">
                                    <span class="custom-label">
                                        <i class="icon-users"></i>&nbsp;&nbsp;{{$t('general.recipients')}}
                                    </span>
                                </div>
                                <div class="contact-info-content">
                                    <span class="custom-value">
                                        {{model.recipient_count}}
                                    </span>
                                </div> 
                            </el-col>
                            <el-col v-if="model.type != 3" class="contact-info-card-col" :md="8">
                            </el-col>
                        </el-row>                                                    
                    </el-card>

                    <el-card :header="$t('models.pinboard.announcement')" v-if="model.type == 3" :loading="loading" class="mt15">
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.pinboard.execution_period.label')">
                                    <el-select style="display: block"
                                               v-model="model.execution_period"
                                               @change="model.execution_end = null">
                                        <el-option
                                                :key="key"
                                                :label="$t(`models.pinboard.execution_period.${period}`)"
                                                :value="parseInt(key)"
                                                v-for="(period, key) in pinboardConstants.execution_period">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <div class="switch-wrapper">
                                    <el-form-item :label="$t('models.pinboard.specify_time_question')">
                                        <el-switch v-model="model.is_execution_time"
                                                   @change="() => {
                                                    !model.is_execution_time ? resetExecutionTime() : '';
                                                    reinitDatePickers();
                                                   }"/>
                                    </el-form-item>
                                    <div class="switcher__desc">
                                        {{$t('models.pinboard.specify_time_description')}}
                                    </div>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20" v-if="model.execution_period == 1">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.pinboard.execution_interval.date')"
                                              :rules="validationRules.execution_date"
                                              prop="execution_date">
                                    <el-date-picker
                                            :key="datePickerKey"
                                            prefix-icon="el-icon-date"
                                            format="dd.MM.yyyy"
                                            style="width: 100%"
                                            type="date"
                                            v-model="model.execution_start"
                                            @change="model.is_execution_time ? setExecutionDateTime() : ''"
                                            value-format="yyyy-MM-dd HH:mm:ss"
                                    >
                                    </el-date-picker>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12" v-if="model.is_execution_time">
                                <el-row :gutter="10">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.pinboard.execution_interval.from')">
                                            <el-time-picker
                                                    v-model="executionStartTime"
                                                    @change="setExecutionDateTime()"
                                                    style="width: 100%"
                                                    :clearable="false"
                                                    :picker-options="{
                                                              selectableRange: '00:00:00 - '+executionEndTime
                                                            }"
                                                    :format="'HH:mm'"
                                                    value-format="HH:mm:00">
                                            </el-time-picker>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.pinboard.execution_interval.separator')">
                                            <el-time-picker
                                                    v-model="executionEndTime"
                                                    @change="setExecutionDateTime()"
                                                    style="width: 100%"
                                                    :clearable="false"
                                                    :picker-options="{
                                                              selectableRange: executionStartTime+' - 23:59:00'
                                                            }"
                                                    :format="'HH:mm'"
                                                    value-format="HH:mm:00">
                                            </el-time-picker>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20" v-else-if="model.execution_period == 2">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.pinboard.execution_interval.start')"
                                              :rules="validationRules.execution_start"
                                              prop="execution_start">
                                    <el-date-picker
                                        :key="datePickerKey"
                                        ref="date1"
                                        @blur="setJustBlurred('date1')"
                                        prefix-icon="el-icon-date"
                                        :picker-options="{disabledDate: disabledExecutionStart}"
                                        :format="model.is_execution_time ? 'dd.MM.yyyy HH:mm' : 'dd.MM.yyyy'"
                                        style="width: 100%"
                                        :type="model.is_execution_time ? 'datetime' : 'date'"
                                        v-model="model.execution_start"
                                        value-format="yyyy-MM-dd HH:mm:ss"
                                    >
                                    </el-date-picker>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.pinboard.execution_interval.end')"
                                              :rules="validationRules.execution_end"
                                              prop="execution_end">
                                    <el-date-picker
                                        :key="datePickerKey"
                                        ref="date2"
                                        @blur="setJustBlurred('date2')"
                                        prefix-icon="el-icon-date"
                                        :picker-options="{disabledDate: disabledExecutionEnd}"
                                        :format="model.is_execution_time ? 'dd.MM.yyyy HH:mm' : 'dd.MM.yyyy'"
                                        style="width: 100%"
                                        :type="model.is_execution_time ? 'datetime' : 'date'"
                                        v-model="model.execution_end"
                                        value-format="yyyy-MM-dd HH:mm:ss"
                                    >
                                    </el-date-picker>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        
                    </el-card>

                    <el-card :header="$t('models.pinboard.buildings')" :loading="loading" class="mt15">
                        <assignment-by-type
                                :resetToAssignList="resetToAssignList"
                                :assignmentType.sync="assignmentType"
                                :toAssign.sync="toAssign"
                                :assignmentTypes="assignmentTypes"
                                :assign="attachBuilding"
                                :toAssignList="toAssignList"
                                :remoteLoading="remoteLoading"
                                :remoteSearch="remoteSearchBuildings"
                        />
                        <relation-list
                            :actions="assignmentsActions"
                            :columns="assignmentsColumns"
                            :filterValue="model.id"
                            fetchAction="getPinboardAssignments"
                            filter="pinboard_id"
                            ref="assignmentsList"
                            v-if="model.id"
                        />

                        <div v-if="!!alreadyAssigned.quarters.length || !!alreadyAssigned.buildings.length">
                            <el-divider></el-divider>

                            <div class="switch-wrapper">
                                <el-form-item :label="$t('models.pinboard.notify_email')" prop="notify_email">
                                    <el-switch v-model="model.notify_email"/>
                                </el-form-item>
                                <div class="switcher__desc">
                                    {{$t('models.pinboard.notify_email_description')}}
                                </div>
                            </div>
                        </div>
                    </el-card>

                    <el-card :header="$t('models.pinboard.placeholders.search_provider')" v-if="model.type == 3 && model.sub_type == 3" :loading="loading" class="mt15">
                        <el-row :gutter="10">
                            <el-col :lg="18" :xl="20">
                                <el-select
                                    :loading="remoteLoading"
                                    :placeholder="$t('models.pinboard.placeholders.search_provider')"
                                    :remote-method="remoteSearchProviders"
                                    class="custom-remote-select"
                                    filterable
                                     clearable
                                    remote
                                    reserve-keyword
                                    style="width: 100%;"
                                    v-model="toAssignProvider"
                                >
                                    <div class="custom-prefix-wrapper" slot="prefix">
                                        <i class="el-icon-search custom-icon"></i>
                                    </div>
                                    <el-option
                                        :key="provider.id"
                                        :label="provider.name"
                                        :value="provider.id"
                                        v-for="provider in toAssignProviderList"/>
                                </el-select>
                            </el-col>
                            <el-col :lg="6" :xl="4">
                                <el-button :disabled="!toAssignProvider" @click="attachProvider" class="full-button"
                                            icon="ti-save" type="primary">
                                    {{$t('general.assign')}}
                                </el-button>
                            </el-col>
                        </el-row>
                        <relation-list
                            :actions="assignmentsProviderActions"
                            :columns="assignmentsProviderColumns"
                            :filterValue="model.id"
                            fetchAction="getServices"
                            filter="pinboard_id"
                            ref="assignmentsProviderList"
                            v-if="model.id"
                        />
                    </el-card>

                    <el-card class="mt15" v-if="model.id && model.type != 3">
                        <div slot="header" class="clearfix">
                            <span>{{$t('models.pinboard.comments')}}</span>
                        </div>
                        <chat class="edit-pinboard-chat" :id="model.id" size="480px" type="pinboard"/>
                    </el-card>
                    <el-card class="mt15">
                        <div slot="header" class="clearfix">
                            <span>{{$t('general.audits')}}</span>
                        </div>
                        <audit :id="model.id" type="pinboard" showFilter/>
                    </el-card>
                </el-col>
            </el-form>
        </el-row>

    </div>
</template>

<script>
    import EditActions from 'components/EditViewActions';
    import PinboardMixin from 'mixins/adminPinboardMixin';
    import FormatDateTimeMixin from 'mixins/formatDateTimeMixin'
    import RelationList from 'components/RelationListing';
    import {displayError, displaySuccess} from "helpers/messages";
    import {mapActions} from 'vuex';
    import {Avatar} from 'vue-avatar'
    import AssignmentByType from 'components/AssignmentByType';
    import { EventBus } from '../../../event-bus.js';
    import EditorConfig from 'mixins/adminEditorConfig';

    const mixin = PinboardMixin({mode: 'edit'});

    export default {
        mixins: [mixin, FormatDateTimeMixin, EditorConfig],
        components: {
            EditActions,
            RelationList,
            Avatar,
            AssignmentByType,
        },
        data() {
            return {
                pinboardCommentCount: 0,
                assignmentsColumns: [{
                    prop: 'name',
                    label: 'general.name'
                }, {
                    prop: 'type',
                    label: 'models.pinboard.assign_type',
                    i18n: this.translateType
                }],
                assignmentsActions: [{
                    width: '180px',
                    buttons: [{
                        title: 'general.unassign',
                        type: 'danger',
                        onClick: this.notifyUnassignment
                    }]
                }],
                assignmentsProviderColumns: [{
                    prop: 'name',
                    label: 'general.name',
                    type: 'serviceName'
                }],
                assignmentsProviderActions: [{
                    width: '180px',
                    buttons: [{
                        icon: 'el-icon-close',
                        title: 'general.unassign',
                        type: 'danger',
                        onClick: this.notifyProviderUnassignment,
                        tooltipMode: true
                    }]
                }],
                activeTab1: "details",
            }
        },
        mounted() {
            this.rolename = this.$store.getters.loggedInUser.roles[0].name;
            EventBus.$on('pinboard-comment-count', pinboard_comment_count => {
                this.pinboardCommentCount = pinboard_comment_count;
            });
            EventBus.$on('pinboard-comment-deleted', () => {
                this.pinboardCommentCount--;
            });
            EventBus.$on('pinboard-comment-added', () => {
                this.pinboardCommentCount++;
            });
        },
        methods: {
            ...mapActions(['unassignPinboardBuilding', 'unassignPinboardQuarter', 'unassignPinboardProvider', 'deletePinboard']),
            disabledExecutionStart(date) {
                const d = new Date(date).getTime();
                const executionEnd = new Date(this.model.execution_end).getTime();
                return executionEnd > 0 && d > executionEnd;
            },
            disabledExecutionEnd(date) {
                const d = new Date(date).getTime();
                const executionStart = new Date(this.model.execution_start).getTime();
                return d <= executionStart;
            },
            notifyUnassignment(row) {
                this.$confirm(this.$t(`general.swal.confirm_change.title`), this.$t('general.swal.confirm_change.warning'), {
                    confirmButtonText: this.$t(`general.swal.confirm_change.confirm_btn_text`),
                    cancelButtonText: this.$t(`general.swal.confirm_change.cancel_btn_text`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading.status = true;

                        await this.unassign(row);

                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.loading.status = false;
                    }
                }).catch(async () => {
                    this.loading.status = false;
                });
            },
            async unassign(toUnassign) {
                let resp;
                if (toUnassign.aType == 1) {
                    resp = await this.unassignPinboardBuilding({
                        id: this.model.id,
                        toAssignId: toUnassign.id
                    })
                } else {
                    resp = await this.unassignPinboardQuarter({
                        id: this.model.id,
                        toAssignId: toUnassign.id
                    })
                }

                if (resp) {
                    await this.fetchCurrentPinboard();
                    this.$refs.assignmentsList.fetch();

                    this.toAssign = '';

                    const type = toUnassign.aType == 1 ? 'building' : 'quarter';
                    displaySuccess(resp)
                }
            },
            async unassignProvider(toUnassign) {
                const resp = await this.unassignPinboardProvider({
                    id: this.model.id,
                    toAssignId: toUnassign.id
                });

                await this.fetchCurrentPinboard();
                this.$refs.assignmentsProviderList.fetch();

                this.toAssignProvider = '';
                displaySuccess(resp)
            },
            notifyProviderUnassignment(row) {
                this.$confirm(this.$t(`general.swal.confirm_change.title`), this.$t('general.swal.confirm_change.warning'), {
                    confirmButtonText: this.$t(`general.swal.confirm_change.confirm_btn_text`),
                    cancelButtonText: this.$t(`general.swal.confirm_change.cancel_btn_text`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading.status = true;

                        await this.unassignProvider(row);

                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.loading.status = false;
                    }
                }).catch(async () => {
                    this.loading.status = false;
                });
            }
        }
    }
</script>

<style lang="scss" scoped>

    .switcher__desc {
        margin-top: 0.5em;
        display: block;
        font-size: 0.9em;
    }
    .custom-select {
        display: block;
    }
    .contact-info-title {
        display: flex;
        justify-content: center;
        .custom-label {
            color: var(--primary-color);
            display: inline-block;
            margin-bottom: 10px;
        }
    }    
    .contact-info-content {
        display: flex;
        justify-content: center;
        .custom-value {        
            line-height: 28px;
        }
    }

    .mb20 {
        margin-bottom: 20px;
    }
    
    .contact-info-card {
        .contact-info-card-row {
            display: flex;
            border-bottom: 1px solid #EBEEF5;
            margin-left: 0 !important;
            margin-right: 0 !important;
            &:first-child {
                .contact-info-card-col {
                    padding-top: 0;
                }
            }
            &:last-child {
                border-bottom: 0;
                .contact-info-card-col {
                    padding-bottom: 0;
                }
            }
            .contact-info-card-col {
                &:first-child {
                    padding-left: 0 !important;
                }
                &:last-child {
                    padding-right: 0 !important;
                }
            }
        }
        
        .contact-info-card-col {
            border-right: 1px solid #EBEEF5;
            min-height: 57px;
            padding-bottom: 10px;
            padding-top: 10px;
            &:last-child {
                border: none;
            }
        }
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

    .ui-media-gallery {
        margin-bottom: 10px;
    }

</style>

<style lang="scss">
    .switcher {
        .el-form-item__content {
            display: flex;
        }
        &__label {
            line-height: 1.4em;
            color: #606266;
        }
        &__label-title {
            display: flex;
            align-items: center;
            min-height: 40px;
        }
        &__label-desc {
            margin-top: 0.6em;
            display: block;
            font-size: 0.9em;
        }
        .el-switch {
            margin-top: 10px;
            margin-left: auto;
        }
    }

    #pinboard-edit-view .el-card__body .el-form-item:last-child {
        margin-bottom: 0;
    }

    .edit-pinboard-chat .add-comment {
        margin-bottom: 0 !important;
    }

    .units-edit {
        .el-input-group--prepend .el-input-group__prepend {
            padding: 0 10px;
            font-weight: bold;
        }
        .el-card .el-card__body, .el-card .el-card__header {
            padding: 20px !important;
        }
        #tab-comments {
            padding-right: 40px !important;
        }
    }
</style>
