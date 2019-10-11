import {mapActions, mapGetters} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';
import PasswordValidatorMixin from './passwordValidatorMixin';
import UploadUserAvatarMixin from './adminUploadUserAvatarMixin';
import axios from '@/axios';

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
                model: {
                    address: {
                        state_id: '',
                        city: '',
                        street: '',
                        house_num: '',
                        zip: ''
                    },
                    email: '',
                    user: {
                        password: '',
                        password_confirmation: '',
                        avatar: '',
                        id: '',
                        email: ''
                    },
                    service_provider_format: '',
                    name: '',
                    phone: '',
                    category: null,
                    settings: []
                },
                statistics: {
                    raw: [{
                        icon: 'ti-plus',
                        color: '#003171',
                        value: 0,
                        description: 'dashboard.requests.total_request'
                    },{
                        icon: 'ti-plus',
                        color: '#26A65B',
                        value: 0,
                        description: 'models.request.status.solved'
                    },{
                        icon: 'ti-plus',
                        color: '#26A65B',
                        value: 0,
                        description: 'models.request.status.pending'
                    },{
                        icon: 'ti-user',
                        color: '#003171',
                        value: 0,
                        description: 'models.building.assigned_buildings'
                    }, ],
                    percentage: {
                        occupied_units: 0,
                        free_units: 0,
                    }
                },
                validationRules: {
                    name: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.name')})
                    }],
                    phone: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.phone')})
                    }],
                    email: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.email')})
                    }, {
                        type: 'email',
                        message: this.$t('validation.email', {attribute: this.$t('general.email')})
                    }, {
                        validator: this.checkavailabilityEmail
                    }],
                    password: [{
                        validator: this.validatePassword
                    }, {
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.password')})
                    }, {
                        min: 6,
                        message: this.$t('validation.min.string', {attribute: this.$t('general.password'), min: 6})
                    }],
                    password_confirmation: [{
                        validator: this.validateConfirmPassword
                    }, {
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.confirm_password')})
                    }],
                    category: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    state_id: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.state')})
                    }],
                    city: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.city')})
                    }],
                    street: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.street')})
                    }],
                    house_num: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    zip: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.zip')})
                    }],
                },
                loading: {
                    state: false,
                    text: 'general.please_wait'
                },
                assignmentTypes: ['building', 'quarter'],
                assignmentType: 'building',
                toAssign: '',
                toAssignList: [],
                isFormSubmission: false
            };
        },
        computed: {
            form() {
                return this.$refs.form;
            },
            ...mapGetters(['states'])
        },
        methods: {
            ...mapActions(['getStates', 'getBuildings', 'getQuarters', 'assignServiceBuilding',
                'assignServiceQuarter']),
            translateType(type) {
                return this.$t(`general.assignmentTypes.${type}`);
            },
            async remoteSearchBuildings(search) {
                if (search === '') {
                    this.resetToAssignList();
                } else {
                    this.remoteLoading = true;

                    try {
                        let resp = [];
                        if (this.assignmentType === 'building') {
                            resp = await this.getBuildings({
                                get_all: true,
                                search,
                            });
                        } else {
                            resp = await this.getQuarters({get_all: true, search});
                        }

                        this.toAssignList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            async checkavailabilityEmail(rule, value, callback) {
                let validateObject = this.model;

                if(this.isFormSubmission == true)
                    return;
                
                if(config.mode == 'add' || ( this.original_email != null && this.original_email !== validateObject.email)) {
                    try {
                        const resp = await axios.get('users/check-email?email=' + validateObject.email);                 
                    } catch(error) {
                        if(error.response.data.success == false) {
                            callback(new Error(error.response.data.message));
                        }
                    }
                }
            },
            attachBuilding() {
                return new Promise(async (resolve, reject) => {
                    if (!this.toAssign || (!this.model.id && config.mode === 'edit')) {
                        return false;
                    }

                    try {

                        let resp;

                        if (this.assignmentType === 'building') {
                            resp = await this.assignServiceBuilding({
                                id: this.model.id,
                                toAssignId: this.toAssign
                            });
                        } else {
                            resp = await this.assignServiceQuarter({
                                id: this.model.id,
                                toAssignId: this.toAssign
                            });
                        }

                        if (resp && resp.data && config.mode === 'edit') {
                            this.$refs.assignmentsList.fetch();
                            this.toAssign = '';
                            displaySuccess(resp)
                        }

                        resolve(true);

                    } catch (e) {
                        displayError(e);
                        reject(false);
                    }
                })
            },
            resetToAssignList() {
                this.toAssignList = [];
                this.toAssign = '';
            },
        },
        created() {
            this.getStates();
        }
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.mixins = [PasswordValidatorMixin({
                    nestedModel: 'user'
                }), UploadUserAvatarMixin];

                mixin.methods = {
                    async submit(afterValid = false) {
                        this.isFormSubmission = true;
                        const valid = await this.form.validate();
                        this.isFormSubmission = false;
                        if (valid) {
                            this.loading.state = true;
                            try {

                                const resp = await this.createService(this.model);


                                if (resp.data.user && resp.data.user.id) {
                                    await this.uploadAvatarIfNeeded(resp.data.user.id);
                                }

                                displaySuccess(resp);

                                this.form.resetFields();
                                if (!!afterValid) {
                                    afterValid(resp);
                                } else {
                                    this.$router.push({
                                        name: 'adminServicesEdit',
                                        params: {id: resp.data.id}
                                    })
                                }
                                return resp;
                            } catch (err) {
                                displayError(err);
                            } finally {
                                this.loading.state = false;
                            }
                        }

                    },

                    ...mixin.methods,
                    ...mapActions(['createService'])
                };
                break;
            case 'edit':
                mixin.mixins = [PasswordValidatorMixin({
                    required: false,
                    nestedModel: 'user'
                }), UploadUserAvatarMixin];

                mixin.methods = {
                    submit() {
                        return new Promise((resolve, reject) => {
                            this.isFormSubmission = true;
                            this.form.validate(async valid => {

                                if (!valid) {
                                    resolve(false);
                                    return false;
                                }
                                this.isFormSubmission = false;
                                this.loading.state = true;
                                let {...params} = this.model;

                                if (params.user.password === '') {
                                    params = _.omit(params, ['user'])
                                }

                                params.user = {
                                    ...params.user,
                                    email: this.model.email
                                };

                                try {
                                    const resp = await this.updateService(params);

                                    if (resp.data.user && resp.data.user.id) {
                                        await this.uploadAvatarIfNeeded(resp.data.user.id);
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

                    async fetchCurrentProvider() {
                        this.loading.state = true;

                        const resp = await this.getService({id: this.$route.params.id});

                        const data = resp.data;

                        // TODO - do not like this, there is an alternative
                        this.$set(this.model, 'id', data.id);
                        this.model.name = data.name;
                        this.model.email = data.email;
                        this.model.phone = data.phone;
                        this.model.category = +data.category;
                        this.model.user.avatar = data.user.avatar;
                        this.model.user.id = data.user.id;
                        this.model.service_provider_format = data.service_provider_format;

                        this.model.settings = data.settings;

                        this.statistics.raw[0].value = data.requests_count;
                        this.statistics.raw[1].value = data.solved_requests_count;
                        this.statistics.raw[2].value = data.pending_requests_count;
                        this.statistics.raw[3].value = data.buildings_count;

                        const respAddress = data.address;

                        if (respAddress) {
                            this.model.address.state_id = respAddress.state.id;
                            this.model.address.city = respAddress.city;
                            this.model.address.street = respAddress.street;
                            this.model.address.house_num = respAddress.house_num;
                            this.model.address.zip = respAddress.zip;
                        }

                        // this.model.role = data.roles[0].name; // what if returns no roles?

                        this.loading.state = false;

                        return this.model;
                    },

                    ...mixin.methods,
                    ...mapActions(['getService', 'updateService'])
                };

                mixin.created = async function () {
                    this.getStates();
                    const {password, password_confirmation} = this.validationRules;

                    [...password, ...password_confirmation].forEach(rule => rule.required = false);
                    

                    await this.fetchCurrentProvider();
                    
                    this.original_email = this.model.email;
                };

                break;
        }
    }


    return mixin;
};


