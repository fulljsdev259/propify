<template>
    <div class="quarters-edit">
        <heading :title="$t('models.quarter.edit')" icon="icon-chat-empty" shadow="heavy">
            <template slot="description" v-if="model.quarter_format">
                <div class="subtitle">{{`${model.quarter_format} > ${model.name}`}}</div>
            </template>
            <edit-actions :saveAction="submit" :deleteAction="deleteQuarter" route="adminQuarters"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-col :md="12">
                <card :loading="loading" :header="$t('general.actions.view')">
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
                                <el-form-item :label="$t('general.state')"
                                              :rules="validationRules.state_id"
                                              prop="state_id"
                                              class="label-block">
                                    <el-select :placeholder="$t('general.state')" style="display: block"
                                               v-model="model.state_id">
                                        <el-option :key="state.id" :label="state.name" :value="state.id"
                                                   v-for="state in states"></el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col> 
                        </el-row>
                    </el-form>
                </card>

                <card :loading="loading" :header="$t('general.requests')" class="mt15">

                    <relation-list
                            :actions="requestActions"
                            :columns="requestColumns"
                            :filterValue="model.id"
                            fetchAction="getRequests"
                            filter="quarter_id"
                            v-if="model.id"
                    />
                </card>
            </el-col>
            <el-col :md="12">
                <card :loading="loading" :header="$t('models.quarter.assignment')">
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
                            </card>
            </el-col>                
            <el-col :md="12">
                <card class="mt15" :loading="loading" :header="$t('models.quarter.buildings')">
                    <relation-list
                        :actions="quarterActions"
                        :columns="quarterColumns"
                        :filterValue="model.id"
                        fetchAction="getBuildings"
                        filter="quarter_id"
                        v-if="model.id"
                    />
                </card>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import QuartersMixin from 'mixins/adminQuartersMixin';
    import {displayError} from "helpers/messages";
    import EditActions from 'components/EditViewActions';
    import {mapActions} from 'vuex';
    import RelationList from 'components/RelationListing';
    import AssignmentByType from 'components/AssignmentByType';

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
            AssignmentByType
        },
        data() {
            return {
                requestColumns: [{
                    type: 'requestResidentAvatar',
                    width: 75,
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
                    label: 'models.request.userType.label',
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
            }
        },
        methods: {
            ...mapActions(['deleteQuarter','getQuarterAssignees','getBuildings']),

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
        },
        mounted() {
            this.$root.$on('changeLanguage', () => this.getStates());
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
        .crud-view {
            margin-top: 1%;
        }
    }
</style>
