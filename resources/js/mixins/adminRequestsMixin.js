import uuid from 'uuid/v1';
import {mapActions} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';
import UploadDocument from 'components/UploadDocument';
import RequestMedia from 'components/RequestMedia';
import {subDays,compareAsc} from 'date-fns'

export default (config = {}) => {
    let mixin = {
        mixins: [],
        components: {
            UploadDocument,
            RequestMedia
        },
        props: {
            title: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                dueDatePickerOptions: {
                    disabledDate(time) {
                        // return time.getTime() < Date.now();
                        return (compareAsc(time.getTime(), subDays(Date.now(),1)) == -1)
                    },
                },
                model: {
                    title: '',
                    category: '',
                    priority: '',
                    visibility: '',
                    provider_ids: [],
                    building: '',
                    created_by: '',
                    location: '',
                    room: '',
                    capture_phase: '',
                    action: null,
                    cost_impact: null,
                    qualification_category: null,
                    percentage: '',
                    component: '',
                    keyword: '',
                    keywords: [],
                    property_managers: [],
                    media: [],
                    sub_category_id: null,
                    relation_id: '',
                    creator: { name:'' },
                    created_at: '',
                    name: '',
                    unit_id: '',
                    quarter_id: '',
                },
                old_model: {},
                validationRules: {
                    category: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    sub_category: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    // priority: [{
                    //     required: true,
                    //     message: this.$t('validation.general.required')
                    // }],
                    // internal_priority: [{
                    //     required: true,
                    //     message: this.$t('validation.general.required')
                    // }],
                    status: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    visibility: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    due_date: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    title: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    description: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                },
                loading: {
                    state: false,
                    text: 'general.please_wait'
                },
                remoteLoading: false,
                categories: [],
                sub_categories: [],
                residents: [],
                media: [],
                toAssign: [],
                toAssignList: [],
                conversations: [],
                address: {},
                locations: [],
                rooms: [],
                capture_phases: [],
                showSubCategory: false,
                showPayer: false,
                showLocation: false,
                showCapturePhase: false,
                showQualification: false,
                showRoom: false,
                showAction: false,
                showCostImpact: false,
                showPercent: false,
                showComponent: false,
                createTag: false,
                editTag: false,
                tags: [],
                alltags: [],
                persons: [],
                uploadOptions: {
                    drop: true,
                    multiple: true,
                    draggable: true,
                    hideUploadButton: true,
                    extensions: 'vnd.openxmlformats-officedocument.wordprocessingml.document,vnd.openxmlformats-officedocument.spreadsheetml.sheet,pdf,png,jpeg,jpg',
                    hideSelectFilesButton: false
                },
                request_id: null,
                audit_id: null,
                relations: [],
            };
        },
        computed: {
            form() {
                return this.$refs.form;
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
        methods: {
            ...mapActions([
                'getResidents', 
                'getQuarters',
                'getUnits',
                'getServices', 
                'uploadRequestMedia', 
                'deleteRequestMedia', 
                'getPropertyManagers', 
                'getUsers', 
                'getAssignees']),
            async remoteSearchResidents(search) {
                if (search === '') {
                    this.residents = [];
                } else {
                    this.remoteLoading = true;

                    try {
                        const {data} = await this.getResidents({
                            get_all: true,
                            has_relation: true,
                            search
                        });
                        
                        this.residents = data;
                        this.residents.forEach(t => t.name = `${t.first_name} ${t.last_name}`);
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            async remoteSearchPersons(search) {

                if (search === '') {
                    this.persons = [];
                } else {
                    this.remoteLoading = true;
                    let exclude_ids = []; //@TODO : get sent reminder ids and show them , check sent_reminder_user_ids variable
                    try {
                        const {data} = await this.getUsers({
                            get_all: true,
                            search,
                            exclude_ids: exclude_ids.join(','),
                            roles: ['manager', 'administrator']
                        });

                        this.persons = data
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            uploadFiles(file) {
                const allowedFiles = [
                    'jpeg', 'png'
                ];
                const extension = file.raw.type.split('/');
                if (extension[1] && allowedFiles.includes(extension[1])) {
                    const url = `data:${file.raw.type};base64,${file.src}`;
                    this.media.push({
                        url,
                        id: uuid()
                    });
                } else {
                    displayError({
                        success: false,
                        message: this.$t('general.errors.files_extension_images')
                    });
                }
            },
            async uploadNewMedia(id, audit_id) {
                if (this.media.length) {
                    for (let i = 0; i < this.media.length; i++) {
                        const image = this.media[i];
                        await this.uploadRequestMedia({
                            id,
                            media: image.file.src,
                            merge_in_audit : audit_id
                        });
                    }
                }
            },
            async deleteMedia(image) {
                if (!image.model_id) {
                    this.media = this.media.filter((files) => {
                        return files.id !== image.id;
                    });
                    displaySuccess({
                        success: true,
                        message: this.$t('general.actions.deleted')
                    });
                } else {
                    const resp = await this.deleteRequestMedia({
                        id: image.model_id,
                        media_id: image.id
                    });
                    this.model.media = this.model.media.filter((files) => {
                        return files.id !== image.id;
                    });
                    displaySuccess(resp);
                }
            },
            async deleteMediaByIndex(index) {
                const resp = await this.deleteRequestMedia({
                    id: this.model.id,
                    media_id: this.model.media[index].id
                });

                this.model.media.splice(index, 1)
                if(this.$refs.auditList){
                    this.$refs.auditList.fetch();
                }
                displaySuccess(resp);
            },
            changeCategory() {
                
                let children = this.$constants.requests.categories_data.parent_child[this.model.category_id]
                if(children.length == 0)
                    this.model.sub_category_id = null;
                this.showSubCategory = children.length > 0 ? true : false;
                let p_category = this.categories.find(category => { return category.id == this.model.category_id});

                this.model.category = p_category
                
                this.showCapturePhase =  p_category.capture_phase == 1 ? true : false;
                this.showAction = p_category.action == 1 ? true : false;
                this.showCostImpact = p_category.cost_impact == 1 ? true : false;
                this.showComponent = p_category.component == 1 ? true : false;
                this.showQualification = p_category.qualification_category == 1 ? true : false;
                

                if(this.showSubCategory) {
                    this.sub_categories = p_category ? p_category.sub_categories : [];

                    if(this.model.sub_category_id != null) {
                        let sub_category = this.sub_categories.find(category => { return category.id == this.model.sub_category_id});
                        this.showRoom = sub_category.room == 1 ? true : false;
                        this.showLocation = sub_category.location == 1 ? true : false;
                    }
                }
                this.showPercent = this.showCostImpact && this.model.cost_impact == 3 ? true : false;
            },
            changeSubCategory() {
                const sub_category = this.sub_categories.find(category => {
                    return category.id == this.model.sub_category_id;
                });
                
                this.model.sub_category = sub_category

                this.model.room = null;
                this.model.location = null;
                this.showRoom = sub_category.room == 1 ? true : false;
                this.showLocation = sub_category.location == 1 ? true : false;
            },
            changeCostImpact() {
                this.showPercent = this.showCostImpact && this.model.cost_impact == 3 ? true : false;
            },
            getLanguageI18n() {

                this.locations = Object.entries(this.$constants.requests.location).map(([value, label]) => ({value: +value, name: this.$t(`models.request.location.${label}`)}))
                this.rooms = Object.entries(this.$constants.requests.room).map(([value, label]) => ({value: +value, name: this.$t(`models.request.room.${label}`)}))
                this.capture_phases = Object.entries(this.$constants.requests.capture_phase).map(([value, label]) => ({value: +value, name: this.$t(`models.request.capture_phase.${label}`)}))
                this.actions = Object.entries(this.$constants.requests.action).map(([value, label]) => ({value: +value, name: this.$t(`models.request.action.${label}`)}))
                this.cost_impacts = Object.entries(this.$constants.requests.cost_impact).map(([value, label]) => ({value: +value, name: this.$t(`models.request.cost_impact.${label}`)}))
                this.qualification_categories = Object.entries(this.$constants.requests.qualification_category).map(([value, label]) => ({value: +value, name: this.$t(`models.request.qualification_category.${label}`)}))
                
                this.categories = this.$constants.requests.categories_data.tree

                if(this.model.category_id)
                {
                    let p_category = this.categories.find(category => {
                        return category.id === this.model.category_id;
                    });

                    this.sub_categories = p_category ? p_category.sub_categories : [];
                }

                this.resident = this.residents.find(resident => resident.id == this.model.resident_id)
                if(this.resident && this.resident.relations) {
                    this.relations = this.resident.relations

                    this.relations = this.relations.map(relation => { 
                        relation.building_room_floor_unit = this.getSelectOptionOfRelation(relation)
                        return relation
                    });
                }
            },
            async deleteTag(tag) {
                
                if(config.mode == 'edit') {
                    const deleteTag = this.alltags.find((item) => {
                        return item.name == tag;
                    });

                    if(deleteTag != null) {
                        const resp = await this.deleteRequestTag({
                            id: this.$route.params.id,
                            tag_id: deleteTag.id
                        });
                        
                    }

                    this.tags = this.tags.filter(item => {
                        return item.name != tag;
                    });
                }
            },
            changeTags(tags) {
                if(tags.length)
                {
                    let addedTag = tags[ tags.length - 1];

                    // check tags entered to see if it's already entered before
                    let existingFlag = false;
                    tags.forEach((tag,index) => {
                        if(index == tags.length - 1)
                            return;
                        if( tag.toLowerCase() == addedTag.toLowerCase() )
                        {
                            existingFlag = true;
                        }
                    })

                    if(existingFlag) {
                        tags.splice(tags.length - 1, 1 )
                        return;
                    }

                    // check alltags to see if there's a match
                    let matchTag = null

                    for(let i = 0; i < this.alltags.length; i++) {
                        if( this.alltags[i].name.toLowerCase() == addedTag.toLowerCase() ) {
                            matchTag = this.alltags[i].name;
                            break;
                        }
                    }

                    if(matchTag) {
                        tags.splice(tags.length - 1, 1 )
                        tags.push(matchTag)
                    }
                }
            },
            changeResident( resident_id ) {
                this.model.relation_id = null
                this.resident = this.residents.find(resident => resident.id == resident_id)
                // this.relations = this.resident.relations.filter( relation => relation.status == 1)
                this.relations = this.resident.relations

                this.relations = this.relations.map(relation => { 
                    relation.building_room_floor_unit = this.getSelectOptionOfRelation(relation)
                    return relation
                });


                if(this.relations.length == 1) {
                    this.model.relation_id = this.relations[0].id
                }
            }
        }
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['createRequest', 'createRequestTags', 'getTags']),
                    async saveRequest() {
                        // this.model.category = this.model.category_id
                        // this.model.sub_category = this.model.sub_category_id 
                        this.model.media = this.media.map(item => item.file.src)
                        const resp = await this.createRequest(this.model);
                        
                        let requestId = resp.data.id;

                        await this.createRequestTags({
                            id: requestId,
                            tags: this.model.keywords
                        });


                        // @TODO there is a upload feature in media uploader and it's using newRequests module. 
                        // And there is a uploader in requests module. Need to check and remove one uploader

                        // if (resp && resp.data) {                            
                        //     if (this.media.length) {
                        //     // TODO - make await for this   
                        //         this.request_id = resp.data.id;            
                        //         this.audit_id = resp.data.audit_id;
                        //         this.$refs.media.startUploading();
                        //     }
                        // }


                        let audit_id = resp.data.audit_id;
                        //await this.uploadNewMedia(resp.data.id, audit_id);
    
                        this.media = [];

                        this.form.resetFields();

                        return resp;

                    },
                    submit(afterValid = false) {
                        return new Promise(async (resolve, reject) => {
                            const valid = await this.form.validate();
                            if (valid) {
                                this.loading.state = true;
                                try {
                                    const resp = await this.saveRequest();
                                    
                                    displaySuccess(resp);

                                    this.form.resetFields();
                                    if (!!afterValid) {
                                        afterValid(resp);
                                    } else {
                                        // this.$router.push({
                                        //     name: 'adminRequestsEdit',
                                        //     params: {id: resp.data.id}
                                        // })
                                        resolve(resp)
                                    }
                                    
                                } catch (err) {
                                    displayError(err);
                                } finally {
                                    this.loading.state = false;
                                }
                            }
                        });
                    },
                };

                mixin.created = async function () {
                    this.loading.state = true;

                    this.validationRules.resident_id = [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }];

                    this.validationRules.relation_id = [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }];
                    
                    this.getLanguageI18n();

                    const tagsResp = await this.getTags({get_all: true, search: ''});

                    if(tagsResp.success == true) 
                    {
                        this.alltags = tagsResp.data;
                    }

                    this.loading.state = false;
                };

                break;
            case 'edit':
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions([
                        'getRequest', 
                        'updateRequest', 
                        'getResident', 
                        'getRequestConversations', 
                        'getAddress', 
                        'getAllAdminsForRequest',
                        'assignUsersToRequest',
                        'getRequestTags',
                        'createRequestTags', 
                        'getTags', 
                        'deleteRequestTag'
                    ]),
                    resetToAssignList() {
                        this.toAssignList = [];
                        this.toAssign = [];
                    },
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
                        
        
                        resp = await this.assignUsersToRequest({
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
                
                        if (search === '') {
                            this.toAssignList = [];
                        } else {
                            this.remoteLoading = true;
                            
                            try {
                                const resp = await this.getAllAdminsForRequest({request_id: this.$route.params.id, is_get_function: true, search})
                                this.toAssignList = resp;
                            } catch (err) {
                                displayError(err);
                            } finally {
                                this.remoteLoading = false;
                            }
                        }
                    },
                    async fetchCurrentRequest() {
                        
                        const resp = await this.getRequest({id: this.$route.params.id});

                        if(resp) {
                            this.model.property_managers = resp.data.property_managers.map((manager) => {
                                manager.name = `${manager.first_name} ${manager.last_name}`;
                                return manager
                            });
                            if(resp.data.hasOwnProperty('reminder_user_ids') && resp.data.reminder_user_ids !== null && resp.data.reminder_user_ids.length) {
                                const {data} = await this.getUsers({get_all: true,roles: ['manager', 'administrator']});    
                                this.persons = data
                            }
                        }

                        this.showSubCategory = resp.data.sub_category ? true : false;
                        this.showLocation = resp.data.sub_category && resp.data.sub_category.location == 1 ? true : false;
                        this.showRoom = resp.data.sub_category && resp.data.sub_category.room == 1 ? true : false;

                        this.showAction = resp.data.category.action == 1 ? true : false;
                        this.showCapturePhase =  resp.data.category.capture_phase == 1 ? true : false;
                        this.showComponent =  resp.data.category.component == 1 ? true : false;
                        this.showCostImpact = resp.data.category.cost_impact == 1 ? true : false;
                        this.showPercent = this.showCostImpact && resp.data.cost_impact == 3 ? true : false;
                        this.showQualification = resp.data.category.qualification_category == 1 ? true : false;
                        
                        
                        //this.showComponent =  resp.data.category.component == 1 || (resp.data.sub_category && resp.data.sub_category.component == 1) ? true : false;
                        
                        const data = resp.data;

                        this.model = Object.assign({}, this.model, data);
                        this.model.name = `${this.model.resident.first_name} ${this.model.resident.last_name}`;
                        this.model.unit_id = this.model.resident.relations.length>0?this.model.resident.relations[0].unit_id:'';
                        this.model.quarter_id = this.model.resident.relations.length>0?this.model.resident.relations[0].quarter_id:'';
                        if(this.model.quarter_id === 0)
                            this.model.quarter_id = ''
                        if(this.model.unit_id === 0)
                            this.model.unit_id = ''

                        this.$set(this.model, 'category_id', data.category.id);
                        if(data.sub_category)
                            this.$set(this.model, 'sub_category_id', data.sub_category.id);
                        this.$set(this.model, 'created_by', data.created_by);

                        //@TODO : check relation.building after api is fixed
                        //this.$set(this.model, 'building', data.relation.building.address.street + ' ' + data.relation.building.address.house_num);
                        this.address = data.relation.address
                        //this.relations = resp.data.resident.relations.filter(item => item.status == 1)
                        this.model.relation_id = data.relation.id
                        await this.getConversations();
                        
                        if (data.resident) {
                            this.model.resident_id = data.resident.id;
                        }
                    },
                    submit() {
                        return new Promise((resolve, reject) => {
                            
                            this.form.validate(async valid => {
                                if (!valid) {
                                    resolve(false);
                                    return false;
                                }

                                this.loading.state = true;
                                let {service_providers, property_managers, ...params} = this.model;
                                
                                // params.category = this.model.category_id
                                // params.sub_category = this.model.sub_category_id

                                let existingsKeys = [];
                                let newTags = [];
                                
                                this.model.keywords.forEach(keyword => {
                                    let tagObj = this.alltags.find((item) => {
                                        return item.name == keyword;
                                    });
                                    
                                    if ( tagObj != null ) {
                                        existingsKeys.push(tagObj.id);
                                    }
                                    else {
                                        newTags.push(keyword);
                                    }
                                })
                                this.old_model = this.model;
                                const requestTags = await this.createRequestTags({
                                    id: this.$route.params.id,
                                    tag_ids: existingsKeys,
                                    tags: newTags
                                });

                                this.tags = []
                                requestTags.data.tags.forEach(item => {
                                    this.tags.push(item)
                                    if(this.alltags.findIndex((el) => {return el.id == item.id}) == -1)
                                    {
                                        this.alltags.push({ id: item.id, name: item.name });
                                    }
                                        
                                })

                                try {
                                    params.media = [...params.media, ...this.media.map(item => item.file.src)]
                                    
                                    const resp = await this.updateRequest(params);

                                    await this.uploadNewMedia(params.id, null);
                                    
                                    //this.model.media = [...this.model.media, ...this.media]
                                    this.media = [];

                                    //await this.fetchCurrentRequest();
                                    //this.$set(this.model, 'service_providers', resp.data.service_providers);
                                    //this.$set(this.model, 'media', resp.data.media);
                                    //this.$set(this.model, 'property_managers', resp.data.property_managers);
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
                    async getConversations() {
                        const resp = await this.getRequestConversations({
                            id: this.model.id
                        });

                        if (resp.data) {
                            this.conversations = resp.data;
                        }
                    },
                };

                mixin.created = async function () {
                    this.loading.state = true;

                    const tagsResp = await this.getTags({get_all: true, search: ''});

                    if(tagsResp.success == true) 
                    {
                        this.alltags = tagsResp.data;
                    }

                    const tags = await this.getRequestTags({id: this.$route.params.id});
                    
                    this.tags = tags.data.data.map(item => ({
                        id: item.id,
                        name: item.name
                    }));

                    tags.data.data.map(item => {
                        this.model.keywords.push(item.name);
                    })

                    await this.fetchCurrentRequest();
                    this.getLanguageI18n();

                    this.loading.state = false;
                };

                break;
        }
    }


    return mixin;
};


