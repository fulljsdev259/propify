<template>
    <div class="units-edit">
        <div class="main-content">
            <heading :title="$t('models.unit.edit')" icon="icon-unit" style="margin-bottom: 20px;" shadow="heavy">
                <template slot="description" v-if="model.unit_format">
                    <div class="subtitle">{{model.unit_format}}</div>
                </template>
                <edit-actions :saveAction="submit" :deleteAction="deleteUnit" route="adminUnits"/>
            </heading>
            <el-row :gutter="20" class="crud-view">
                <el-col :md="12">
                    <el-tabs type="border-card" v-model="activeTab1">
                        <el-tab-pane :label="$t('general.actions.view')" name="details">
                            <el-form :model="model" label-position="top" label-width="192px" ref="form">
                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.unit.building')" :rules="validationRules.building_id"
                                                    prop="building_id">
                                            <el-select
                                                :loading="remoteLoading"
                                                :placeholder="$t('general.placeholders.search')"
                                                :remote-method="remoteSearchBuildings"
                                                filterable 
                                                remote
                                                reserve-keyword
                                                style="width: 100%;"
                                                v-model="model.building_id">
                                                <el-option
                                                    :key="building.id"
                                                    :label="building.name"
                                                    :value="building.id"
                                                    v-for="building in buildings"/>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.unit.name')" :rules="validationRules.name" prop="name">
                                            <el-input autocomplete="off" type="text" v-model="model.name"></el-input>
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

                                    

                                    <el-col :md="6" v-if="model.type >= 3">
                                        <el-form-item :label="$t('general.monthly_rent_net')"
                                                    :rules="validationRules.monthly_rent_net"
                                                    prop="monthly_rent_net">
                                            <el-input 
                                                autocomplete="off" 
                                                step="0.01" 
                                                type="number"
                                                v-model="model.monthly_rent_net" 
                                            >
                                                <template slot="prepend">CHF</template>
                                            </el-input>
                                        </el-form-item>
                                    </el-col>

                                    <el-col :md="6">
                                        <el-form-item :label="$t('models.unit.floor')" :rules="validationRules.floor" prop="floor">
                                            <el-input autocomplete="off" type="number" v-model="model.floor" min="-3"></el-input>
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
                                                    v-model="model.room_no">
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

                                            <el-input autocomplete="off" type="number" min="0" v-model="model.sq_meter">
                                                <template slot="prepend">m2</template>
                                            </el-input>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="8" v-if="hasAttic(model.building_id) && (model.type == 1 || model.type == 2)">
                                        <el-form-item :rules="validationRules.attic">
                                            <label class="attic-label">{{ $t('models.unit.attic') }}</label>
                                            <el-switch v-model="model.attic"/>
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
                </el-col>
                <el-col :md="12">
                    <el-tabs type="border-card" v-model="activeRightTab">
                        <el-tab-pane name="residents">
                            <span slot="label">
                                <el-badge :value="residentCount" :max="99" class="admin-layout">{{ $t('models.unit.assignment') }}</el-badge>
                            </span>
                            <assignment
                                    :toAssign.sync="toAssign"
                                    :assign="assignResident"
                                    :toAssignList="toAssignList"
                                    :remoteLoading="remoteLoading"
                                    :remoteSearch="remoteSearchResidents"
                                    :multiple="multiple"
                            />
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
                        <el-tab-pane name="contracts">
                            <span slot="label">
                                <el-badge :value="contractCount" :max="99" class="admin-layout">{{ $t('general.contracts') }}</el-badge>
                            </span>
                            
                            <el-row :gutter="20">
                                <h3 class="chart-card-header">
                                    <el-button style="float:right" type="primary" @click="toggleDrawer" icon="icon-plus" size="mini" round>{{$t('models.resident.contract.add')}}</el-button>    
                                </h3>
                                
                            </el-row>
                            <contract-list-table
                                    :items="model.contracts"
                                    @edit-contract="editContract"
                                    @delete-contract="deleteContract">
                            </contract-list-table>
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
                                filter="unit_id"
                                v-if="model.id"
                            />
                        </el-tab-pane>
                    </el-tabs>
                </el-col>
            </el-row>
        </div>
        <ui-drawer :visible.sync="visibleDrawer" :z-index="1" direction="right" docked>
            <template v-if="editingContract || isAddContract">
                <ui-divider content-position="left"><i class="icon-handshake-o ti-user icon"></i> &nbsp;&nbsp;{{ $t('models.resident.contract.title') }}</ui-divider>
                <div class="content" v-if="visibleDrawer">
                    <contract-form v-if="editingContract" 
                                mode="edit" 
                                :hide-building-and-units="true" 
                                :show-resident="true"
                                :building_id="model.building.id" 
                                :unit_id="model.id" 
                                :data="editingContract" 
                                :resident_type="1" 
                                :resident_id="editingContract.id" 
                                :visible.sync="visibleDrawer" 
                                :edit_index="editingContractIndex" 
                                @update-contract="updateContract" 
                                :used_units="used_units"/>
                    <contract-form v-else 
                                mode="add" 
                                :hide-building-and-units="true"
                                :show-resident="true"
                                :building_id="model.building.id" 
                                :unit_id="model.id" 
                                :resident_type="1" 
                                :visible.sync="visibleDrawer" 
                                @add-contract="addContract" 
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
    import ContractForm from 'components/ContractForm';
    import ContractListTable from 'components/ContractListTable';
    import {displayError, displaySuccess} from "helpers/messages";
    import { EventBus } from '../../../event-bus.js';
    

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
            ContractForm,
            ContractListTable
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
                assigneesColumns: [{
                    prop: 'name',
                    label: 'general.name',
                    type: 'residentName'
                }, {
                    prop: 'statusString',
                    label: 'models.request.user_type.label',
                    i18n: this.translateType
                }],
                assigneesActions: [{
                    width: '180px',
                    buttons: [{
                        title: 'general.unassign',
                        tooltipMode: true,
                        type: 'danger',
                        icon: 'el-icon-close',
                        onClick: this.notifyUnassignment
                    }]
                }],
                multiple: false,
                visibleDrawer: false,
                fileCount: 0,
                requestCount: 0,
                residentCount: 0,
                contractCount: 0,
                activeTab1: 'details',
                activeRightTab: 'residents',
                activeRequestTab: 'requests',
                editingContract: null,
                isAddContract: false,
                editingContractIndex: -1,
            }
        },
        methods: {
            ...mapActions([
                "deleteUnit",
                "uploadUnitFile", 
                "deleteUnitFile",
            ]),
            hasAttic(id) {
                let hasAttic = false;
                this.buildings.map(building => {
                    if(building.id == this.model.building_id) {
                        hasAttic = building.attic;
                    }
                });
                return hasAttic;
            },
            toggleDrawer() {
                this.visibleDrawer = true;
                this.isAddContract = true;
                document.getElementsByTagName('footer')[0].style.display = "none";
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
                console.log('media', this.model)
                file.order = this.model.media.length + 1;
                this.uploadUnitFile({
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
                this.deleteUnitFile({
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
            isNumber: function(evt) {
                console.log(evt)
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                    evt.preventDefault();;
                } else {
                    return true;
                }
            },
            addContract (data) {
                this.model.contracts.push(data);
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
                }).catch(() => {
                });
            },
        },
        mounted() {
             EventBus.$on('request-get-counted', request_count => {
                this.requestCount = request_count;
            });
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
            used_units() {
                return this.model.contracts.map(item => item.unit_id)
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
                        this.editingContract = null
                        this.isAddContract = false
                        document.getElementsByTagName('footer')[0].style.display = "block";
                    }
                }
            }
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

            /deep/ #tab-files, /deep/ #tab-requests, /deep/ #tab-residents, /deep/ #tab-contracts {
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