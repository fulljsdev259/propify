import {mapActions, mapGetters} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';

export default (config = {}) => {
    let mixin = {
        data() {
            return {
                model: {
                    id: '',
                    name: '',
                    description: '',
                    buildings: [],
                    count_of_buildings: null,
                    state_id: '',
                    city: '',
                    zip: '',
                    street: 'street',
                    house_num: 'house_num',
                    media: [],
                    internal_quarter_id: '',
                    types: [],
                    url: '',
                    workflows: [],
                },
                quarter_format: '',
                validationRules: {
                    types: [{
                        required: true,
                        message: this.$t('models.quarter.required')
                    }], 
                    name: [{
                        required: true,
                        message: this.$t('models.quarter.required')
                    }], 
                    state_id: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.state')})
                    }],
                    city: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.city')})
                    }],
                    zip: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.zip')})
                    }],
                    internal_quarter_id: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.internal_quarter_id')})
                    }],
                },
                loading: {
                    state: false,
                    text: 'general.please_wait'
                },
                buildingsCount: 20,
                remoteLoading: false,
                toAssign: [],
                toAssignList: [],
                old_model: null,
            }
        },
        computed: {
            form() {
                return this.$refs.form;
            },
            ...mapGetters(['states'])
        },
        methods: {
            ...mapActions([
                'getStates', 
                'assignUserToQuarter', 
                'assignUsersToQuarter', 
                'getQuarter', 
                'getUsers', 
                'getAllAdminsForQuarter', 
                'getPropertyManagers', 
                'unassignQuarterAssignee'
            ]),
            resetToAssignList() {
                this.toAssignList = [];
                this.toAssign = [];
            },
            // async assignUser() {
            //     if (!this.toAssign || !this.model.id) {
            //         return false;
            //     }
            //     let resp;

            //     let assign_user = this.toAssignList.find(item => item.id == this.toAssign )

            //     resp = await this.assignUserToQuarter({
            //                 id: this.model.id,
            //                 user_id: this.toAssign,
            //                 role: assign_user.roles[0].name,
            //             });

            //     if (resp && resp.data) {
            //         displaySuccess(resp)                           
            //         this.resetToAssignList();
            //         this.$refs.assigneesList.fetch();    
            //         if(this.$refs.auditList){
            //             this.$refs.auditList.fetch();
            //         }
            //     }
            // },
            async assignUsers() {
                if (!this.toAssign || !this.model.id) {
                    return false;
                }
                let resp;

                let user_params = []

                this.toAssign.forEach(id => {
                    let assign_user = this.toAssignList.find(item => item.id == id )
                    user_params.push({user_id: id, role: assign_user.roles[0].name})
                })
                

                resp = await this.assignUsersToQuarter({
                            id: this.model.id,
                            user_params: user_params
                        });

                if (resp && resp.data) {
                    displaySuccess(resp)                           
                    this.resetToAssignList();
                    this.$refs.assigneesList.fetch();    
                    if(this.$refs.auditList){
                        this.$refs.auditList.fetch();
                    }
                }
            },
            async remoteSearchAssignees(search) {

                /*if (!this.$can(this.$permissions.assign.quarter)) {
                    return false;
                }*/

                if (search === '') {
                    this.toAssignList = [];
                } else {
                    this.remoteLoading = true;
                    
                    try {
                        const resp = await this.getAllAdminsForQuarter({quarter_id: this.$route.params.id, is_get_function: true, search})
                        this.toAssignList = resp;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            unassignQuarter(assignee) {
                this.$confirm(this.$t(`general.swal.confirm_change.title`), this.$t('general.swal.confirm_change.warning'), {
                    confirmButtonText: this.$t(`general.swal.confirm_change.confirm_btn_text`),
                    cancelButtonText: this.$t(`general.swal.confirm_change.cancel_btn_text`),
                    type: 'warning'
                }).then(async () => {
                    try {                        
                        const resp = await this.unassignQuarterAssignee({                            
                            assignee_id: assignee.id
                        });

                        displaySuccess(resp);

                        this.$refs.assigneesList.fetch();
                        if(this.$refs.auditList){
                            this.$refs.auditList.fetch();
                        }

                    } catch (e) {
                        displayError(e);
                    } finally {
                        this.loading.status = false;
                    }
                }).catch(() => {
                    this.loading.status = false;
                })

            },
            getLanguageI18n() {
                this.getStates();
                this.types = Object.entries(this.$constants.quarters.type).map(([value, label]) => ({value: +value, name: this.$t(`models.quarter.types.${label}`)}))
                this.assignment_types = Object.entries(this.$constants.quarters.assignment_type).map(([value, label]) => ({value: +value, name: this.$t(`models.quarter.assignment_types.${label}`)}))
            },
        },
        watch: {
            "$i18n.locale": {
                immediate: true,
                handler(val) {
                    this.getLanguageI18n();
                }
            }
        },
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['createQuarter']),
                    async saveQuarter() {
                        const {state_id, city, street, house_num, zip, ...restParams} = this.model;
                        const resp = await this.createQuarter({
                            address: {
                                state_id,
                                city,
                                street,
                                house_num,
                                zip
                            },
                            ...restParams
                        });
                        return resp;
                    },
                    submit(afterValid = false) {
                        return new Promise(async (resolve, reject) => {
                            const valid = await this.form.validate();
                            if (valid) {
                                this.loading.state = true;
                                try {
                                    const resp = await this.saveQuarter();
                                    displaySuccess(resp);
                                    
                                    this.form.resetFields();
                                    if (!!afterValid) {
                                        afterValid(resp);
                                    } else {
                                        // this.$router.push({
                                        //     name: 'adminQuartersEdit',
                                        //     params: {id: resp.data.id}
                                        // })
                                        resolve(resp);
                                    }
                                    
                                } catch (err) {
                                    displayError(err);
                                    reject(err);
                                } finally {
                                    this.loading.state = false;
                                }
                            }
                        });
                    },
                };

                mixin.created = async function () {
                    this.loading.state = true;
                    //this.getLanguageI18n();
                    this.loading.state = false;
                };

                break;

            case 'edit':
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['getQuarter', 'updateQuarter']),
                    async fetchCurrentQuarter() {
                        const resp = await this.getQuarter({id: this.$route.params.id});
                        
                        const {
                            address: {
                                state: {
                                    id: state_id
                                },
                                ...restAddress
                            },
                           
                            ...restData
                        } = resp

                        this.model = {state_id, ...restAddress, ...restData};

                        this.fileCount = this.model.media.length
                        this.relationCount = this.model.relations.length
                        this.workflowCount = this.model.workflows.length

                    },
                    submit(goToListing = false) {
                        return new Promise((resolve, reject) => {
                            this.form.validate(async valid => {
                                if (!valid) {
                                    resolve(false);
                                    return false;
                                }

                                this.loading.state = true;
                                
                                try {
                                    
                                    const {state_id, city, street, house_num, zip, ...restParams} = this.model;

                                    const resp = await this.updateQuarter({
                                        address: {
                                            state_id,
                                            city,
                                            street,
                                            house_num,
                                            zip
                                        },
                                        ...restParams
                                    });    
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
                };

                mixin.created = async function () {
                    this.loading.state = true;
                    //this.getLanguageI18n();
                    await this.fetchCurrentQuarter();
                    this.loading.state = false;
                };

                break;
        }
    }

    return mixin;
}
