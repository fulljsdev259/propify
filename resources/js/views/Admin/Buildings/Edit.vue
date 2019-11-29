<template>
    <div class="buildings-edit " v-loading.fullscreen.lock="loading.state">
        <div class="main-content">
        <heading :title="$t('models.building.edit_title')" icon="icon-commerical-building" shadow="heavy" bgClass="bg-transparent">
            <template slot="description" v-if="model.building_format">
                <div class="subtitle">{{`${model.building_format} > ${model.name}`}}</div>
            </template>
            <edit-actions :saveAction="submit" :deleteAction="batchDeleteBuilding" route="adminBuildings" :editMode="editMode" @edit-mode="handleChangeEditMode" ref="editActions"/>
            <!-- <template>
                <div class="action-group">
                    <el-button @click="submit" size="mini" type="primary" round> {{this.$t('general.actions.save')}}</el-button>
                    <el-button @click="saveAndClose" size="mini" type="primary" round> {{this.$t('general.actions.save_and_close')}}
                    </el-button>
                    <el-button @click="batchDeleteBuilding" size="mini" type="danger" round icon="ti-trash"> {{this.$t('general.actions.delete')}}</el-button>
                    <el-button @click="goToListing" size="mini" type="warning" round> {{this.$t('general.actions.close')}}
                    </el-button>
                </div>
            </template> -->
        </heading>
        <!-- <div class="warning-bar" v-if="!loading.state && !model.has_email_receptionists">
            <div class="message" type="info">
                <i class="icon-info-circled"></i>{{$t('models.building.warning_bar.message')}}
            </div>
            <div class="title" @click="gotoEmailReceptionistDrawer">
                {{$t('models.building.warning_bar.title')}}
            </div>
        </div> -->
        <el-row :gutter="20" class="crud-view">
            
            <el-col :md="12">
                    <el-card class="building-details">
                        <el-form :model="model" label-position="top" label-width="192px" ref="form" class="edit-details-form">
                            <el-row :gutter="20">
                                <el-col :span="12" class="left-pane">
                                    <img :src="require('img/default_img_object.png')"/>

                                    <div v-if="!editId" class="quarter-id">
                                        <span @dblclick="editId=editMode">{{ model.internal_building_id }}</span>
                                    </div>
                                    <el-form-item 
                                        v-if="editMode && editId" 
                                        :rules="validationRules.internal_building_id"
                                        prop="internal_building_id" 
                                        class='quarter-id'
                                    >
                                        <el-input type="text" v-model="model.internal_building_id" ></el-input>
                                    </el-form-item>

                                    <span v-if="!editName" class="quarter-name" @dblclick="editName=editMode">{{ quarterName }}</span>
                                     <el-form-item  prop="quarter_id" v-if="editMode && editName" class="edit-name-input">
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
                                <el-col :span="12" class='right-pane'>
                                   <div v-if="!editMode" class="detail-item">
                                        <span>{{ $t('general.street') }} / {{ $t('models.building.house_num') }}</span>
                                        <span>{{ model.street }} {{ model.house_num }}</span>
                                    </div>
                                    <el-col :span="12">
                                        <el-form-item v-if="editMode" :label="$t('general.street')" :rules="validationRules.street"
                                                    prop="street"
                                                    style="max-width: 512px;">
                                            <el-input type="text" v-model="model.street" v-on:change="setBuildingName" ></el-input>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="12">
                                        <el-form-item v-if="editMode" :label="$t('models.building.house_num')"
                                                    :rules="validationRules.house_num"
                                                    prop="house_num" style="max-width: 512px;">
                                            <el-input type="text" v-model="model.house_num" v-on:change="setBuildingName"></el-input>
                                        </el-form-item>
                                    </el-col>
                                    <!-- <el-col :md="10">
                                        <el-form-item :label="$t('general.name')" :rules="validationRules.name"
                                                    prop="name"
                                                    style="max-width: 512px;">
                                            <el-input type="text" v-model="model.name"  :disabled="!editMode"></el-input>
                                        </el-form-item>
                                    </el-col> -->
                                    
                                   <div v-if="!editMode" class="detail-item">
                                        <span>{{ $t('models.building.type') }}</span>
                                        <span>{{ buildingTypes }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('models.building.type')"
                                                class="label-block"
                                                :rules="validationRules.types"
                                                prop="types">
                                        <multi-select
                                            :name="$t('models.building.type')"
                                            :data="types"
                                            :selectedOptions="model.types"
                                            tagColor="#9E9FA0"
                                            bgColor="#f6f5f7"
                                            showMultiTag
                                            @select-changed="model.types=$event"
                                        ></multi-select>
                                    </el-form-item>

                                    <div v-if="!editMode" class="detail-item">
                                        <span>{{ $t('general.zip') }} / {{ $t('general.city') }}</span>
                                        <span>{{ model.zip }} {{ model.city }}</span>
                                    </div>
                                    <el-col :md="7" v-if="editMode" class="pl-0">
                                        <el-form-item :label="$t('general.zip')" :rules="validationRules.zip"
                                                    prop="zip"
                                                    style="max-width: 512px;">
                                            <el-input type="text" v-model="model.zip" ></el-input>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="17" v-if="editMode" class="pr-0">
                                        <el-form-item :label="$t('general.city')" :rules="validationRules.city"
                                                    prop="city"
                                                    style="max-width: 512px;">
                                            <el-input type="text" v-model="model.city"></el-input>
                                        </el-form-item>
                                    </el-col>

                                    <div v-if="!editMode" class="detail-item">
                                        <span>{{ $t('general.state') }}</span>
                                        <span>{{ buildingState }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('general.state')"
                                                :rules="validationRules.state_id"
                                                prop="state_id">
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
                                    
                                    <div v-if="!editMode" class="detail-item">
                                        <span>{{ $t('models.building.floor_nr') }}</span>
                                        <span>{{ model.floor_nr }}</span>
                                    </div>
                                    <div v-if="!editMode" class="detail-item">
                                        <span>{{ $t('models.building.under_floor') }}</span>
                                        <span>{{ model.under_floor }}</span>
                                    </div>
                                    <el-col :md="12" v-if="editMode" class="pl-0">
                                        <el-form-item :label="$t('models.building.floor_nr')"
                                                    :rules="validationRules.floor_nr"
                                                    prop="floor_nr" style="max-width: 512px;">
                                            <el-input type="number" v-model="model.floor_nr" ></el-input>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="12" v-if="editMode" class="pr-0">
                                        <el-form-item :label="$t('models.building.under_floor')"
                                                    :rules="validationRules.under_floor"
                                                    prop="under_floor">
                                            <el-input type="number"
                                                    :min="0"
                                                    :max="3"
                                                    v-model.number="model.under_floor"></el-input>
                                        </el-form-item>
                                    </el-col>

                                    <div v-if="!editMode" class="detail-item">
                                        <span>{{ $t('models.unit.attic') }}</span>
                                        <span>{{ model.attic?$t('general.yes'):$t('general.no') }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :rules="validationRules.attic" class="detail-attic">
                                        <label class="attic-label">{{ $t('models.unit.attic') }}</label>
                                        <el-switch v-model="model.attic" />
                                    </el-form-item>
                                    <!-- <el-col :span="12">
                                        <el-form-item :label="$t('models.building.under_floor.title')"
                                                    :rules="validationRules.floor"
                                                    :prop="'floor.' + 0">
                                            <el-input type="number"
                                                    :min="0"
                                                    v-model.number="model.floor"></el-input>
                                        </el-form-item>
                                    </el-col> -->
                                </el-col>
                            </el-row>
                        </el-form>
                    </el-card>
                <!-- <div>
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
                </el-row> -->
            </el-col>
            <el-col :md="12">
                <el-tabs type="border-card" v-model="activeRightTab">
                    <el-tab-pane name="residents">                        
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
                    <el-tab-pane name="units" >
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
                                            v-if="selectedFileCategory"/>
                            
                        </div>
                    </el-tab-pane>
                    

                    
                    <!-- <el-tab-pane name="relations">
                        <span slot="label">
                            <el-badge :value="relationCount" :max="99" class="admin-layout">{{ $t('general.relations') }}</el-badge>
                        </span>
                        
                        <el-button style="float:right" type="primary" @click="toggleAddDrawer" icon="icon-plus" size="mini" round>{{$t('models.resident.relation.add')}}</el-button>    
                        <relation-list-table
                                    :items="model.relations"
                                    :hide-building="true"
                                    @edit-relation="editRelation"
                                    @delete-relation="deleteRelation">
                        </relation-list-table>
                    </el-tab-pane> -->
                    <!-- <el-tab-pane name="managers">
                        <span slot="label">
                            <el-badge :value="managerCount" :max="99" class="admin-layout">{{ $t('general.box_titles.managers') }}</el-badge>
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
                    </el-tab-pane> -->
                    <!-- <el-tab-pane name="providers">
                        <span slot="label">
                            <el-badge :value="serviceCount" :max="99" class="admin-layout">{{ $t('models.building.providers') }}</el-badge>
                        </span>                         -->
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
                        <!-- <assignment-by-type
                            :resetToAssignList="resetToAssignProviderList"
                            :assignmentType.sync="assignmentType"
                            :toAssign.sync="toAssignProvider"
                            :assignmentTypes="serviceAssignmentTypes"
                            :assign="attachProvider"
                            :toAssignList="toAssignProviderList"
                            :remoteLoading="remoteLoading"
                            :remoteSearch="remoteSearchProviders"
                        /> -->
                        <!-- <el-row :gutter="10" id="providerAssignBox">
                            <el-col id="providerSelect">
                                <el-select
                                    clearable
                                    :loading="remoteLoading"
                                    :placeholder="$t('general.placeholders.search')"
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
                            <el-col id="providerAssignBtn">
                                <el-button :disabled="!toAssignProvider" @click="attachProvider" class="full-button"
                                            icon="ti-save" type="primary">
                                    &nbsp;{{$t('general.assign')}}
                                </el-button>
                            </el-col>
                        </el-row> -->
                        <!-- <relation-list
                            :actions="assignmentsProviderActions"
                            :columns="assignmentsProviderColumns"
                            :filterValue="model.id"
                            fetchAction="getServices"
                            filter="building_id"
                            ref="assignmentsProviderList"
                            v-if="model.id"
                        /> -->
                    <!-- </el-tab-pane> -->
                    
                    <!--<el-tab-pane name="assignees">
                        <span slot="label">
                            <el-badge :value="assigneeCount" :max="99" class="admin-layout">{{ $t('general.box_titles.managers') }}</el-badge>
                        </span>
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
                                <el-button :disabled="!toAssign || !userAssignmentType || userAssignmentType.length == 0" @click="assignUser" class="full-button"
                                            icon="ti-save" type="primary">
                                    &nbsp;{{$t('general.assign')}}
                                </el-button>
                            </el-col>
                        </el-row>
                        <relation-list
                            :actions="assigneesActions"
                            :columns="assigneesColumns"
                            :filterValue="model.id"
                            fetchAction="getBuildingAssignees"
                            filter="building_id"
                            ref="assigneesList"
                            v-if="model.id"
                        />
                    </el-tab-pane>-->
                </el-tabs>
                
                <el-tabs type="border-card" v-model="activeRequestTab">
                    <el-tab-pane name="requests" >
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
                        <audit v-if="model.id" :id="model.id" type="building" ref="auditList" showFilter/>
                    </el-tab-pane>
                    <!-- <el-tab-pane name="settings" :disabled="true">
                        <span slot="label" class="icon-cog" @click="toggleDrawer">
                        </span>
                    </el-tab-pane> -->
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
        <ui-drawer :visible.sync="visibleDrawer" :z-index="2" direction="right" docked>
            <template v-if="editingRelation || isAddRelation">
                <ui-divider content-position="left"><i class="icon-handshake-o ti-user icon"></i> &nbsp;&nbsp;{{ $t('models.resident.relation.title') }} </ui-divider>
                <!-- <ui-divider content-position="left"><i class="icon-handshake-o ti-user icon"></i> &nbsp;&nbsp;{{ $t('models.resident.relation.title') }} {{ editingRelation ? '[' + editingRelation.relation_format + ']' : '' }} </ui-divider> -->
                    
                <div class="content" v-if="visibleDrawer">
                    <relation-form v-if="editingRelation" 
                                mode="edit"
                                :hide-building="true" 
                                :show-resident="true"
                                :building_id="model.id" 
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
                                :hide-building="true" 
                                :show-resident="true"
                                :building_id="model.id" 
                                :resident_type="1" 
                                :visible.sync="visibleDrawer" 
                                @add-relation="addRelation" 
                                @delete-relation="deleteRelation"
                                :used_units="used_units"/>
                </div>
            </template>
            <template v-else>
                <!-- <el-tabs type="card" v-model="activeDrawerTab" stretch v-if="visibleDrawer">
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
                            <email-receptionist-form 
                                        :is-building="true" 
                                        :building_id="model.id" 
                                        :quarter_id="model.quarter_id" 
                                        :visible.sync="visibleDrawer"
                                        @update-has-email-receptionists="updateHasEmailReceptionists"
                                        :is_global="model.global_email_receptionist"
                                        />
                        </div>

                    </el-tab-pane>
                </el-tabs> -->

                <ui-divider content-position="left"><i class="icon-cog"></i> &nbsp;&nbsp;{{ $t('general.emergency.title') }} </ui-divider>
                
                <div class="content" v-if="visibleDrawer">
                    <emergency-settings-form :visible.sync="visibleDrawer"/>
                </div>

            </template>
        </ui-drawer>
        <edit-close-dialog 
            :centerDialogVisible="visibleDialog"
            @clickYes="submit(), editMode=!editMode, visibleDialog=false"
            @clickNo="model=_.clone(old_model, true), editMode=!editMode, visibleDialog=false"
            @clickCancel="visibleDialog=false"
        ></edit-close-dialog>
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
    import RelationForm from 'components/RelationForm';
    import RelationListTable from 'components/RelationListTable';
    import BuildingFileListTable from 'components/BuildingFileListTable';
    import EditCloseDialog from 'components/EditCloseDialog';
    import EditActions from 'components/EditViewActions';
    import MultiSelect from 'components/Select';

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
            RelationForm,
            RelationListTable,
            BuildingFileListTable,
            EditCloseDialog,
            EditActions,
            MultiSelect,
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
                    type: 'residentNameAndType',
                    label: 'general.name',
                    translate: this.translateResidentType
                }, {
                //     prop: 'name',
                //     label: 'general.name',
                //     type: 'residentName'
                // }, {
                //     prop: 'type',
                //     label: 'models.resident.relation.type.label',
                //     i18n: this.translateResidentType
                // }, {
                    type: 'residentRelation',
                    label: 'models.resident.relation.title'
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
                assigneesColumns: [{
                    type: 'assignProviderManagerAvatars',
                    width: 70,
                }, {
                    type: 'assigneesName',
                    prop: 'name',
                    label: 'general.name'
                }, {
                    prop: 'assignment_types',
                    label: 'general.assignment_types.label',
                    i18n: this.translateAssignmentType
                }],
                assigneesActions: [{
                    width: 70,
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
                unitActions: [/*{
                    width: 70,
                    buttons: [{
                        title: 'general.actions.edit',
                        onClick: this.unitEditView,
                        tooltipMode: true,
                        icon: 'ti-search'
                    }]
                }*/],
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
                requestActions: [/*{
                    width: 70,
                    buttons: [{
                        icon: 'ti-search',
                        title: 'general.actions.edit',
                        onClick: this.requestEditView,
                        tooltipMode: true
                    }]
                }*/],
                assignmentsProviderColumns: [{
                //     type: 'assignProviderManagerAvatars',
                //     width: 70,
                // }, {
                    prop: 'name',
                    label: 'general.name',
                    type: 'serviceName'
                }],
                assignmentsProviderActions: [{
                    width: 70,
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
                assigneeCount: 0,
                relationCount: 0,
                auditCount: 0,
                visibleDrawer: false,
                editingRelation: null,
                isAddRelation: false,
                editingRelationIndex: -1,
                activeDrawerTab: "emergency",
                editMode: false,
                editId: false,
                editName: false,
                visibleDialog: false,
            };
        },
        methods: {
            ...mapActions([
                'getSettings',
                "uploadBuildingFile",
                "deleteBuildingFile",                
                "getBuildingAssignees",
                "assignManagerToBuilding",
                "unassignBuildingAssignee",
                "assignUsersToBuilding",
                "unassignUserToBuilding",
                "deleteBuilding",
                'deleteBuildingWithIds', 
                'checkUnitRequestWidthIds'
            ]),
            handleChangeEditMode() {
                if(!this.editMode) {
                    this.editMode = !this.editMode;
                    this.old_model = _.clone(this.model, true);
                } else {
                    if(JSON.stringify(this.old_model) !== JSON.stringify(this.model)) {
                        this.visibleDialog = true;
                    } else {
                        this.editMode = !this.editMode;
                        this.editId = false;
                        this.editName = false;
                    }
                }
            },
            updateHasEmailReceptionists(flag) {
                this.model.has_email_receptionists = flag
            },
            translateType(type) {
                return this.$t(`general.assignment_types.${type}`);
            },
            translateResidentType(type) {
                return this.$t(`models.resident.relation.type.${this.constants.relations.type[type]}`);
            },
            translateAssignmentType(types) {
                let translatedTypes = []
                types.map(type => {
                    translatedTypes.push(this.$t(`models.quarter.assignment_types.${this.constants.quarters.assignment_type[type]}`))
                })

                return translatedTypes.join(', ')
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
                        if(this.$refs.auditList){
                            this.$refs.auditList.fetch();
                        }
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
                    name: 'adminResidentsEdit',
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
                    if(this.fileCount){
                        this.fileCount ++;
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
                this.deleteBuildingFile({
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
            },
            gotoEmailReceptionistDrawer() {
                this.visibleDrawer = true
                this.activeDrawerTab = "email_receptionist"
            },
            toggleAddDrawer() {
                this.visibleDrawer = true
                this.isAddRelation = true
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
                const resp = await this.unassignProviderToBuilding({
                    id: this.model.id,
                    toAssignId: toUnassign.id
                });

                this.$refs.assignmentsProviderList.fetch(); 
                if(this.$refs.auditList){
                    this.$refs.auditList.fetch();
                }
                this.resetToAssignProviderList();
                this.serviceCount--;
                displaySuccess(resp.data)
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
        },
        mounted() {
            this.$root.$on('changeLanguage', () => this.getStates());            

            EventBus.$on('assignee-get-counted', manager_count => {                
                this.managerCount = manager_count;
                
            });
            EventBus.$on('assignee-get-counted', assignee_count => {                
                this.assigneeCount = assignee_count;
            });
            EventBus.$on('unit-get-counted', unit_count => {
                this.unitCount = unit_count;
            });
            EventBus.$on('request-get-counted', request_count => {
                this.requestCount = request_count;
            });
            EventBus.$on('resident-get-counted', resident_count => {                
                this.residentCount = resident_count;
            });
            EventBus.$on('audit-get-counted', audit_count => {
                this.auditCount = audit_count;
            });
            // this.fileCount = this.model.media.length;

            this.getTypes();
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
            quarterName() {
                let result = '';
                this.quarters.forEach((item) => {
                    if(item.id == this.model.quarter_id)
                        result = item.name;
                });
                return result;
            },
            buildingTypes() {
                let result = '';
                this.types.forEach((type) => {
                    if(this.model.types && this.model.types.indexOf(type.id) !== -1) {
                        if(result !== '')
                            result = `${result}, `;
                        result =  `${result}${type.name}`;
                    }
                });
                return result;
            },
            buildingState() {
                let result = '';
                this.states.forEach((state) => {
                    if(state.id == this.model.state_id)
                        result = state.name;
                });
                return result;
            }
        },
        watch: {
            'visibleDrawer': {
                immediate: false,
                handler (state) {
                    // TODO - auto blur container if visible is true first
                    if (!state) {
                        this.editingRelation = null
                        this.isAddRelation = false
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

            // .warning-bar {
            //     background-color: var(--primary-color); 
            //     color: white;
            //     min-height: 20px;
            //     padding: 10px;
            //     margin-bottom: 10px;
            //     display: flex;

            //     .message {
            //         flex-grow: 1;
            //         font-size: 13px;
            //         line-height: 20px;

            //         i {
            //             font-size: 15px;
            //             margin-right: 5px;
            //         }
            //     }

            //     .title {
            //         float: right;
            //         font-size: 15px;
            //         font-weight: bold;
            //         text-transform: uppercase;
            //         min-width: 140px;
            //         cursor: pointer;
            //     }
            // }
            .el-card.building-details {
                padding: 0;
                border-color: var(--border-color-base);
                border-radius: 6px;
                background-color: #f6f5f7;

                .pr-0 {
                    padding-right: 0px !important;
                }
                .pl-0 {
                    padding-left: 0px !important;
                }

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
                    /deep/ .edit-name-input {
                        margin: 0px;
                        .el-input__inner {
                            font-size: 24px;
                            font-family: 'Radikal';
                            font-weight: 700;
                            color: var(--text-color);
                            &::-webkit-input-placeholder {
                                color: var(--text-color);
                                font-weight: bold;
                            }
                        }
                        &:nth-type-of(1) {
                            margin-top: 10px !important;
                            margin-bottom: 40px;  
                        }
                    }
                }

                .right-pane {
                    background-color: var(--color-white);
                    .el-form-item {
                        margin-bottom: 10px !important;
                    }
                    .detail-item {
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
                    .el-form-item.detail-attic {
                        clear: both;
                        :global(.el-form-item__content) {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            width: 100%;
                            label {
                                flex: 1;
                            }
                        }
                    }
                }
            }
            .crud-view > .el-col {
                margin-bottom: 1em;
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

            span.icon-cog {
                cursor: pointer;
                float: right;
            }

            #providerAssignBox {
                display: flex;

                #providerSelect {
                    width: 100%;
                }

                #providerAssignBtn {
                    flex: 1;
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

    .btn-assign {
        width: 100%;
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
</style>
