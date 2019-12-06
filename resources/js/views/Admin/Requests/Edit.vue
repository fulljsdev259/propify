<template>
    <div class="request-edit mb20" v-if="constants" v-loading.fullscreen.lock="loading.state">
        <heading :title="$t('models.request.edit_title')" icon="icon-chat-empty" shadow="heavy" class="bg-transparent">
            <template slot="description" v-if="model.request_format">
                <div class="subtitle">{{model.request_format}}</div>
            </template>
            <el-button
                    @click="downloadPDF"
                    size="mini"
                    type="danger"
                    icon="icon-file-pdf"
                    class="download-pdf el-button--transparent"
            >
                {{ $t('models.request.download_pdf.title') }}
            </el-button>
            <edit-actions :saveAction="submit" :deleteAction="deleteRequest" route="adminRequests" :editMode="editMode" @edit-mode="handleChangeEditMode" ref="editActions"/>
        </heading>
        <div class="crud-view" id="edit_request">
            <el-form :model="model" label-position="top" label-width="192px" ref="form">
                <el-row :gutter="20">
                    <el-col :md="12">
                        <el-tabs type="border-card" v-model="activeTab0" class="edit-tab">
                            <el-tab-pane  :label="$t('models.request.request_details')" id="request_details" name="request_details_pane">
                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.request.category')"
                                                    :rules="validationRules.category"
                                                    prop="category_id">
                                            <el-select
                                                :disabled="$can($permissions.update.serviceRequest) || !editMode"
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
                                    <el-col :md="12" v-if="this.showQualification == true">
                                        <el-form-item :label="$t('models.request.category_options.qualification_category')">
                                            <el-select 
                                                :disabled="$can($permissions.update.serviceRequest) || !editMode"
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
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.request.prop_title')" :rules="validationRules.title"
                                                        prop="title">
                                            <el-input 
                                                    :disabled="$can($permissions.update.serviceRequest) || !editMode" 
                                                    type="text"
                                                    v-model="model.title"/>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.request.created_by')" >
                                            <el-input 
                                                    :disabled="!editMode" 
                                                    type="text"
                                                    readonly
                                                    :value="model.creator.name"/>
                                                    <span class="created-date"> {{ model.created_at.slice(0, -3) }}</span>
                                        </el-form-item>
                                    </el-col>
                                    
                                    <el-col :md="24">
                                        <el-form-item 
                                            :label="$t('general.description')" :rules="validationRules.description"
                                            prop="description"
                                            :key="editorKey"
                                        >
                                            <el-input
                                                :disabled="!editMode"
                                                type="textarea"
                                                :rows="4"
                                                placeholder="Please input"
                                                v-model="model.description">
                                            </el-input>


                                        </el-form-item>
                                       
                                    </el-col>
                                    <el-col :md="12"
                                            v-if="this.showSubCategory == true">
                                        <el-form-item :label="$t('models.request.defect_location.label')"
                                                    :rules="validationRules.sub_category"
                                                    prop="sub_category_id">
                                            <el-select
                                                :disabled="$can($permissions.update.serviceRequest) || !editMode"
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
                                   
                                   
                                    <!-- <el-col :md="6" v-if="this.showPayer == true">
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
                                    <el-col :md="6" v-if="this.showPayer == true" style="height: 97px">
                                        <el-form-item 
                                            :label="$t('models.request.category_options.payer_amount')"
                                            :rules="validationRules.amount"
                                            prop="amount">
                                            <el-input 
                                                type="number"
                                                v-model="model.amount" 
                                            >
                                                <template slot="prepend">CHF</template>
                                            </el-input>
                                        </el-form-item>
                                    </el-col> -->
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
                                                :disabled="!editMode"
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
                                <!-- <el-row :gutter="20" class="summary-row" style="margin-bottom: 0;padding-bottom: 0;">
                                    <el-col :md="8" class="summary-item" id="resident">
                                        <el-form-item v-if="model.resident">
                                            <label slot="label">
                                                {{$t('general.resident')}}
                                            </label>
                                            <router-link :to="{name: 'adminResidentsEdit', params: {id: model.resident.id}}"
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
                                </el-row> -->
                                <!-- <el-row :gutter="20" class="summary-row"> -->
                                    <!-- <el-col :md="8" class="summary-item">
                                        <el-form-item :label="$t('models.request.priority.label')">
                                            <strong v-if="$constants.requests.priority[model.priority]">{{$t(`models.request.priority.${$constants.requests.priority[model.priority]}`)}}</strong>
                                        </el-form-item>
                                    </el-col> -->
                                    <!-- <el-col :md="8" class="summary-item">
                                        <el-form-item :label="$t('models.resident.relation.title')" v-if="this.model.relation">
                                            {{this.model.relation.building_id + " -- " + this.model.relation.unit_id}}
                                        </el-form-item>

                                        <el-form-item :label="$t('models.resident.relation.title')" :rules="validationRules.relation_id"
                                                    v-else
                                                    prop="relation_id">
                                            <el-select v-model="model.relation_id" 
                                                        :placeholder="$t('resident.placeholder.relation')"
                                                        class="custom-select">
                                                <el-option v-for="relation in dirtyRelations" 
                                                            :key="relation.id" 
                                                            :label="relation.building_room_floor_unit" 
                                                            :value="relation.id" />
                                            </el-select>
                                        </el-form-item>

                                    </el-col> -->

                                    <!-- <el-col :md="8" class="summary-item">
                                        <el-form-item :label="$t('models.request.visibility.label')">
                                            <strong>{{$constants.requests.visibility[model.visibility]}}</strong>
                                        </el-form-item>
                                    </el-col> -->
                                <!-- </el-row> -->

                                

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
                            </el-tab-pane>
                            <el-tab-pane name="request_images" class="px-10">
                                <span slot="label">
                                    {{ $t('models.request.images') }}
                                    <!-- <el-badge :value="mediaCount" :max="99" class="admin-layout">{{ $t('models.request.images') }}</el-badge> -->
                                </span>
                                <!-- <el-alert
                                    v-if="( !media || media.length == 0) && mediaCount == 0"
                                    :title="$t('models.request.no_images_message')"
                                    type="info"
                                    show-icon
                                    :closable="false"
                                >
                                </el-alert> -->

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
                        <el-tabs type="border-card" v-model="activeTab1"  class="edit-tab">
                            <el-tab-pane label="Standort" name="request_details">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.request.resident.name')">
                                        <el-input 
                                                :disabled="$can($permissions.update.serviceRequest) || !editMode" 
                                                type="text"
                                                v-model="model.name"/>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="6">
                                    <el-form-item :label="$t('models.resident.quarter.name')" >
                                        <el-select 
                                                :disabled="!editMode"
                                                :placeholder="$t(`general.placeholders.select`)"
                                                class="custom-select"
                                                v-model="model.quarter_id"
                                            >
                                                <el-option
                                                    :key="quarter.id"
                                                    :label="quarter.name"
                                                    :value="quarter.id"
                                                    v-for="quarter in quarters">
                                                </el-option>
                                            </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="6">
                                    <el-form-item :label="$t('models.resident.unit.name')">
                                        <el-select 
                                                :disabled="!editMode"
                                                :placeholder="$t(`general.placeholders.select`)"
                                                class="custom-select"
                                                v-model="model.unit_id"
                                            >
                                                <el-option
                                                    :key="unit.id"
                                                    :label="unit.name"
                                                    :value="unit.id"
                                                    v-for="unit in units">
                                                </el-option>
                                            </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <img :src="require('img/default_img_object.png')" style="width: 100%"/>
                                </el-col>
                                <el-col :md="12">
                                    <el-col :md="24" class="pl-0 pr-0"
                                            v-if="this.showSubCategory == true && this.showLocation == true">
                                        <el-form-item :label="$t('models.request.category_options.range')">
                                            <el-select 
                                                :disabled="$can($permissions.update.serviceRequest) || !editMode"
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
                                    <el-col :md="24" class="pl-0 pr-0"
                                            v-if="this.showSubCategory == true && this.showRoom == true">
                                        <el-form-item :label="$t('models.request.category_options.room')">
                                            <el-select 
                                                :disabled="$can($permissions.update.serviceRequest) || !editMode"
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
                                    <el-col :md="24" v-if="this.showComponent == true" class="pr-0 pl-0">
                                        <el-form-item :label="$t('models.request.category_options.component')">
                                            <el-input v-model="model.component" :disabled="!editMode"></el-input>
                                        </el-form-item>
                                    </el-col>
                                </el-col>
                            </el-tab-pane>
                            <el-tab-pane label="Zusatzinformationen" name="addinatonal_info">
                                 <el-col :md="12" v-if="this.showCapturePhase == true">
                                    <el-form-item :label="$t('models.request.category_options.capture_phase')">
                                        <el-select 
                                            :disabled="$can($permissions.update.serviceRequest) || !editMode"
                                            :placeholder="$t(`general.placeholders.select`)"
                                            class="custom-select"
                                            v-model="model.capture_phase"
                                        >
                                            <el-option
                                                :key="phase.value"
                                                :label="phase.name"
                                                :value="phase.value"
                                                v-for="phase in capture_phases">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                            </el-tab-pane>

                        </el-tabs>
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
                        
                            <el-tabs id="comments-card" v-if="model.id" type="border-card" value="assignment" class="edit-tab">
                                <el-tab-pane name="assignment">
                                    <span slot="label">
                                        {{ $t('models.request.assignment') }}
                                    </span>
                                    <el-col :md="24">
                                        <users-assignment
                                                :resetToAssignList="resetToAssignList"
                                                :toAssign.sync="toAssign"
                                                :assign="assignUsers"
                                                :toAssignList="toAssignList"
                                                :remoteLoading="remoteLoading"
                                                :remoteSearch="remoteSearchAssignees"
                                        ></users-assignment>
                                        <!-- <relation-list
                                            :actions="assigneesActions"
                                            :columns="assigneesColumns"
                                            :filterValue="model.id"
                                            fetchAction="getAssignees"
                                            filter="request_id"
                                            ref="assigneesList"
                                            v-if="model.id"
                                        /> -->
                                        <el-button @click="expandRelationList=!expandRelationList" class="relation-expand-button">Expand</el-button>
                                        <relation-list
                                            class="relation-expand-list"
                                            :actions="assigneesActions"
                                            :columns="assigneesColumns"
                                            :filterValue="model.id"
                                            fetchAction="getAssignees"
                                            filter="request_id"
                                            ref="assigneesList"
                                            v-if="model.id && expandRelationList"
                                        />
                                    </el-col>
                                </el-tab-pane>
                                <el-tab-pane name="assignment1">
                                    <span slot="label">
                                        Verantwortlich
                                    </span>
                                    <el-col :md="24">
                                        <users-assignment
                                                :resetToAssignList="resetToAssignList"
                                                :toAssign.sync="toAssign"
                                                :assign="assignUsers"
                                                :toAssignList="toAssignList"
                                                :remoteLoading="remoteLoading"
                                                :remoteSearch="remoteSearchAssignees"
                                        ></users-assignment>
                                        <relation-list
                                            :actions="assigneesActions"
                                            :columns="assigneesColumns"
                                            :filterValue="model.id"
                                            fetchAction="getAssignees"
                                            filter="request_id"
                                            ref="assigneesList"
                                            v-if="model.id"
                                        />
                                    </el-col>
                                </el-tab-pane>
                            </el-tabs>
 
                            <el-tabs class="action-tabs edit-tab" type="border-card" v-model="activeActionTab">
                                <el-tab-pane :label="$t('models.request.actions')" name="actions">
                                    <el-row :gutter="20">                                    
                                        <el-col :md="12">
                                            <el-form-item :label="$t('models.request.status.label')"
                                                        :rules="validationRules.status"
                                                        prop="status">
                                                <el-select :placeholder="$t('models.request.placeholders.status')"
                                                        class="custom-select"
                                                        :disabled="!editMode"
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
                                        <el-col :md="12"
                                            v-if="this.showAction == true">
                                            <el-form-item :label="$t('models.request.action.label')"
                                                        prop="action">
                                                <el-select :disabled="$can($permissions.update.serviceRequest) || !editMode"
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
                                                    <!-- <div class="reminder-box" v-if="editMode">
                                                        <label class="switcher__label">
                                                            {{$t('models.request.active_reminder_switcher')}}
                                                        </label>
                                                        <el-switch  v-model="model.active_reminder"/>
                                                    </div> -->
                                                </template>
                                                
                                                <el-date-picker
                                                    :disabled="$can($permissions.update.serviceRequest) || !editMode"
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
                                        <el-col :md="showPercent?8:12" v-if="this.showCostImpact == true">
                                             <el-form-item :label="$t('models.request.cost_impact.label')"
                                                    prop="cost_impact">
                                                <el-select :disabled="$can($permissions.update.serviceRequest) || !editMode"
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
                                        <el-col :md="4" v-if="this.showPercent == true">
                                            <el-form-item 
                                                :label="$t('models.request.category_options.payer_percent')"
                                                :rules="validationRules.percentage"
                                                prop="percentage">
                                                <el-input 
                                                    type="text"
                                                    v-model="model.percentage" 
                                                    :disabled="!editMode"
                                                >
                                                    <template slot="append">%</template>
                                                </el-input>
                                            </el-form-item>
                                        </el-col>
                                        
                                    </el-row>
                                    <el-row :gutter="10"> 
                                        <el-col :md="12" v-if="model.active_reminder">
                                            <el-form-item :label="$t('models.request.days_left')"
                                                        prop="days_left_due_date">
                                                <el-input v-model="model.days_left_due_date" :disabled="!editMode" type="number"></el-input>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :md="12" v-if="model.active_reminder">
                                            <el-form-item :label="$t('models.request.send_person')"
                                                        prop="reminder_user_ids ">
                                                <el-select
                                                    :loading="remoteLoading"
                                                    :placeholder="$t('models.request.placeholders.person')"
                                                    :remote-method="remoteSearchPersons"
                                                    filterable
                                                    multiple
                                                    remote
                                                    reserve-keyword
                                                    :disabled="!editMode"
                                                    style="width: 100%;"
                                                    v-model="model.reminder_user_ids ">
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
                                    <div class="switch-wrapper" v-if="editMode">
                                        <el-form-item :label="$t('models.request.public_title')" prop="is_public">
                                            <el-switch v-model="model.is_public" />
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
                                            <el-select v-model="model.visibility" :disabled="!editMode">
                                                <el-option :key="k" :label="$t(`models.request.visibility.${visibility}`)" :value="parseInt(k)" v-for="(visibility, k) in $constants.requests.visibility">
                                                </el-option>
                                            </el-select>
                                        </div>
                                    </el-form-item>
                                    <div v-if="model.is_public && editMode" class="switch-wrapper">
                                        <el-form-item :label="$t('models.request.send_notification_title')" prop="send_notification">
                                            <el-switch v-model="model.send_notification"/>
                                        </el-form-item>
                                        <div class="switcher__desc">
                                            {{ $t('models.request.send_notification_desc') }}
                                        </div>
                                    </div>
                                </el-tab-pane>
                            </el-tabs>
                        </template>
                        <!--                    v-if="(!$can($permissions.update.serviceRequest)) || ($can($permissions.update.serviceRequest) && (media.length || (model.media && model.media.length)))"-->
                        
                        <el-tabs id="comments-card" v-if="model.id" type="border-card" v-model="activeTab2">
                            <el-tab-pane name="comments">
                                <span slot="label">
                                    {{ $t('models.request.comments') }}
                                    <!-- <el-badge :value="requestCommentCount" :max="99" class="admin-layout">{{ $t('models.request.comments') }}</el-badge> -->
                                </span>
                                <chat :id="model.id" type="request" show-templates :newStyle="true"/>
                            </el-tab-pane>
                            <el-tab-pane name="internal-notices">
                                <span slot="label">
                                    {{ $t('models.request.internal_notices') }}
                                    <!-- <el-badge :value="noticeCommentCount" :max="99" class="admin-layout">{{ $t('models.request.internal_notices') }}</el-badge> -->
                                </span>
                                <chat :id="model.id" type="internalNotices" :newStyle="true"/>
                            </el-tab-pane>
                            <el-tab-pane name="audit" style="height: 400px;overflow:auto;">
                                <span slot="label">
                                    {{ $t('general.audits') }}
                                    <!-- <el-badge :value="auditCount" :max="99" class="admin-layout">{{ $t('general.audits') }}</el-badge> -->
                                </span>
                                <audit v-if="model.id" :id="model.id" type="request" ref="auditList" showFilter/>
                            </el-tab-pane>
                        </el-tabs>
                    
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
            v-if="model.id && ((model.service_providers && model.service_providers.length) || (model.property_managers && model.property_managers.length))"
        />
        <edit-close-dialog
                :centerDialogVisible="visibleDialog"
                @clickYes="submit(), visibleDialog=false, $refs.editActions.goToListing()"
                @clickNo="visibleDialog=false, $refs.editActions.goToListing()"
                @clickCancel="visibleDialog=false"
        ></edit-close-dialog>
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
    import EditCloseDialog from 'components/EditCloseDialog';
    import UsersAssignment from 'components/UsersAssignment';

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
            EditCloseDialog,
            UsersAssignment
        },
        data() {
            return {
                editMode: false,
                visibleDialog: false,
                requestCommentCount: 0,
                auditCount: 0,
                noticeCommentCount: 0,
                activeTab0: 'request_details_pane',
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
                    prop: 'company_name',
                    label: 'general.roles.label',
                }, {
                    type: 'assignProviderManagerFunctions',
                }],
                assigneesActions: [{
                    width: 120,
                    align: 'right',
                    buttons: [{
                        title: 'models.request.notify',
                        tooltipMode: true,
                        icon: 'el-icon-position',
                        view: 'request',
                        onClick: this.openNotifyProvider
                    }, {
                        title: 'general.unassign',
                        tooltipMode: true,
                        type: 'danger',
                        icon: 'el-icon-delete',
                        onClick: this.notifyUnassignment
                    }]
                }],
                rolename: null,
                inputVisible: false,
                editMode: false,
                expandRelationList: false,
                units: [],
                quarters: [],
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
            },
        },
        async mounted() {
            this.rolename = this.$store.getters.loggedInUser.roles[0].name;
            this.$root.$on('media-upload-finished', () => {
                if(this.$refs.auditList){
                    this.$refs.auditList.fetch();
                }
            });
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
            handleChangeEditMode() {
                if(!this.editMode) {
                    this.editMode = !this.editMode;
                    this.old_model = _.clone(this.model, true);
                } else {
                    if(JSON.stringify(this.old_model) !== JSON.stringify(this.model)) {
                        this.visibleDialog = true;
                    } else {
                        this.$refs.editActions.goToListing();
                    }
                }
            },
            translateType(type) {
                return this.$t(`general.roles.${type}`);
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
                            if(this.$refs.auditList){
	                            this.$refs.auditList.fetch();
                            }
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
            },
            async fetchRemoteQuarters(search = '') {
                const quarters = await this.getQuarters({get_all: true, search});

                return quarters.data
            },
            async fetchRemoteUnits(search = '') {
                const units = await this.getUnits({get_all: true, search});

                return units.data
            },
        },
        async created() {
            this.units = await this.fetchRemoteUnits();
            this.quarters = await this.fetchRemoteQuarters();
        },
        watch: {
            'model.percentage' (newVal, oldVal) {
                if(newVal === '')
                    this.model.percentage = 0;
                else
                    this.model.percentage = parseInt(newVal);
            }
        }
    };
</script>
<style lang="scss" scoped>
    // .download-pdf {
    //     margin-right: 5px;
    // }

    .request-edit {
        .heading {
            margin-bottom: 20px;
        }
        .crud-view {
            padding: 0 20px;
            .created-date {
                position: absolute;
                right: 20px;
            }
        }
        .relation-expand-button {
            background-color: transparent;
            padding: 0px;
            &:hover {
                box-shadow: none;
                font-weight: 700;
            }
        }
        .relation-expand-list {
            padding: 5px 50px;
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
    .request-edit {
        .el-form-item__content {
            .el-input.el-input-group {
                .el-input-group__append {
                    padding: 2px 8px 0;
                    font-weight: 600;
                    border: none;
                    background-color: #f6f5f7 !important;
                }
            }
            .el-input.el-input-group.border-left-radious-0 {
                    border-top-left-radius: 0px;
                    border-bottom-left-radius: 0px;
            }
            .el-select.right-border-radius-0 .el-input__inner {
                border-top-right-radius: 0px;
                border-bottom-right-radius: 0px;
            }
        }
        // .el-input {
        //     height: 40px;
        // }
    }

    .switch-item {
        display: flex;
        margin: 0;
        padding: 0;

        .el-form-item__label, .el-form-item__content {
            line-height: 20px;
        }
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


    .edit-details-form .el-tabs .el-row {
        .el-col {
            padding-right: 10px !important;
            padding-left: 10px !important;
        }
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
        .px-10 {
            padding-right: 10px !important;
            padding-left: 10px !important;
        }
        .el-form > .el-row > .el-col {
            margin-bottom: 1em;
        }
        .image-tab-title {
            display: block;
            margin-bottom: 5px;
        }
        .el-form-item {
            margin-bottom: 16px;
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
            &.edit-tab .el-tabs__content {
                padding: 20px 10px !important;
            }
            .el-tabs__nav-wrap.is-top {
                border-radius: 6px 6px 0 0;
            }
        }

        .el-card.is-always-shadow {
            margin-right: 10px;
            margin-left: 10px;
            margin-bottom: 40px;
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
