import {mapActions} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';
import PasswordValidatorMixin from './passwordValidatorMixin';
import UploadUserAvatarMixin from './adminUploadUserAvatarMixin';
import PropertyManagerTitlesMixin from './methods/propertyManagerTitleTypes';
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
                model: {
                    user: {
                        email: '',
                        password: '',
                        password_confirmation: '',
                        name: '',
                        phone: '',
                    },
                    settings: {
                        language: ''
                    },
                    email: '',
                    password: '',
                    password_confirmation: '',
                    name: '',
                    phone: '',
                },
                roles: [{
                        id:1, name:'manager'
                    }, {
                        id:2, name:'administrator'
                    }
                ],
                addedAssigmentList: [],
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
                    first_name: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.first_name')})
                    }],
                    last_name: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.last_name')})
                    }],
                    language: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.language')})
                    }],
                    type: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.roles.label')})
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
                    linkedin_url: [{
                        type: 'url',
                        message: this.$t('validation.url', {attribute: this.$t('models.property_manager.linkedin_url')})
                    }],
                    xing_url: [{
                        type: 'url',
                        message: this.$t('validation.url', {attribute: this.$t('models.property_manager.xing_url')})
                    }],
                    title: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.salutation')})
                    }]
                },
                loading: {
                    state: false,
                    text: 'general.please_wait'
                },
                requests: [],
                assignmentTypes: ['building', 'quarter'],
                assignmentType: 'building',
                toAssign: '',
                toAssignList: [],
                alreadyAssigned: {
                    buildings: [],
                    quarters: []
                },
                remoteLoading: false,
                isFormSubmission: false,
            };
        },
        computed: {
            form() {
                return this.$refs.form;
            },
        },
        methods: {
            ...mapActions(['getBuildings', 'getQuarters', 'assignBuilding', 'assignQuarter', 'unassignBuilding', 'unassignQuarter']),
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

                            resp.data = resp.data.filter((building) => {
                                return !this.alreadyAssigned.buildings.includes(building.id)
                            });
                        } else {
                            resp = await this.getQuarters({get_all: true, search});

                            resp.data = resp.data.filter((quarter) => {
                                return !this.alreadyAssigned.quarters.includes(quarter.id)
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
    
                if(config.mode == 'add' || ( this.original_email != null && this.original_email !== validateObject.user.email)) {
                    try {
                        const resp = await axios.get('users/check-email?email=' + validateObject.user.email);                
                    } catch(error) {
                        if(error.response.data.success == false) {
                            callback(new Error(error.response.data.message));
                        }
                    }
                }
            },
            resetToAssignList() {
                this.toAssignList = [];
                this.toAssign = '';
            },
            async attachBuilding() {
                if (!this.toAssign || !this.model.id) {
                    return false;
                }
                try {

                    let resp;

                    if (this.assignmentType === 'building') {
                        resp = await this.assignBuilding({
                            id: this.model.id,
                            toAssignId: this.toAssign
                        });
                    } else {
                        resp = await this.assignQuarter({
                            id: this.model.id,
                            toAssignId: this.toAssign
                        });
                    }

                    if (resp && resp.data) {
                        await this.fetchCurrentManager();
                        this.$refs.assignmentsList.fetch();
                        this.toAssign = '';
                        this.toAssignList = [];                        
                        displaySuccess(resp)
                    }
                } catch (e) {      
                    displayError(e)            
                }

            },
            async saveAddedAssigmentList(modelId) {
                try {
                    let resp;

                    this.addedAssigmentList.forEach(async element => {
                        if (element.type === 'building') {
                            resp = await this.assignBuilding({
                                id: modelId,
                                toAssignId: element.id
                            });
                        } else {
                            resp = await this.assignQuarter({
                                id: modelId,
                                toAssignId: element.id
                            });
                        }
                    });
                } catch (e) {
                    displayError(e)
                }
            },
            async attachAddedAssigmentList(assigmentId) {
                let assigment = this.toAssignList.filter(n => n.id === assigmentId)[0];

                if (!!assigment) {
                    this.addedAssigmentList.push({
                        id: assigmentId,
                        name: assigment.name,
                        type: this.assignmentType
                    });
                }

                this.alreadyAssigned.buildings = this.addedAssigmentList.filter(item => item['type'] === 'building').map((building) => building.id);
                this.alreadyAssigned.quarters = this.addedAssigmentList.filter(item => item['type'] === 'quarter').map((quarter) => quarter.id);

                this.toAssign = '';
                this.toAssignList = [];
            },
            async unassign(toUnassign) {
                let resp;
                if (toUnassign.aType == 1) {
                    resp = await this.unassignBuilding({
                        id: this.model.id,
                        toAssignId: toUnassign.id
                    })
                } else {
                    resp = await this.unassignQuarter({
                        id: this.model.id,
                        toAssignId: toUnassign.id
                    })
                }

                if (resp) {
                    await this.fetchCurrentManager();
                    this.$refs.assignmentsList.fetch();

                    this.toAssign = '';
                    const type = toUnassign.aType == 1 ? 'Building' : 'Quarter';
                    displaySuccess(resp);
                }
            }
        }
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.mixins = [PasswordValidatorMixin({
                }), UploadUserAvatarMixin, PropertyManagerTitlesMixin];

                mixin.methods = {
                    async submit(afterValid = false) {
                        this.isFormSubmission = true;
                        const valid = await this.form.validate();
                        this.isFormSubmission = false;
                        if (valid) {
                            this.loading.state = true;
                            try {

                                this.model.user.password = this.model.password
                                this.model.user.password_confirmation = this.model.password_confirmation
                                this.model.user.email = this.model.email
                                this.model.user.name = this.model.name
                                this.model.user.phone = this.model.phone

                                const resp = await this.createPropertyManager(this.model);

                                if (resp.data.user && resp.data.user.id) {
                                    await this.uploadAvatarIfNeeded(resp.data.user.id);
                                }                                  
                                displaySuccess(resp);

                                this.form.resetFields();

                                this.saveAddedAssigmentList(resp.data.id);

                                if (!!afterValid) {
                                    afterValid(resp);
                                } else {
                                    this.$router.push({
                                        name: 'adminPropertyManagersEdit',
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
                    ...mapActions(['createPropertyManager'])
                };
                break;
            case 'edit':
                mixin.mixins = [PasswordValidatorMixin({
                    required: false,
                }), UploadUserAvatarMixin, PropertyManagerTitlesMixin];

                mixin.methods = {
                    submit() {
                        return new Promise((resolve, reject) => {
                            this.isFormSubmission = true;
                            this.form.validate(async valid => {
                                if (!valid) {
                                    resolve(false);
                                    return
                                }
                                this.isFormSubmission = false;
                                this.loading.state = true;

                                this.model.user.password = this.model.password
                                this.model.user.password_confirmation = this.model.password_confirmation
                                this.model.user.email = this.model.email
                                this.model.user.name = this.model.name
                                this.model.user.phone = this.model.phone

                                let {...params} = this.model;

                                if (params.password === '') {
                                    params = _.omit(params, ['password', 'password_confirmation'])
                                }

                                try {
                                    params.buildings = params.building_ids;
                                    this.model.building_ids = params.building_ids;
                                    const resp = await this.updatePropertyManager(params);

                                    if (resp.data.user && resp.data.user.id) {
                                        await this.uploadAvatarIfNeeded(resp.data.user.id);
                                    }

                                    this.model = Object.assign({}, this.model, resp.data);                                    
                                    displaySuccess(resp);
                                    resolve(true);
                                } catch (err) {
                                    displayError(err);
                                    resolve(false);
                                } finally {
                                    this.loading.state = false;
                                }
                            })
                        });
                    },

                    async fetchCurrentManager() {
                        const resp = await this.getPropertyManager({id: this.$route.params.id});
                        const data = resp.data;
                        this.model = Object.assign({}, this.model, data);

                        this.model.email = this.model.user.email
                        this.model.name = this.model.user.name
                        this.model.phone = this.model.user.phone

                        this.alreadyAssigned.buildings = this.model.buildings.map((building) => building.id);
                        this.alreadyAssigned.quarters = this.model.quarters.map((quarter) => quarter.id);

                        this.statistics.raw[0].value = this.model.requests_count;
                        this.statistics.raw[1].value = this.model.solved_requests_count;
                        this.statistics.raw[2].value = this.model.pending_requests_count;
                        this.statistics.raw[3].value = this.model.buildings_count;

                        this.original_email = this.model.user.email;

                        return resp.data;
                    },

                    ...mixin.methods,
                    ...mapActions(['getPropertyManager', 'updatePropertyManager', 'getRequests'])
                };

                mixin.created = async function () {
                    const {password, password_confirmation} = this.validationRules;

                    [...password, ...password_confirmation].forEach(rule => rule.required = false);

                    this.loading.state = true;

                    await this.fetchCurrentManager();

                    this.loading.state = false;
                };

                break;
        }
    }


    return mixin;
};


