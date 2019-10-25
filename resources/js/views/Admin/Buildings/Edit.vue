<template>
    <div class="buildings-edit ">
        <div class="main-content">
        <heading :title="$t('models.building.edit_title')" icon="icon-commerical-building" shadow="heavy">
            <template slot="description" v-if="model.building_format">
                <div class="subtitle">{{`${model.building_format} > ${model.name}`}}</div>
            </template>
            <template>
                <div class="action-group">
                    <el-button @click="submit" size="mini" type="primary" round> {{this.$t('general.actions.save')}}</el-button>
                    <el-button @click="saveAndClose" size="mini" type="primary" round> {{this.$t('general.actions.save_and_close')}}
                    </el-button>
                    <el-button @click="batchDeleteBuilding" size="mini" type="danger" round icon="ti-trash"> {{this.$t('general.actions.delete')}}</el-button>
                    <el-button @click="goToListing" size="mini" type="warning" round> {{this.$t('general.actions.close')}}
                    </el-button>
                </div>
            </template>
        </heading>
        <div class="warning-bar">
            <div class="message" type="info">
                <i class="icon-info-circled"></i>{{$t('models.building.warning_bar.message')}}
            </div>
            <div class="title" @click="gotoEmailReceptionistDrawer">
                {{$t('models.building.warning_bar.title')}}
            </div>
        </div>
        <el-row :gutter="20" class="crud-view">
            
            <el-col :md="12">
                <el-tabs type="border-card" v-model="activeTab">
                    <el-tab-pane :label="$t('general.actions.view')" name="details">
                        <el-form :model="model" label-position="top" label-width="192px" ref="form">
                            <el-row :gutter="20">
                                <el-col :md="10">
                                    <el-form-item :label="$t('general.street')" :rules="validationRules.street"
                                                  prop="street"
                                                  style="max-width: 512px;">
                                        <el-input type="text" v-model="model.street" v-on:change="setBuildingName"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="4">
                                    <el-form-item :label="$t('models.building.house_num')"
                                                  :rules="validationRules.house_num"
                                                  prop="house_num" style="max-width: 512px;">
                                        <el-input type="text" v-model="model.house_num" v-on:change="setBuildingName"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="10">
                                    <el-form-item :label="$t('general.name')" :rules="validationRules.name"
                                                  prop="name"
                                                  style="max-width: 512px;">
                                        <el-input type="text" v-model="model.name"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :md="4">
                                    <el-form-item :label="$t('general.zip')" :rules="validationRules.zip"
                                                  prop="zip"
                                                  style="max-width: 512px;">
                                        <el-input type="text" v-model="model.zip"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="8">
                                    <el-form-item :label="$t('general.city')" :rules="validationRules.city"
                                                  prop="city"
                                                  style="max-width: 512px;">
                                        <el-input type="text" v-model="model.city"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.state')"
                                                  :rules="validationRules.state_id"
                                                  prop="state_id" style="max-width: 512px;">
                                        <el-select
                                            filterable
                                            clearable 
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
                            <el-row class="last-form-row" :gutter="20">
                                <el-col :md="8">
                                    <el-form-item :label="$t('models.building.quarter')" prop="quarter_id"
                                                  style="max-width: 512px;">
                                        <el-select
                                                :loading="remoteLoading"
                                                :placeholder="$t('general.placeholders.search')"
                                                :remote-method="remoteSearchQuarters"
                                                filterable
                                                remote
                                                reserve-keyword
                                                style="width: 100%;"
                                                v-model="model.quarter_id">
                                            <el-option
                                                    :label="$t('general.none')"
                                                    value=""
                                            />
                                            <el-option
                                                    :key="quarter.id"
                                                    :label="quarter.name"
                                                    :value="quarter.id"
                                                    v-for="quarter in quarters"/>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="8">
                                    <el-form-item :label="$t('models.building.floor_nr')"
                                                  :rules="validationRules.floor_nr"
                                                  prop="floor_nr" style="max-width: 512px;">
                                        <el-input type="number" v-model="model.floor_nr"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="8">
                                    <el-form-item :rules="validationRules.attic">
                                        <label class="attic-label">{{ $t('models.unit.attic') }}</label>
                                        <el-switch v-model="model.attic"/>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="12">
                                    <el-form-item :label="$t('models.building.under_floor')"
                                                  :rules="validationRules.floor"
                                                  prop="under_floor">
                                        <el-input type="number"
                                                  :min="0"
                                                  v-model.number="model.under_floor"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.building.internal_building_id')"
                                                  :rules="validationRules.internal_building_id"
                                                  prop="internal_building_id" style="max-width: 512px;">
                                        <el-input type="text" v-model="model.internal_building_id"></el-input>
                                    </el-form-item>
                                </el-col>
                                <!-- <el-col :span="12">
                                    <el-form-item :label="$t('models.building.under_floor')"
                                                  :rules="validationRules.floor"
                                                  :prop="'floor.' + 0">
                                        <el-input type="number"
                                                  :min="0"
                                                  v-model.number="model.floor"></el-input>
                                    </el-form-item>
                                </el-col> -->
                                
                            </el-row>
                        </el-form>
                    </el-tab-pane>
                    <el-tab-pane name="units" v-loading="loading.state">
                        <span slot="label">
                            <el-badge :value="unitCount" :max="99" class="admin-layout">{{ $t('models.building.units') }}</el-badge>
                        </span>
                        <relation-list
                            :actions="unitActions"
                            :columns="unitColumns"
                            :filterValue="model.id"
                            fetchAction="getUnitsWithResidents"
                            filter="building_id"
                            v-if="model.id"
                        />
                    </el-tab-pane>
                    <el-tab-pane name="files">
                        <span slot="label">
                            <el-badge :value="fileCount" :max="99" class="admin-layout">{{ $t('models.building.files') }}</el-badge>
                        </span>
                        <draggable @sort="sortFiles" v-model="model.media">
                            <transition-group name="list-complete">
                                <div key="list-complete-item" class="list-complete-item">
                                    <el-table
                                        :data="model.media"
                                        style="width: 100%"
                                        v-if="model.media && model.media.length"
                                        :show-header="false"
                                        >
                                        <el-table-column
                                            prop="collection_name"
                                        >
                                            <template slot-scope="scope">
                                                <strong>{{$t(`models.building.media_category.${scope.row.collection_name}`)}}</strong>
                                            </template>
                                        </el-table-column>
                                        <el-table-column
                                            align="right"
                                        >
                                            <template slot-scope="scope">
                                                <a :href="scope.row.url" class="file-name" target="_blank">
                                                    {{scope.row.name}}
                                                </a>
                                            </template>
                                        </el-table-column>
                                        <el-table-column
                                            align="right"
                                        >
                                            <template slot-scope="scope">
                                                <el-button icon="el-icon-close" type="danger" @click="deleteDocument('media', scope.$index)" size="mini"/>
                                            </template>
                                        </el-table-column>
                                    </el-table>
                                </div>
                                <!-- <el-row :gutter="10" :key="element.name" class="list-complete-item"
                                        v-for="(element, i) in model.media">
                                    <el-col :md="12">
                                        <strong>{{$t(`models.building.${element.collection_name}`)}}</strong>
                                    </el-col>
                                    <el-col :md="11">
                                        <a :href="element.url" class="file-name" target="_blank">
                                            {{element.name}}
                                        </a>
                                    </el-col>
                                    <el-col :md="1" style="text-align: right">
                                        <el-button :style="{color: 'red'}" @click="deleteDocument('media', i)"
                                                   icon="ti-close" size="mini" type="text"
                                        />
                                    </el-col>
                                </el-row> -->
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
                                             v-if="selectedFileCategory"/><!-- @TODO this is uploading file on the spot, is it okay? need to confirm -->
                            
                        </div>
                    </el-tab-pane>
                    

                    
                </el-tabs>
                <div>
                    <raw-grid-statistics-card :cols="8" :data="statistics.raw"/>
                </div>
                <el-row :gutter="15" type="flex">
                    <el-col :span="12">
                        <circular-progress-statistics-card
                            :percentage="+statistics.percentage.occupied_units"
                            :title="$t('models.building.occupied_units')"
                            :color="getUnitsCountColor('occupied_units', 'name')"/>
                    </el-col>
                    <el-col :span="12">
                        <circular-progress-statistics-card
                            :percentage="+statistics.percentage.free_units"
                            :title="$t('models.building.free_units')"
                            :color="getUnitsCountColor('free_units', 'name')"/>
                    </el-col>
                </el-row>
            </el-col>
            <el-col :md="12">
                <el-tabs type="border-card" v-model="activeRightTab">
                    <el-tab-pane name="residents" v-loading="loading.state">                        
                        <span slot="label">
                            <el-badge :value="residentCount" :max="99" class="admin-layout">{{ $t('general.residents') }}</el-badge>
                        </span>
                        <relation-list
                            :actions="residentActions"
                            :columns="residentColumns"
                            :filterValue="model.id"
                            fetchAction="getResidents"
                            filter="building_id"
                            v-if="model.id"
                        />
                    </el-tab-pane>
                    <el-tab-pane name="contracts">
                        <span slot="label">
                            <el-badge :value="contractCount" :max="99" class="admin-layout">{{ $t('general.contracts') }}</el-badge>
                        </span>
                        
                        <el-button style="float:right" type="primary" @click="toggleAddDrawer" icon="icon-plus" size="mini" round>{{$t('models.resident.contract.add')}}</el-button>    
                        <contract-list-table
                                    :items="model.contracts"
                                    @edit-contract="editContract"
                                    @delete-contract="deleteContract">
                        </contract-list-table>
                    </el-tab-pane>
                    <el-tab-pane name="managers">
                        <span slot="label">
                            <el-badge :value="managerCount" :max="99" class="admin-layout">{{ $t('models.building.managers') }}</el-badge>
                        </span>
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
                            fetchAction="getBuildingAssignees"
                            filter="building_id"
                            ref="assigneesList"
                            v-if="model.id"
                        />
                    </el-tab-pane>
                    <el-tab-pane name="companies">
                        <span slot="label">
                            <el-badge :value="serviceCount" :max="99" class="admin-layout">{{ $t('models.building.companies') }}</el-badge>
                        </span>                        
                        <!-- <label class="card-label">{{$t('settings.contact_enable.label')}}</label>
                        <el-select
                                placeholder="Chose"
                                style="width: 100%;"
                                v-model="model.contact_enable"
                        >
                            <el-option
                                    :key="contactEnableValue.id"
                                    :label="contactEnableValue.label"
                                    :value="contactEnableValue.value"
                                    v-for="contactEnableValue in contactEnableValues"/>
                        </el-select>
                        <el-divider class="mt15" /> -->
                        <el-row :gutter="10">
                            <el-col :lg="18" :xl="20">
                                <el-select
                                    clearable
                                    :loading="remoteLoading"
                                    :placeholder="$t('models.building.add_companies')"
                                    :remote-method="remoteSearchProviders"
                                    class="custom-remote-select"
                                    filterable
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
                            filter="building_id"
                            ref="assignmentsProviderList"
                            v-if="model.id"
                        />
                    </el-tab-pane>
                </el-tabs>
                
                <el-tabs type="border-card" v-model="activeRequestTab">
                    <el-tab-pane name="requests" class="aa" :class="requestCount >= 99 ? 'wide-pane' : ''">
                        <span slot="label" class="bb">
                            <el-badge :value="requestCount" :max="99" class="admin-layout">{{ $t('general.requests') }}</el-badge>
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
                        <audit v-if="model.id" :id="model.id" type="building" showFilter/>
                    </el-tab-pane>
                    <el-tab-pane name="settings" :disabled="true">
                        <span slot="label" class="icon-cog" @click="toggleDrawer">
                        </span>
                    </el-tab-pane>
                </el-tabs>
            </el-col>
        </el-row>
        <DeleteBuildingModal 
            :deleteBuildingVisible="deleteBuildingVisible"
            :delBuildingStatus="delBuildingStatus"
            :closeModal="closeDeleteBuildModal"
            :deleteSelectedBuilding="deleteSelectedBuilding"
        />
        </div>
        <ui-drawer :visible.sync="visibleDrawer" :z-index="1" direction="right" docked>
            <template v-if="editingContract || isAddContract">
                <ui-divider content-position="left"><i class="icon-handshake-o ti-user icon"></i> &nbsp;&nbsp;{{ $t('models.resident.contract.title') }}</ui-divider>
                    
                <div class="content" v-if="visibleDrawer">
                    <contract-form v-if="editingContract" 
                                mode="edit"
                                :hide-building="true" 
                                :show-resident="true"
                                :building_id="model.id" 
                                :data="editingContract" 
                                :resident_type="1" 
                                :resident_id="editingContract.id" 
                                :visible.sync="visibleDrawer" 
                                :edit_index="editingContractIndex" 
                                @update-contract="updateContract" 
                                :used_units="used_units"/>
                    <contract-form v-else 
                                mode="add" 
                                :hide-building="true" 
                                :show-resident="true"
                                :building_id="model.id" 
                                :resident_type="1" 
                                :visible.sync="visibleDrawer" 
                                @add-contract="addContract" 
                                :used_units="used_units"/>
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
                            <email-receptionist-form :is-building="true" :quarter_id="model.quarter_id" :visible.sync="visibleDrawer"/>
                        </div>

                    </el-tab-pane>
                </el-tabs>
            </template>
        </ui-drawer>
        
    </div>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import RawGridStatisticsCard from 'components/RawGridStatisticsCard';
    import CircularProgressStatisticsCard from 'components/CircularProgressStatisticsCard';
    import ColoredStatisticsCard from 'components/ColoredStatisticsCard.vue';
    import BuildingsMixin from 'mixins/adminBuildingsMixin';
    import UploadDocument from 'components/UploadDocument';
    import draggable from 'vuedraggable';
    import RelationList from 'components/RelationListing';    
    import globalFunction from "helpers/globalFunction";
    import DeleteBuildingModal from 'components/DeleteBuildingModal';
    import AssignmentByType from 'components/AssignmentByType';
    import EmergencySettingsForm from 'components/EmergencySettingsForm';
    import EmailReceptionistForm from 'components/EmailReceptionistForm';
    import { EventBus } from '../../../event-bus.js';
    import ContractForm from 'components/ContractForm';
    import ContractListTable from 'components/ContractListTable';

    export default {
        mixins: [globalFunction, BuildingsMixin({
            mode: 'edit'
        })],
        components: {
            Heading,
            Card,
            RawGridStatisticsCard,
            CircularProgressStatisticsCard,
            ColoredStatisticsCard,
            UploadDocument,
            draggable,
            RelationList,
            DeleteBuildingModal,
            AssignmentByType,
            EmergencySettingsForm,
            EmailReceptionistForm,
            ContractForm,
            ContractListTable
        },
        data() {
            return {
                selectedFileCategory: 'house_rules',
                activeTab: 'details',
                activeRightTab: 'residents',
                activeRequestTab: 'requests',
                residentColumns: [{
                    type: 'requestResidentAvatar',
                    width: 70                    
                }, {
                    prop: 'name',
                    label: 'general.name',
                    type: 'residentName'
                }, {
                    prop: 'status',
                    i18n: this.residentStatusLabel,
                    withBadge: this.residentStatusBadge,
                    label: 'models.resident.status.label'
                }],
                residentActions: [{
                    buttons: [{
                        title: 'models.resident.view',
                        onClick: this.residentEditView,
                        icon: 'el-icon-user',
                        tooltipMode: true
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
                    prop: 'type',
                    label: 'models.request.user_type.label',
                    i18n: this.translateType
                }],
                assigneesActions: [{
                    width: '180px',
                    buttons: [{
                        title: 'models.building.unassign_manager',
                        type: 'danger',
                        onClick: this.unassignBuilding,
                        tooltipMode: true,
                        icon: 'el-icon-close',
                        view: 'building'
                    }]
                }],
                unitColumns: [{
                    prop: 'name',
                    label: 'models.unit.name'
                },{
                    prop: 'typeLabel',
                    label: 'models.unit.type.label'
                },{
                    prop: 'activeResidents',
                    label: 'general.resident',
                    type: 'unitResidentAvatar',
                }],
                unitActions: [{
                    width: 80,
                    buttons: [{
                        title: 'general.actions.edit',
                        onClick: this.unitEditView,
                        tooltipMode: true,
                        icon: 'el-icon-edit'
                    }]
                }],
                requestColumns: [{
                    type: 'requestResidentAvatar',
                    width: 90,
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
                    width: 120,
                    buttons: [{
                        icon: 'ti-pencil',
                        title: 'general.actions.edit',
                        onClick: this.requestEditView,
                        tooltipMode: true
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
                remoteLoading: false,
                deleteBuildingVisible: false,
                multiple: true,
                delBuildingStatus: -1, // 0: unit, 1: request, 2: both
                contactUseGlobalAddition: '',
                fileCount: 0,                
                residentCount: 0,
                managerCount: 0,
                unitCount: 0,
                requestCount: 0,
                contractCount: 0,
                auditCount: 0,
                visibleDrawer: false,
                editingContract: null,
                isAddContract: false,
                editingContractIndex: -1,
                activeDrawerTab: "emergency"
            };
        },
        methods: {
            ...mapActions([
                'getSettings',
                "uploadBuildingFile",
                "deleteBuildingFile",
                "deleteBuildingService",
                "getBuildingAssignees",
                "assignManagerToBuilding",
                "unassignBuildingAssignee",
                "assignUsersToBuilding",
                "unassignUserToBuilding",
                "deleteBuilding",
                'deleteBuildingWithIds', 
                'checkUnitRequestWidthIds'
            ]),
            translateType(type) {
                return this.$t(`models.request.user_type.${type}`);
            },
            fetchSettings() {
                this.getSettings().then((resp) => {
                    this.contactUseGlobalAddition = resp.data.contact_enable ? this.$t('settings.contact_enable.show') : this.$t('settings.contact_enable.hide');
                }).catch((error) => {
                    displayError(error);
                });
            },
            unassignBuilding(assignee) {
                this.$confirm(this.$t(`general.swal.confirm_change.title`), this.$t('general.swal.confirm_change.warning'), {
                    confirmButtonText: this.$t(`general.swal.confirm_change.confirm_btn_text`),
                    cancelButtonText: this.$t(`general.swal.confirm_change.cancel_btn_text`),
                    type: 'warning'
                }).then(async () => {
                    try {                        
                        const resp = await this.unassignBuildingAssignee({                            
                            assignee_id: assignee.id
                        });

                        displaySuccess(resp);

                        this.$refs.assigneesList.fetch();

                    } catch (e) {
                        displayError(e);
                    } finally {
                        this.loading.status = false;
                    }
                }).catch(() => {
                    this.loading.status = false;
                })

            },
            residentEditView(row) {
                this.$router.push({
                    name: 'adminResidentsView',
                    params: {
                        id: row.id
                    }
                });
            },
            assigneeEditView(row) {                    
                if(row.type == 'user'){
                    this.$router.push({
                        name: 'adminUsersEdit',
                        params: {
                            id: row.edit_id
                        }
                    });
                }
                else if(row.type == 'manager'){
                    this.$router.push({
                        name: 'adminPropertyManagersEdit',
                        params: {
                            id: row.edit_id
                        }
                    });
                }             
            },
            unitEditView(row) {
                 this.$router.push({
                    name: 'adminUnitsEdit',
                    params: {
                        id: row.id
                    }
                });
            },
            requestEditView(request) {
                this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id: request.id
                    }
                })
            },
            requestStatusBadge(status) {                
                return this.getRequestStatusColor(status);
            },
            requestStatusLabel(status) {
                return this.$t(`models.request.status.${this.requestStatusConstants[status]}`)
            },
            insertDocument(prop, file) {
                file.order = this.model.media.length + 1;
                this.uploadBuildingFile({
                    id: this.model.id,
                    [`${prop}_upload`]: file.src
                }).then((resp) => {
                    displaySuccess(resp);
                    this.model.media.push(resp.media);
                }).catch((err) => {
                    displayError(err);
                });
            },
            deleteDocument(prop, index) {
                this.deleteBuildingFile({
                    id: this.model.id,
                    media_id: this.model[prop][index].id
                }).then((resp) => {
                    displaySuccess(resp);
                    this.fileCount--;
                    this.model[prop].splice(index, 1);
                    this.setOrder(prop);
                }).catch((error) => {
                    displayError(error);
                })
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
                if(this.fileCount){
                    this.fileCount++;
                } else {
                    this.fileCount = 1;
                }
            },
            removeService(service) {
                this.deleteBuildingService({
                    building_id: this.$route.params.id,
                    id: service.id
                }).then((resp) => {
                    this.model.service_providers = this.model.service_providers.filter((provider) => {
                        return provider.id !== service.id;
                    });
                    this.serviceCount--;
                    displaySuccess(resp);
                }).catch((error) => {
                    displayError(error);
                });
            },            
            async batchDeleteBuilding() {
                try {              
                    const resp = await this.checkUnitRequestWidthIds({ids:[this.model.id]});                    
                    this.delBuildingStatus = resp.data;

                    if(this.delBuildingStatus == -1) {
                        this.$confirm(this.$t('general.swal.delete.text'), this.$t('general.swal.delete.title'), {
                            type: 'warning'
                        }).then(() => {
                            this.deleteBuilding({id:this.model.id})
                                .then(r => {
                                    displaySuccess(r);
                                    this.goToListing();
                                })
                                .catch(err => displayError(err));                            
                        }).catch(() => {
                        });
                    }else {
                        this.deleteBuildingVisible = true;
                    }
                } catch(err) {
                    displayError(err);
                } finally {                    
                }
            },     
            async deleteSelectedBuilding(isUnits, isRequests) {
                try {
                    const resp = await this.deleteBuildingWithIds({
                        ids: [this.model.id],
                        is_units: isUnits,
                        is_requests: isRequests
                    });
                    this.deleteBuildingVisible = false;
                    displaySuccess(resp); 
                    this.goToListing();            
                } catch (err) {
                    displayError(err);
                } finally {
                }
            },
            closeDeleteBuildModal() {
                this.deleteBuildingVisible = false;
            },

            async saveAndClose() {
                try {
                    const resp = await this.submit();
                    if (resp) {
                        this.goToListing();
                    }
                } catch (e) {
                    console.log(e)
                }
            },
            goToListing() {
                return this.$router.push({
                    name: "adminBuildings",
                    query: this.queryParams
                })
            },
            setBuildingName(event ) {
                this.model.name = this.model.street + ' ' + this.model.house_num;
            },
            toggleDrawer() {
                this.visibleDrawer = true
                document.getElementsByTagName('footer')[0].style.display = "none"
            },
            gotoEmailReceptionistDrawer() {
                this.visibleDrawer = true
                this.activeDrawerTab = "email_receptionist"
                document.getElementsByTagName('footer')[0].style.display = "none"
            },
            toggleAddDrawer() {
                this.visibleDrawer = true
                this.isAddContract = true
                document.getElementsByTagName('footer')[0].style.display = "none"
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
            },
            async unassignProvider(toUnassign) {
                const resp = await this.unassignServiceBuilding({
                    id: toUnassign.id,
                    toAssignId: this.model.id
                });

                this.$refs.assignmentsProviderList.fetch(); 
                this.resetToAssignProviderList();
                this.serviceCount--;
                displaySuccess(resp.data)
            },
            addContract (data) {
                this.model.contracts.push(data);
                this.contractCount ++;
            },
            editContract(index) {
                console.log('this.model.contracts', this.model.contracts, index)
                this.editingContract = this.model.contracts[index];
                this.editingContractIndex = index;
                this.visibleDrawer = true;
                document.getElementsByTagName('footer')[0].style.display = "none";
            },
            updateContract(index, params) {
                this.model.contracts[index] = params;
            },
            deleteContract(index) {

                this.$confirm(this.$t(`general.swal.delete_contract.text`), this.$t(`general.swal.delete_contract.title`), {
                    type: 'warning'
                }).then(async () => {
                    await this.$store.dispatch('contracts/delete', {id: this.model.contracts[index].id})
                    this.model.contracts.splice(index, 1)
                    this.contractCount --;
                }).catch(() => {
                });
            },
        },
        mounted() {
            this.$root.$on('changeLanguage', () => this.getStates());            

            EventBus.$on('assignee-get-counted', manager_count => {                
                this.managerCount = manager_count;
                if(this.managerCount >= 99) {
                    document.getElementById('tab-managers').style.paddingRight = '50px';
                }
            });
            EventBus.$on('unit-get-counted', unit_count => {
                this.unitCount = unit_count;
                if(this.unitCount >= 99) {
                    document.getElementById('tab-units').style.paddingRight = '50px';
                }
            });
            EventBus.$on('request-get-counted', request_count => {
                this.requestCount = request_count;
                if(this.requestCount >= 99) {
                    document.getElementById('tab-requests').style.paddingRight = '50px';
                }
            });
            EventBus.$on('resident-get-counted', resident_count => {                
                this.residentCount = resident_count;
                if(this.residentCount >= 99) {
                    document.getElementById('tab-residents').style.paddingRight = '50px';
                }
            });
            EventBus.$on('audit-get-counted', audit_count => {
                this.auditCount = audit_count;
                if(this.auditCount >= 99) {
                    document.getElementById('tab-audit').style.paddingRight = '50px';
                }
            });
            // this.fileCount = this.model.media.length;
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
        watch: {
            'visibleDrawer': {
                immediate: false,
                handler (state) {
                    // TODO - auto blur container if visible is true first
                    if (!state) {
                        this.editingContract = null
                        this.isAddContract = false
                        document.getElementsByTagName('footer')[0].style.display = "block";
                    }
                }
            }
        },
    }
</script>

<style lang="scss">
    .el-tabs--border-card {
        border-radius: 6px;
        .el-tabs__header {
            border-radius: 6px 6px 0 0;
        }
        .el-tabs__nav-wrap.is-top {
            border-radius: 6px 6px 0 0;
        }
    }
    
    #tab-files, #tab-companies, #tab-requests, #tab-residents, #tab-contracts, #tab-managers, #tab-units, #tab-audit{
        padding-right: 40px;
    }
</style>
<style lang="scss" scoped>
    .last-form-row {
        margin-bottom: -22px;
    }


    .mt15 {
        margin-top: 15px;
    }

    .buildings-edit {
        overflow: hidden;
        flex: 1;

        .main-content {
            overflow-x: hidden;
            overflow-y: scroll;
            height: 100%;

            .heading {
                //margin-bottom: 20px;
            }

            .warning-bar {
                background-color: var(--primary-color); 
                color: white;
                min-height: 20px;
                padding: 10px;
                margin-bottom: 10px;
                display: flex;

                .message {
                    flex-grow: 1;
                    font-size: 13px;
                    line-height: 20px;

                    i {
                        font-size: 15px;
                        margin-right: 5px;
                    }
                }

                .title {
                    float: right;
                    font-size: 15px;
                    font-weight: bold;
                    text-transform: uppercase;
                    min-width: 140px;
                    cursor: pointer;
                }
            }

            .action-group > .el-button:not(:first-child) {
                margin-left: 0px;
            }

            > .el-row > .el-col {
                /*min-width: 448px;*/
                /*max-width: 576px;*/

                :global(.el-card) {
                    label {
                        margin-bottom: .5em;
                        display: block;
                    }
                }

                > *:not(:last-of-type) {
                    margin-bottom: 1em;
                }
            }

            > .el-row > .el-col:last-of-type:not(.custom-column) {
                /*min-width: 448px;*/
                /*max-width: 576px;*/

                :global(.el-card) {
                    label {
                        margin-bottom: .5em;
                        display: block;
                    }
                }

                > *:not(:last-of-type) {
                    margin-bottom: 1em;
                }
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

    .btn-assign {
        width: 100%;
    }

</style>
