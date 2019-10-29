<template>
    <div class="units-add">
        <heading :title="$t('models.pinboard.add')" icon="icon-megaphone-1" shadow="heavy" style="margin-bottom: 20px;">
            <add-actions :saveAction="submit" route="adminPinboard" editRoute="adminPinboardEdit"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-form :model="model" label-position="top" label-width="192px" ref="form">
                <el-col :md="12">
                    <card :header="$t('models.property_manager.details_card')" :loading="loading" class="mb20">
                        <el-row :gutter="20">
                            <el-col :lg="model.announcement? 12 : 8">
                                <el-form-item :label="$t('models.pinboard.type.label')">
                                    <!-- <el-select style="display: block" v-model="model.announcement" @change="changeAnnouncement">
                                        <el-option
                                            :label="$t(`models.pinboard.type.article`)"
                                            :value="false"
                                        >
                                        </el-option>
                                        <el-option
                                            :label="$t(`models.pinboard.type.announcement`)"
                                            :value="true"
                                        >
                                        </el-option>
                                    </el-select> -->
                                    <el-select style="display: block" v-model="model.type" @change="() => {changeAnnouncement(); replacePinboardTitle()}">
                                        <el-option
                                            :label="$t(`models.pinboard.type.post`)"
                                            :value="1"
                                        >
                                        </el-option>
                                        <el-option
                                            :label="$t(`models.pinboard.type.announcement`)"
                                            :value="3"
                                        >
                                        </el-option>
                                        <el-option
                                            :label="$t(`models.pinboard.type.article`)"
                                            :value="4"
                                            v-if="rolename == 'administrator'"
                                        >
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :lg="8" v-if="this.model.type != 3">
                                <el-form-item :label="$t('models.pinboard.status.label')">
                                    <el-select style="display: block" v-model="model.status">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.pinboard.status.${status}`)"
                                            :value="parseInt(key)"
                                            v-for="(status, key) in pinboardConstants.status">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :lg="16" v-if="model.type == 3">
                                <el-row :gutter="20">
                                    <el-col :lg="model.sub_type == 3 ? 12 : 24">
                                        <el-form-item :label="$t('models.pinboard.sub_type.label')">
                                            <el-select style="display: block" v-model="model.sub_type" @change="changeSubType">
                                                <el-option
                                                        :key="key"
                                                        :label="$t(`models.pinboard.sub_type.${subtype}`)"
                                                        :value="parseInt(key)"
                                                        v-for="(subtype, key) in pinboardConstants.sub_type[3]">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :lg="12" v-if="model.sub_type == 3">
                                        <el-form-item :label="$t('models.pinboard.category.label')">
                                            <el-select style="display: block" v-model="model.category">
                                                <el-option
                                                        :key="key"
                                                        :label="$t(`models.pinboard.category.${category}`)"
                                                        :value="parseInt(key)"
                                                        v-for="(category, key) in pinboardConstants.category">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                            </el-col>
                            <el-col :lg="8" v-else>
                                <el-form-item :label="$t('models.pinboard.visibility.label')">
                                    <el-select style="display: block" v-model="model.visibility">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.pinboard.visibility.${visibility}`)"
                                            :value="parseInt(key)"
                                            v-for="(visibility, key) in pinboardConstants.visibility">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <template v-if="this.model.type == 3">
                            <el-form-item :label="$t('models.pinboard.title_label')" :rules="validationRules.title"
                                          prop="title">
                                <el-input type="text" v-model="model.title"></el-input>
                            </el-form-item>
                        </template>
                        <el-form-item :label="$t('general.content')" :rules="validationRules.content"
                                      prop="content"
                                      :key="editorKey">
                            <yimo-vue-editor
                                    :config="editorConfig"
                                    v-model="model.content"/>
                        </el-form-item>
                        <el-form-item v-if="this.model.type == 3 && this.model.sub_type == 3">
                            <label>{{$t('models.pinboard.category_default_image_label')}}</label>
                            <el-switch v-model="model.category_image"/>
                            <el-row :gutter="20">
                                <img
                                    src="~img/announcement_category/1.png"
                                    class="user-image"
                                    v-if="this.model.category == 1"
                                    width="50%" 
                                    height="50%"/>
                                <img
                                    src="~img/announcement_category/2.png"
                                    class="user-image"
                                    v-else-if="this.model.category == 2"
                                    width="50%" 
                                    height="50%"/>
                                <img
                                    src="~img/announcement_category/3.png"
                                    class="user-image"
                                    v-else-if="this.model.category == 3"
                                    width="50%" 
                                    height="50%"/>
                                <img
                                    src="~img/announcement_category/4.png"
                                    class="user-image"
                                    v-else-if="this.model.category == 4"
                                    width="50%" 
                                    height="50%"/>
                                <img
                                    src="~img/announcement_category/5.png"
                                    class="user-image"
                                    v-else-if="this.model.category == 5"
                                    width="50%" 
                                    height="50%"/>  
                            </el-row>  
                        </el-form-item> 
                        
                        <el-form-item :label="model.type == 3 ? $t('models.pinboard.attachments') : $t('models.pinboard.images')">
                            <!-- <upload-document @fileUploaded="uploadFiles" class="drag-custom" drag multiple/>
                            <div class="mt15">
                                <media :data="mediaFiles" @deleteMedia="deleteMedia"
                                       v-if="media.length || (model.media && model.media.length)"></media>
                            </div> -->
                            <el-alert
                                :title="$t('general.upload_all_desc')"
                                type="info"
                                show-icon
                                :closable="false"
                            >
                            </el-alert>
                            <media-uploader ref="media" :id="pinboard_id" :audit_id="audit_id" type="pinboard" layout="grid" v-model="media" :upload-options="uploadOptions" />
                        </el-form-item>

                    </card>


                </el-col>
                <el-col :md="12">
                    <card :loading="loading" class="mb20" :header="$t('models.pinboard.buildings')">
                        <assignment-by-type
                            :resetToAssignList="resetToAssignList"
                            :assignmentType.sync="assignmentType"
                            :toAssign.sync="toAssign"
                            :assignmentTypes="assignmentTypes"
                            :assign="attachAddedAssigmentList"
                            :toAssignList="toAssignList"
                            :remoteLoading="remoteLoading"
                            :remoteSearch="remoteSearchBuildings"
                        />
                        <relation-list
                            :actions="assignmentsActions"
                            :columns="assignmentsColumns"
                            :addedAssigmentList="addedAssigmentList"
                            :fetchStatus="false"
                            :filterValue="false"
                            :fetchAction="false"
                            :filter="false"
                            ref="assignmentsList"
                        />

                        <div v-if="addedAssigmentList.length">
                            <el-divider></el-divider>

                            <div class="switch-wrapper">
                                <el-form-item :label="$t('models.pinboard.notify_email')" prop="notify_email">
                                    <el-switch v-model="model.notify_email"/>
                                </el-form-item>
                                <div class="switcher__desc">
                                    {{$t('models.pinboard.notify_email_description')}}
                                </div>
                            </div>
                        </div>
                    </card>
                    <template v-if="this.model.type == 3">

                        <card v-if="this.model.type == 3 && this.model.sub_type == 3"
                              :loading="loading"
                              class="mt15"
                              :header="$t('models.pinboard.placeholders.search_provider')">
                            <assignment-by-type
                                :resetToAssignList="resetToAssignProviderList"
                                :toAssign.sync="toAssignProvider"
                                :assign="attachAddedProviderAssigmentList"
                                :toAssignList="toAssignProviderList"
                                :remoteLoading="remoteLoading"
                                :remoteSearch="remoteSearchProviders"
                            />
                            <relation-list
                                :actions="assignmentsProviderActions"
                                :columns="assignmentsProviderColumns"
                                :addedAssigmentList="addedProviderAssigmentList"
                                :fetchStatus="false"
                                :filterValue="false"
                                :fetchAction="false"
                                :filter="false"
                                ref="assignmentsProviderList"
                            />
                        </card>

                        <card :loading="loading" class="mt15" :header="$t('models.pinboard.announcement')">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.pinboard.execution_period.label')">
                                        <el-select style="display: block"
                                                   v-model="model.execution_period"
                                                   @change="executionPeriodChange()">
                                            <el-option
                                                    :key="key"
                                                    :label="$t(`models.pinboard.execution_period.${period}`)"
                                                    :value="parseInt(key)"
                                                    v-for="(period, key) in pinboardConstants.execution_period">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <div class="switch-wrapper">
                                        <el-form-item :label="$t('models.pinboard.specify_time_question')">
                                            <el-switch v-model="model.is_execution_time"
                                                    @change="isExecutionTimeChange()"/>
                                        </el-form-item>
                                        <div class="switcher__desc">
                                            {{$t('models.pinboard.specify_time_description')}}
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20" v-if="model.execution_period == 1">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.pinboard.execution_interval.date')"
                                                  :rules="validationRules.execution_start"
                                                  prop="execution_start">
                                        <el-date-picker
                                                :key="datePickerKey"
                                                prefix-icon="el-icon-date"
                                                format="dd.MM.yyyy"
                                                style="width: 100%"
                                                type="date"
                                                v-model="model.execution_start"
                                                @change="model.is_execution_time ? setExecutionDateTime() : ''"
                                                value-format="yyyy-MM-dd HH:mm:ss"
                                        >
                                        </el-date-picker>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12" v-if="model.is_execution_time">
                                        <el-row :gutter="10">
                                            <el-col :md="12">
                                                <el-form-item :label="$t('models.pinboard.execution_interval.from')">
                                                    <el-time-picker
                                                            v-model="executionStartTime"
                                                            @change="setExecutionDateTime()"
                                                            style="width: 100%"
                                                            :clearable="false"
                                                            :picker-options="{
                                                              selectableRange: '00:00:00 - '+executionEndTime
                                                            }"
                                                            :format="'HH:mm'"
                                                            value-format="HH:mm:00">
                                                    </el-time-picker>
                                                </el-form-item>
                                            </el-col>
                                            <el-col :md="12">
                                                <el-form-item :label="$t('models.pinboard.execution_interval.separator')">
                                                    <el-time-picker
                                                            v-model="executionEndTime"
                                                            @change="setExecutionDateTime()"
                                                            style="width: 100%"
                                                            :clearable="false"
                                                            :picker-options="{
                                                              selectableRange: executionStartTime+' - 23:59:00'
                                                            }"
                                                            :format="'HH:mm'"
                                                            value-format="HH:mm:00">
                                                    </el-time-picker>
                                                </el-form-item>
                                            </el-col>
                                        </el-row>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20" v-else-if="model.execution_period == 2">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.pinboard.execution_interval.start')"
                                                  :rules="validationRules.execution_start"
                                                  prop="execution_start">
                                        <el-date-picker
                                            :key="datePickerKey"
                                            ref="date1"
                                            @blur="setJustBlurred('date1')"
                                            prefix-icon="el-icon-date"
                                            :picker-options="{disabledDate: disabledExecutionStart}"
                                            :format="model.is_execution_time ? 'dd.MM.yyyy HH:mm' : 'dd.MM.yyyy'"
                                            style="width: 100%"
                                            :type="model.is_execution_time ? 'datetime' : 'date'"
                                            v-model="model.execution_start"
                                            value-format="yyyy-MM-dd HH:mm:ss"
                                        >
                                        </el-date-picker>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.pinboard.execution_interval.end')"
                                                  :rules="validationRules.execution_end"
                                                  prop="execution_end">
                                        <el-date-picker
                                            :key="datePickerKey"
                                            ref="date2"
                                            @blur="setJustBlurred('date2')"
                                            prefix-icon="el-icon-date"
                                            :picker-options="{disabledDate: disabledExecutionEnd}"
                                            :format="model.is_execution_time ? 'dd.MM.yyyy HH:mm' : 'dd.MM.yyyy'"
                                            style="width: 100%"
                                            :type="model.is_execution_time ? 'datetime' : 'date'"
                                            v-model="model.execution_end"
                                            value-format="yyyy-MM-dd HH:mm:ss"
                                        >
                                        </el-date-picker>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </card>
                    </template>

                </el-col>
            </el-form>

        </el-row>
    </div>
</template>

<script>
    import PinboardMixin from 'mixins/adminPinboardMixin';
    import AddActions from 'components/EditViewActions';
    import EditorConfig from 'mixins/adminEditorConfig';
    import AssignmentByType from 'components/AssignmentByType';
    import RelationList from 'components/RelationListing';

    const mixin = PinboardMixin({mode: 'add'});
    export default {
        mixins: [mixin, EditorConfig],
        components: {
            AddActions,
            AssignmentByType,
            RelationList,
        },
        data() {
            return {
                assignmentsColumns: [{
                    prop: 'name',
                    label: 'general.name'
                }, {
                    prop: 'type',
                    label: 'models.pinboard.assign_type',
                    i18n: this.translateType
                }],
                assignmentsActions: [{
                    width: 80,
                    buttons: [{
                        icon: 'el-icon-close',
                        title: 'general.unassign',
                        type: 'danger',
                        onClick: this.notifyUnassignment,
                        tooltipMode: true,
                    }]
                }],
                assignmentsProviderColumns: [{
                    prop: 'name',
                    label: 'general.name',
                    type: 'serviceName'
                }],
                assignmentsProviderActions: [{
                    width: 80,
                    buttons: [{
                        icon: 'el-icon-close',
                        title: 'general.unassign',
                        type: 'danger',
                        onClick: this.notifyProviderUnassignment,
                        tooltipMode: true
                    }]
                }],
            }
        },
        mounted() {
            this.rolename = this.$store.getters.loggedInUser.roles[0].name;
        },
        methods: {
            notifyUnassignment(row) {
                this.addedAssigmentList.forEach(element => {
                    if (element === row) {
                        let index = this.addedAssigmentList.indexOf(element);
                        this.addedAssigmentList.splice(index, 1);
                    }
                });
            },
            notifyProviderUnassignment(row) {
                this.addedProviderAssigmentList.forEach(element => {
                    if (element === row) {
                        let index = this.addedProviderAssigmentList.indexOf(element);
                        this.addedProviderAssigmentList.splice(index, 1);
                    }
                });
            },
            replacePinboardTitle() {
                this.$route.meta.title = `Add ${this.$constants.pinboard.type[this.model.type]} Pinboard`;
                this.$router.replace({
                    params: {
                        type: this.$constants.pinboard.type[this.model.type]
                    },
                    name: 'adminPinboardAdd'
                });
            },
            disabledExecutionStart(date) {
                const d = new Date(date).getTime();
                const executionEnd = new Date(this.model.execution_end).getTime();
                return executionEnd > 0 && d > executionEnd;
            },
            disabledExecutionEnd(date) {
                const d = new Date(date).getTime();
                const executionStart = new Date(this.model.execution_start).getTime();
                return d <= executionStart;
            },
            changeAnnouncement(nValue) {
                if(nValue) {
                    this.model.status = 2;
                }else {
                    this.model.status = 1;
                }
            }
        }
    }
</script>


<style lang="scss">

    .switcher {
        .el-form-item__content {
            display: flex;
        }
        &__label {
            line-height: 1.4em;
            color: #606266;
        }
        &__label-title {
            display: flex;
            align-items: center;
            min-height: 40px;
        }
        &__label-desc {
            margin-top: 0.6em;
            display: block;
            font-size: 0.9em;
        }
        .el-switch {
            margin-left: auto;
        }
    }

    .switcher__desc {
        margin-top: 0.5em;
        display: block;
        font-size: 0.9em;
    }
</style>

<style lang="scss" scoped>
    .custom-select {
        display: block;
    }

    .custom-label {
        color: var(--primary-color);
    }

    .mb20 {
        margin-bottom: 20px;
    }

</style>
