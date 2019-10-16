<template>
    <div class="services-edit mb20">
        <heading :title="$t('models.service.edit_title')" icon="icon-tools" shadow="heavy">
            <template slot="description" v-if="model.service_provider_format">
                <div class="subtitle">{{model.service_provider_format}}</div>
            </template>
            <edit-actions :saveAction="submit" :deleteAction="deleteService" route="adminServices"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-col :md="12">
                <el-form :model="model" label-position="top" label-width="192px" ref="form">
                    <card :loading="loading" :header="$t('models.service.company_details')">
                        <el-row class="last-form-row" :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.service.category.label')" prop="category">
                                    <el-select :placeholder="$t('models.service.placeholders.category')"
                                               style="width: 100%"
                                               v-model="model.category">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.service.category.${value}`)"
                                            :value="+key"
                                            v-for="(value, key) in $constants.serviceProviders.category">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('general.name')" :rules="validationRules.name" prop="name">
                                    <el-input type="text" v-model="model.name"/>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </card>
                    <card class="mt15" :loading="loading" :header="$t('models.service.contact_details')">
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('general.street')" :rules="validationRules.street"
                                              prop="address.street">
                                    <el-input type="text" v-model="model.address.street"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-row :gutter="10">
                                    <el-col :md="8">
                                        <el-form-item :label="$t('general.zip')" :rules="validationRules.zip"
                                                      prop="address.zip">
                                            <el-input type="text" v-model="model.address.zip"></el-input>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="16">
                                        <el-form-item :label="$t('general.city')" :rules="validationRules.city"
                                                      prop="address.city">
                                            <el-input type="text" v-model="model.address.city"></el-input>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                            </el-col>
                        </el-row>

                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('general.state')"
                                              :rules="validationRules.state_id"
                                              prop="address.state_id">
                                    <el-select 
                                        filterable
                                         clearable
                                        :placeholder="$t('general.state')" 
                                        style="display: block"
                                        v-model="model.address.state_id"
                                    >
                                        <el-option :key="state.id" :label="state.name" :value="state.id"
                                                   v-for="state in states"></el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('general.phone')" prop="phone">
                                    <el-input type="text" v-model="model.phone"/>
                                </el-form-item>
                            </el-col>
                        </el-row>

                        <el-row class="last-form-row" :gutter="20">
                            <el-col :md="24">
                                <el-form-item :label="$t('general.language')" prop="settings.language">
                                    <select-language :activeLanguage.sync="model.settings.language"/>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </card>
                    <card :loading="loading" class="mt15" :header="$t('models.service.user_credentials')">
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.user.profile_image')">
                                    <cropper :resize="false" :viewportType="'square'" @cropped="cropped"/>
                                    <img :src="`/${model.avatar}?${Date.now()}`"
                                         style="width: 100%" v-if="!avatar.length && model.avatar">
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('general.email')" :rules="validationRules.email"
                                              prop="email">
                                    <el-input type="email"
                                              v-model="model.email"
                                              class="dis-autofill"
                                              readonly
                                              onfocus="this.removeAttribute('readonly');"
                                    />
                                </el-form-item>
                            </el-col>
                        </el-row>

                        <el-row class="last-form-row" :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('general.password')" :rules="validationRules.password"
                                              autocomplete="off"
                                              prop="password">
                                    <el-input type="password"
                                              v-model="model.password"
                                              class="dis-autofill"
                                              readonly
                                              onfocus="this.removeAttribute('readonly');"
                                    />
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('general.confirm_password')"
                                              :rules="validationRules.password_confirmation"
                                              prop="password_confirmation">
                                    <el-input type="password" v-model="model.password_confirmation"/>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </card>
                </el-form>
            </el-col>
            <el-col :md="12">
                <raw-grid-statistics-card :cols="8" :data="statistics.raw"/>
                <card :loading="loading" class="mt15" :header="$t('general.assignment')">
            
                    <assignment-by-type
                        :resetToAssignList="resetToAssignList"
                        :assignmentType.sync="assignmentType"
                        :toAssign.sync="toAssign"
                        :assignmentTypes="assignmentTypes"
                        :assign="attachBuilding"
                        :toAssignList="toAssignList"
                        :remoteLoading="remoteLoading"
                        :remoteSearch="remoteSearchBuildings"
                    />
                    <relation-list
                        :actions="assignmentsActions"
                        :columns="assignmentsColumns"
                        :filterValue="model.id"
                        fetchAction="getServiceAssignments"
                        filter="provider_id"
                        ref="assignmentsList"
                        v-if="model && model.id"
                    />

                </card>
                <card :loading="loading" class="mt15" :header="$t('general.requests')">

                    <relation-list
                        :actions="requestActions"
                        :columns="requestColumns"
                        :filterValue="model.id"
                        fetchAction="getRequests"
                        filter="service_id"
                        v-if="model && model.id"
                    />
                </card>
            </el-col>
        </el-row>

    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import EditActions from 'components/EditViewActions';
    import ServicesMixin from 'mixins/adminServicesMixin';
    import Cropper from 'components/Cropper';
    import RelationList from 'components/RelationListing';
    import {mapActions} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import SelectLanguage from 'components/SelectLanguage';
    import AssignmentByType from 'components/AssignmentByType';
    import RawGridStatisticsCard from 'components/RawGridStatisticsCard';

    export default {
        name: 'AdminServicesEdit',
        mixins: [ServicesMixin({
            mode: 'edit'
        })],
        components: {
            Heading,
            Card,
            Cropper,
            EditActions,
            RelationList,
            SelectLanguage,
            AssignmentByType,
            RawGridStatisticsCard
        },
        data() {
            return {
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
                assignmentsColumns: [{
                    prop: 'name',
                    label: 'general.name',
                    type: 'assigneesName'
                }, {
                    prop: 'type',
                    label: 'models.service.assign_type',
                    i18n: this.translateType
                }],
                assignmentsActions: [{
                    width: '180px',
                    buttons: [{
                        title: 'general.unassign',
                        type: 'danger',
                        onClick: this.notifyUnassignment
                    }]
                }]
            }
        },
        methods: {
            ...mapActions(['unassignServiceBuilding', 'unassignServiceQuarter', 'deleteService']),
            requestEditView(row) {
                this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id: row.id
                    }
                })
            },

            notifyUnassignment(row) {
                this.$confirm(this.$t(`general.swal.confirm_change.title`), this.$t('general.swal.confirm_change.warning'), {
                    confirmButtonText: this.$t(`general.swal.confirm_change.confirm_btn_text`),
                    cancelButtonText: this.$t(`general.swal.confirm_change.cancel_btn_text`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading.status = true;

                        await this.unassign(row);

                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.loading.status = false;
                    }
                }).catch(async () => {
                    this.loading.status = false;
                });
            },
            async unassign(toUnassign) {
                let resp;
                if (toUnassign.aType == 1) {
                    resp = await this.unassignServiceBuilding({
                        id: this.model.id,
                        toAssignId: toUnassign.id
                    })
                } else {
                    resp = await this.unassignServiceQuarter({
                        id: this.model.id,
                        toAssignId: toUnassign.id
                    })
                }

                if (resp) {
                    this.$refs.assignmentsList.fetch();

                    this.toAssign = '';

                    const type = toUnassign.aType == 1 ? 'building' : 'quarter';

                    displaySuccess(resp)
                }
            }
        },
        mounted() {
            this.$root.$on('changeLanguage', () => this.getStates());
        },
    }
</script>

<style lang="scss">
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

    .services-edit {
        .heading {
            margin-bottom: 20px;
        }
    }

    .group-name {
        width: 192px;
        text-align: right;
        padding-right: 10px;
        box-sizing: border-box;
        font-size: 16px;
        font-weight: bold;
        color: #6AC06F;
    }

    .mb15 {
        margin-bottom: 15px;
    }
</style>
