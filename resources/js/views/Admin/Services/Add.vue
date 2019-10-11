<template>
    <div class="services-add">
        <heading :title="$t('models.service.add_title')" icon="icon-tools" shadow="heavy">
            <add-actions :saveAction="submit" route="adminServices" editRoute="adminServicesEdit"/>
        </heading>
        <div class="crud-view">
            <el-form :model="model" :rules="validationRules" ref="form">
                <el-row :gutter="20">
                    <el-col :md="12">
                        <card :loading="loading" :header="$t('models.service.company_details')">
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item class="label-block" :label="$t('models.service.category.label')" :rules="validationRules.category" prop="category">
                                    <el-select :placeholder="$t('models.service.placeholders.category')" style="display: block;"
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

                        <card class="mt15" :loading="loading" :header="$t('models.service.user_credentials')">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.user.profile_image')">
                                        <cropper :resize="false" :viewportType="'square'" @cropped="cropped"/>
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
                                                  prop="user.password">
                                        <el-input type="password"
                                                  v-model="model.user.password"
                                                  class="dis-autofill"
                                                  readonly
                                                  onfocus="this.removeAttribute('readonly');"
                                        />
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.confirm_password')" :rules="validationRules.password_confirmation"
                                                  prop="user.password_confirmation">
                                        <el-input type="password" v-model="model.user.password_confirmation"/>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </card>
                    </el-col>

                    <el-col :md="12">
                        <card :loading="loading" :header="$t('models.service.contact_details')">
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
                                <el-col :md="12">
                                    <el-form-item class="label-block" :label="$t('general.state')" :rules="validationRules.state_id" prop="address.state_id">
                                        <el-select :placeholder="$t('general.state')" style="display: block"
                                                   v-model="model.address.state_id">
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
                                <el-col :md="24">
                                    <el-form-item class="label-block" :label="$t('general.language')" prop="language">
                                        <select-language :activeLanguage.sync="model.language"/>
                                    </el-form-item>
                                </el-col>
                            </el-row>
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
</style>
<style lang="scss" scoped>
    .services-add {
        .heading {
            margin-bottom: 20px;
        }
    }
</style>

