<template>
    <div class="propertymanager-edit" v-loading.fullscreen.lock="loading.state">
        <heading :title="$t('models.property_manager.edit_title')" icon="icon-users" shadow="heavy" bgClass="bg-transparent">
            <template slot="description" v-if="model.property_manager_format">
                <div class="subtitle">{{model.property_manager_format}}</div>
            </template>
            <edit-actions :saveAction="submit" :deleteAction="deletePropertyManager" route="adminPropertyManagers" :editMode="editMode" @edit-mode="handleChangeEditMode" ref="editActions"/>
        </heading>
        <div class="crud-view">
            <el-form :model="model" label-position="top" label-width="192px" ref="form">
                <el-row :gutter="20">
                    <el-col :md="12">
                        <el-card class="propertymanager-detail">
                            <el-row :gutter="20">
                                <el-col :span="12" class="propertymanager_avatar">
                                    <div class="image-container" :class="{'hide-action-icon': !editMode}">
                                        <img v-if="!editMode" 
                                            :src="!avatar.length && user.avatar
                                                    ? '/'+user.avatar_variations[3]
                                                    : model.avatar==null && model.title == 'mr'
                                                        ? '/images/man.png'
                                                        : model.avatar==null && model.title == 'mrs'
                                                            ? '/images/woman.png'
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
                                                    : model.avatar==null && model.title == 'mr'
                                                        ? '/images/man.png'
                                                        : model.avatar==null && model.title == 'mrs'
                                                            ? '/images/woman.png'
                                                            : ''
                                            "
                                            :showCamera="model.avatar==null"
                                            @cropped="cropped"/>
                                    </div>
                                    <el-form-item v-if="editMode" :rules="validationRules.title"
                                                prop="title" class="salutation-select">
                                        <el-select style="display: block" v-model="model.title">
                                            <el-option
                                                :key="title"
                                                :label="$t(`general.salutation_option.${title}`)"
                                                :value="title"
                                                v-for="title in $constants.propertyManager.title">
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
                                <el-col :span="12">
                                    <!-- <div v-if="!editMode" class="propertymanager-info-item">
                                        <span>{{ $t('models.property_manager.slogan') }}</span>
                                        <span>{{ model.slogan }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" style="margin-bottom: 0;" :label="$t('models.property_manager.slogan')"
                                                :rules="validationRules.slogan"
                                                prop="slogan">
                                        <el-input type="text" v-model="model.slogan"/>
                                    </el-form-item>
                                     -->
                                  <div v-if="!editMode" class="propertymanager-info-item">
                                        <span>{{ $t('general.status.label') }}</span>
                                        <span>{{ propertyManagerStatus }}</span>
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
                                                    v-for="(status, k) in $constants.propertyManager.status">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>

                                      <div v-if="!editMode" class="propertymanager-info-item">
                                        <span>{{ $t('general.function') }}</span>
                                        <span>{{ propertyManagerType }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" class="label-block" :label="$t('general.function')" :rules="validationRules.type"
                                                    prop="type">
                                        <el-select style="display: block" v-model="model.type" :placeholder="$t('general.placeholders.select')">
                                            <el-option
                                                    :key="k"
                                                    :label="$t(`general.roles.${status}`)"
                                                    :value="parseInt(k)"
                                                    v-for="(status, k) in $constants.propertyManager.type">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                    
                                    

                                   
                                    <!-- <div v-if="!editMode" class="propertymanager-info-item">
                                        <span>{{ $t('general.phone') }}</span>
                                        <span>{{ model.phone }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('general.phone')" prop="phone">
                                        <el-input type="text" v-model="model.phone"/>
                                    </el-form-item> -->

                                    <div v-if="!editMode" class="propertymanager-info-item">
                                        <span>{{ $t('general.mobile') }}</span>
                                        <span>{{ model.mobile_phone }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('general.mobile')" prop="mobile_phone">
                                        <el-input type="text" v-model="model.mobile_phone"/>
                                    </el-form-item>

                                    <div v-if="!editMode" class="propertymanager-info-item">
                                        <span>{{ $t('general.email') }}</span>
                                        <span>{{ model.email }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('general.email')"
                                                    :rules="validationRules.email"
                                                    prop="email">
                                        <el-input type="email" v-model="model.email"/>
                                    </el-form-item>
                                    <!-- <el-form-item :label="$t('general.language')" :rules="validationRules.language" 
                                            prop="settings.language">
                                        <select-language :activeLanguage.sync="model.settings.language"/>
                                    </el-form-item> -->

  

                                     <!-- <div v-if="!editMode" class="propertymanager-info-item">
                                        <span>{{ $t('models.property_manager.profession') }}</span>
                                        <span>{{ model.profession }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('models.property_manager.profession')"
                                                    :rules="validationRules.profession"
                                                    prop="profession">
                                        <el-input type="text" v-model="model.profession"/>
                                    </el-form-item> -->

                                    
                                    <!-- <el-col :md="12">
                                        <el-form-item :label="$t('general.confirm_password')"
                                                      :rules="validationRules.password_confirmation"
                                                      prop="password_confirmation">
                                            <el-input 
                                                type="password" 
                                                v-model="model.password_confirmation"
                                                autocomplete="off"
                                                readonly
                                                onfocus="this.removeAttribute('readonly');"
                                            />
                                        </el-form-item>
                                    </el-col> -->

                                    <!-- <div v-if="!editMode" class="propertymanager-info-item">
                                        <span>{{ $t('models.property_manager.linkedin_url') }}</span>
                                        <span>{{ model.linkedin_url }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('models.property_manager.linkedin_url')"
                                                    :rules="validationRules.linkedin_url"
                                                    prop="linkedin_url">
                                        <el-input type="text" v-model="model.linkedin_url">
                                            <template slot="prepend"><i class="icon-linkedin"></i></template>
                                        </el-input>
                                    </el-form-item> -->

                                    <!-- <div v-if="!editMode" class="propertymanager-info-item">
                                        <span>{{ $t('models.property_manager.xing_url') }}</span>
                                        <span>{{ model.xing_url }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('models.property_manager.xing_url')"
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
                                    </el-form-item> -->

                                    <el-form-item v-if="editMode" :label="$t('general.password')"
                                                    :rules="validationRules.password"
                                                    prop="password">
                                        <el-input 
                                                type="password"
                                                autocomplete="off"
                                                v-model="model.password"
                                                class="dis-autofill"
                                                readonly
                                                onfocus="this.removeAttribute('readonly');"
                                        />
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </el-card>

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
                                    fetchAction="getAssignments"
                                    filter="manager_id"
                                    ref="assignmentsList"
                                    v-if="model.id"
                            />
                        </card>
                    </el-col>
                    <el-col :md="12">
                        <!-- <raw-grid-statistics-card :cols="8" :data="statistics.raw"/> -->

                        <card class="mt15" :header="$t('general.requests')">
                            <relation-list
                                :actions="requestActions"
                                :columns="requestColumns"
                                :filterValue="model.id"
                                fetchAction="getRequests"
                                filter="property_manager_id"
                                v-if="model.user && model.user.id"
                            />
                        </card>
                        <el-card class="mt15">
                            <div slot="header" class="clearfix">
                                <span>{{$t('general.audits')}}</span>
                            </div>
                            <audit v-if="model.id" :id="model.id" type="manager" ref="auditList" showFilter/>
                        </el-card>
                    </el-col>
                </el-row>
            </el-form>
        </div>       
        <edit-close-dialog
            :centerDialogVisible="visibleDialog"
            @clickYes="visibleDialog=false, submit(true), $refs.editActions.goToListing()"
            @clickNo="visibleDialog=false, $refs.editActions.goToListing()"
            @clickCancel="visibleDialog=false"
        ></edit-close-dialog>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import PropertyManagersMixin from 'mixins/adminPropertyManagersMixin';
    import Cropper from 'components/Cropper';
    import RelationList from 'components/RelationListing';
    import EditActions from 'components/EditViewActions';
    import {mapGetters, mapActions} from 'vuex';
    import globalFunction from "helpers/globalFunction";
    import AssignmentByType from 'components/AssignmentByType';
    import RawGridStatisticsCard from 'components/RawGridStatisticsCard';
    import SelectLanguage from 'components/SelectLanguage';
    import EditCloseDialog from 'components/EditCloseDialog';

    export default {
        name: 'AdminPropertyManagersEdit',
        mixins: [globalFunction, PropertyManagersMixin({
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
            SelectLanguage,
            EditCloseDialog
        },
        data() {
            return {
                activeTab: "details",
                requestColumns: [/*{
                    type: 'requestResidentAvatar',
                    width: 90,
                    prop: 'resident',
                    label: 'general.resident'
                },*/{
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
                    label: 'general.name'
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
                        onClick: this.notifyUnassignment,
                        tooltipMode: true,
                        icon: 'el-icon-close',                
                    }]
                }*/],
                editMode: false,
                editName: false,
                visibleDialog: false,
            }
        },
        methods: {
            ...mapActions(['deletePropertyManager']),
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
                console.log(this.editMode);
            },
            requestEditView(row) {
                this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id: row.id
                    }
                })
            },
            translateType(type) {
                return this.$t(`general.assignment_types.${type}`);
            },
            translateRequestStatus(status) {
                return this.$t(`models.request.status.${this.requestStatusConstants[status]}`);
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
            },
            propertyManagerStatus() {
                let result = '';
                if(this.$constants.propertyManager.status.hasOwnProperty(this.model.status))
                        result = this.$t(`general.status.${this.$constants.propertyManager.status[this.model.status]}`);
                return result;
            },
            propertyManagerType() {
                let result = '';
                if(this.$constants.propertyManager.type.hasOwnProperty(this.model.type))
                        result = this.$t(`general.roles.${this.$constants.propertyManager.type[this.model.type]}`);
                return result;
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

    .propertymanager-edit {
        .el-input .el-input-group__prepend {
            border-color: transparent;
        }
        .el-card {
            margin: 0 10px 40px;
            box-shadow: none !important;
            border: 1px solid var(--border-color-base);
            border-radius: 6px;
        }
        .el-row {
            display: flex;
        }
        .propertymanager-detail {
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
                    margin-bottom: 20px;
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
            .propertymanager-info-item {
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
    .last-form-row {
        margin-bottom: -22px;
    }

    .propertymanager-edit {
        .heading {
            margin-bottom: 40px;
        }

        .crud-view {
            > .el-form > .el-row > .el-col {
                margin-bottom: 1em;
            }
        }

        .propertymanager_avatar {
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
</style>
