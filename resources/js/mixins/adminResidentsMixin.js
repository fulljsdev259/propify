import {mapActions, mapGetters} from 'vuex';
import PasswordValidatorMixin from './passwordValidatorMixin';
import EmailCheckValidatorMixin from './emailCheckValidatorMixin';
import residentTypeCheckValidatorMixin from './residentTypeCheckValidatorMixin';
import ResidentTitleTypes from './methods/residentTitleTypes';
import {displayError, displaySuccess} from '../helpers/messages';
import UploadUserAvatarMixin from './adminUploadUserAvatarMixin';
import { parse } from 'querystring';

export default (config = {}) => {
    let mixin = {
        props: {
            title: {
                type: String,
                required: true
            }   
        },
        data() {
            return {
                remoteLoading: false,
                buildings: [],
                units: [],
                user: {},
                unit: {},
                birthDatePickerOptions: {
                    disabledDate(time) {
                        return time.getTime() > Date.now();
                    },
                },
                model: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    birth_date: '',
                    mobile_phone: '',
                    private_phone: '',
                    work_phone: '',
                    title: '',
                    company: '',
                    settings: {
                        language: '',
                    },
                    nation: '',
                    type: '',
                    contracts: [],
                    status: 1
                },
                visibleDrawer: false,
                editingContract: null,
                editingContractIndex: -1,
                validationRules: {
                    first_name: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.first_name')})
                    }],
                    last_name: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.last_name')})
                    }],
                    language: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.language')})
                    }],
                    email: [{
                        required: true,
                        message: this.$t("general.email_validation.required")
                    }, {
                        type: 'email',
                        message: this.$t("general.email_validation.email")
                    },{
                        validator: this.checkavailabilityEmail
                    }],
                    password: [{
                        validator: this.validatePassword
                    }, {
                        min: 6,
                        message: this.$t("general.password_validation.min")
                    }],
                    password_confirmation: [{
                        validator: this.validateConfirmPassword,
                    }],
                    birth_date: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.resident.birth_date')})
                    }],
                    title: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.salutation')})
                    }],
                    type: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }]
                },
                loading: {
                    state: false,
                    text: 'general.please_wait'
                },
                avatar: ''
            };
        },
        methods: {
            isFileImage (file) {
                const ext = file.name.split('.').pop()

                return ['jpg', 'jpeg', 'gif', 'bmp', 'png'].includes(ext);
            },
            isFilePDF (file) {
                debugger;
                const ext = file.name.split('.').pop()
                return ['.pdf'].includes(ext);
            },
            addContract (data) {
                this.model.contracts.push(data);
            },
            editContract(index) {
                this.editingContract = this.model.contracts[index];
                this.editingContractIndex = index;
                this.visibleDrawer = true;
                document.getElementsByTagName('footer')[0].style.display = "none";
            },
            updateContract(index, params) {
                this.$set(this.model.contracts, index, params);
            },
            deleteContract(index) {

                this.$confirm(this.$t(`general.swal.delete_contract.text`), this.$t(`general.swal.delete_contract.title`), {
                    type: 'warning'
                }).then(async () => {
                    if(config.mode == "edit" ) {
                        await this.$store.dispatch('contracts/delete', {id: this.model.contracts[index].id})
                    }
                    this.model.contracts.splice(index, 1)
                    this.visibleDrawer = false;
                }).catch(() => {
                });
            },
            toggleDrawer() {
                this.visibleDrawer = true;
                document.getElementsByTagName('footer')[0].style.display = "none";
                //this.$root.$refs.footer.css('display: none');
                //this.$el.querySelector('.footer').css('display: none');
            },
            ...mapActions(['getCountries', 'uploadMediaFile']),
        },
        async mounted() {
            await this.getCountries();
        },
        computed: {
            form() {
                return this.$refs.form;
            },
            used_units() {
                return this.model.contracts.map(item => item.unit_id)
            },
            ...mapGetters(['countries'])
        },
        watch: {
            'visibleDrawer': {
                immediate: false,
                handler (state) {
                    // TODO - auto blur container if visible is true first
                    if (!state) {
                        this.editingContract = null
                        document.getElementsByTagName('footer')[0].style.display = "block";
                    }
                }
            }
        },
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.mixins = [PasswordValidatorMixin(), EmailCheckValidatorMixin(), residentTypeCheckValidatorMixin(), ResidentTitleTypes, UploadUserAvatarMixin];

                mixin.methods = {
                    submit(afterValid = false) {
                        return new Promise((resolve, reject) => {
                            this.form.validate(async valid => {
                                if (!valid) {
                                    return reject(valid);
                                }

                                this.loading.state = true;
                                
                                this.model.contracts.forEach(contract => {
                                    contract.monthly_rent_gross = Number(contract.monthly_rent_net) + Number(contract.monthly_maintenance)
                                })

                                let {email, password, password_confirmation, ...resident} = this.model;

                                try {

                                    resident.status = 2
                                    const today = new Date().getTime();
                                    resident.contracts.forEach(contract => {
                                        const start_date = new Date(contract.start_date).getTime();
                                        const end_date = new Date(contract.end_date).getTime();
                                        if(contract.duration == 1 && start_date <= today )
                                            resident.status = 1
                                        if(contract.duration == 2 && start_date <= today && end_date > today)
                                            resident.status = 1
                                            
                                    })
                                    const resp = await this.createResident({
                                        user: {
                                            email,
                                            password,
                                            password_confirmation: password_confirmation
                                        },
                                        ...resident
                                    });

                                    if (resp.data.user && resp.data.user.id) {
                                        this.uploadAvatarIfNeeded(resp.data.user.id);
                                    }

                                    
                                    displaySuccess(resp);

                                    this.model.rent_start = '';
                                    this.form.resetFields();
                                    if (!!afterValid) {
                                        afterValid(resp);
                                    } else {
                                        // this.$router.push({
                                        //     name: 'adminResidentsView',
                                        //     params: {id: resp.data.id}
                                        // })
                                        resolve(resp)
                                    }
                                } catch (err) {
                                    displayError(err);
                                    reject(err);
                                } finally {
                                    this.loading.state = false;
                                }
                            });
                        });
                    },

                    ...mixin.methods,
                    ...mapActions(['createResident'])
                };

                break;
            case 'edit':
                mixin.mixins = [PasswordValidatorMixin({required: false}), EmailCheckValidatorMixin(), residentTypeCheckValidatorMixin(), ResidentTitleTypes, UploadUserAvatarMixin];

                mixin.methods = {
                    submit() {
                        return new Promise((resolve, reject) => {
                            this.form.validate(async valid => {
                                if (!valid) {
                                    resolve(false);
                                    return false;
                                }
                                this.loading.state = true;
                                let {password_confirmation, ...params} = this.model;

                                if (params.password === '') {
                                    params = _.omit(params, ['password'])
                                }

                                try {
                                    const resp = await this.updateResident(params);

                                    if (resp.data.user && resp.data.user.id) {
                                        this.uploadAvatarIfNeeded(resp.data.user.id);
                                    }

                                    displaySuccess(resp);
                                    resolve(true);
                                } catch (err) {
                                    displayError(err);
                                    resolve(false);
                                } finally {
                                    this.loading.state = false;
                                }
                            });
                        });
                    },

                    ...mixin.methods,
                    ...mapActions(['getResident', 'updateResident'])
                };

            case 'view':
                mixin.mixins = [PasswordValidatorMixin({required: false}), EmailCheckValidatorMixin(), ResidentTitleTypes, UploadUserAvatarMixin];
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['getResident'])
                }

                mixin.computed = {
                    ...mixin.computed
                };

                mixin.created = async function () {
                    const {password, password_confirmation} = this.validationRules;

                    [...password, ...password_confirmation].forEach(rule => rule.required = false);

                    try {
                        this.loading.state = true;

                        const {address, building, unit, user, ...r} = await this.getResident({id: this.$route.params.id});
                        this.user = user;
                        this.model = Object.assign({}, this.model, r);
                        this.original_email = this.user.email;
                        this.model.email = user.email;
                        this.model.avatar = user.avatar;
                        this.model.nation = +this.model.nation

                        this.validationRules.type = [{
                            required: true,
                            message: this.$t('validation.general.required')
                        },{
                            type: Number,
                            message: this.$t('validation.general.required')
                        },{
                            validator: this.checkavailabilityResidentType
                        }]
                    } catch (err) {
                        this.$router.replace({
                            name: 'adminResidents'
                        });

                        displayError(err);
                    } finally {
                        this.loading.state = false;
                    }
                };

                break;
        }
    }

    return mixin;
};