<template>
    <div class="units-edit" v-loading.fullscreen.lock="loading.state">
        <div class="main-content">
            <heading :title="$t('models.unit.edit')" icon="icon-unit" style="margin-bottom: 20px;" shadow="heavy" bgClass="bg-transparent">
                <template slot="description" v-if="model.unit_format">
                    <div class="subtitle">{{model.unit_format}}</div>
                </template>
                <edit-actions 
                    :saveAction="submit" 
                    :deleteAction="deleteUnit" 
                    route="adminUnits" 
                    :editMode="editMode"
                    @edit-mode="handleChangeEditMode"
                    ref="editActions"/>
            </heading>
            <el-row :gutter="20" class="crud-view">
                <el-col :md="12">
                    <el-tabs type="border-card" v-model="activeTab1">
                        <el-tab-pane :label="$t('general.box_titles.details')" name="details">
                            <el-form :model="model" label-position="top" label-width="192px" ref="form"  class="edit-details-form">
                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.building.quarter')" 
                                                :rules="validationRules.quarter_id" 
                                                prop="quarter_id">
                                            <el-select
                                                    :loading="remoteLoading"
                                                    :placeholder="$t('general.placeholders.search')"
                                                    :remote-method="remoteSearchQuarters"
                                                    filterable
                                                    remote
                                                    reserve-keyword
                                                    style="width: 100%;"
                                                    :disabled="!editMode"
                                                    @change="changeQuarter"
                                                    v-model="model.quarter_id">
                                                <el-option
                                                        :key="quarter.id"
                                                        :label="quarter.name"
                                                        :value="quarter.id"
                                                        v-for="quarter in quarters"/>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.unit.building')" 
                                                    :rules="validationRules.building_id"
                                                    prop="building_id">
                                            <el-select
                                                :loading="remoteLoading"
                                                :placeholder="$t('general.placeholders.search')"
                                                :remote-method="remoteSearchBuildings"
                                                filterable 
                                                remote
                                                reserve-keyword
                                                style="width: 100%;"
                                                :disabled="!editMode"
                                                v-model="model.building_id">
                                                <el-option
                                                    :key="building.id"
                                                    :label="building.name"
                                                    :value="building.id"
                                                    v-for="building in buildings"/>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    
                                </el-row>
                                <el-row :gutter="20">

                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.unit.type.label')" :rules="validationRules.type"
                                                    prop="type">
                                            <el-select
                                                filterable 
                                                :placeholder="$t('models.unit.type.label')" 
                                                class="w100p"
                                                style="width: 100%;"
                                                :disabled="!editMode"
                                                v-model="model.type"
                                            >
                                                <el-option
                                                        :key="key"
                                                        :label="$t('models.unit.type.' + value )"
                                                        :value="+key"
                                                        v-for="(value, key) in $constants.units.type">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="6">
                                        <el-form-item :label="$t('models.unit.name')" :rules="validationRules.name" prop="name">
                                            <el-input autocomplete="off" type="text" v-model="model.name" :disabled="!editMode"></el-input>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="6">
                                        <el-form-item :label="$t('models.unit.floor')" :rules="validationRules.floor" prop="floor">
                                            <el-input autocomplete="off" type="number" v-model="model.floor" min="-3" :disabled="!editMode"></el-input>
                                        </el-form-item>
                                    </el-col>

                                    <el-col :md="12" v-if="model.type >= 3">
                                        <el-form-item :label="$t('general.monthly_rent_net')"
                                                    :rules="validationRules.monthly_rent_net"
                                                    prop="monthly_rent_net">
                                            <el-input 
                                                autocomplete="off" 
                                                step="0.01" 
                                                type="number"
                                                v-model="model.monthly_rent_net" 
                                                :disabled="!editMode"
                                            >
                                                <template slot="prepend">CHF</template>
                                            </el-input>
                                        </el-form-item>
                                    </el-col>

                                    <el-col :md="24" v-if="model.type < 3">
                                        <div class="el-table el-table--fit el-table--enable-row-hover el-table--enable-row-transition monthly-rent-data" 
                                                style="width: 100%;">
                                            <div class="el-table__header-wrapper">
                                                <table cellspacing="0" cellpadding="0" border="0" class="el-table__header">
                                                    <thead>
                                                        <tr>
                                                            <th class="data is-leaf">
                                                                <div class="cell">{{$t('general.monthly_rent_net')}}</div>
                                                            </th>
                                                            <th class="symbol is-leaf">
                                                                <div class="cell"></div>
                                                            </th>
                                                            <th class="data is-leaf">
                                                                <div class="cell">{{$t('general.maintenance')}}</div>
                                                            </th>
                                                            <th class="symbol is-leaf">
                                                                <div class="cell"></div>
                                                            </th>
                                                            <th class="data is-leaf">
                                                                <div class="cell">{{$t('general.gross_rent')}}</div>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <div class="el-table__body-wrapper is-scrolling-none">
                                                <table cellspacing="0" cellpadding="0" border="0" class="el-table__body">
                                                    <tbody>
                                                        <tr>
                                                            <td class="data">
                                                                <div class="cell">
                                                                    <el-form-item 
                                                                        :rules="validationRules.monthly_rent_net"
                                                                        prop="monthly_rent_net">
                                                                        <el-input 
                                                                            type="number"
                                                                            v-model="model.monthly_rent_net"  
                                                                            :disabled="!editMode"
                                                                        >
                                                                            <template slot="prepend">CHF</template>
                                                                        </el-input>
                                                                    </el-form-item>
                                                                </div>
                                                            </td>
                                                            <td class="symbol">
                                                                <div class="cell">
                                                                    +
                                                                </div>
                                                            </td>
                                                            <td class="data">
                                                                <div class="cell">
                                                                    <el-form-item 
                                                                        :rules="validationRules.monthly_maintenance"
                                                                        prop="monthly_maintenance">
                                                                        <el-input 
                                                                            type="number"
                                                                            v-model="model.monthly_maintenance"  
                                                                            :disabled="!editMode"
                                                                        >
                                                                            <template slot="prepend">CHF</template>
                                                                        </el-input>
                                                                    </el-form-item>
                                                                </div>
                                                            </td>
                                                            <td class="symbol">
                                                                <div class="cell">
                                                                    =
                                                                </div>
                                                            </td>
                                                            <td class="data">
                                                                <div class="cell">
                                                                    <el-form-item 
                                                                        prop="monthly_rent_net">
                                                                        {{( Number(model.monthly_rent_net) + Number(model.monthly_maintenance) ).toFixed(2)}}
                                                                    </el-form-item>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </el-col>

                                </el-row>
                                <el-row class="last-form-row" :gutter="20">
                                    <el-col :md="8" v-if="model.type === 1">
                                        <el-form-item :label="$t('models.unit.room_no')" :rules="validationRules.room_no"
                                                    prop="room_no"
                                        >
                                            <el-select :placeholder="$t('general.placeholders.select')" class="w100p"
                                                    style="width: 100%;"
                                                    v-model="model.room_no" :disabled="!editMode">
                                                <el-option :key="room.value"
                                                        :label="room.label"
                                                        :value="room.value"
                                                        v-for="room in rooms"/>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>

                                    <el-col :md="8">
                                        <el-form-item
                                            v-if="model.type >=1 && model.type <= 4" 
                                            :label="$t('models.unit.sq_meter')" 
                                            prop="sq_meter">

                                            <el-input autocomplete="off" type="number" min="0" v-model="model.sq_meter" :disabled="!editMode">
                                                <template slot="prepend">m2</template>
                                            </el-input>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="8" v-if="hasAttic(model.building_id) && (model.type == 1 || model.type == 2)">
                                        <el-form-item :rules="validationRules.attic" >
                                            <label class="attic-label">{{ $t('models.unit.attic') }}</label>
                                            <el-switch v-model="model.attic" :disabled="!editMode"/>
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
                        <el-tab-pane name="plan">
                            <span slot="label">
                                Plan
                            </span>
                            <div align="right" style="margin-bottom: 15px">
                                <el-button @click="visiblePlanDrawer = true">show Plan Drawer</el-button>
                                <el-button @click="demo.file = '/storage/floor-plan.jpg', visiblePlanModal = true">show IMG</el-button>
                                <el-button @click="demo.file = '/storage/Ansicht-1-6.pdf', visiblePlanModal = true">show PDF</el-button>
                            </div>
                        </el-tab-pane>

                    </el-tabs>
                </el-col>
                <el-col :md="12">
                    <el-tabs type="border-card" v-model="activeRightTab">
                        <el-tab-pane name="residents">
                            <span slot="label">
                                <el-badge :value="residentCount" :max="99" class="admin-layout">{{ $t('general.residents') }}</el-badge>
                            </span>
                            <relation-list
                                    :actions="assigneesActions"
                                    :columns="assigneesColumns"
                                    :filterValue="false"
                                    :fetchAction="false"
                                    :filter="false"
                                    :fetchStatus="false"
                                    :addedAssigmentList="addedAssigmentList"
                                    ref="assigneesList"
                                    v-if="addedAssigmentList"
                            />
                        </el-tab-pane>
                        <!-- <el-tab-pane name="relations">
                            <span slot="label">
                                <el-badge :value="relationCount" :max="99" class="admin-layout">{{ $t('general.relations') }}</el-badge>
                            </span>

                            <el-button style="float:right" type="primary" @click="toggleDrawer" icon="icon-plus" size="mini" round>{{$t('models.resident.relation.add')}}</el-button>    
                            <relation-list-table
                                    :items="model.relations"
                                    :hide-building="true"
                                    :hide-unit="true"
                                    @edit-relation="editRelation"
                                    @delete-relation="deleteRelation">
                            </relation-list-table>
                        </el-tab-pane> -->
                    </el-tabs>

                    <el-tabs type="border-card" v-model="activeRequestTab">
                        <el-tab-pane name="requests">
                            <span slot="label">
                                <el-badge :value="requestCount" :max="99" class="admin-layout">{{ $t('general.requests') }}</el-badge>
                            </span>
                            
                            <relation-list
                                :actions="requestActions"
                                :columns="requestColumns"
                                :filterValue="model.id"
                                fetchAction="getRequests"
                                filter="unit_id"
                                v-if="model.id"
                            />
                        </el-tab-pane>
                        <el-tab-pane name="audit" style="height: 400px;overflow:auto;">
                            <span slot="label">
                                {{ $t('general.audits') }}
                                <!-- <el-badge :value="auditCount" :max="99" class="admin-layout">{{ $t('general.audits') }}</el-badge> -->
                            </span>
                            <audit v-if="model.id" :id="model.id" type="unit" ref="auditList" showFilter/>
                        </el-tab-pane>
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
                                :hide-building-and-units="true" 
                                :show-resident="true"
                                :building_id="model.building.id" 
                                :unit_id="model.id" 
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
                                :hide-building-and-units="true"
                                :show-resident="true"
                                :building_id="model.building.id" 
                                :unit_id="model.id" 
                                :resident_type="1" 
                                :visible.sync="visibleDrawer" 
                                @add-relation="addRelation" 
                                @delete-relation="deleteRelation"
                                :used_units="used_units"/>
                </div>
            </template>
            <template v-else>
                <ui-divider content-position="left"><i class="icon-cog"></i> &nbsp;&nbsp;Emergency</ui-divider>
                
                <div class="content" v-if="visibleDrawer">
                    <emergency-settings-form :visible.sync="visibleDrawer"/>
                </div>
            </template>
            
        </ui-drawer>
        <edit-close-dialog 
            :centerDialogVisible="visibleDialog"
            @clickYes="submit(), visibleDialog=false, $refs.editActions.goToListing()"
            @clickNo="visibleDialog=false, $refs.editActions.goToListing()"
            @clickCancel="visibleDialog=false"
        ></edit-close-dialog>

        <FloorPreviewModal v-if="demo.file && visiblePlanModal"
                           :visible.sync="visiblePlanModal"
                           :initialMarkers="initialMarkers"
                           :fileUrl="demo.file"/>

        <el-drawer
                title="Add Plan"
                :visible.sync="visiblePlanDrawer"
                direction="rtl"
                custom-class="plan-drawer"
                ref="pdfDrawer"
        >
            <div class="plan-drawer__content">
                <el-form>
                    <el-form-item>
                        <el-alert
                                :title="$t('general.upload_file_desc')"
                                type="info"
                                show-icon
                                :closable="false"
                        >
                        </el-alert>
                        <upload-document @fileUploaded="" class="drag-custom" drag multiple
                                         ref="pdfUpload"
                                         accept-type=".pdf, .doc, .docx, .xls, .xlsx"/>
                    </el-form-item>
                    <el-form-item label="Name">
                        <el-input autocomplete="off"
                                  v-model="demo.name">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="Desc">
                        <el-input
                                :autosize="{minRows: 4}"
                                type="textarea"
                                v-model="demo.desc">
                        </el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-checkbox v-model="demo.is_primary">Is primary</el-checkbox>
                    </el-form-item>
                </el-form>
                <div class="plan-drawer__footer">
<!--                    <el-button type="primary" @click="() => {uploadFiles(demo.file); $refs.pdfDrawer.closeDrawer()}">Save</el-button>-->
                    <el-button type="primary" @click="$refs.pdfDrawer.closeDrawer()">Save</el-button>
                    <el-button @click="visiblePlanDrawer = false">Cancel</el-button>
                </div>
            </div>
        </el-drawer>
    </div>
</template>
<script>
    import {mapActions, mapGetters} from 'vuex';
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import EditActions from 'components/EditViewActions';
    import UnitsMixin from 'mixins/adminUnitsMixin';
    import RelationList from 'components/RelationListing';
    import Assignment from 'components/Assignment';
    import EmergencySettingsForm from 'components/EmergencySettingsForm';
    import UploadDocument from 'components/UploadDocument';
    import draggable from 'vuedraggable';
    import RelationForm from 'components/RelationForm';
    import RelationListTable from 'components/RelationListTable';
    import BuildingFileListTable from 'components/BuildingFileListTable';
    import {displayError, displaySuccess} from "helpers/messages";
    import { EventBus } from '../../../event-bus.js';
    import EditCloseDialog from 'components/EditCloseDialog';
    import FloorPreviewModal from 'components/FloorPreviewModal';

    export default {
        mixins: [UnitsMixin({
            mode: 'edit',
            withRelation: true
        })],
        components: {
            Heading,
            Card,
            EditActions,
            RelationList,
            Assignment,
            EmergencySettingsForm,
            UploadDocument,
            draggable,
            RelationForm,
            RelationListTable,
            BuildingFileListTable,
            EditCloseDialog,
            FloorPreviewModal,
        },
        data() {
            return {
                initialMarkers: [{
                    id: 'mark1',
                    left: 100,
                    top: 150,
                }, {
                    id: 'mark2',
                    left: 150,
                    top: 200,
                }, {
                    id: 'mark3',
                    left: 200,
                    top: 250,
                }],
                demo: {
                    name: '',
                    desc: '',
                    is_primary: false,
                    file: '',
                },
                visiblePlanDrawer: false,
                visiblePlanModal: false,

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
                assigneesColumns: [{
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
                assigneesActions: [
                    // {
                    // width: 70,
                    // buttons: [{
                    //     title: 'models.resident.view',
                    //     onClick: this.residentEditView,
                    //     icon: 'el-icon-user',
                    //     tooltipMode: true
                    // }, {
                    //     title: 'general.unassign',
                    //     tooltipMode: true,
                    //     type: 'danger',
                    //     icon: 'el-icon-close',
                    //     onClick: this.notifyUnassignment
                    // }]
                    // }
                ],
                multiple: false,
                visibleDrawer: false,
                fileCount: 0,
                requestCount: 0,
                residentCount: 0,
                relationCount: 0,
                auditCount: 0,
                activeTab1: 'details',
                activeRightTab: 'residents',
                activeRequestTab: 'requests',
                editingRelation: null,
                isAddRelation: false,
                editingRelationIndex: -1,
                editMode: false,
                visibleDialog: false,
            }
        },
        methods: {
            ...mapActions([
                "deleteUnit",
                "uploadUnitFile", 
                "deleteUnitFile",
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
            translateResidentTypes(types) {
                if(types.constructor === Array){
                    let translatedTypes = types.map(type => this.$t(`models.resident.relation.type.${this.$constants.relations.type[type]}`))
                    return translatedTypes.join(', ')
                }
            },
            toggleDrawer() {
                this.visibleDrawer = true;
                this.isAddRelation = true;
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
                this.uploadUnitFile({
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
                this.deleteUnitFile({
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
            isNumber: function(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                    evt.preventDefault();;
                } else {
                    return true;
                }
            },
            addRelation (data) {
                this.model.relations.push(data);
                this.relationCount ++; //@TODO : update the assigned residents data accordingly
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
            residentEditView(row) {
                this.$router.push({
                    name: 'adminResidentsEdit',
                    params: {
                        id: row.id
                    }
                });
            },
        },
        mounted() {
             EventBus.$on('request-get-counted', request_count => {
                this.requestCount = request_count;
            });
            EventBus.$on('audit-get-counted', audit_count => {
                this.auditCount = audit_count;
            });
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
            used_units() {
                return this.model.relations.map(item => item.unit_id)
            },
            residentStatusConstants() {
                return this.constants.residents.status
            },
        },          
        watch: {
            "model.type" () {
                if(this.model.type >= 3)
                    this.model.attic = false;
                    
                if(this.model.type >= 5) {
                    this.model.sq_meter = '';
                }
            },
            "model.building_id" () {
                if(this.hasAttic(this.model.building_id) == false) 
                    this.model.attic = false;
            },
            'visibleDrawer': {
                immediate: false,
                handler (state) {
                    // TODO - auto blur container if visible is true first
                    if (!state) {
                        this.editingRelation = null
                        this.isAddRelation = false
                    }
                }
            },
        }
        
       
    }
</script>
<style lang="scss">
    .el-card .el-card__body {
        display: flex;
        flex-direction: column;
    }
    .el-form-item__content {
        .el-input.el-input-group {
            .el-input-group__prepend {
                padding: 2px 8px 0;
                font-weight: 600;
            }
        }
    }

    .plan-drawer__content {
        padding: 0 20px;
    }
</style>
<style lang="scss" scoped>

    .last-form-row {
        margin-bottom: -22px;
    }

    .units-edit {
        overflow: hidden;
        flex: 1;

        .main-content {
            overflow-x: hidden;
            overflow-y: scroll;
            height: 100%;

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
            

            /deep/ .monthly-rent-data {
                background: transparent;
                table {
                    width: 100%;
                    cursor: initial;
                    background: transparent;
                    thead, tbody {
                        width: 100%;
                        background: transparent;
                        tr {
                            display: flex;
                            width: 100%;
                            background: transparent;

                            
                            .data {
                                flex: 1;
                                display: flex;
                                align-items: center;
                                background: transparent;
                                .cell {
                                    width: 100%;
                                    text-align: left;
                                    
                                    .el-form-item {
                                        margin-bottom: 0;

                                        &.is-error {
                                            // margin-bottom: 27px;
                                        }
                                    }

                                    /deep/ .el-input.el-input-group {
                                        .el-input-group__prepend {
                                            padding: 2px 8px 0;
                                            font-weight: 600;
                                        }
                                        .el-input__inner {
                                            padding: 5px;
                                        }
                                    }
                                }
                            }
                            
                            .symbol {
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                width: 20px;
                                background: transparent;
                                .cell {
                                    text-overflow: initial;
                                    font-size: 16px;
                                    padding: 0;
                                }
                            }

                            td {
                                padding: 25px 0;

                                .cell {
                                    overflow: visible;
                                }
                            }

                            td:last-child .cell {
                                padding-left: 10px !important;
                                text-align: left;
                            }
                        }
                    }
                }
            }

            span.icon-cog {
                cursor: pointer;
            }

            
        }
    }
    .ui-drawer {
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

</style>
