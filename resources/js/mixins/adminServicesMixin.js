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
                        avatar_variations: '',
                        id: '',
                        email: ''
                    },
                    service_provider_format: '',
                    company_name: '',
                    last_name: '',
                    first_name: '',
                    title: '',
                    phone: '',
                    mobile_phone: '',
                    category: null,
                    settings: {
                        language: 'en' //@TODO : remove language
                    },
                    password: '',
                    password_confirmation: '',
                    avatar: '',
                    id: '',
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
                    company_name: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('models.service.company_name')})
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
                    // password_confirmation: [{
                    //     validator: this.validateConfirmPassword
                    // }, {
                    //     required: true,
                    //     message: this.$t('validation.required', {attribute: this.$t('general.confirm_password')})
                    // }],
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
                    language: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    title: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.salutation')})
                    }],
                    first_name: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.first_name')})
                    }],
                    last_name: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.last_name')})
                    }],
                },
                loading: {
                    state: false,
                    text: 'general.please_wait'
                },
                assignmentTypes: ['quarter', 'building'],
                assignmentType: 'quarter',
                toAssign: '',
                toAssignList: [],
                isFormSubmission: false,
                user: {},
                titles: null,
                old_model: null,
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
                'assignServiceQuarter', 'getServiceAssignments']),
            translateType(type) {
                return this.$t(`general.assignment_types.${type}`);
            },
            async remoteSearchBuildings(search) {
                if (search === '') {
                    this.resetToAssignList();
                } else {
                    this.remoteLoading = true;

                    try {
                        let resp = [],
                            exclude_ids = [];

                        const serviceAssignments = await this.getServiceAssignments({provider_id: this.$route.params.id});

                        if (this.assignmentType === 'building') {
                            serviceAssignments.data.data.map(item => {
                                if(item.assignmentType === 'building'){
                                    exclude_ids.push(item.id);
                                }
                            });

                            resp = await this.getBuildings({
                                get_all: true,
                                search,
                                exclude_ids: exclude_ids.join(',')
                            });
                        } else {
                            serviceAssignments.data.data.map(item => {
                                if(item.assignmentType === 'quarter'){
                                    exclude_ids.push(item.id);
                                }
                            });

                            resp = await this.getQuarters({
                                get_all: true,
                                search,
                                exclude_ids: exclude_ids.join(',')
                            });
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
                            if(this.$refs.auditList){
                                this.$refs.auditList.fetch();
                            }
                            this.resetToAssignList();
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
            this.titles = Object.entries(this.$constants.serviceProviders.title).map(([value, label]) => ({value: label, name: this.$t(`general.salutation_option.${label}`)}))

            this.getStates();
        }
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.mixins = [PasswordValidatorMixin({
                }), UploadUserAvatarMixin];

                mixin.methods = {
                     submit(afterValid = false) {
                        return new Promise(async (resolve, reject) => {
                            this.isFormSubmission = true;
                            const valid = await this.form.validate();
                            this.isFormSubmission = false;
                            if (valid) {
                                this.loading.state = true;
                                try {

                                    this.model.user.password = this.model.password
                                    //this.model.user.password_confirmation = this.model.password_confirmation
                                    this.model.user.password_confirmation = this.model.password
                                    this.model.user.avatar = this.model.avatar
                                    this.model.user.email = this.model.email
                                    const resp = await this.createService(this.model);


                                    if (resp.data.user && resp.data.user.id) {
                                        await this.uploadAvatarIfNeeded(resp.data.user.id);
                                    }

                                    displaySuccess(resp);

                                    this.form.resetFields();
                                    if (!!afterValid) {
                                        afterValid(resp);
                                    } else {
                                        // this.$router.push({
                                        //     name: 'adminServicesEdit',
                                        //     params: {id: resp.data.id}
                                        // })
                                        resolve(resp)
                                    }
                                    return resp;
                                } catch (err) {
                                    displayError(err);
                                    reject(err);
                                } finally {
                                    this.loading.state = false;
                                }
                            }
                        });
                    },

                    ...mixin.methods,
                    ...mapActions(['createService'])
                };
                break;
            case 'edit':
                mixin.mixins = [PasswordValidatorMixin({
                    required: false,
                }), UploadUserAvatarMixin];

                mixin.methods = {
                    submit(goToListing = false) {
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

                                params.user.password = params.password
                                //params.user.password_confirmation = params.password_confirmation
                                params.user.password_confirmation = params.password
                                params.user.avatar = params.avatar
                                params.user.email = params.email

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
                                    this.old_model = _.clone(this.model, true);
                                    if(this.$refs.auditList){
                                        this.$refs.auditList.fetch();
                                    }
                                    displaySuccess(resp);
                                    resolve(true);
                                } catch (err) {
                                    displayError(err);
                                    resolve(false);
                                } finally {
                                    this.loading.state = false;
                                    if(goToListing) {
                                        this.$refs.editActions.goToListing()
                                    }
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

                        this.model.company_name = data.company_name;
                        this.model.first_name = data.first_name;
                        this.model.last_name = data.last_name;
                        this.model.title = data.title;
                        this.model.email = data.email;
                        this.model.phone = data.phone;
                        this.model.mobile_phone = data.mobile_phone;
                        this.model.category = +data.category;
                        
                        this.model.user.avatar = data.user.avatar;
                        this.model.user.avatar_variations = data.user.avatar_variations;
                        this.model.user.id = data.user.id;
                        this.model.service_provider_format = data.service_provider_format;
                        
                        this.model.avatar = data.user.avatar;

                        this.user = data.user

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
                    this.titles = Object.entries(this.$constants.serviceProviders.title).map(([value, label]) => ({value: label, name: this.$t(`general.salutation_option.${label}`)}))

                    this.getStates();
                    // const {password, password_confirmation} = this.validationRules;

                    // [...password, ...password_confirmation].forEach(rule => rule.required = false);
                    const {password} = this.validationRules;

                    [...password].forEach(rule => rule.required = false);
                    

                    await this.fetchCurrentProvider();

                    this.$refs['form'].clearValidate();
                    
                    this.original_email = this.model.email;
                };

                break;
            case 'view':
                mixin.mixins = [PasswordValidatorMixin({required: false}), UploadUserAvatarMixin];
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['getService'])
                }

                mixin.computed = {
                    ...mixin.computed
                };

                mixin.created = async function () {
                    // const {password, password_confirmation} = this.validationRules;

                    // [...password, ...password_confirmation].forEach(rule => rule.required = false);
                    const {password} = this.validationRules;

                    [...password].forEach(rule => rule.required = false);

                    try {
                        this.loading.state = true;

                        const {data: {address, unit, user, ...r}} = await this.getService({id: this.$route.params.id});
                        
                        this.user = user;
                        
                        this.model = Object.assign({}, this.model, r);
                        this.original_email = this.user.email;
                        this.model.email = user.email;
                        this.model.avatar = user.avatar;

                    } catch (err) {
                        this.$router.replace({
                            name: 'adminServices'
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


