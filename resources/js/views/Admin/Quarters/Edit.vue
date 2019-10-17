<template>
    <div class="quarters-edit">
        <div class="main-content">
            <heading :title="$t('models.quarter.edit')" icon="icon-chat-empty" shadow="heavy">
                <template slot="description" v-if="model.quarter_format">
                    <div class="subtitle">{{`${model.quarter_format} > ${model.name}`}}</div>
                </template>
                <edit-actions :saveAction="submit" :deleteAction="deleteQuarter" route="adminQuarters"/>
            </heading>
            <el-row :gutter="20" class="crud-view">
                <el-col :md="12">
                    <el-tabs type="border-card" v-model="activeTab1">
                        <el-tab-pane :label="$t('general.actions.view')" name="details">
                            <el-form :model="model" ref="form">
                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('resident.name')" :rules="validationRules.name"
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
                                                    <el-input type="text" v-model="model.zip"></el-input>
                                                </el-form-item>
                                            </el-col>
                                            <el-col :md="16">
                                                <el-form-item :label="$t('general.city')" :rules="validationRules.city"
                                                            prop="city">
                                                    <el-input type="text" v-model="model.city"></el-input>
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
                                </el-row>
                            </el-form>
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
                        <el-tab-pane name="assignees" v-loading="loading.state">                        
                            <span slot="label">
                                <el-badge :value="assigneeCount" :max="99" class="admin-layout">{{ $t('models.quarter.assignment') }}</el-badge>
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
                                fetchAction="getQuarterAssignees"
                                filter="quarter_id"
                                ref="assigneesList"
                                v-if="model.id"
                            />
                        </el-tab-pane>
                        <el-tab-pane name="managers">
                            <span slot="label">
                                <el-badge :value="buildingCount" :max="99" class="admin-layout">{{ $t('models.quarter.buildings') }}</el-badge>
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
                                filter="building_id"
                                v-if="model.id"
                            />
                        </el-tab-pane>
                        <el-tab-pane name="settings" :disabled="true">
                            <span slot="label" class="icon-cog" @click="toggleDrawer">
                            </span>
                        </el-tab-pane>
                    </el-tabs>
                </el-col>
            </el-row>
        </div>
        <ui-drawer :visible.sync="visibleDrawer" :z-index="1" direction="right" docked>
            <ui-divider content-position="left"><i class="icon-cog"></i> &nbsp;&nbsp;Emergency</ui-divider>
            
            <div class="content" v-if="visibleDrawer">
                <emergency-settings-form :visible.sync="visibleDrawer"/>
            </div>
        </ui-drawer>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import QuartersMixin from 'mixins/adminQuartersMixin';
    import {displayError, displaySuccess} from "helpers/messages";
    import EditActions from 'components/EditViewActions';
    import {mapActions} from 'vuex';
    import RelationList from 'components/RelationListing';
    import AssignmentByType from 'components/AssignmentByType';
    import EmergencySettingsForm from 'components/EmergencySettingsForm';
    import UploadDocument from 'components/UploadDocument';
    import draggable from 'vuedraggable';
    import { EventBus } from '../../../event-bus.js';

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
            UploadDocument,
            draggable,
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
                    width: 120,
                    buttons: [{
                        icon: 'ti-pencil',
                        title: 'general.actions.edit',
                        onClick: this.requestEditView,
                        tooltipMode: true
                    }]
                }],
                assigneesActions: [{
                    width: '180px',
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
                    prop: 'type',
                    label: 'models.request.user_type.label',
                    i18n: this.translateType
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
                    align: 'center',
                    prop: 'residents',
                    propLimit: 2,
                    count: 'residents_count',
                    label: 'general.residents'
                }],
                quarterActions: [{
                    width: '90px',
                    buttons: [{
                        icon: 'ti-pencil',
                        title: 'general.actions.edit',
                        onClick: this.buildingEditView,
                        tooltipMode: true
                    }]
                }],
                visibleDrawer: false,
                fileCount: 0,
                requestCount: 0,
                assigneeCount: 0,
                buildingCount: 0,
                activeTab1: 'details',
                activeRightTab: 'assignees',
                activeRequestTab: 'requests',
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
                if(this.fileCount){
                    this.fileCount++;
                } else {
                    this.fileCount = 1;
                }
            },
            insertDocument(prop, file) {
                file.order = this.model.media.length + 1;
                this.uploadQuarterFile({
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
                this.deleteQuarterFile({
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
            toggleDrawer() {
                this.visibleDrawer = true;
                document.getElementsByTagName('footer')[0].style.display = "none";
            },
        },
        mounted() {
            this.$root.$on('changeLanguage', () => this.getStates());

            EventBus.$on('request-get-counted', request_count => {
                this.requestCount = request_count;
            });

            EventBus.$on('assignee-get-counted', assignee_count => {                
                this.assigneeCount = assignee_count;
            });

            EventBus.$on('building-get-counted', building_count => {
                this.buildingCount = building_count;
            });
            
        },
        watch: {
            'visibleDrawer': {
                immediate: false,
                handler (state) {
                    // TODO - auto blur container if visible is true first
                    if (!state) {
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

        /deep/ #tab-files, /deep/ #tab-requests, /deep/ #tab-managers, /deep/ #tab-assignees {
            padding-right: 40px;
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
