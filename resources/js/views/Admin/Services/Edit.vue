<template>
    <div class="services-edit mb20" v-loading.fullscreen.lock="loading.state">
        <heading :title="$t('models.service.edit_title')" icon="icon-tools" shadow="heavy" bgClass="bg-transparent">
            <template slot="description" v-if="model.service_provider_format">
                <div class="subtitle">{{model.service_provider_format}}</div>
            </template>
            <edit-actions :saveAction="submit" :deleteAction="deleteService" route="adminServices" :editMode="editMode" @edit-mode="handleChangeEditMode" ref="editActions"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-col :md="12">
                <el-form :model="model" label-position="top" label-width="192px" ref="form">
                    <el-card class="service-detail">
                        <el-row class="last-form-row" :gutter="20">
                            <el-col :md="12" class='service_avatar'>
                                <div class="image-container" :class="{'hide-action-icon': !editMode}">
                                    <img v-if="!editMode" 
                                        :src="!avatar.length && user.avatar
                                                ? '/'+user.avatar_variations[3]
                                                : model.avatar==null
                                                    ? '/images/company.png'
                                                    : ''"/>
                                    <cropper
                                        v-if="editMode"
                                        :boundary="{
                                            width: 150,
                                            height: 150
                                        }"
                                        :viewport="{
                                            width: 150,
                                            height: 150
                                        }"
                                        :resize="false"
                                        :defaultAvatarSrc="
                                            !avatar.length && user.avatar
                                                ? '/'+user.avatar_variations[3]
                                                : model.avatar==null
                                                    ? '/images/company.png'
                                                    : ''
                                        "
                                        :showCamera="model.avatar==null"
                                        @cropped="cropped"/>
                                </div>
                                <el-form-item v-if="editMode" :rules="validationRules.title"
                                              prop="title"
                                              class="label-block salutation-select">
                                    <el-select :disabled="!editMode" :placeholder="$t('general.placeholders.select')" style="display: block" v-model="model.title">
                                        <el-option
                                                :key="title.value"
                                                :label="title.name"
                                                :value="title.value"
                                                v-for="title in titles">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                                <div 
                                        v-if="!editName" 
                                        class="first_name"
                                        @dblclick="editName=editMode"
                                    >
                                        {{ model.first_name }}
                                    </div>
                                    <el-form-item 
                                        v-if="editMode && editName"
                                        :rules="validationRules.first_name"
                                        prop="first_name"
                                        class="edit-name-input"
                                    >
                                        <el-input autocomplete="off" type="text" v-model="model.first_name" ></el-input>
                                    </el-form-item>
                                    <div 
                                        v-if="!editName" 
                                        class="last_name"
                                        @dblclick="editName=editMode"
                                    >
                                        {{ model.last_name }}
                                    </div>
                                    <el-form-item 
                                        v-if="editMode && editName"
                                        :rules="validationRules.last_name"
                                        prop="last_name"
                                        class="edit-name-input"
                                    >
                                        <el-input autocomplete="off" type="text" v-model="model.last_name" ></el-input>
                                    </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <div v-if="!editMode" class="service-info-item">
                                    <span>{{ $t('general.function') }}</span>
                                    <span>{{ serviceFunction }}</span>
                                </div>
                                <el-form-item v-if="editMode" :label="$t('general.function')" prop="category">
                                    <el-select :disabled="!editMode" :placeholder="$t('general.function')"
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

                                <div v-if="!editMode" class="service-info-item">
                                    <span>{{ $t('models.service.company_name') }}</span>
                                    <span>{{ model.company_name }}</span>
                                </div>
                                <el-form-item v-if="editMode" :label="$t('models.service.company_name')" :rules="validationRules.company_name" prop="company_name">
                                    <el-input :disabled="!editMode" type="text" v-model="model.company_name"/>
                                </el-form-item>

                                
                                
                                <div v-if="!editMode" class="service-info-item">
                                    <span>{{ $t('general.street') }}</span>
                                    <span>{{ model.address.street }}</span>
                                </div>
                                <el-form-item v-if="editMode" :label="$t('general.street')" :rules="validationRules.street"
                                              prop="address.street">
                                    <el-input :disabled="!editMode" type="text" v-model="model.address.street"></el-input>
                                </el-form-item>
                                
                                <div v-if="!editMode" class="service-info-item">
                                    <span>{{ $t('general.zip') }} / {{ $t('general.city') }}</span>
                                    <span>{{ model.address.zip }} {{ model.address.city }}</span>
                                </div>
                                <el-col :span="7" class="pl-0">
                                    <el-form-item v-if="editMode" :label="$t('general.zip')" :rules="validationRules.zip"
                                                    prop="address.zip">
                                        <el-input :disabled="!editMode" type="text" v-model="model.address.zip"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="17" class="pr-0">
                                    <el-form-item v-if="editMode" :label="$t('general.city')" :rules="validationRules.city"
                                                    prop="address.city">
                                        <el-input :disabled="!editMode" type="text" v-model="model.address.city"></el-input>
                                    </el-form-item>
                                </el-col>

                                <div v-if="!editMode" class="service-info-item">
                                    <span>{{ $t('general.state') }}</span>
                                    <span>{{ serviceState }}</span>
                                </div>
                                <el-form-item v-if="editMode" :label="$t('general.state')"
                                              :rules="validationRules.state_id"
                                              prop="address.state_id">
                                    <el-select :disabled="!editMode"
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

                                <div v-if="!editMode" class="service-info-item">
                                    <span>{{ $t('general.phone') }}</span>
                                    <span>{{ model.phone }}</span>
                                </div>
                                <el-form-item v-if="editMode" :label="$t('general.phone')" prop="phone">
                                    <el-input  :disabled="!editMode" type="text" v-model="model.phone"/>
                                </el-form-item>

                                <div v-if="!editMode" class="service-info-item">
                                    <span>{{ $t('general.mobile_phone') }}</span>
                                    <span>{{ model.mobile_phone }}</span>
                                </div>
                                <el-form-item v-if="editMode" :label="$t('general.mobile_phone')" prop="mobile_phone">
                                    <el-input  :disabled="!editMode" type="text" v-model="model.mobile_phone"/>
                                </el-form-item>

                        <!-- <el-row class="last-form-row" :gutter="20">
                            <el-col :md="24">
                                <el-form-item :label="$t('general.language')" :rules="validationRules.language" prop="settings.language">
                                    <select-language :disabled="!editMode" :activeLanguage.sync="model.settings.language"/>
                                </el-form-item>
                            </el-col>
                        </el-row> -->
                                <div v-if="!editMode && model.status" class="service-info-item">
                                    <span>{{ $t('general.status.label') }}</span>
                                    <span>{{ $t(`general.status.${$constants.serviceProviders.status[model.status]}`) }}</span>
                                </div>
                                <el-form-item v-if="editMode" class="label-block" :label="$t('general.status.label')"
                                                :rules="validationRules.status"
                                                prop="status">
                                    <el-select :placeholder="$t('general.placeholders.select')"
                                            style="display: block"
                                            v-model="model.status">
                                        <el-option
                                                :key="k"
                                                :label="$t(`general.status.${status}`)"
                                                :value="parseInt(k)"
                                                v-for="(status, k) in $constants.serviceProviders.status">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                                
                                <div v-if="!editMode" class="service-info-item">
                                    <span>{{ $t('general.email') }}</span>
                                    <span>{{ model.email }}</span>
                                </div>
                                <el-form-item v-if="editMode" :label="$t('general.email')" :rules="validationRules.email"
                                                prop="email">
                                    <el-input :disabled="!editMode" type="email"
                                                v-model="model.email"
                                                class="dis-autofill"
                                                readonly
                                                onfocus="this.removeAttribute('readonly');"
                                    />
                                </el-form-item>

                                <el-form-item v-if="editMode" :label="$t('general.password')" :rules="validationRules.password"
                                                autocomplete="off"
                                                prop="password">
                                    <el-input :disabled="!editMode" type="password"
                                                v-model="model.password"
                                                class="dis-autofill"
                                                readonly
                                                onfocus="this.removeAttribute('readonly');"
                                    />
                                </el-form-item>
                            </el-col>
                            <!-- <el-col :md="12">
                                <el-form-item :label="$t('general.confirm_password')"
                                              :rules="validationRules.password_confirmation"
                                              prop="password_confirmation">
                                    <el-input :disabled="!editMode" type="password" v-model="model.password_confirmation"/>
                                </el-form-item>
                            </el-col> -->
                        </el-row>
                    </el-card>
                </el-form>
            </el-col>
            <el-col :md="12">
<!--                <raw-grid-statistics-card :cols="8" :data="statistics.raw"/>-->
               <card class="mt15" :header="$t('general.box_titles.buildings_and_quarters')">
            
                    <!-- <assignment-by-type
                        :resetToAssignList="resetToAssignList"
                        :assignmentType.sync="assignmentType"
                        :toAssign.sync="toAssign"
                        :assignmentTypes="assignmentTypes"
                        :assign="attachBuilding"
                        :toAssignList="toAssignList"
                        :remoteLoading="remoteLoading"
                        :remoteSearch="remoteSearchBuildings"
                    /> -->
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
                <card :header="$t('general.requests')">

                    <relation-list
                        :actions="requestActions"
                        :columns="requestColumns"
                        :filterValue="model.id"
                        fetchAction="getRequests"
                        filter="service_id"
                        v-if="model && model.id"
                    />
                </card>
                <el-card class="mt15">
                    <div slot="header" class="clearfix">
                        <span>{{$t('general.audits')}}</span>
                    </div>
                    <audit v-if="model.id" :id="model.id" type="provider" ref="auditList" showFilter/>
                </el-card>
            </el-col>
        </el-row>

        <edit-close-dialog
                :centerDialogVisible="visibleDialog"
                @clickYes="visibleDialog=false, submit(true)"
                @clickNo="visibleDialog=false, $refs.editActions.goToListing()"
                @clickCancel="visibleDialog=false"
        ></edit-close-dialog>
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
    import EditCloseDialog from 'components/EditCloseDialog';

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
            RawGridStatisticsCard,
            EditCloseDialog,
        },
        data() {
            return {
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
                assignmentsColumns: [{
                    prop: 'name',
                    label: 'general.name',
                    type: 'assigneesName'
                }, {
                    prop: 'type',
                    label: 'general.assignment_types.label',
                    i18n: this.translateType
                }],
                assignmentsActions: [/*{
                    width: 70,
                    buttons: [{
                        title: 'general.unassign',
                        type: 'danger',
                        icon: 'el-icon-close',
                        onClick: this.notifyUnassignment,
                        tooltipMode: true,
                    }]
                }*/],
                editMode: false,
                editName: false,
                visibleDialog: false,
            }
        },
        computed: {
            serviceFunction() {
                let result = '';
                for(let item in this.$constants.serviceProviders.category) {
                    if(this.model.category == item) {
                        result = this.$t(`models.service.category.${this.$constants.serviceProviders.category[item]}`);
                    }
                }
                return result;
            },
            serviceState() {
                let result = '';
                result = this.states.find((state) => {
                    return state.id === this.model.address.state_id;
                });
                if(result === undefined)
                    result = '';
                else   
                    result = result.name;
                return result;
            }
        },
        methods: {
            ...mapActions(['unassignServiceBuilding', 'unassignServiceQuarter', 'deleteService']),
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

                    this.resetToAssignList();

                    const type = toUnassign.aType == 1 ? 'building' : 'quarter';                    
                    if(this.$refs.auditList){
                        this.$refs.auditList.fetch();
                    }
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

    .services-edit {
        .el-input .el-input-group__prepend {
            border-color: transparent;
        }
        .el-card {
            margin: 0 10px 40px;
            box-shadow: none !important;
            border: 1px solid var(--border-color-base);
            border-radius: 6px;
        }
        .service-detail {
            background-color: #f6f5f7;
            
            &.el-card {
                border-radius: 6px;
                .el-card__body {
                    padding: 0 !important;
                }
            }
            .el-col:first-child {
                padding: 40px 10px 20px 40px !important;
                .image-container {
                    margin-bottom: 15px;
                }
                .salutation-select {
                    margin: 0 0 3px;
                    & .el-input__inner {
                        font-size: 20px;
                        color: var(--color-text-primary);
                    }
                }
                .first_name, .last_name {
                    padding-left: 10px;
                    text-transform: capitalize;
                    font-size: 32px;
                    font-weight: 900;
                    color: var(--color-text-primary);
                    line-height: 1;
                }
                .edit-name-input {
                    margin: 0px;
                    .el-input__inner {
                        font-size: 32px;
                        font-family: 'Radikal';
                        font-weight: 700;
                    }
                    &:nth-type-of(1) {
                        margin-top: 10px !important;
                        margin-bottom: 40px;  
                    }
                }
                .last_name {
                    margin-top: 10px;
                    margin-bottom: 20px;
                }
            }
            .el-col:last-child {
                padding: 30px 20px 20px !important;
                background-color: var(--color-white);
                
                .el-input__prefix {
                    left: 11px;
                    font-weight: 900;
                    font-size: 9px;
                    color: var(--color-black);

                }
            }
            .service-info-item {
                display: flex;
                justify-content: space-between;
                margin-bottom: 25px;
                padding: 0 20px;
                span {
                    font-size: 15px;
                    &:first-child {
                        color: var(--color-text-secondary);
                        text-align: left;
                    }
                    &:nth-child(2) {
                        text-align: right;
                    }
                }
            }
            .status {
                margin-bottom: 40px;
                margin-left: 10px;
                .el-tag {
                    font-weight: 700;
                    border: transparent;
                    padding: 0 15px;
                    font-size: 13px;
                    height: 30px;
                    line-height: 30px;
                }
            }
            .type {
                margin-left: 10px;
                margin-top: 10px;
                font-size: 16px;
                color: var(--color-text-secondary);
            }
            .actions {
                text-align: center;
                margin-top: 100px;
                margin-bottom: 40px;
                .el-button {
                    font-size: 20px;
                    &:not(:last-of-type) {
                        margin-right: 20px;
                    }
                }
                .action-download {
                    color: #8E8F26;
                    background-color: rgba(#8e8f26, 0.2);
                }
                .action-email {
                    color: #317085;
                    background-color: rgba(#317085, 0.2);
                }
                .action-chat {
                    color: #640032;
                    background-color: rgba(#640032, 0.2);
                }
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

    .services-edit {
        .crud-view {
            > .el-col {
                margin-bottom: 1em;
            }
        }

        .service_avatar {
            .image-container {
                position: relative;
                display: inline-block;

                img {
                    border-radius: 50%;
                    width: 120px;
                    height: 120px;
                }
                
                .edit-icon {
                    position: absolute;
                    right: 0;
                    top: 0;
                    z-index: 999;
                }
                &.hide-action-icon {
                    :global(.avatar-box__btn) {
                        display: none;
                    }
                }
                /deep/ .avatar-box {
                    width: 120px;
                    height: 120px;
                    border-radius: 50%;

                    .el-avatar {
                        width: 120px !important;
                        height: 120px !important;
                        line-height: 120px !important;
                        border-radius: 50%;
                        img {
                            border-radius: 50%;
                        }
                    }
                    .avatar-box__btn {
                        right: 0;
                        top: 0;
                        left: unset;
                        width: 20px;
                        height: 20px;
                        border-radius: 50%;
                        border: 4px solid var(--color-white);
                        background-color: var(--color-text-primary);

                        .el-icon-camera-solid {
                            position: absolute;
                            right: 2px;
                            top: 2px;
                            transform: rotate(90deg);
                            font-size: 16px;
                            &:before {
                                content: "\270E" !important;
                            }
                        }
                    }
                }
            }
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
