<template>
    <div>
        <heading
            :title="$t('models.cleanify.page_title')"
            class="custom-heading"
            icon="icon-water"
        />
        <el-row :gutter="20" class="mt15">
            <el-col>
                <card :loading="loading" style="max-width: 1024px">
                    <el-form :model="model" :rules="validationRules" label-position="top" label-width="120px"
                             ref="form"
                    >
                        <el-row :gutter="10">
                            <el-col :md="8">
                                <el-form-item :label="$t('general.salutation')" prop="title">
                                    <el-select :placeholder="$t('general.placeholders.select')" style="display: block" v-model="model.title">
                                        <el-option
                                            :key="title"
                                            :label="$t(`resident.salutation_option.${title}`)"
                                            :value="title"
                                            v-for="title in titles">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :md="8">
                                <el-form-item :label="$t('general.last_name')" prop="lastName">
                                    <el-input type="text" v-model="model.lastName"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="8">
                                <el-form-item :label="$t('general.first_name')" prop="firstName">
                                    <el-input type="text" v-model="model.firstName"/>
                                </el-form-item>
                            </el-col>
                        </el-row>

                        <el-row :gutter="10">
                            <el-col :md="12">
                                <el-form-item :label="$t('general.email')" prop="email">
                                    <el-input type="email" v-model="model.email"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('general.phone')" prop="phone">
                                    <el-input type="text" v-model="model.phone"/>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row :gutter="10">
                            <el-col :md="12">
                                <el-form-item :label="$t('general.zip')" prop="zip"
                                >
                                    <el-input type="text" v-model="model.zip"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('general.city')"
                                              prop="city"
                                >
                                    <el-input type="text" v-model="model.city"></el-input>
                                </el-form-item>

                            </el-col>
                        </el-row>
                        <el-form-item :label="$t('models.cleanify.address')"
                                      prop="address"
                        >
                            <el-input type="text" v-model="model.address"></el-input>
                        </el-form-item>

                        <el-form-item
                            prop="terms"
                        >
                            <el-checkbox v-model="model.terms">{{$t('models.cleanify.terms_and_conditions')}}
                            </el-checkbox>
                        </el-form-item>
                        <el-form-item>
                            <el-button @click="submit()" icon="ti-save" type="primary">
                                {{$t('models.cleanify.save')}}
                            </el-button>
                        </el-form-item>

                    </el-form>
                </card>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import {mapActions} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";

    export default {
        components: {
            Heading,
            Card
        },
        data() {
            return {
                model: {
                    firstName: '',
                    lastName: '',
                    address: '',
                    zip: '',
                    city: '',
                    email: '',
                    phone: '',
                    title: '',
                    terms: false
                },
                validationRules: {
                    email: [
                        {
                            required: true,
                            message: this.$t("general.email_validation.required")
                        },
                        {
                            type: 'email',
                            message: this.$t("general.email_validation.email")
                        }
                    ],
                    lastName: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.last_name')})
                    }],
                    firstName: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.first_name')})
                    }],
                    phone: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.phone')})
                    }],
                    address: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.address')})
                    }],
                    zip: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.zip')})
                    }],
                    city: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.city')})
                    }],
                    title: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.salutation')})
                    }],
                    terms: [{
                        trigger: 'blue',
                        validator: this.termValidator
                    }]
                },
                titles: ['mr', 'mrs'],
                loading: {
                    state: false,
                    text: this.$t('resident.please_wait')
                }
            }
        },
        methods: {
            ...mapActions(['sendCleanifyRequest']),
            termValidator(rule, value, callback) {
                if (!value) {
                    callback(new Error(this.$t('validation.terms.required')));
                } else {
                    callback();
                }
            },
            submit() {
                this.$refs.form.validate(async (valid) => {
                    if (!valid) {
                        return false;
                    }

                    this.loading.state = true;

                    try {
                        const payload = Object.assign({}, this.model, {
                            first_name: this.model.firstName,
                            last_name: this.model.lastName
                        });

                        const resp = await this.sendCleanifyRequest(payload);
                        if (resp && resp.data) {
                            displaySuccess({
                                success: true,
                                message: this.$t('models.cleanify.success')
                            });
                            this.$refs.form.resetFields();
                        }
                    } catch (e) {
                        displayError(e);
                    } finally {
                        this.loading.state = false;
                    }
                })
            }
        }
    }
</script>
