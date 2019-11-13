<template>
    <div class="quarters-edit" v-loading.fullscreen.lock="loading.state">
        <div class="main-content">
            <heading :title="$t('models.quarter.edit')" icon="icon-chat-empty" shadow="heavy">
                <template slot="description" v-if="model.quarter_format">
                    <!-- <div class="subtitle">{{`${model.quarter_format} > ${model.name}`}}</div> -->
                    <div class="subtitle">{{model.quarter_format}}</div>
                </template>
                <edit-actions :saveAction="submit" :deleteAction="deleteQuarter" route="adminQuarters"/>
            </heading>
            <el-row :gutter="20" class="crud-view">
                <el-col :md="12">
                    <el-tabs type="border-card" v-model="activeTab1">
                        <el-tab-pane :label="$t('general.box_titles.details')" name="details">
                            <el-form :model="model" ref="form">
                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.quarter.types.label')" :rules="validationRules.type"
                                                class="label-block"
                                                prop="types">
                                            <!-- <el-select :placeholder="$t('general.placeholders.select')"
                                                        style="display: block"
                                                        v-model="model.type">
                                                <el-option
                                                        :key="type.value"
                                                        :label="type.name"
                                                        :value="type.value"
                                                        v-for="type in types">
                                                </el-option>
                                            </el-select> -->
                                            <el-select
                                                    :placeholder="$t('general.placeholders.select')"
                                                    style="display: block"
                                                    v-model="model.types"
                                                    multiple
                                                    filterable>
                                                <el-option
                                                        :key="type.value"
                                                        :label="type.name"
                                                        :value="type.value"
                                                        v-for="type in types">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                
                                    <el-col :md="12">
                                        <el-form-item :label="$t('general.name')" :rules="validationRules.name"
                                                    prop="name">
                                            <el-input type="text" v-model="model.name"/>
                                        </el-form-item>
                                    </el-col>
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
                                    <el-col :md="12">
                                        <el-row :gutter="10">
                                            <el-col :md="8">
                                                <el-form-item :label="$t('general.zip')" :rules="validationRules.zip"
                                                            prop="zip">
                                                    <el-input type="text" v-model="model.zip"/>
                                                </el-form-item>
                                            </el-col>
                                            <el-col :md="16">
                                                <el-form-item :label="$t('general.city')" :rules="validationRules.city"
                                                            prop="city">
                                                    <el-input type="text" v-model="model.city"/>
                                                </el-form-item>
                                            </el-col>
                                        </el-row>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-form-item 
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
                                    <el-col :md="12">
                                        <el-form-item :label="$t('general.internal_quarter_id')" :rules="validationRules.internal_quarter_id"
                                                        prop="internal_quarter_id">
                                            <el-input type="text" v-model="model.internal_quarter_id"></el-input>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.quarter.url')" :rules="validationRules.url"
                                                        prop="url">
                                            <el-input type="text" v-model="model.url"></el-input>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane name="files">
                            <span slot="label">
                                <el-badge :value="fileCount" :max="99" class="admin-layout">{{ $t('general.box_titles.files') }}</el-badge>
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


                    <!-- <card :loading="loading" :header="$t('general.requests')" class="mt15">
                        <div slot="header" style="width: 100%;">
                            {{$t('general.requests')}}
                            <span style="float:right" class="icon-cog" @click="toggleDrawer"></span>
                        </div>
                        <relation-list
                                :actions="requestActions"
                                :columns="requestColumns"
                                :filterValue="model.id"
                                fetchAction="getRequests"
                                filter="quarter_id"
                                v-if="model.id"
                        />
                    </card> -->

                </el-col>
                <el-col :md="12">
                    <el-tabs type="border-card" v-model="activeRightTab">
                        <el-tab-pane name="assignees">                        
                            <span slot="label">
                                <el-badge :value="assigneeCount" :max="99" class="admin-layout">{{ $t('general.box_titles.managers') }}</el-badge>
                            </span>
                            <!-- <assignment-by-type
                                :resetToAssignList="resetToAssignList"
                                :assignmentType.sync="assignmentType"
                                :toAssign.sync="toAssign"
                                :assignmentTypes="assignmentTypes"
                                :assign="assignUser"
                                :toAssignList="toAssignList"
                                :remoteLoading="remoteLoading"
                                :remoteSearch="remoteSearchAssignees"
                            /> -->
                            <el-row :gutter="10" id="managerAssignBox">
                                <el-col id="managerSelect">
                                    <el-select
                                        clearable
                                        :loading="remoteLoading"
                                        :placeholder="$t('general.placeholders.search')"
                                        :remote-method="remoteSearchAssignees"
                                        class="custom-remote-select"
                                        filterable
                                        remote
                                        reserve-keyword
                                        style="width: 100%;"
                                        v-model="toAssign"
                                    >
                                        <div class="custom-prefix-wrapper" slot="prefix">
                                            <i class="el-icon-search custom-icon"></i>
                                        </div>
                                        <el-option
                                                :key="assignee.id"
                                                :label="assignee.name"
                                                :value="assignee.id"
                                                v-for="assignee in toAssignList"/>
                                    </el-select>
                                </el-col>
                                <el-col>
                                    <el-select
                                            :placeholder="$t('general.placeholders.select')"
                                            style="display: block"
                                            multiple
                                            v-model="userAssignmentType"
                                            filterable>
                                        <el-option
                                                :key="type.value"
                                                :label="type.name"
                                                :value="type.value"
                                                v-for="type in assignment_types">
                                        </el-option>
                                    </el-select>
                                </el-col>
                                <el-col id="managerAssignBtn">
                                    <el-button :disabled="!toAssign" @click="assignUser" class="full-button"
                                                icon="ti-save" type="primary">
                                        &nbsp;{{$t('general.assign')}}
                                    </el-button>
                                </el-col>
                            </el-row>
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
                        <el-tab-pane name="buildings">
                            <span slot="label">
                                <el-badge :value="buildingCount" :max="99" class="admin-layout">{{ $t('general.box_titles.buildings') }}</el-badge>
                            </span>
                            <relation-list
                                :actions="quarterActions"
                                :columns="quarterColumns"
                                :filterValue="model.id"
                                fetchAction="getBuildings"
                                filter="quarter_id"
                                v-if="model.id"
                            />
                        </el-tab-pane>
                        <el-tab-pane name="residents">                        
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
                        </el-tab-pane>
                        <!-- <el-tab-pane name="contracts">
                            <span slot="label">
                                <el-badge :value="contractCount" :max="99" class="admin-layout">{{ $t('general.contracts') }}</el-badge>
                            </span>
                            
                            <el-button style="float:right" type="primary" @click="toggleAddDrawer" icon="icon-plus" size="mini" round>{{$t('models.resident.contract.add')}}</el-button>    
                            <contract-list-table
                                    :items="model.contracts"
                                    @edit-contract="editContract"
                                    @delete-contract="deleteContract">
                            </contract-list-table>
                        </el-tab-pane> -->
                    </el-tabs>
                    
                    <el-tabs type="border-card" v-model="activeRequestTab">
                        <el-tab-pane name="requests">
                            <span slot="label">
                                <el-badge :value="requestCount" :max="99" class="admin-layout">{{ $t('general.requests') }}</el-badge>
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
                        <el-tab-pane name="audit" style="height: 400px;overflow:auto;">
                            <span slot="label">
                                {{ $t('general.audits') }}
                                <!-- <el-badge :value="auditCount" :max="99" class="admin-layout">{{ $t('general.audits') }}</el-badge> -->
                            </span>
                            <audit v-if="model.id" :id="model.id" type="quarter" ref="auditList" showFilter/>
                        </el-tab-pane>
                        <el-tab-pane name="workflow">
                            <span slot="label">
                                {{ $t('models.quarter.workflow.label') }}
                            </span>
                            <el-row :gutter="20">
                                <div class="workflow-button-bar">
                                    <el-button 
                                            type="primary" 
                                            @click="showAddWorkflow" 
                                            icon="icon-plus" 
                                            size="mini" 
                                            round>
                                            {{ $t('models.quarter.workflow.add') }}
                                    </el-button>
                                </div>
                            </el-row>
                            <workflow-form v-if="isAddWorkflow"
                                mode="add" 
                                :quarter_id="model.id" 
                                :visible.sync="visibleDrawer"
                                @add-workflow="addWorkflow"/>
                            <el-collapse accordion>
                                 <el-collapse-item
                                        :key="workflow.title"
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
                                            @update-workflow="updateWorkflow"/>
                                    <el-row v-else>
                                        <el-col :md="4" class="workflow-label">
                                            <span>{{$t(`models.request.category_list.${workflow.category.name}`)}}</span>
                                        </el-col>
                                        <el-col :md="1" class="workflow-label">
                                            <span>{{$t('models.quarter.workflow.by')}}</span>
                                        </el-col>
                                        <el-col :md="5" class="workflow-label">
                                            <el-tag 
                                                    type="primary" 
                                                    :key="building.id"
                                                    v-for="building in workflow.buildings">
                                                    {{building.address.house_num}}
                                            </el-tag>
                                        </el-col>
                                        <el-col :md="1" class="workflow-label">
                                            <span>{{$t('models.quarter.workflow.to')}}</span>
                                        </el-col>
                                        <el-col :md="6" class="workflow-label">
                                            <el-tag 
                                                    type="primary" 
                                                    :key="user.id"
                                                    v-for="user in workflow.to_users">
                                                {{user.name}}
                                            </el-tag>
                                        </el-col>
                                        <el-col :md="1" class="workflow-label">
                                            <span>{{$t('models.quarter.workflow.cc')}}</span>
                                        </el-col>
                                        <el-col :md="6" class="workflow-label">
                                            <el-tag 
                                                    :key="user.id"
                                                    v-for="user in workflow.cc_users">
                                                {{user.name}}
                                            </el-tag>
                                        </el-col>
                                    </el-row>
                                    <el-row v-if="!isEditingWorkflow[$index]">
                                        <el-col class="edit workflow-button-bar">
                                            <el-button 
                                                type="primary" 
                                                @click="showEditWorkflow($index)"
                                                icon="icon-search" 
                                                size="mini" 
                                                round>
                                                {{ $t('models.quarter.workflow.edit') }}
                                            </el-button>
                                        </el-col>
                                    </el-row>
                                </el-collapse-item>
                            </el-collapse>
                        </el-tab-pane>
                        <!-- <el-tab-pane name="settings" :disabled="true">
                            <span slot="label" class="icon-cog" @click="toggleDrawer">
                            </span>
                        </el-tab-pane> -->
                    </el-tabs>
                </el-col>
            </el-row>
        </div>
        <ui-drawer :visible.sync="visibleDrawer" :z-index="1" direction="right" docked>
            <template v-if="editingContract || isAddContract">
                <ui-divider content-position="left"><i class="icon-handshake-o ti-user icon"></i> &nbsp;&nbsp;{{ $t('models.resident.contract.title') }} {{ editingContract ? '[' + editingContract.contract_format + ']' : '' }} </ui-divider>
                    
                <div class="content" v-if="visibleDrawer"> 
                    <contract-form v-if="editingContract" 
                                mode="edit" 
                                :quarter_id="model.id" 
                                :show-resident="true"
                                :data="editingContract" 
                                :resident_type="1" 
                                :resident_id="editingContract.id" 
                                :visible.sync="visibleDrawer" 
                                :edit_index="editingContractIndex" 
                                @update-contract="updateContract" 
                                @delete-contract="deleteContract"
                                :used_units="used_units"/>
                    <contract-form v-else 
                                mode="add" 
                                :quarter_id="model.id" 
                                :show-resident="true"
                                :resident_type="1" 
                                :visible.sync="visibleDrawer" 
                                @add-contract="addContract" 
                                @delete-contract="deleteContract"
                                :used_units="used_units"/>
                </div>
            </template>
            <template v-else-if="isWorkflow">
                
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
                    
            </template>
            <template v-else>
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
            </template>
        </ui-drawer>
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
    import AssignmentByType from 'components/AssignmentByType';
    import EmergencySettingsForm from 'components/EmergencySettingsForm';
    import EmailReceptionistForm from 'components/EmailReceptionistForm';
    import WorkflowForm from 'components/WorkflowForm';
    import UploadDocument from 'components/UploadDocument';
    import draggable from 'vuedraggable';
    import { EventBus } from '../../../event-bus.js';
    import ContractForm from 'components/ContractForm';
    import ContractListTable from 'components/ContractListTable';
    import BuildingFileListTable from 'components/BuildingFileListTable';

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
            AssignmentByType,
            EmergencySettingsForm,
            EmailReceptionistForm,
            WorkflowForm,
            UploadDocument,
            draggable,
            ContractForm,
            ContractListTable,
            BuildingFileListTable
        },
        data() {
            return {
                selectedFileCategory: 'house_rules',
                requestColumns: [{
                    type: 'requestResidentAvatar',
                    width: 100,
                    prop: 'resident',
                    label: 'general.resident'
                }, {
                    type: 'requestTitleWithDesc',
                    label: 'models.request.prop_title'
                }, {
                    type: 'requestStatus',
                    width: 120,
                    label: 'models.request.status.label'
                }],
                requestActions: [{
                    width: 70,
                    buttons: [{
                        icon: 'ti-search',
                        title: 'general.actions.edit',
                        onClick: this.requestEditView,
                        tooltipMode: true
                    }]
                }],
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
                    width: 70,
                }, {
                    type: 'assigneesName',
                    prop: 'name',
                    label: 'general.name'
                }, {
                    prop: 'role',
                    label: 'general.assignment_types.label',
                    i18n: this.translateAssignmentType
                }],
                quarterColumns: [{
                    type: 'buildingName',
                    prop: 'name',
                    label: 'general.name'
                }, {
                    align: 'center',
                    prop: 'units_count',
                    label: 'dashboard.buildings.total_units'
                }, {
                    type: 'buildingResidentAvatars',
                    prop: 'residents',
                    propLimit: 2,
                    count: 'residents_count',
                    label: 'general.residents'
                }],
                quarterActions: [{
                    width: 70,
                    buttons: [{
                        icon: 'ti-search',
                        title: 'general.actions.edit',
                        onClick: this.buildingEditView,
                        tooltipMode: true
                    }]
                }],
                residentColumns: [{
                    type: 'requestResidentAvatar',
                    width: 70                    
                }, {
                    type: 'residentNameAndType',
                    label: 'general.name',
                    translate: this.translateResidentType
                }, {
                //     prop: 'name',
                //     label: 'general.name',
                //     type: 'residentName'
                // }, {
                //     prop: 'type',
                //     label: 'models.resident.type.label',
                //     i18n: this.translateResidentType
                // }, {
                    type: 'residentContract',
                    label: 'models.resident.contract.title'
                }, {
                    prop: 'status',
                    i18n: this.residentStatusLabel,
                    withBadge: this.residentStatusBadge,
                    label: 'models.resident.status.label'
                }],
                residentActions: [{
                    width: 70,
                    buttons: [{
                        title: 'models.resident.view',
                        onClick: this.residentEditView,
                        icon: 'el-icon-user',
                        tooltipMode: true
                    }]
                }],
                visibleDrawer: false,
                auditCount: 0,
                fileCount: 0,
                requestCount: 0,
                assigneeCount: 0,
                buildingCount: 0,
                contractCount: 0,
                residentCount: 0,
                activeTab1: 'details',
                activeRightTab: 'assignees',
                activeRequestTab: 'requests',
                editingContract: null,
                editingWorkflow: null,
                isAddContract: false,
                isWorkflow: false,
                isAddWorkflow: false,
                isEditingWorkflow: [],
                editingContractIndex: -1,
                editingWorkflowIndex: -1,
                activeDrawerTab: "emergency",
                workflows: []
            }
        },
        methods: {
            ...mapActions([
                'deleteQuarter',
                'getQuarterAssignees',
                'getBuildings',
                "uploadQuarterFile", 
                "deleteQuarterFile",
            ]),
            translateType(type) {
                return this.$t(`general.assignment_types.${type}`);
            },
            translateResidentType(type) {
                return this.$t(`models.resident.type.${this.constants.residents.type[type]}`);
            },
            translateAssignmentType(type) {
                return this.$t(`models.quarter.assignment_types.${this.constants.quarters.assignment_type[type]}`);
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
                document.getElementsByTagName('footer')[0].style.display = "none";
            },
            toggleAddDrawer() {
                this.isAddContract = true
                this.visibleDrawer = true;
                
                document.getElementsByTagName('footer')[0].style.display = "none";
            },
            toggleWorkflowDrawer() {
                this.visibleDrawer = true
                this.isWorkflow = true
                document.getElementsByTagName('footer')[0].style.display = "none";
            },
            showAddWorkflow() {
                this.isAddWorkflow = true
            },
            showEditWorkflow(index) {
                this.$set(this.isEditingWorkflow, index, true)
                this.isEditingWorkflow[index] = true
            },
            editWorkflowDrawer(index) {
                this.visibleDrawer = true
                this.isWorkflow = true

                this.editingWorkflow = this.workflows[index];
                this.editingWorkflowIndex = index;

                document.getElementsByTagName('footer')[0].style.display = "none";
            },
            addContract (data) {
                this.model.contracts.push(data);
                this.contractCount ++;
            },
            editContract(index) {
                this.editingContract = this.model.contracts[index];
                this.editingContractIndex = index;
                this.visibleDrawer = true;
                document.getElementsByTagName('footer')[0].style.display = "none";
            },
            updateContract(index, params) {
                this.$set(this.model.contracts, index, params);
            },
            deleteContract(index) {

                this.$confirm(this.$t(`general.swal.delete_contract.text`), this.$t(`general.swal.delete_contract.title`), {
                    type: 'warning'
                }).then(async () => {
                    await this.$store.dispatch('contracts/delete', {id: this.model.contracts[index].id})
                    this.model.contracts.splice(index, 1)
                    this.contractCount --;
                    this.visibleDrawer = false;
                }).catch(() => {
                });
            },
            async remoteSearchBuildings(search) {
                if (search === '') {
                    this.resetBuildingList();
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getBuildings({
                            get_all: true,
                            quarter_id: this.model.id,
                            search
                        });

                        this.workflowBuildingList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            resetBuildingList() {
                this.workflowBuildingList = [];
                this.selectedWorkflowBuilding = [];
            },
            async remoteSearchToUsers(search) {
                if (search === '') {
                    this.resetToUserList();
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getUsers({
                            get_all: true,
                            get_role: true,
                            search,
                            roles: ['manager', 'administrator', 'provider']
                        });


                        this.workflowToUserList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            resetToUserList() {
                this.workflowToUserList = [];
                this.selectedWorkflowToUser = [];
            },
            async remoteSearchCcUsers(search) {
                if (search === '') {
                    this.resetCcUserList();
                } else {
                    this.remoteLoading = true;

                    try {
                       const resp = await this.getUsers({
                            get_all: true,
                            get_role: true,
                            search,
                            roles: ['manager', 'administrator', 'provider']
                        });

                        this.workflowCcUserList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            resetCcUserList() {
                this.workflowCcUserList = [];
                this.selectedWorkflowCcUser = [];
            },
            addWorkflow(flow) {
                console.log('add flow', flow)
                this.isEditingWorkflow.push(false)
                this.model.workflows.push(flow)
                this.isAddWorkflow = false
            },
            updateWorkflow(index, flow) {
                console.log('update flow', index, flow)
                this.$set(this.isEditingWorkflow, index, false)
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
                return this.model.contracts.map(item => item.unit_id)
            },
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
                        this.editingContract = null
                        this.isAddContract = false
                        this.isWorkflow = false
                        this.editingWorkflow = null
                        document.getElementsByTagName('footer')[0].style.display = "block";
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
            margin-top: 1%;
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
    
    #managerAssignBox {
        display: flex;

        #managerSelect {
            width: 100%;
        }

        #managerAssignBtn {
            flex: 1;
        }
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
        padding-bottom: 10px;

        &.edit {
            padding-top: 10px;
        }
    }

    .el-tag {
        background-color: var(--primary-color);
        color: white;
        border-radius: 6px;
    }

    .el-collapse {
        .el-collapse-item__content {
            padding-bottom: 10px;
        }
    }
</style>
