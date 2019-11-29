<template>
    <div class="services-add" v-loading.fullscreen.lock="loading.state">
        <heading :title="$t('models.service.add_title')" icon="icon-tools" shadow="heavy" bgClass="bg-transparent">
            <add-actions :saveAction="submit" route="adminServices" editRoute="adminServicesEdit"/>
        </heading>
        <div class="crud-view">
            <el-form :model="model" :rules="validationRules" ref="form" class="add-form">
                <el-row :gutter="20">
                    <el-col :md="12">
                        <card :header="$t('models.service.company_details')">
                            <el-row :gutter="20">
                                <el-col :md="8">
                                    <el-form-item class="label-block" :label="$t('general.function')" :rules="validationRules.category" prop="category">
                                        <el-select :placeholder="$t('general.function')" style="display: block;"
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
                                <el-col :md="8">
                                    <el-form-item class="label-block" :label="$t('general.status.label')"
                                                        :rules="validationRules.status"
                                                        prop="status">
                                        <el-select :placeholder="$t('general.placeholders.select')" style="display: block"
                                                v-model="model.status">
                                            <el-option
                                                    :key="k"
                                                    :label="$t(`general.status.${status}`)"
                                                    :value="parseInt(k)"
                                                    v-for="(status, k) in $constants.serviceProviders.status">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="8">
                                    <el-form-item :label="$t('models.service.company_name')" :rules="validationRules.company_name" prop="company_name">
                                        <el-input type="text" v-model="model.company_name"/>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :md="8">
                                    <el-form-item :rules="validationRules.title"
                                                  :label="$t('general.salutation')"
                                                  prop="title"
                                                  class="label-block">
                                        <el-select :placeholder="$t('general.placeholders.select')" style="display: block" v-model="model.title">
                                            <el-option
                                                    :key="title.value"
                                                    :label="title.name"
                                                    :value="title.value"
                                                    v-for="title in titles">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="8">
                                    <el-form-item :label="$t('general.last_name')" :rules="validationRules.last_name" prop="last_name">
                                        <el-input type="text" v-model="model.last_name"/>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="8">
                                    <el-form-item :label="$t('general.first_name')" :rules="validationRules.first_name" prop="first_name">
                                        <el-input type="text" v-model="model.first_name"/>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </card>

                        <card class="mt15" :header="$t('models.service.user_credentials')">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.user.profile_image')">
                                        <cropper
                                                :boundary="{
                                                    width: 250,
                                                    height: 360
                                                }"
                                                :viewport="{
                                                    width: 250,
                                                    height: 250
                                                }"
                                                :resize="false"
                                                :defaultAvatarSrc="'/images/company.png'"
                                                :showCamera="model.avatar==''"
                                                @cropped="cropped"/>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.email')" :rules="validationRules.email" prop="email">
                                        <el-input type="email"
                                                  v-model="model.email"
                                                  class="dis-autofill"
                                                  readonly
                                                  onfocus="this.removeAttribute('readonly');"
                                        />
                                    </el-form-item>
                                </el-col>
                            </el-row>
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
                                <!-- <el-col :md="12">
                                    <el-form-item :label="$t('general.confirm_password')" :rules="validationRules.password_confirmation"
                                                  prop="password_confirmation">
                                        <el-input type="password" v-model="model.password_confirmation"/>
                                    </el-form-item>
                                </el-col> -->
                            </el-row>
                        </card>
                    </el-col>

                    <el-col :md="12">
                        <card :header="$t('models.service.contact_details')">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.street')" :rules="validationRules.street" prop="address.street">
                                        <el-input type="text" v-model="model.address.street"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-col :md="8">
                                        <el-form-item :label="$t('general.zip')" :rules="validationRules.zip" prop="address.zip">
                                            <el-input type="text" v-model="model.address.zip"></el-input>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="16">
                                        <el-form-item :label="$t('general.city')" :rules="validationRules.city" prop="address.city">
                                            <el-input type="text" v-model="model.address.city"></el-input>
                                        </el-form-item>
                                    </el-col>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :md="8">
                                    <el-form-item class="label-block" :label="$t('general.state')" :rules="validationRules.state_id" prop="address.state_id">
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
                                <el-col :md="8">
                                    <el-form-item :label="$t('general.phone')" prop="phone">
                                        <el-input type="text" v-model="model.phone"/>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="8">
                                    <el-form-item :label="$t('general.mobile_phone')" prop="mobile_phone">
                                        <el-input type="text" v-model="model.mobile_phone"/>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <!-- <el-row :gutter="20">
                                <el-col :md="24">
                                    <el-form-item class="label-block" :rules="validationRules.language" :label="$t('general.language')" prop="settings.language">
                                        <select-language :activeLanguage.sync="model.settings.language"/>
                                    </el-form-item>
                                </el-col>
                            </el-row> -->
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
    import ServicesMixin from 'mixins/adminServicesMixin';
    import Cropper from 'components/Cropper';
    import AddActions from 'components/EditViewActions';
    import SelectLanguage from 'components/SelectLanguage';


    export default {
        name: 'AdminServicesAdd',
        mixins: [ServicesMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            Cropper,
            AddActions,
            SelectLanguage
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

    .services-add .crud-view {
        margin: 0 10px !important;
        .add-form > .el-row {
            .el-col:first-child {
                padding-left: 10px !important;
            }
            .el-col:last-child {
                padding-right: 10px !important;
            }
        }
    }
</style>

