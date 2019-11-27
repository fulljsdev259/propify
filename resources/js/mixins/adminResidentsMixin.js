import {mapActions, mapGetters} from 'vuex';
import PasswordValidatorMixin from './passwordValidatorMixin';
import EmailCheckValidatorMixin from './emailCheckValidatorMixin';
import residentTypeCheckValidatorMixin from './residentTypeCheckValidatorMixin';
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
                new_media: [],
                user: {
                    avatar_variations: ''
                },
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
                        language: 'en', //@TODO : remove language
                    },
                    nation: '',
                    //type: '',
                    types: [],
                    relations: [],
                    status: 1
                },
                old_model: {},
                original_type: null,
                visibleDrawer: false,
                visibleRelationDialog: false,
                visibleMediaDialog: false,
                editingRelation: null,
                editingRelationIndex: -1,
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
                    }, {
                        validator: this.checkavailabilityEmail
                    }],
                    password: [{
                        validator: this.validatePassword
                    }, {
                        min: 6,
                        message: this.$t("general.password_validation.min")
                    }],
                    // password_confirmation: [{
                    //     validator: this.validateConfirmPassword,
                    // }],
                    birth_date: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.resident.birth_date')})
                    }],
                    title: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.salutation')})
                    }],
                    // type: [{
                    //         required: true,
                    //         message: this.$t('validation.general.required')
                    //     }, {
                    //         validator: this.checkavailabilityResidentType
                    //     }
                    // ],
                    // tenant_type: [{
                    //     required: true,
                    //     message: this.$t('validation.required',{attribute: this.$t('models.resident.tenant_type.label')})
                    // }],
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
            async addRelation (data) {
                if(config.mode == 'add') {
                    //this.original_type = this.model.type
                    this.model.relations.push(data);
                }
                else {
                    const resp = await this.$store.dispatch('relations/get', {resident_id : this.model.id});
                    this.model.relations = resp.data
                    this.$refs.mediaList.fetch();
                }
                
            },
            editRelation(index) {
                if(this.model.relations[index].garant == 1) {
                    return this.$router.push({
                        name: 'adminResidentsEdit',
                        params: {
                            id: this.model.relations[index].resident_id
                        }
                    }).then(() => {
                        window.location.reload()
                    }).catch(err => {})
                }
                else {
                    this.editingRelation = this.model.relations[index];
                    this.editingRelationIndex = index;
                    this.visibleDrawer = true;
                    this.visibleRelationDialog = true;
                }
            },
            updateRelation(index, params) {
                this.$set(this.model.relations, index, params);
            },
            deleteRelation(index) {

                this.$confirm(this.$t(`general.swal.delete_relation.text`), this.$t(`general.swal.delete_relation.title`), {
                    type: 'warning'
                }).then(async () => {
                    if(config.mode == "edit" ) {
                        await this.$store.dispatch('relations/delete', {id: this.model.relations[index].id})
                    }
                    this.model.relations.splice(index, 1)
                    this.visibleDrawer = false;
                    this.visibleRelationDialog = false;
                }).catch(() => {
                });
            },
            toggleDrawer() {
                this.visibleDrawer = true;
            },
            showRelationDialog() {
                this.visibleRelationDialog = true;
            },
            showMediaDialog() {
                this.visibleMediaDialog = true;
            },
            closeMediaDialog() {
                this.visibleMediaDialog = false;
            },
            uploadMedia() {
                this.new_media.map(async item => {
                    if(!item.url) {
                        item.id = this.model.id
                        let resp = await this.uploadMediaFile(item)

                        if(resp && resp.success) {
                            displaySuccess(resp)
                            this.$refs.mediaList.fetch();
                        }

                        
                    }
                })
                this.visibleMediaDialog = false;
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
                return this.model.relations.map(item => item.unit_id)
            },
            ...mapGetters(['countries'])
        },
        watch: {
            'visibleDrawer': {
                immediate: false,
                handler (state) {
                    // TODO - auto blur container if visible is true first
                    if (!state) {
                        this.editingRelation = null
                    }
                }
            },
            'visibleRelationDialog': {
                immediate: false,
                handler (state) {
                    // TODO - auto blur container if visible is true first
                    if (!state) {
                        this.editingRelation = null
                    }
                }
            }
        },
        created() {
            this.titles = Object.entries(this.$constants.residents.title).map(([value, label]) => ({value: label, name: this.$t(`general.salutation_option.${label}`)}))
        },
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.mixins = [PasswordValidatorMixin(), EmailCheckValidatorMixin(), residentTypeCheckValidatorMixin(), UploadUserAvatarMixin];

                mixin.methods = {
                    submit(afterValid = false) {
                        return new Promise((resolve, reject) => {
                            this.form.validate(async valid => {
                                if (!valid) {
                                    return reject(valid);
                                }

                                this.loading.state = true;
                                
                                this.model.relations.forEach(relation => {
                                    relation.monthly_rent_gross = Number(relation.monthly_rent_net) + Number(relation.monthly_maintenance)
                                })

                                //let {email, password, password_confirmation, ...resident} = this.model;
                                let {email, password, ...resident} = this.model;


                                try {

                                    // resident.status = 2
                                    // const today = new Date().getTime();
                                    // resident.relations.forEach(relation => {
                                    //     const start_date = new Date(relation.start_date).getTime();
                                    //     const end_date = new Date(relation.end_date).getTime();
                                    //     if(relation.duration == 1 && start_date <= today )
                                    //         resident.status = 1
                                    //     if(relation.duration == 2 && start_date <= today && end_date > today)
                                    //         resident.status = 1
                                            
                                    // })
                                    const resp = await this.createResident({
                                        user: {
                                            email,
                                            password,
                                            password_confirmation: password
                                            //password_confirmation: password_confirmation
                                        },
                                        ...resident
                                    });

                                    if (resp.data.user && resp.data.user.id) {
                                        await this.uploadAvatarIfNeeded(resp.data.user.id);
                                    }

                                    
                                    displaySuccess(resp);

                                    this.model.start_date = '';
                                    this.form.resetFields();
                                    if (!!afterValid) {
                                        afterValid(resp);
                                    } else {
                                        // this.$router.push({
                                        //     name: 'adminResidentsEdit',
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
                mixin.mixins = [PasswordValidatorMixin({required: false}), EmailCheckValidatorMixin(), residentTypeCheckValidatorMixin(), UploadUserAvatarMixin];

                mixin.methods = {
                    submit() {
                        return new Promise((resolve, reject) => {
                            this.form.validate(async valid => {
                                if (!valid) {
                                    resolve(false);
                                    return false;
                                }
                                this.loading.state = true;
//                                let {password_confirmation, ...params} = this.model;
                                let {...params} = this.model;

                                if (params.password === '') {
                                    params = _.omit(params, ['password'])
                                }

                                params.password_confirmation = params.password
                                this.old_model = this.model;
                                try {
                                    const resp = await this.updateResident(params);

                                    if (resp.data.user && resp.data.user.id) {                                        
                                        await this.uploadAvatarIfNeeded(resp.data.user.id);
                                    }
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
                                }
                            });
                        });
                    },

                    ...mixin.methods,
                    ...mapActions(['getResident', 'updateResident'])
                };

            case 'view':
                mixin.mixins = [PasswordValidatorMixin({required: false}), EmailCheckValidatorMixin(), residentTypeCheckValidatorMixin(), UploadUserAvatarMixin];
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['getResident'])
                }

                mixin.computed = {
                    ...mixin.computed
                };

                mixin.created = async function () {
                    // const {password, password_confirmation} = this.validationRules;
                    
                    // [...password, ...password_confirmation].forEach(rule => rule.required = false);

                    
                    this.titles = Object.entries(this.$constants.residents.title).map(([value, label]) => ({value: label, name: this.$t(`general.salutation_option.${label}`)}))
                    try {
                        this.loading.state = true;

                        const {building, unit, user, ...r} = await this.getResident({id: this.$route.params.id});
                        this.user = user;
                        
                        this.model = Object.assign({}, this.model, r);
                        
                        this.original_email = this.user.email;
                        //this.original_type = this.model.type;
                        this.model.email = user.email;
                        this.model.avatar = user.avatar;
                        if(this.model.nation)
                            this.model.nation = +this.model.nation

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