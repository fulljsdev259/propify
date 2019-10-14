<template>
    <div class="units-add">
        <heading :title="$t('models.pinboard.add')" icon="icon-megaphone-1" shadow="heavy" style="margin-bottom: 20px;">
            <add-actions :saveAction="submit" route="adminPinboard" editRoute="adminPinboardEdit"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-form :model="model" label-position="top" label-width="192px" ref="form">
                <el-col :md="12">
                    <card :header="$t('models.propertyManager.details_card')" :loading="loading" class="mb20">
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
                            <upload-document @fileUploaded="uploadFiles" class="drag-custom" drag multiple/>
                            <div class="mt15">
                                <media :data="mediaFiles" @deleteMedia="deleteMedia"
                                       v-if="media.length || (model.media && model.media.length)"></media>
                            </div>
                        </el-form-item>

                    </card>


                </el-col>
                <el-col :md="12">
                    <card :loading="loading" class="mb20" :header="$t('models.pinboard.buildings')">
                        <el-row :gutter="10">
                            <el-col :lg="6">
                                <el-select @change="resetToAssignList"
                                           class="custom-select"
                                           v-model="assignmentType"
                                >
                                    <el-option
                                        :key="type"
                                        :label="$t(`general.assignmentTypes.${type}`)"
                                        :value="type"
                                        v-for="(type) in assignmentTypes">
                                    </el-option>
                                </el-select>
                            </el-col>
                            <el-col :lg="18" :xl="18">
                                <el-select
                                    :loading="remoteLoading"
                                    :placeholder="$t('general.placeholders.search')"
                                    :remote-method="remoteSearchBuildings"
                                    class="custom-remote-select"
                                    filterable
                                    remote
                                    reserve-keyword
                                    style="width: 100%;"
                                    v-model="toAssign"
                                >
                                    <div class="custom-prefix-wrapper" slot="prefix">
                                        <i class="el-icon-search custom-icon"></i>
                                    </div>
                                    <el-option
                                        :key="building.id"
                                        :label="building.name"
                                        :value="building.id"
                                        v-for="building in toAssignList"/>
                                </el-select>
                            </el-col>
                        </el-row>
                    </card>
                    <template v-if="this.model.type == 3">

                        <card :loading="loading" class="mt15" :header="$t('models.pinboard.placeholders.search_provider')">
                            <el-row :gutter="10">
                                <el-col :lg="24" :xl="24">
                                    <el-select
                                        :loading="remoteLoading"
                                        :placeholder="$t('models.pinboard.placeholders.search_provider')"
                                        :remote-method="remoteSearchProviders"
                                        class="custom-remote-select"
                                        filterable
                                        remote
                                        reserve-keyword
                                        style="width: 100%;"
                                        v-model="toAssignProvider"
                                    >
                                        <div class="custom-prefix-wrapper" slot="prefix">
                                            <i class="el-icon-search custom-icon"></i>
                                        </div>
                                        <el-option
                                            :key="provider.id"
                                            :label="provider.name"
                                            :value="provider.id"
                                            v-for="provider in toAssignProviderList"/>
                                    </el-select>
                                </el-col>
                            </el-row>
                        </card>

                        <card :loading="loading" class="mt15" :header="$t('models.pinboard.announcement')">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.pinboard.execution_period.label')">
                                        <el-select style="display: block"
                                                   v-model="model.execution_period"
                                                   @change="model.execution_end = null">
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
                                                    @change="() => {
                                                        !model.is_execution_time ? resetExecutionTime() : '';
                                                        reinitDatePickers();
                                                    }"/>
                                        </el-form-item>
                                        <div>
                                            {{$t('resident.notifications.service')}}
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="model.execution_period == 2 ? $t('models.pinboard.execution_interval.start') : $t('models.pinboard.execution_interval.date')"
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
                                <el-col :md="12" v-if="model.execution_period == 2">
                                    <el-form-item :label="$t('models.pinboard.execution_interval.end')"
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
                            <div class="switch-wrapper">
                                <el-form-item :label="$t('models.pinboard.notify_email')" prop="notify_email">
                                    <el-switch v-model="model.notify_email"/>
                                </el-form-item>
                                <div>
                                    {{$t('resident.notifications.service')}}
                                </div>
                            </div>
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

    const mixin = PinboardMixin({mode: 'add'});
    export default {
        mixins: [mixin, EditorConfig],
        components: {
            AddActions,
        },
        mounted() {
            this.rolename = this.$store.getters.loggedInUser.roles[0].name;
        },
        methods: {
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
</style>

<style scoped>
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
