<template>
    <div class="units-edit">
        <div class="main-content">
        <heading :title="$t('general.actions.edit')" icon="icon-unit" style="margin-bottom: 20px;" shadow="heavy">
            <template slot="description" v-if="model.unit_format">
                <div class="subtitle">{{model.unit_format}}</div>
            </template>
            <edit-actions :saveAction="submit" :deleteAction="deleteUnit" route="adminUnits"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-col :md="12">
                <card :loading="loading" :header="$t('general.actions.edit')">
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
                                    <el-select :placeholder="$t('models.unit.type.label')" class="w100p"
                                               style="width: 100%;"
                                               v-model="model.type">
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
                                    <el-input autocomplete="off" step="0.01" type="number"
                                              v-model="model.monthly_rent_net">
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
                                                                <el-input type="text"
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
                                                                <el-input type="text"
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
                                                                {{Number(model.monthly_rent_net) + Number(model.monthly_maintenance)}}
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
                            <el-col :md="6" v-if="model.type === 1">
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

                            <el-col :md="6">
                                <el-form-item
                                    v-if="model.type >=1 && model.type <= 4" 
                                    :label="$t('models.unit.sq_meter')" 
                                    prop="sq_meter">

                                    <el-input autocomplete="off" type="number" v-model="model.sq_meter">
                                        <template slot="prepend">m2</template>
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12" style="display: flex" v-if="hasAttic(model.building_id) && (model.type == 1 || model.type == 2)">
                                <el-form-item :label="$t('models.unit.attic')" :rules="validationRules.attic"
                                              class="switch-wrapper">
                                    <el-switch v-model="model.attic">
                                    </el-switch>
                                </el-form-item>
                            </el-col>

                        </el-row>
                    </el-form>
                </card>

                <card class="mt15" :loading="loading" :header="$t('models.unit.assignment')">
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
                </card>
            </el-col>
            <el-col :md="12">
                <card :loading="loading" :header="$t('general.requests')">
                    <el-button style="float:right" type="primary" @click="toggleDrawer" size="mini" round>Settings Drawer</el-button>
                    <relation-list
                        :actions="requestActions"
                        :columns="requestColumns"
                        :filterValue="model.id"
                        fetchAction="getRequests"
                        filter="unit_id"
                        v-if="model.id"
                    />
                </card>
            </el-col>
        </el-row>
        </div>
        <ui-drawer :visible.sync="visibleDrawer" :z-index="1" direction="right" docked>
            <ui-divider content-position="left"><i class="icon-tools"></i> &nbsp;&nbsp;Emergency</ui-divider>
            
            <div class="content" v-if="visibleDrawer">
                <emergency-settings-form :visible.sync="visibleDrawer"/>
            </div>
        </ui-drawer>
    </div>
</template>

<script>
    import {mapActions} from 'vuex';
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import EditActions from 'components/EditViewActions';
    import UnitsMixin from 'mixins/adminUnitsMixin';
    import RelationList from 'components/RelationListing';
    import Assignment from 'components/Assignment';

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
            Assignment
        },
        data() {
            return {
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
                    label: 'models.request.userType.label',
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
                multiple: false
            }
        },
        methods: {
            ...mapActions([
                "deleteUnit"
            ]),
            hasAttic(id) {
                let hasAttic = false;
                this.buildings.map(building => {
                    if(building.id == this.model.building_id) {
                        hasAttic = building.attic;
                    }
                });
                return hasAttic;
            }
        },
        watch: {
            "model.floor" () {
                if(this.model.floor < -3)
                    this.model.floor = -3; 
            },
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
    .main-content {
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
    }

</style>