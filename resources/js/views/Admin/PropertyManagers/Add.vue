<template>
    <div class="services-add">
        <heading :title="$t('models.propertyManager.add')" icon="icon-users" shadow="heavy">
            <add-actions :saveAction="submit" route="adminPropertyManagers" editRoute="adminPropertyManagersEdit"/>
        </heading>
        <div class="crud-view">
            <el-form :model="model" ref="form">
                <el-row :gutter="20">
                    <el-col :md="12">
                        <card :loading="loading" :header="$t('models.propertyManager.details_card')">

                            <el-row :gutter="20">
                                <el-col :md="8">
                                    <el-form-item class="label-block" :label="$t('general.salutation')" :rules="validationRules.title"
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
                                    <el-form-item class="label-block" :label="$t('general.language')" :rules="validationRules.language"
                                                  prop="settings.language">
                                        <select-language :activeLanguage.sync="model.settings.language"/>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="8">
                                    <el-form-item :label="$t('models.propertyManager.profession')"
                                                  :rules="validationRules.profession"
                                                  prop="profession">
                                        <el-input type="text" v-model="model.profession"/>
                                    </el-form-item>
                                </el-col>
                            </el-row>

                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.firstName')"
                                                  :rules="validationRules.first_name"
                                                  prop="first_name">
                                        <el-input type="text" v-model="model.first_name"/>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.lastName')" :rules="validationRules.last_name"
                                                  prop="last_name">
                                        <el-input type="text" v-model="model.last_name"/>
                                    </el-form-item>
                                </el-col>
                            </el-row>

                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.phone')" prop="phone">
                                        <el-input type="text" v-model="model.phone"/>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :rules="validationRules.email" label="Email" prop="email">
                                        <el-input type="email" v-model="model.email"/>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </card>

                        <card class="mt15" :loading="loading" :header="$t('models.propertyManager.social_card')">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.propertyManager.linkedin_url')"
                                                  :rules="validationRules.linkedin_url"
                                                  prop="linkedin_url">
                                        <el-input type="text" v-model="model.linkedin_url">
                                            <template slot="prepend"><i class="icon-linkedin"></i></template>
                                        </el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.propertyManager.xing_url')" :rules="validationRules.xing_url"
                                                  prop="xing_url">
                                        <el-input type="text"
                                                  v-model="model.xing_url"
                                                  class="dis-autofill"
                                                  readonly
                                                  onfocus="this.removeAttribute('readonly');">
                                            <template slot="prepend"><i class="icon-xing"></i></template>
                                        </el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </card>
                    </el-col>
                    <el-col :md="12">
                        <card :loading="loading" :header="$t('models.propertyManager.profile_card')">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.password')" :rules="validationRules.password" autocomplete="off"
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
                                    <el-form-item :label="$t('general.confirm_password')" :rules="validationRules.password_confirmation"
                                                  prop="password_confirmation">
                                        <el-input type="password" v-model="model.password_confirmation"/>
                                    </el-form-item>
                                </el-col>
                            </el-row>

                            <el-form-item :label="$t('models.user.profile_image')">
                                <cropper :resize="false" :viewportType="'circle'" @cropped="cropped"/>
                            </el-form-item>

                            <el-form-item :label="$t('models.propertyManager.slogan')" :rules="validationRules.slogan"
                                          prop="slogan">
                                <el-input type="text" v-model="model.slogan"/>
                            </el-form-item>
                        </card>

                        <card :loading="loading" class="mt15" :header="$t('general.assignment')">
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
    import PropertyManagersMixin from 'mixins/adminPropertyManagersMixin';
    import Cropper from 'components/Cropper';
    import AddActions from 'components/EditViewActions';
    import SelectLanguage from 'components/SelectLanguage';
    import RelationList from 'components/RelationListing';
    import AssignmentByType from 'components/AssignmentByType';

    export default {
        name: 'AdminPropertyManagersAdd',
        mixins: [PropertyManagersMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            Cropper,
            AddActions,
            SelectLanguage,
            AssignmentByType,
            RelationList,
        },
        data() {
            return {
                assignmentsColumns: [{
                    prop: 'name',
                    label: 'general.title'
                }, {
                    prop: 'type',
                    label: 'models.propertyManager.assignType',
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
            notifyUnassignment(row) {
                this.addedAssigmentList.forEach(element => {
                    if (element === row) {
                        let index = this.addedAssigmentList.indexOf(element);
                        this.addedAssigmentList.splice(index, 1);
                    }
                });
            },
        }
    }
</script>

<style lang="scss">
    .label-block .el-form-item__label {
        display: block;
        float: none;
        text-align: left;
    }
    
</style>
<style lang="scss" scoped>
    .services-add {
        .heading {
            margin-bottom: 20px;
        }
    }
</style>

