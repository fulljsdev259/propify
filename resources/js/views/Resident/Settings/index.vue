<template>
    <div class="settings">
        <ui-heading class="custom-heading" icon="icon-cog" :title="$t('resident.user_settings')"/>
        <ui-divider/>
        <el-tabs v-model="active">
            <el-tab-pane label="Personal Informations" name="personal_informations">
                <el-row>
                    <el-col :span="12" :xs="24">
                        <card>
                            <el-form :model="loggedInUser" label-width="120px" ref="accform">
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
                                            :defaultAvatarSrc="loggedInUser.avatar ? '/'+loggedInUser.avatar : ''"
                                            @cropped="cropped"/>

                                </el-form-item>
                                <el-form-item :label="$t('general.email')" :rules="accountValidationRules.email"
                                              prop="email">
                                    <el-input autocomplete="off" type="email" v-model="loggedInUser.email"></el-input>
                                </el-form-item>
                                <el-form-item>
                                    <el-button @click="submitEditDetailsForm()" icon="ti-save" type="primary">
                                        {{$t('general.actions.save')}}
                                    </el-button>
                                </el-form-item>
                            </el-form>
                        </card>
                    </el-col>
                </el-row>
            </el-tab-pane>
            <el-tab-pane :label="$t('resident.security')" name="security">
                <el-row>
                    <el-col :span="12" :xs="24">
                        <card>
                            <el-form :model="changePassword" label-width="210px" ref="changePasswordForm" size="medium">
                                <el-form-item :label="$t('resident.old_password')" :rules="passwordValidationRules.password_old"
                                              prop="password_old">
                                    <el-input autocomplete="off" type="password"
                                              v-model="changePassword.password_old"></el-input>
                                </el-form-item>
                                <el-form-item :label="$t('resident.new_password')" :rules="passwordValidationRules.password"
                                              prop="password">
                                    <el-input autocomplete="off" type="password"
                                              v-model="changePassword.password"></el-input>
                                </el-form-item>
                                <el-form-item :label="$t('resident.confirm_password')"
                                              :rules="passwordValidationRules.password_confirmation"
                                              prop="password_confirmation">
                                    <el-input autocomplete="off" type="password"
                                              v-model="changePassword.password_confirmation"></el-input>
                                </el-form-item>
                                <el-form-item>
                                    <el-button @click="submitChangePasswordForm" icon="ti-save" type="primary">
                                        {{$t('resident.change')}}
                                    </el-button>
                                    <el-button @click="resetForm">{{$t('resident.cancel')}}</el-button>
                                </el-form-item>
                            </el-form>
                        </card>
                    </el-col>
                </el-row>
            </el-tab-pane>
            <el-tab-pane :label="$t('resident.default_address')" name="default_address" v-if="contracts.length > 1 || expired == true">
                <el-row>
                    <el-col :span="12" :xs="24">
                        <card>
                            <el-form :model="defaultAddress" label-width="140px" ref="defaultAddressForm" size="medium">
                                <el-alert                                     
                                    :title="$t('resident.default_contract_expired')"
                                    type="info"
                                    show-icon
                                    :closable="false"
                                    v-if="expired"
                                >
                                </el-alert>
                                <el-form-item :label="$t('resident.default_contract_id')" :rules="defaultAddressValidationRules.default_contract_id"
                                              prop="default_contract_id">
                                    <el-select v-model="defaultAddress.default_contract_id" 
                                                :placeholder="$t('resident.placeholder.contract')"
                                                class="custom-select"
                                                filterable
                                                value-key="loggedInUser.resident.default_contract_id">
                                        <el-option v-for="contract in dirtyContracts" 
                                                    :key="contract.id" 
                                                    :label="contract.building_room_floor_unit" 
                                                    :value="contract.id" />
                                    </el-select>
                                </el-form-item>
                                <el-form-item>
                                    <el-button @click="submitDefaultAddressForm" icon="ti-save" type="primary">
                                        {{$t('general.actions.save')}}
                                    </el-button>
                                </el-form-item>
                            </el-form>
                        </card>
                    </el-col>
                </el-row>
            </el-tab-pane>
            <!-- <el-tab-pane :label="$t('settings.notifications')" name="language">
                <card>
                    <el-form label-position="right" label-width="200px">
                        <el-form-item :label="$t('settings.summary.label')">
                            <el-select v-model="loggedInUser.settings.summary">
                                <el-option :key="summary" :label="$t('settings.summary.' + summary )" :value="summary"
                                           v-for="summary in summaryValues"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="$t('settings.service')">
                            <el-switch v-model="loggedInUser.settings.service_notification"></el-switch>
                        </el-form-item>
                        <el-form-item :label="$t('settings.pinboard')">
                            <el-switch v-model="loggedInUser.settings.pinboard_notification"></el-switch>
                        </el-form-item>
                        <el-form-item :label="$t('settings.listing')">
                            <el-switch v-model="loggedInUser.settings.martketplace_notification"></el-switch>
                        </el-form-item>
                        <el-form-item :label="$t('settings.admin')">
                            <el-switch v-model="loggedInUser.settings.admin_notification"></el-switch>
                        </el-form-item>
                        <el-form-item :label="$t('resident.choose_language')">
                                <el-radio-group v-model="loggedInUser.settings.language">
                                    <el-radio-button label="fr">
                                        <span class="flag-icon flag-icon-fr"></span> {{$t('resident.languages.fr')}}
                                    </el-radio-button>
                                    <el-radio-button label="de">
                                        <span class="flag-icon flag-icon-de"></span> {{$t('resident.languages.de')}}
                                    </el-radio-button>
                                    <el-radio-button label="en">
                                        <span class="flag-icon flag-icon-us"></span> {{$t('resident.languages.en')}}
                                    </el-radio-button>
                                    <el-radio-button label="it">
                                        <span class="flag-icon flag-icon-it"></span> {{$t('resident.languages.it')}}
                                    </el-radio-button>
                                </el-radio-group>
                        </el-form-item>
                        <el-form-item>
                            <el-button @click="settingsUpdated" icon="ti-save" type="primary">{{$t('resident.actions.save')}}</el-button>
                        </el-form-item>
                    </el-form>
                </card>
            </el-tab-pane> -->
        </el-tabs>
    </div>
</template>

<script>
    // TODO: REFACTOR THIS COMPONENT, it is old and has junk code

    import Heading from 'components/Heading';
    import {mapGetters, mapState, mapActions} from 'vuex';
    import {displayError, displaySuccess} from 'helpers/messages';
    import Card from 'components/Card';
    import Cropper from 'components/Cropper';


    export default {
        name: 'ResidentSettings',
        components: {
            Card,
            Heading,
            Cropper
        },
        data() {
            return {
                active: 'personal_informations',
                dialogImageUrl: '',
                dialogVisible: false,
                language: 'en',
                content: '',
                imageUrl: '',
                accountValidationRules: {
                    email: [
                        {
                            required: true,
                            message: this.$t("general.email_validation.required")
                        },
                        {
                            type: 'email',
                            message: this.$t("general.email_validation.email")
                        }
                    ]
                },
                passwordValidationRules: {
                    password_old: [
                        {
                            required: true,
                            message: this.$t("general.password_validation.old_password_required")
                        },
                        {
                            min: 6,
                            message: this.$t("general.password_validation.old_password_min")
                        }
                    ],
                    password: [
                        {
                            validator: this.validatePassword,
                        },
                        {
                            required: true
                        },
                        {
                            min: 6,
                            message: this.$t("general.password_validation.min")
                        }
                    ],
                    password_confirmation: [
                        {
                            validator: this.validateConfirmPassword,
                        },
                        {
                            required: true
                        }
                    ],
                },
                defaultAddressValidationRules: {
                    default_contract_id: [
                        {
                            required: true,
                            message: this.$t("models.quarter.required")
                        },
                    ],
                },
                changePassword: {
                    password_old: '',
                    password: '',
                    password_confirmation: ''
                },
                defaultAddress: {
                    default_contract_id: '',
                },
                image: '',
                summaryValues: [
                    "daily", "monthly", "yearly"
                ],
                expired: false
            };
        },
        computed: {
            ...mapState({
                loggedInUser: ({users}) => {
                    return users.loggedInUser;
                },
                contracts: ({users}) => {
                    return users.loggedInUser.resident.contracts.filter(item => item.status == 1);
                }
            }),
            ...mapGetters(["getAllAvailableLanguages", "loggedInUser"]),
            dirtyContracts() {
                return this.contracts.map(contract => { 
                    let floor_label;
                    if(contract.unit.attic == 'attic')
                    {
                        floor_label = this.$t('models.unit.floor_title.top_floor')
                    }
                    else if(contract.unit.floor > 0)
                    {
                        floor_label = contract.unit.floor + ". " + this.$t('models.unit.floor_title.upper_ground_floor')
                    }
                    else if(contract.unit.floor == 0)
                    {
                        floor_label = this.$t('models.unit.floor_title.ground_floor')
                    }
                    else if(contract.unit.floor < 0)
                    {
                        floor_label = contract.unit.floor + ". " + this.$t('models.unit.floor_title.under_ground_floor')
                    }
                    contract.building_room_floor_unit = contract.building.name + " -- " + contract.unit.room_no + " " + this.$t('models.unit.rooms') + " -- " + floor_label + " -- " +  contract.unit.name
                    return contract
                });
            },
            
        },
        methods: {
            ...mapActions(['updateUserSettings', 'changeUserPassword', 'changeDetails', 'uploadAvatar', 'me', 'updateDefaultContract']),
            cropped(e) {
                this.image = e;
            },
            upload() {
                if (this.image) {
                    this.uploadAvatar({
                        image_upload: this.image
                    }).catch((err) => {
                        displayError(err)
                    });
                }
            },
            submitEditDetailsForm() {
                this.$refs.accform.validate(async (valid) => {
                    if (!valid) {
                        return false;
                    }

                    const payload = {
                        name: this.loggedInUser.name,
                        email: this.loggedInUser.email,
                        phone: this.loggedInUser.phone,
                    };

                    try {
                        const resp = await this.changeDetails(payload);
                        await this.upload();
                        await this.me();
                        displaySuccess(resp);
                    } catch (e) {
                        displayError(e);
                    }
                });
            },
            submitChangePasswordForm() {
                this.$refs.changePasswordForm.validate((valid) => {
                    if (valid) {
                        this.changeUserPassword(this.changePassword).then((response) => {
                            displaySuccess(response); 
                            this.resetForm();
                        }).catch((err) => {
                            displayError(err);
                        });
                    } else {
                        return false;
                    }
                });
            },
            submitDefaultAddressForm() { //@TODO need to check it again : said API is ready, but now working correctly.
                this.$refs.defaultAddressForm.validate(async (valid) => {
                    if (!valid) {
                        return false;
                    }

                    const payload = {
                        default_contract_id: this.defaultAddress.default_contract_id
                    };

                    try {
                        const resp = await this.updateDefaultContract(payload);
                        await this.me();
                        displaySuccess(resp);
                    } catch (e) {
                        displayError(e);
                    }
                });
            },
            validatePassword(rule, value, callback) {
                if (value === '') {
                    callback(new Error(this.$t("general.password_validation.required")));
                } else {
                    if (this.changePassword.password_confirmation !== '') {
                        this.$refs.changePasswordForm.validateField('password_confirmation');
                    }
                    callback();
                }
            },
            validateConfirmPassword(rule, value, callback) {
                if (value === '') {
                    callback(new Error(this.$t('validation.required',{attribute: this.$t('resident.confirm_password')})));
                } else if (value !== this.changePassword.password) {
                    callback(new Error(this.$t('validation.confirmed',{attribute: this.$t('resident.confirm_password')})));
                } else {
                    callback();
                }
            },
            resetForm() {
                this.$refs.changePasswordForm.resetFields();
            },
            settingsUpdated() {
                this.updateUserSettings(this.loggedInUser).then((resp) => {
                    this.$i18n.locale = this.loggedInUser.settings.language;
                    displaySuccess({
                        success: true,
                        message: this.$t('settings.updated')
                    });
                }).catch((err) => {
                    displayError(err);
                });
            },
            handleRemove(file, fileList) {
                
            },
            handlePictureCardPreview(file) {
                this.dialogImageUrl = file.url;
                this.dialogVisible = true;
            }
        },
        mounted () {
            this.defaultAddress.default_contract_id = this.loggedInUser.resident.default_contract_id
            if(!this.contracts.find(item => item.id == this.defaultAddress.default_contract_id)) {
                this.expired = true
                this.defaultAddress.default_contract_id = undefined
            }
        }
    }
</script>

<style lang="scss" scoped>
    .settings {
        .custom-heading {
            margin-bottom: 2em;
        }

        &:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            background-image: url('~img/50761378_23843142320790435_749282303489867776_n.png');
            background-repeat: no-repeat;
            background-position: calc(100% + 6em) calc(100% + 6em);
            width: 100%;
            height: 100%;
        }

        :global(.el-input__prefix) {
            left: 12px;
        }

        .el-button :global(span) {
            margin-left: 8px;
        }

        .custom-select {
            width: 100%;
        }

        .el-alert {
            margin-bottom: 10px;
        }
        
        .el-tabs {
            :global(.el-tabs__content) {
                padding: 2px;

                :global(.el-tab-pane) {
                    .el-row {
                        .el-col:first-of-type {
                            max-width: 768px;;
                        }
                    }

                    &:nth-of-type(4) .el-row .el-col {
                        .el-form {
                            .el-form-item {
                                .el-radio-group {
                                    .el-radio-button {
                                        :global(.el-radio-button__inner) {
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            flex-direction: column;

                                            :global(span) {
                                                margin-bottom: .5em;
                                            }
                                        }
                                    }
                                }

                                :global(.el-form-item__label) {
                                    line-height: 61px;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
</style>
