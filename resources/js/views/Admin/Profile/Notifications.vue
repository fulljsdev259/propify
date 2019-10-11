<template>
    <el-card>
        <el-form label-width="192px" ref="form">
            <el-form-item :label="$t('settings.summary.label')">
                <el-select v-model="user.settings.summary">
                    <el-option :key="summary" :label="$t('settings.summary.' + summary )" :value="summary"
                               v-for="summary in summaryValues"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item :label="$t('settings.service')">
                <el-switch v-model="user.settings.service_notification"></el-switch>
            </el-form-item>
            <el-form-item :label="$t('settings.pinboard')">
                <el-switch v-model="user.settings.pinboard_notification"></el-switch>
            </el-form-item>
            <el-form-item :label="$t('settings.admin')">
                <el-switch v-model="user.settings.admin_notification"></el-switch>
            </el-form-item>
            <el-form-item :label="$t('settings.language')" style="max-width:450px;">
                <select-language :activeLanguage.sync="user.settings.language"/>
            </el-form-item>
            <el-form-item>
                <el-button @click="settingsUpdated" icon="ti-save" type="primary">{{$t('general.actions.save')}}</el-button>
            </el-form-item>
        </el-form>

    </el-card>
</template>

<script>
    import {mapGetters, mapState, mapActions} from 'vuex';
    import {displayError, displaySuccess} from 'helpers/messages';
    import SelectLanguage from 'components/SelectLanguage';

    export default {
        name: 'AdminSettingsAccount',
        components: {
            SelectLanguage
        },
        data() {
            return {
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
                    name: [
                        {
                            required: true,
                            message: this.$t('validation.required',{attribute: this.$t('general.name')})
                        }
                    ],
                },
                summaryValues: [
                    "daily", "monthly", "yearly"
                ],
            };
        },

        computed: {
            ...mapGetters(["getAllAvailableLanguages"]),
            ...mapState({
                user: ({users}) => users.loggedInUser
            })
        },

        methods: {
            ...mapActions(['updateUserSettings']),

            async settingsUpdated() {
                const resp = await this.updateUserSettings(this.user);
                displaySuccess(resp);
            },
        }

    }
</script>

<style lang="scss" scoped>
    .el-card {
        border-radius: 6px;
        box-shadow: 0 1px 3px transparentize(#000, .88),
        0 1px 2px transparentize(#000, .76);
        position: relative;

        &:before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background-image: url('~img/51554884_23843403016020020_4498652095628443648_n.png');
            background-repeat: no-repeat;
            background-size: contain;
            background-position: right bottom;
        }

        .el-form {
            /*max-width: 512px;*/

            .el-button :global([class*="ti"]) {
                margin-right: 8px;
            }
        }
    }
</style>
