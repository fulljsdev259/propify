<template>
    <div class="services-edit">
        <heading :title="$t('models.houseOwner.edit_title')" icon="icon-users" shadow="heavy">
            <template slot="description" v-if="model.property_manager_format">
                <div class="subtitle">{{model.property_manager_format}}</div>
            </template>
            <edit-actions :saveAction="submit" :deleteAction="deleteHouseOwner" route="adminHouseOwners"/>
        </heading>
        <div class="crud-view">
            <el-form :model="model" label-position="top" label-width="192px" ref="form">
                <el-row :gutter="20">
                    <el-col :md="12">
                        <el-tabs type="border-card" v-model="activeTab">
                            <el-tab-pane :label="$t('models.houseOwner.details_card')" name="details">
                                <el-row :gutter="20">
                                    <el-col :md="8">
                                        <el-form-item :label="$t('general.salutation')" :rules="validationRules.title"
                                                    prop="title">
                                            <el-select style="display: block" v-model="model.title">
                                                <el-option
                                                    :key="title"
                                                    :label="$t(`general.salutation_option.${title}`)"
                                                    :value="title"
                                                    v-for="title in titles">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="8">
                                        <el-form-item :label="$t('general.language')" :rules="validationRules.language" 
                                                prop="settings.language">
                                            <select-language :activeLanguage.sync="model.settings.language"/>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="8">
                                        <el-form-item :label="$t('models.houseOwner.profession')"
                                                      :rules="validationRules.profession"
                                                      prop="profession">
                                            <el-input type="text" v-model="model.profession"/>
                                        </el-form-item>
                                    </el-col>
                                </el-row>

                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.houseOwner.firstName')"
                                                    :rules="validationRules.first_name"
                                                    prop="first_name">
                                            <el-input type="text" v-model="model.first_name"/>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.houseOwner.lastName')"
                                                    :rules="validationRules.last_name"
                                                    prop="last_name">
                                            <el-input type="text" v-model="model.last_name"/>
                                        </el-form-item>
                                    </el-col>
                                </el-row>


                                <el-row class="last-form-row" :gutter="20">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('general.phone')" prop="phone">
                                            <el-input type="text" v-model="model.phone"/>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-form-item :label="$t('general.email')"
                                                      :rules="validationRules.email"
                                                      prop="email">
                                            <el-input type="email" v-model="model.email"/>
                                        </el-form-item>
                                    </el-col>
                                </el-row>

                            </el-tab-pane>
                            <el-tab-pane :label="$t('models.houseOwner.profile_card')" name="profile">
                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.houseOwner.password')"
                                                      :rules="validationRules.password"
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
                                        <el-form-item :label="$t('models.houseOwner.confirm_password')"
                                                      :rules="validationRules.password_confirmation"
                                                      prop="password_confirmation">
                                            <el-input type="password" v-model="model.password_confirmation"/>
                                        </el-form-item>
                                    </el-col>
                                </el-row>

                                <el-form-item :label="$t('models.user.profile_image')">
                                    <cropper :resize="false" :viewportType="'circle'" @cropped="cropped"/>
                                    <img :src="`/${model.user.avatar}?${Date.now()}`"
                                         style="width: 100%;max-width: 200px;"
                                         v-if="!avatar.length && model.user.avatar">
                                </el-form-item>

                                <el-form-item style="margin-bottom: 0;" :label="$t('models.houseOwner.slogan')"
                                              :rules="validationRules.slogan"
                                              prop="slogan">
                                    <el-input type="text" v-model="model.slogan"/>
                                </el-form-item>
                            </el-tab-pane>
                            <el-tab-pane :label="$t('models.houseOwner.social_card')" name="social">
                                <el-row class="last-form-row" :gutter="20">

                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.houseOwner.linkedin_url')"
                                                      :rules="validationRules.linkedin_url"
                                                      prop="linkedin_url">
                                            <el-input type="text" v-model="model.linkedin_url">
                                                <template slot="prepend"><i class="icon-linkedin"></i></template>
                                            </el-input>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.houseOwner.xing_url')"
                                                      :rules="validationRules.xing_url"
                                                      prop="xing_url">
                                            <el-input type="text"
                                                      v-model="model.xing_url"
                                                      class="dis-autofill"
                                                      readonly
                                                      onfocus="this.removeAttribute('readonly');"
                                            >
                                                <template slot="prepend"><i class="icon-xing"></i></template>
                                            </el-input>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                            </el-tab-pane>
                        </el-tabs>

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
                                    fetchAction="getAssignments"
                                    filter="manager_id"
                                    ref="assignmentsList"
                                    v-if="model.id"
                            />
                        </card>
                    </el-col>
                    <el-col :md="12">
                        <raw-grid-statistics-card :cols="8" :data="statistics.raw"/>

                        <card :loading="loading" class="mt15" :header="$t('general.requests')">
                            <relation-list
                                :actions="requestActions"
                                :columns="requestColumns"
                                :filterValue="model.id"
                                fetchAction="getRequests"
                                filter="property_manager_id"
                                v-if="model.user && model.user.id"
                            />
                        </card>
                    </el-col>
                </el-row>
            </el-form>
        </div>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import HouseOwnersMixin from 'mixins/adminHouseOwnersMixin';
    import Cropper from 'components/Cropper';
    import RelationList from 'components/RelationListing';
    import EditActions from 'components/EditViewActions';
    import {mapGetters, mapActions} from 'vuex';
    import globalFunction from "helpers/globalFunction";
    import AssignmentByType from 'components/AssignmentByType';
    import RawGridStatisticsCard from 'components/RawGridStatisticsCard';
    import SelectLanguage from 'components/SelectLanguage';

    export default {
        name: 'AdminHouseOwnersEdit',
        mixins: [globalFunction, HouseOwnersMixin({
            mode: 'edit'
        })],
        components: {
            Heading,
            Card,
            Cropper,
            RelationList,
            EditActions,
            AssignmentByType,
            RawGridStatisticsCard,
            SelectLanguage
        },
        data() {
            return {
                activeTab: "details",
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
                    width: '120',
                    buttons: [{
                        icon: 'ti-pencil',
                        title: 'general.actions.edit',
                        onClick: this.requestEditView,
                        tooltipMode: true
                    }]
                }],
                assignmentsColumns: [{
                    prop: 'name',
                    label: 'general.name'
                }, {
                    prop: 'type',
                    label: 'models.houseOwner.assignType',
                    i18n: this.translateType
                }],
                assignmentsActions: [{
                    width: '180px',
                    buttons: [{
                        title: 'general.unassign',
                        type: 'danger',
                        onClick: this.notifyUnassignment,
                        tooltipMode: true,
                        icon: 'el-icon-close',                        
                    }]
                }]
            }
        },
        methods: {
            ...mapActions(['deleteHouseOwner']),
            requestEditView(row) {
                this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id: row.id
                    }
                })
            },
            translateType(type) {
                return this.$t(`general.assignmentTypes.${type}`);
            },
            translateRequestStatus(status) {
                return this.$t(`models.request.status.${this.requestStatusConstants[status]}`);
            },
            notifyUnassignment(row) {
                this.$confirm(this.$t(`general.swal.confirmChange.title`), this.$t('general.swal.confirmChange.warning'), {
                    confirmButtonText: this.$t(`general.swal.confirmChange.confirmBtnText`),
                    cancelButtonText: this.$t(`general.swal.confirmChange.cancelBtnText`),
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

            requestStatusBadge(status) {                
                return this.getRequestStatusColor(status);
            },
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
            requestStatusConstants() {
                return this.constants.requests.status
            }
        }
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

    .services-edit {
        .heading {
            margin-bottom: 20px;
        }
    }
</style>
