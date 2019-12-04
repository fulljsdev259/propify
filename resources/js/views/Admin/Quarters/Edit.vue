<template>
    <div class="quarters-edit" v-loading.fullscreen.lock="loading.state">
        <div class="main-content">
            <heading :title="$t('models.quarter.edit')" icon="icon-chat-empty" shadow="heavy" bgClass="bg-transparent">
                <template slot="description" v-if="model.quarter_format">
                    <!-- <div class="subtitle">{{`${model.quarter_format} > ${model.name}`}}</div> -->
                    <div class="subtitle">{{model.url}}</div>
                </template>
                <edit-actions :saveAction="submit" :deleteAction="deleteQuarter" route="adminQuarters" :editMode="editMode" @edit-mode="handleChangeEditMode" ref="editActions"/>
            </heading>
            <el-row :gutter="20" class="crud-view">
                <el-col :md="12">
                    <el-card class="quarter-details">
                        <el-form :model="model" ref="form"  class="edit-details-form">
                            <el-row :gutter="20">
                                <el-col :md="12" class="left-pane">
                                    <img :src="require('img/default_img_object.png')"/>

                                    <div v-if="!editMode" class="quarter-id">
                                        <span v-if="!editMode" @dblclick="editId=editMode">{{ model.internal_quarter_id }}</span>
                                    </div>

                                    <span v-if="!editMode" class="quarter-name" @dblclick="editName=editMode">{{ model.name }}</span>
                                    
                                </el-col>
                                <el-col :md="12" class="right-pane">
                                    
                                    <el-form-item 
                                        v-if="editMode" 
                                        :label="$t('general.internal_quarter_id')"
                                        :rules="validationRules.internal_quarter_id"
                                        prop="internal_quarter_id" 
                                        class='quarter-id'
                                    >
                                        <el-input type="text" v-model="model.internal_quarter_id" ></el-input>
                                    </el-form-item>
                                    <el-form-item v-if="editMode" :label="$t('general.name')" :rules="validationRules.name"
                                                prop="name">
                                        <el-input type="text" v-model="model.name"  />
                                    </el-form-item>
                                    <div v-if="!editMode" class="quarter-detail-item">
                                        <span>{{ $t('models.quarter.url') }}</span>
                                        <span>{{ model.url }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('models.quarter.url')" :rules="validationRules.url"
                                                    prop="url">
                                        <el-input type="text" v-model="model.url" ></el-input>
                                    </el-form-item>

                                    <div v-if="!editMode" class="quarter-detail-item">
                                        <span>{{ $t('models.quarter.types.label') }}</span>
                                        <span>{{ quarterTypes }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('models.quarter.types.label')" :rules="validationRules.types"
                                            class="label-block" 
                                            prop="types">
                                        <!-- <el-select
                                                :placeholder="$t('general.placeholders.select')"
                                                style="display: block"
                                                v-model="model.types"
                                                multiple
                                                :disabled="!editMode"
                                                filterable>
                                            <el-option
                                                    :key="type.value"
                                                    :label="type.name"
                                                    :value="type.value"
                                                    v-for="type in types">
                                            </el-option>
                                        </el-select> -->
                                        <multi-select
                                            :name="$t('general.placeholders.select')"
                                            :data="types"
                                            :selectedOptions="model.types"
                                            tagColor="#9E9FA0"
                                            showMultiTag
                                            @select-changed="model.types=$event"
                                        ></multi-select>
                                    </el-form-item>
                                
                                    <!-- <el-col :md="12">
                                        <el-form-item class="label-block" :label="$t('models.quarter.count_of_buildings')"
                                                    prop="title">
                                            <el-select style="display: block" 
                                                    clearable
                                                    v-model="model.count_of_buildings">
                                                <el-option
                                                        :key="building"
                                                        :value="building"
                                                        v-for="building in buildingsCount">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                    </el-col> -->
                                
                                    <div v-if="!editMode" class="quarter-detail-item">
                                        <span>{{ $t('general.zip') }} / {{ $t('general.city') }}</span>
                                        <span>{{ model.zip }} {{ model.city }}</span>
                                    </div>
                                    <el-col :span="7" class="pl-0">
                                        <el-form-item v-if="editMode" :label="$t('general.zip')" :rules="validationRules.zip"
                                                    prop="zip">
                                            <el-input type="text" v-model="model.zip"/>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="17" class="pr-0">
                                        <el-form-item v-if="editMode" :label="$t('general.city')" :rules="validationRules.city"
                                                    prop="city">
                                            <el-input type="text" v-model="model.city" />
                                        </el-form-item>
                                    </el-col>

                                    <div v-if="!editMode" class="quarter-detail-item">
                                        <span>{{ $t('general.state') }}</span>
                                        <span>{{ quarterState }}</span>
                                    </div>
                                    <el-form-item  
                                        v-if="editMode"
                                        :label="$t('general.state')"
                                        :rules="validationRules.state_id"
                                        prop="state_id"
                                        class="label-block"
                                    >
                                        <el-select 
                                            clearable
                                            filterable
                                            :placeholder="$t('general.state')" 
                                            style="display: block"
                                            v-model="model.state_id"
                                        >
                                            <el-option :key="state.id" :label="state.name" :value="state.id"
                                                    v-for="state in states"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col> 
                            </el-row>
                        </el-form>
                    </el-card>

                    <el-tabs type="border-card" v-model="activeTab2">
                        <el-tab-pane name="audit" style="height: 400px;overflow:auto;">
                            <span slot="label">
                                {{ $t('general.audits') }}
                                <!-- <el-badge :value="auditCount" :max="99" class="admin-layout">{{ $t('general.audits') }}</el-badge> -->
                            </span>
                            <audit v-if="model.id" :id="model.id" type="quarter" ref="auditList" showFilter/>
                        </el-tab-pane>
                        <el-tab-pane name="files">
                            <span slot="label">
                                {{ $t('general.box_titles.files') }}
                                <!-- <el-badge :value="fileCount" :max="99" class="admin-layout">{{ $t('general.box_titles.files') }}</el-badge> -->
                            </span>
                            <draggable @sort="sortFiles" v-model="model.media">
                                <transition-group name="list-complete">
                                    
                                    <div key="list-complete-item" class="list-complete-item">
                                        <building-file-list-table 
                                                :items="model.media" 
                                                @delete-document="deleteDocument">
                                        </building-file-list-table>
                                    </div>
                                </transition-group>
                            </draggable>
                            <div class="mt15">
                                <label class="card-label">{{$t('models.building.add_files')}}</label>
                                <el-select :placeholder="$t('models.building.select_media_category')"
                                        class="category-select"
                                        v-model="selectedFileCategory">
                                    <el-option
                                        :key="item"
                                        :label="$t('models.building.media_category.' + item)"
                                        :value="item"
                                        v-for="item in $constants.file_categories">
                                    </el-option>
                                </el-select>
                                <el-alert
                                    :title="$t('general.upload_file_desc')"
                                    type="info"
                                    show-icon
                                    :closable="false"
                                >
                                </el-alert>
                                <upload-document @fileUploaded="uploadFiles" class="drag-custom" drag multiple
                                                accept-type=".pdf, .doc, .docx, .xls, .xlsx"
                                                v-if="selectedFileCategory"/><!-- @TODO this is uploading file on the spot, is it okay? need to confirm -->
                                
                            </div>
                        </el-tab-pane>

                    </el-tabs>

                </el-col>
                <el-col :md="12">
                    <el-tabs type="border-card" v-model="activeRightTab">
                        <el-tab-pane name="assignees">                        
                            <span slot="label">
                                {{ $t('general.box_titles.managers') }}
                                <!-- <el-badge :value="assigneeCount" :max="99" class="admin-layout">{{ $t('general.box_titles.managers') }}</el-badge> -->
                            </span>
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
                                fetchAction="getQuarterAssignees"
                                filter="quarter_id"
                                ref="assigneesList"
                                v-if="model.id"
                            />
                        </el-tab-pane>
                       <el-tab-pane name="workflow">
                            <span slot="label">
                                {{ $t('models.quarter.workflow.label') }}
                                <!-- <el-badge :value="workflowCount" :max="99" class="admin-layout">{{ $t('models.quarter.workflow.label') }} </el-badge> -->
                            </span>
                            
                            
                            <workflow-form v-if="isAddWorkflow"
                                mode="add" 
                                :quarter_id="model.id" 
                                :visible.sync="visibleDrawer"
                                class="add-work-flow"
                                @add-workflow="addWorkflow"
                                @cancel-add-workflow="cancelAddWorkflow"/>
                            <el-collapse accordion>
                                 <el-collapse-item
                                        :key="'workflow' + $index + workflow.title"
                                        :label="`${workflow.name}`"
                                        :value="workflow.title"
                                        :name="workflow.title"
                                        v-for="(workflow, $index) in model.workflows">
                                    <template slot="title">
                                        {{workflow.title}}
                                    </template>
                                    <workflow-form v-if="isEditingWorkflow[$index]"
                                            mode="edit" 
                                            :quarter_id="model.id" 
                                            :data="workflow"
                                            :editing_index="$index"
                                            :visible.sync="visibleDrawer"
                                            @update-workflow="updateWorkflow"
                                            @delete-workflow="deleteWorkflow"
                                            @cancel-edit-workflow="cancelEditWorkflow"/>
                                    <el-row v-else>
                                        <el-col :md="24" class="workflow-label">
                                            <el-tag type="primary">
                                                    {{$t(`models.request.category_list.${workflow.category.name}`)}}
                                            </el-tag>

                                            <span>{{$t('models.quarter.workflow.by')}}</span>
                                        
                                            <el-tag 
                                                    type="primary" 
                                                    :key="'building' + building.id"
                                                    v-for="building in workflow.buildings">
                                                    {{building.address.house_num}}
                                            </el-tag>
                                        
                                            <span>{{$t('models.quarter.workflow.to')}}</span>
                                        
                                            <el-tag 
                                                    type="primary" 
                                                    :key="'to_user' + user.id"
                                                    v-for="user in workflow.to_users">
                                                {{user.name}}
                                            </el-tag>
                                            
                                            <span v-if="workflow.cc_users.length > 0">{{$t('models.quarter.workflow.cc')}}</span>
                                        
                                            <el-tag 
                                                    type="primary" 
                                                    :key="'cc_user' + user.id"
                                                    v-for="user in workflow.cc_users">
                                                {{user.name}}
                                            </el-tag>
                                        </el-col>
                                    </el-row>
                                    <el-row v-if="!isEditingWorkflow[$index]">
                                        <el-col :md="24" class="edit workflow-button-bar" v-if="editMode">
                                            <!-- <el-button 
                                                type="danger" 
                                                @click="deleteWorkflow($index)"
                                                icon="icon-trash-empty" 
                                                size="mini" 
                                                class="round-btn">
                                                {{ $t('models.quarter.workflow.delete') }}
                                            </el-button> -->

                                            <el-button 
                                                type="danger" 
                                                @click="showEditWorkflow($index)"
                                                icon="icon-pencil" 
                                                size="mini" 
                                                class="round-btn">
                                                {{ $t('models.quarter.workflow.edit') }}
                                            </el-button>
                                        </el-col>
                                    </el-row>
                                </el-collapse-item>
                            </el-collapse>

                            <div class="workflow-button-bar">
                                <el-button 
                                        type="primary" 
                                        @click="showAddWorkflow" 
                                        icon="icon-plus" 
                                        size="mini" 
                                        v-if="editMode"
                                        class="add-workflow-btn">
                                    {{ $t('models.quarter.workflow.add') }}
                                </el-button>
                            </div>
                        </el-tab-pane>
                        <!-- <el-tab-pane name="residents">                        
                            <span slot="label">
                                <el-badge :value="residentCount" :max="99" class="admin-layout">{{ $t('general.residents') }}</el-badge>
                            </span>
                            <relation-list
                                :actions="residentActions" 
                                :columns="residentColumns"
                                :filterValue="model.id"
                                fetchAction="getResidents"
                                filter="quarter_id"
                                v-if="model.id"
                            />
                        </el-tab-pane> -->
                        <!-- <el-tab-pane name="relations">
                            <span slot="label">
                                <el-badge :value="relationCount" :max="99" class="admin-layout">{{ $t('general.relations') }}</el-badge>
                            </span>
                            
                            <el-button style="float:right" type="primary" @click="toggleAddRelationDrawer" icon="icon-plus" size="mini" round>{{$t('models.resident.relation.add')}}</el-button>    
                            <relation-list-table
                                    :items="model.relations"
                                    @edit-relation="editRelation"
                                    @delete-relation="deleteRelation">
                            </relation-list-table>
                        </el-tab-pane> -->
                        <el-tab-pane name="buildings">
                            <span slot="label">
                                {{ $t('general.box_titles.buildings') }}
                                <!-- <el-badge :value="buildingCount" :max="99" class="admin-layout">{{ $t('general.box_titles.buildings') }}</el-badge> -->
                            </span>
                            <relation-list
                                :actions="buildingActions"
                                :columns="buildingColumns"
                                :filterValue="model.id"
                                fetchAction="getBuildings"
                                filter="quarter_id"
                                :show-header="true"
                                v-if="model.id"
                            />
                        </el-tab-pane>
                    </el-tabs>
                    
                    <el-tabs type="border-card" v-model="activeRequestTab">
                        <el-tab-pane name="requests">
                            <span slot="label">
                                {{ $t('general.requests') }}
                                <!-- <el-badge :value="requestCount" :max="99" class="admin-layout">{{ $t('general.requests') }}</el-badge> -->
                            </span>
                            <span class="icon-cog" @click="toggleDrawer">
                            </span>
                            <relation-list
                                :actions="requestActions"
                                :columns="requestColumns"
                                :filterValue="model.id"
                                fetchAction="getRequests"
                                filter="building_id"
                                v-if="model.id"
                            />
                        </el-tab-pane>
 
                       
                        <!-- <el-tab-pane name="settings" :disabled="true">
                            <span slot="label" class="icon-cog" @click="toggleDrawer">
                            </span>
                        </el-tab-pane> -->
                    </el-tabs>
                </el-col>
            </el-row>
        </div>
        <ui-drawer :visible.sync="visibleDrawer" :z-index="2" direction="right" docked>
            <template v-if="editingRelation || isAddRelation">
                <ui-divider content-position="left"><i class="icon-handshake-o ti-user icon"></i> &nbsp;&nbsp;{{ $t('models.resident.relation.title') }} </ui-divider>
                <!-- <ui-divider content-position="left"><i class="icon-handshake-o ti-user icon"></i> &nbsp;&nbsp;{{ $t('models.resident.relation.title') }} {{ editingRelation ? '[' + editingRelation.relation_format + ']' : '' }} </ui-divider> -->
                    
                <div class="content" v-if="visibleDrawer"> 
                    <relation-form v-if="editingRelation" 
                                mode="edit" 
                                :quarter_id="model.id" 
                                :show-resident="true"
                                :data="editingRelation" 
                                :resident_type="1" 
                                :resident_id="editingRelation.id" 
                                :visible.sync="visibleDrawer" 
                                :edit_index="editingRelationIndex" 
                                @update-relation="updateRelation" 
                                @delete-relation="deleteRelation"
                                :used_units="used_units"/>
                    <relation-form v-else 
                                mode="add" 
                                :quarter_id="model.id" 
                                :show-resident="true"
                                :resident_type="1" 
                                :visible.sync="visibleDrawer" 
                                @add-relation="addRelation" 
                                @delete-relation="deleteRelation"
                                :used_units="used_units"/>
                </div>
            </template>
            <!-- <template v-else-if="isWorkflow">
                
                <ui-divider content-position="left"><i class="icon-handshake-o ti-user icon"></i> &nbsp;&nbsp;{{ $t('models.quarter.workflow.label') }} </ui-divider>
                
                <div class="content" v-if="visibleDrawer">
                    <workflow-form v-if="editingWorkflow" 
                                mode="edit" 
                                :quarter_id="model.id" 
                                :data="editingWorkflow" 
                                :visible.sync="visibleDrawer"
                                @update-workflow="addWorkflow"/>
                    <workflow-form v-else 
                                mode="add" 
                                :quarter_id="model.id" 
                                :visible.sync="visibleDrawer"
                                @add-workflow="addWorkflow"/>
                </div>
                    
            </template> -->
            <!-- <template v-else>
                <el-tabs type="card" v-model="activeDrawerTab" stretch v-if="visibleDrawer">
                    <el-tab-pane name="emergency" lazy>
                        <div slot="label">
                            <i class="icon-cog"></i>
                            {{$t('general.emergency.title')}}
                        </div>
                        <div class="content" v-if="visibleDrawer">
                            <emergency-settings-form :visible.sync="visibleDrawer"/>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane name="email_receptionist" lazy>
                        <div slot="label">
                            <i class="icon-mail"></i>
                            {{$t('general.email_receptionist.title')}}
                        </div>
                        
                        <div class="content" v-if="visibleDrawer">
                            
                            <email-receptionist-form :quarter_id="model.id" :visible.sync="visibleDrawer"/>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </template> -->
            <template v-else>
                <ui-divider content-position="left"><i class="icon-cog"></i> &nbsp;&nbsp;{{ $t('general.emergency.title') }} </ui-divider>
                
                <div class="content" v-if="visibleDrawer">
                    <emergency-settings-form :visible.sync="visibleDrawer"/>
                </div>
            </template>
        </ui-drawer>
         <edit-close-dialog 
            :centerDialogVisible="visibleDialog"
            @clickYes="visibleDialog=false, submit(true)"
            @clickNo="visibleDialog=false, $refs.editActions.goToListing()"
            @clickCancel="visibleDialog=false"
        ></edit-close-dialog>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import QuartersMixin from 'mixins/adminQuartersMixin';
    import {displayError, displaySuccess} from "helpers/messages";
    import EditActions from 'components/EditViewActions';
    import {mapActions, mapGetters} from 'vuex';
    import RelationList from 'components/RelationListing';
    import UsersAssignment from 'components/UsersAssignment';
    import EmergencySettingsForm from 'components/EmergencySettingsForm';
    import EmailReceptionistForm from 'components/EmailReceptionistForm';
    import WorkflowForm from 'components/WorkflowForm';
    import UploadDocument from 'components/UploadDocument';
    import draggable from 'vuedraggable';
    import { EventBus } from '../../../event-bus.js';
    import RelationForm from 'components/RelationForm';
    import RelationListTable from 'components/RelationListTable';
    import BuildingFileListTable from 'components/BuildingFileListTable';
    import EditCloseDialog from 'components/EditCloseDialog';
    import MultiSelect from 'components/Select';

    export default {
        name: 'AdminRequestsEdit',
        mixins: [QuartersMixin({
            mode: 'edit',
            withRelation: true
        })],
        components: {
            Heading,
            Card,
            EditActions,
            RelationList,
            UsersAssignment,
            EmergencySettingsForm,
            EmailReceptionistForm,
            WorkflowForm,
            UploadDocument,
            draggable,
            RelationForm,
            RelationListTable,
            BuildingFileListTable,
            EditCloseDialog,
            MultiSelect,
        },
        data() {
            return {
                selectedFileCategory: 'house_rules',
                requestColumns: [{
                    type: 'requestIcon',
                    label: 'models.request.prop_title',
                    width: 60,
                }, {
                    type: 'requestTitleWithDesc',
                    label: 'models.request.prop_title'
                }, {
                    type: 'requestStatus',
                    width: 50,
                    label: 'models.request.status.label'
                }],
                requestActions: [/*{
                    width: 70,
                    buttons: [{
                        icon: 'ti-search',
                        title: 'general.actions.edit',
                        onClick: this.requestEditView,
                        tooltipMode: true
                    }]
                }*/],
                assigneesActions: [{
                    width: 70,
                    buttons: [ {
                        title: 'general.unassign',
                        tooltipMode: true,
                        type: 'danger',
                        icon: 'el-icon-close',
                        onClick: this.unassignQuarter
                    }]
                }],
                 assigneesColumns: [{
                    type: 'assignProviderManagerAvatars',
                    width: 40,
                }, {
                    type: 'assigneesName',
                    prop: 'name',
                    label: 'general.name',
                    width: 150,
                }, {
                    type: 'companyName',
                    prop: 'company_name',
                    label: 'general.name'
                }, {
                    type: 'assignProviderManagerFunctions',
                }/*, {
                    prop: 'assignment_types',
                    label: 'general.assignment_types.label',
                    i18n: this.translateAssignmentType
                }*/],
                buildingColumns: [{
                    type: 'buildingHouseName',
                    prop: 'address.house_num',
                    label: 'models.building.building_no'
                }, {
                    type: 'buildingTypes',
                    prop: 'types',
                    label: 'models.building.type'
                }, {
                    align: 'center',
                    prop: 'count_of_apartments_units',
                    label: 'models.building.count_of_apartments_units'
                }/*, {
                    type: 'buildingResidentAvatars',
                    prop: 'residents',
                    propLimit: 2,
                    count: 'residents_count',
                    label: 'general.residents'
                }*/],
                buildingActions: [/*{
                    width: 70,
                    buttons: [{
                        icon: 'ti-search',
                        title: 'general.actions.edit',
                        onClick: this.buildingEditView,
                        tooltipMode: true
                    }]
                }*/],
                residentColumns: [{
                    type: 'requestResidentAvatar',
                    width: 70                    
                }, {
                    type: 'residentNameAndType',
                    label: 'general.name',
                    translate: this.translateResidentTypes
                }, {
                //     prop: 'name',
                //     label: 'general.name',
                //     type: 'residentName'
                // }, {
                //     prop: 'type',
                //     label: 'models.resident.relation.type.label',
                //     i18n: this.translateResidentTypes
                // }, {
                    type: 'residentRelation',
                    label: 'models.resident.relation.title'
                }, {
                    prop: 'status',
                    i18n: this.residentStatusLabel,
                    withBadge: this.residentStatusBadge,
                    label: 'models.resident.status.label'
                }],
                residentActions: [/*{
                    width: 70,
                    buttons: [{
                        title: 'models.resident.view',
                        onClick: this.residentEditView,
                        icon: 'el-icon-user',
                        tooltipMode: true
                    }]
                }*/],
                visibleDrawer: false,
                auditCount: 0,
                fileCount: 0,
                requestCount: 0,
                assigneeCount: 0,
                buildingCount: 0,
                relationCount: 0,
                residentCount: 0,
                workflowCount: 0,
                activeTab1: 'details',
                activeTab2: 'audit',
                activeRightTab: 'assignees',
                activeRequestTab: 'requests',
                editingRelation: null,
                editingWorkflow: null,
                isAddRelation: false,
                isWorkflow: false,
                isAddWorkflow: false,
                isEditingWorkflow: [],
                editingRelationIndex: -1,
                editingWorkflowIndex: -1,
                activeDrawerTab: "emergency",
                workflows: [],
                editMode: false,
                editId: false,
                editName: false,
                visibleDialog: false,
            }
        },
        methods: {
            ...mapActions([
                'deleteQuarter',
                'getQuarterAssignees',
                'getBuildings',
                "uploadQuarterFile", 
                "deleteQuarterFile",
                'saveQuarterWorkflow',
                'updateQuarterWorkflow',
                'deleteQuarterWorkflow'
            ]),
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
            translateResidentTypes(types) {
                if(types.constructor === Array){
                    let translatedTypes = types.map(type => this.$t(`models.resident.relation.type.${this.$constants.relations.type[type]}`))
                    return translatedTypes.join(', ')
                }
            },
            translateAssignmentType(types) {
                let translatedTypes = []
                types.map(type => {
                    translatedTypes.push(this.$t(`models.quarter.assignment_types.${this.constants.quarters.assignment_type[type]}`))
                })

                return translatedTypes.join(', ')
            },
            translateFunction(item) {
                console.log(item)
            },
            residentStatusBadge(status) {
                const classObject = {
                    1: 'icon-success',
                    2: 'icon-danger'
                };

                return classObject[status];
            },
            residentStatusLabel(status) {
                return this.$t(`models.resident.status.${this.residentStatusConstants[status]}`)
            },
            requestEditView(row) {
                this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id: row.id
                    }
                })
            },

            buildingEditView(row) {
                this.$router.push({
                    name: 'adminBuildingsEdit',
                    params: {
                        id: row.id
                    }
                })
            },

            setOrder() {
                _.each(this.model.media, (file, i) => {
                    file.order = i + 1;
                });
                this.$forceUpdate();
            },
            sortFiles() {
                this.setOrder();
            },
            uploadFiles(file) {
                this.insertDocument(this.selectedFileCategory, file);
            },
            insertDocument(prop, file) {
                file.order = this.model.media.length + 1;
                this.uploadQuarterFile({
                    id: this.model.id,
                    [`${prop}_upload`]: file.src
                }).then((resp) => {
                    displaySuccess(resp);
                    this.model.media.push(resp.media);
                    if(this.fileCount){
                        this.fileCount++;
                    } else {
                        this.fileCount = 1;
                    }
                    if(this.$refs.auditList){
                        this.$refs.auditList.fetch();
                    }
                }).catch((err) => {
                    displayError(err);
                });
            },
            deleteDocument(prop, index) {
                this.deleteQuarterFile({
                    id: this.model.id,
                    media_id: this.model[prop][index].id
                }).then((resp) => {
                    displaySuccess(resp);
                    this.fileCount--;
                    this.model[prop].splice(index, 1);
                    this.setOrder(prop);
                    if(this.$refs.auditList){
                        this.$refs.auditList.fetch();
                    }
                }).catch((error) => {
                    displayError(error);
                })
            },
            toggleDrawer() {
                this.visibleDrawer = true;
            },
            toggleAddRelationDrawer() {
                this.isAddRelation = true
                this.visibleDrawer = true;
            },
            addRelation (data) {
                this.model.relations.push(data);
                this.relationCount ++;
            },
            editRelation(index) {
                this.editingRelation = this.model.relations[index];
                this.editingRelationIndex = index;
                this.visibleDrawer = true;
            },
            updateRelation(index, params) {
                this.$set(this.model.relations, index, params);
            },
            deleteRelation(index) {

                this.$confirm(this.$t(`general.swal.delete_relation.text`), this.$t(`general.swal.delete_relation.title`), {
                    type: 'warning'
                }).then(async () => {
                    await this.$store.dispatch('relations/delete', {id: this.model.relations[index].id})
                    this.model.relations.splice(index, 1)
                    this.relationCount --;
                    this.visibleDrawer = false;
                }).catch(() => {
                });
            },
            showAddWorkflow() {
                this.isAddWorkflow = true

            },
            showEditWorkflow(index) {
                this.$set(this.isEditingWorkflow, index, true)
                this.isEditingWorkflow[index] = true
            },
            async addWorkflow(workflow) {
                workflow.quarter_id = this.model.id
                let resp = await this.saveQuarterWorkflow(workflow)

                if(resp && resp.success) {
                    displaySuccess(resp);
                    this.isEditingWorkflow.push(false)
                    this.model.workflows.push(workflow)
                    this.isAddWorkflow = false
                    this.workflowCount ++
                }
                
            },
            cancelAddWorkflow() {
                this.isAddWorkflow = false
            },
            async updateWorkflow(index, workflow) {
                workflow.quarter_id = this.model.id
                workflow.id = this.model.workflows[index].id
                let resp = await this.updateQuarterWorkflow(workflow)
                
                if(resp && resp.success) {
                    displaySuccess(resp);
                    this.$set(this.model.workflows, index, workflow)
                    this.$set(this.isEditingWorkflow, index, false)
                }
                
            },
            cancelEditWorkflow(index) {
                this.$set(this.isEditingWorkflow, index, false)
            },
            async deleteWorkflow(index) {
                console.log(index)
                let resp = await this.deleteQuarterWorkflow({id: this.model.workflows[index].id})

                if(resp && resp.success) {
                    displaySuccess(resp);
                    this.model.workflows.splice(index, 1)
                    this.workflowCount --
                }
            }
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
            requestStatusConstants() {
                return this.constants.requests.status
            },
            residentStatusConstants() {
                return this.constants.residents.status
            },
            contactEnableValues() {
                this.fetchSettings();
                return [{
                    value: 1,
                    label: `${this.$t('settings.contact_enable.use_global')} (${this.contactUseGlobalAddition})`,
                }, {
                    value: 2,
                    label: this.$t('settings.contact_enable.show'),
                }, {
                    value: 3,
                    label: this.$t('settings.contact_enable.hide'),
                }]
            },
            used_units() {
                return this.model.relations.map(item => item.unit_id)
            },
            quarterTypes() {
                let result = '';
                this.types.forEach((type) => {
                    if(this.model.types && this.model.types.indexOf(type.value) !== -1) {
                        if(result !== '')
                            result = `${result}, `;
                        result =  `${result}${type.name}`;
                    }
                });
                return result;
            },
            quarterState() {
                let result = '';
                this.states.forEach((state) => {
                    if(state.id == this.model.state_id)
                        result = state.name;
                });
                return result;
            }
        },
        mounted() {

            EventBus.$on('request-get-counted', request_count => {
                this.requestCount = request_count;
            });

            EventBus.$on('assignee-get-counted', assignee_count => {                
                this.assigneeCount = assignee_count;
            });

            EventBus.$on('resident-get-counted', resident_count => {                
                this.residentCount = resident_count;
            });

            EventBus.$on('building-get-counted', building_count => {
                this.buildingCount = building_count;
            });

            EventBus.$on('audit-get-counted', audit_count => {
                this.auditCount = audit_count;
            });

            this.workflowCategories = [
                'General options',
                'Malfunction',
                'Mangel'
            ]
        },
        watch: {
            'visibleDrawer': {
                immediate: false,
                handler (state) {
                    // TODO - auto blur container if visible is true first
                    if (!state) {
                        this.editingRelation = null
                        this.isAddRelation = false
                        this.isWorkflow = false
                        this.editingWorkflow = null
                    }
                }
            }
        },
    }
</script>

<style lang="scss">
    .label-block .el-form-item__label {
        display: block;
        float: none;
        text-align: left;
    }

    .el-card .el-card__body {
        display: flex;
        flex-direction: column;
    }
</style>
<style lang="scss" scoped>
    .el-tabs--border-card {
        border-radius: 6px;
        .el-tabs__header {
            border-radius: 6px 6px 0 0;
        }
        .el-tabs__nav-wrap.is-top {
            border-radius: 6px 6px 0 0;
        }
    }

    .last-form-row {
        margin-bottom: -22px;
    }

    

    .quarters-edit {
        overflow: hidden;
        flex: 1;

        .main-content {
            overflow-x: hidden;
            overflow-y: scroll;
            height: 100%;
        }

        .crud-view {
            .el-card.quarter-details {
                padding: 0;
                margin: 0px 10px 40px;
                box-shadow: none !important;
                border-color: var(--border-color-base);
                border-radius: 6px;
                background-color: #f6f5f7;

                .el-row {
                    display: flex;
                }

                :global(.el-card__body) {
                    padding: 0px;
                    background-color: inherit;
                }

                .left-pane, .right-pane {
                    padding: 20px !important;
                    .el-col {
                        &:nth-of-type(1) {
                            padding-left: 0px !important;
                        }
                        &:nth-of-type(2) {
                            padding-right: 0px !important;
                        }
                    }
                }

                .left-pane {
                    img {
                        width: 100%;
                        margin-bottom: 15px;
                    }
                    .quarter-id {
                        position: absolute;
                        top: 35px;
                        left: 35px;
                        span {
                            padding: 3px 15px;
                            background-color: rgba(#fff, 0.7);
                            border-radius: 2px;
                            color: var(--color-primary);
                            font-size: 13px;
                            font-weight: 600;
                        }
                    }
                    .quarter-name {
                        font-weight: 900;
                        font-size: 24px;
                        font-family: 'Radikal Bold';
                        letter-spacing: 1.2px;
                        color: var(--text-color);
                    }
                }

                .right-pane {
                    background-color: var(--color-white);

                    .el-form-item {
                        margin-bottom: 10px !important;
                    }

                    .quarter-detail-item {
                        margin-bottom: 25px;
                        display: flex;
                        justify-content: space-between;
                        span:first-child {
                            text-align: left;
                            font-weight: 600;
                            color: var(--text-color);
                        }
                        span:first-child {
                            text-align: right;
                        }
                    }
                }
            }
        }


        /deep/ .el-tabs--border-card {
            border-radius: 6px;
            .el-tabs__header {
                border-radius: 6px 6px 0 0;
            }
            .el-tabs__nav-wrap.is-top {
                border-radius: 6px 6px 0 0;
            }
        }

        .crud-view.el-row > .el-col > .el-tabs {
            
            margin-bottom: 1em;
            
        }

        /deep/ .el-tabs__nav.is-top {
            width: 100%;
            display: flex;

            #tab-settings {
                flex-grow: 1;
                span.icon-cog {
                    cursor: pointer;
                    color: var(--color-text-primary);
                    float: right;
                }
            }
        }
    }

    span.icon-cog {
        cursor: pointer;
        float: right;
    }
    
    .ui-drawer {
        .el-tabs {
            height: 100%;
            /deep/ .el-tabs__header {
                margin-bottom: 0;
            }
            /deep/ .el-tabs__content{
                height: 100%;

                .el-tab-pane {
                    height: 100%;
                }
            }
        }

        .ui-divider {
            margin: 32px 16px 0 16px;
            i {
                padding-right: 0;
            }

            /deep/ .ui-divider__content {
                left: 0;
                z-index: 1;
                padding-left: 0;
                font-size: 16px;
                font-weight: 700;
                color: var(--color-primary);
            }
        }

        .content {
            height: calc(100% - 70px);
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            padding: 16px;
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            position: relative;

        }
    }

    .list-complete-item {
        transition: all 1s;
        display: flex;
        justify-content: space-between;
        border-top: 1px solid #eee;

        > div {
            width: 100%;
        }

        & > .el-col {
            border-left: 1px solid #eee;
            padding-top: 10px;
            min-height: 50px;
            padding-bottom: 10px;
            display: flex;
            align-items: center;

            &:last-child {
                border-right: 1px solid #eee;
                justify-content: center;
            }
        }

        &:last-child {
            border-bottom: 1px solid #eee;
        }
    }

    .list-complete-enter, .list-complete-leave-active {
        opacity: 0;
    }

    .card-label {
        display: block;
        margin-bottom: 15px;
    }

    .file-name {
        max-width: 75%;
        word-wrap: break-word;
        color: #333;
    }

    .category-select {
        margin-bottom: 10px;
        width: 100%;
    }

    .workflow-label {
        line-height: 40px;
    }

    .workflow-button-bar {
        display: flex;
        justify-content: flex-end;
        margin-top: 40px;

        // &.edit {
        //     padding-top: 40px;
        // }
    }

    .el-tag {
        background-color: var(--primary-color);
        color: white;
        border-radius: 6px;
    }

    .el-collapse-item {
        margin-bottom: 10px;
    }

    .el-collapse {
        border-top: 0;
        border-bottom: 0;

        /deep/ .el-collapse-item__header {
            padding-left: 15px;
            background: #f6f5f7;
            border-radius: 6px;
        }

        /deep/ .el-collapse-item__content {
            padding-bottom: 0px;
            // padding-left: 1em;
            padding-left: 0;
        }

        /deep/ .el-collapse-item__wrap {
            padding-top: 5px;
            border-bottom: 0;
        }
    }

    .round-btn {
        border-radius: 3px;
    }

    .add-workflow-btn {
        // border-radius: 5px;
        // padding: 3px;
        //padding: 12px 15px;
    }

    .assign-button {
        background-color: #3D3F41;
        color: var(--color-white);
        &:hover {
            background-color: var(--background-color-base);
        }
    }
</style>
