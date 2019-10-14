<template>
    <div>
        <card>
            <el-form :model="model" label-width="224px" ref="form">
                <el-form-item :label="$t('general.name')" :rules="validationRules.name" prop="name">
                    <el-input autocomplete="off" type="text" v-model="model.name"></el-input>
                </el-form-item>
                <el-form-item :label="$t('general.email')" :rules="validationRules.email" prop="email">
                    <el-input autocomplete="off" type="email" v-model="model.email"></el-input>
                </el-form-item>
                <el-form-item :label="$t('general.phone')" prop="phone">
                    <el-input type="text" v-model="model.phone"></el-input>
                </el-form-item>
                <el-form-item :label="$t('general.old_password')" :rules="validationRules.password_old"
                              prop="password_old">
                    <el-input autocomplete="off" type="password"
                              v-model="model.password_old"></el-input>
                </el-form-item>
                <el-form-item :label="$t('general.new_password')" :rules="validationRules.password"
                              prop="password">
                    <el-input autocomplete="off" type="password"
                              v-model="model.password"></el-input>
                </el-form-item>
                <el-form-item :label="$t('general.new_password_confirmation')" :rules="validationRules.password_confirmation"
                              prop="password_confirmation">
                    <el-input autocomplete="off" type="password"
                              v-model="model.password_confirmation"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button @click="submit" icon="ti-save" type="primary">
                        {{$t('general.actions.save')}}
                    </el-button>
                </el-form-item>
            </el-form>
        </card>
    </div>
</template>

<script>
    import {mapState, mapMutations, mapActions} from 'vuex';
    import {displayError, displaySuccess} from 'helpers/messages';
    import Card from 'components/Card';
    import { EventBus } from '../../../event-bus.js';


    export default {
        components: {
            Card
        },
        data() {
            return {
                model: {
                    name: '',
                    email: '',
                    phone: '',
                    password_old: '',
                    password: '',
                    password_confirmation: ''
                },
                validationRules: {
                    email: [{
                        required: true,
                        message: this.$t("general.email_validation.required")
                    }, {
                        type: 'email',
                        message: this.$t("general.email_validation.email")
                    }],
                    name: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.name')})
                    }],
                    password_old: [{
                        required: true,
                        message: this.$t("general.password_validation.old_password_required")
                    }],
                }
            };
        },
        methods: {
            ...mapActions(['changeDetails', 'me']),
            submit() {
                this.$refs.form.validate(async valid => {
                    if (valid) {
                        try {
                            const {data, ...message} = await this.changeDetails(this.model);

                            displaySuccess(message);

                            this.me();

                            setTimeout(() => {EventBus.$emit('profile-username-change', {})}, 1000);
                        } catch (err) {
                            displayError(err);
                        }
                    }
                });
            },
        },
        computed: {
            ...mapState(['users'])
        },
        created() {
            const {name, email, phone} = this.users.loggedInUser;

            this.model = {name, email, phone};
        },
    }
</script>

<style lang="scss" scoped>
    .el-card {
        &:before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background-image: url('~img/51675218_23843429092960149_5298969442502311936_n.png');
            background-repeat: no-repeat;
            background-size: 24em;
            background-position: calc(100% + 32px) center;
        }

        .el-form {
            max-width: 512px;

            &-item:last-child {
                margin-bottom: 0;
            }

            .el-button :global([class*="ti"]) {
                margin-right: 8px;
            }
        }
    }
</style>
