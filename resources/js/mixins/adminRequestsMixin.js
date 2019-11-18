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
                    qualification: null,
                    component: '',
                    keyword: '',
                    keywords: [],
                    property_managers: [],
                    media: [],
                    sub_category_id: null,
                    contract_id: ''
                },
                validationRules: {
                    category: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    sub_category: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    qualification: [{
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
                toAssignList: [],
                media: [],
                assignmentTypes: ['managers', 'services'],
                assignmentType: 'managers',
                toAssign: '',
                conversations: [],
                address: {},
                locations: [],
                rooms: [],
                capture_phases: [],
                showSubCategory: false,
                showPayer: false,
                showLocation: false,
                showCapturePhase: false,
                showRoom: false,
                showQualification: false,
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
                contracts: [],
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
            ...mapActions(['getResidents', 'getServices', 'uploadRequestMedia', 'deleteRequestMedia', 'getPropertyManagers', 'assignProvider', 'assignManager', 'getUsers', 'assignAdministrator','getAssignees']),
            async remoteSearchResidents(search) {
                if (search === '') {
                    this.residents = [];
                } else {
                    this.remoteLoading = true;

                    try {
                        const {data} = await this.getResidents({
                            get_all: true,
                            has_contract: true,
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
            async remoteSearchAssignees(search) {

                if (!this.$can(this.$permissions.assign.request)) {
                    return false;
                }

                if (search === '') {
                    this.resetToAssignList();
                } else {
                    this.remoteLoading = true;
                    
                    try {
                        let resp = [];
                        const respAssignee = await this.getAssignees({request_id: this.$route.params.id});                        
                        let exclude_ids = [];                                                
                        if (this.assignmentType === 'managers') {
                            respAssignee.data.data.map(item => {
                                if(item.type === 'manager'){
                                    exclude_ids.push(item.edit_id);
                                }                                
                            })
                            resp = await this.getPropertyManagers({
                                get_all: true,
                                search,
                                exclude_ids: exclude_ids.join(',')
                            });
                        } else if(this.assignmentType === 'administrator'){
                            respAssignee.data.data.map(item => {
                                if(item.type === 'user'){                                    
                                    exclude_ids.push(item.edit_id);
                                }                                
                            })
                            resp = await this.getUsers({
                                get_all: true,
                                search,
                                exclude_ids: exclude_ids.join(','),
                                role: 'administrator'
                            });
                        }
                        else if(this.assignmentType === 'services') {
                            respAssignee.data.data.map(item => {
                                if(item.type === 'provider'){
                                    exclude_ids.push(item.edit_id);
                                }                                
                            })
                            resp = await this.getServices({
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
            resetToAssignList() {
                this.toAssignList = [];
                this.toAssign = '';
            },
            async assignUser() {
                if (!this.toAssign || !this.model.id) {
                    return false;
                }
                let resp;

                if (this.assignmentType === 'managers') {
                    resp = await this.assignManager({
                        request: this.model.id,
                        toAssignId: this.toAssign
                    });
                } else if (this.assignmentType === 'administrator') {
                    resp = await this.assignAdministrator({
                        request: this.model.id,
                        toAssignId: this.toAssign
                    });
                }else {
                    resp = await this.assignProvider({
                        request: this.model.id,
                        toAssignId: this.toAssign
                    });
                }

                if (resp && resp.data) {
                    await this.fetchCurrentRequest();
                    this.toAssign = '';
                    this.$refs.assigneesList.fetch();
                    if(this.$refs.auditList){
                        this.$refs.auditList.fetch();
                    }
                    displaySuccess(resp.data)
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
                this.showQualification = p_category.qualification == 1 ? true : false;
                this.showComponent = p_category.component == 1 ? true : false;
                

                if(this.showSubCategory) {
                    this.sub_categories = p_category ? p_category.sub_categories : [];

                    if(!this.showQualification && this.model.sub_category_id != null) {
                        let sub_category = this.sub_categories.find(category => { return category.id == this.model.sub_category_id});
                        this.showQualification = sub_category.qualification == 1 ? true : false;
                        this.showCapturePhase =  sub_category.capture_phase == 1 ? true : false;
                        this.showComponent =  sub_category.component == 1 ? true : false;
                    }
                }

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
                this.showQualification = sub_category.qualification == 1 ? true : false;
                this.showCapturePhase = sub_category.capture_phase == 1 ? true : false;
                this.showComponent = sub_category.component == 1 ? true : false;
            },
            changeQualification() {
                this.showPayer = this.model.qualification == 5 ? true : false;
            },
            selectedCategoryHasQualification(categoryId) {
                if (!categoryId) {
                    return false;
                }

                const categoryArr = this.categories.filter((category) => {
                    return category.id === categoryId && category.qualification;
                });

                if (categoryArr.length) {
                    return true;
                }

                return false;
            },
            getLanguageI18n() {

                this.locations = Object.entries(this.$constants.requests.location).map(([value, label]) => ({value: +value, name: this.$t(`models.request.location.${label}`)}))
                this.rooms = Object.entries(this.$constants.requests.room).map(([value, label]) => ({value: +value, name: this.$t(`models.request.room.${label}`)}))
                this.capture_phases = Object.entries(this.$constants.requests.capture_phase).map(([value, label]) => ({value: +value, name: this.$t(`models.request.capture_phase.${label}`)}))
                this.qualifications = Object.entries(this.$constants.requests.qualification).map(([value, label]) => ({value: +value, name: this.$t(`models.request.qualification.${label}`)}))
                this.categories = this.$constants.requests.categories_data.tree

                if(this.model.category_id)
                {
                    let p_category = this.categories.find(category => {
                        return category.id === this.model.category_id;
                    });

                    this.sub_categories = p_category ? p_category.sub_categories : [];
                }

                this.resident = this.residents.find(resident => resident.id == this.model.resident_id)
                if(this.resident && this.resident.contracts) {
                    this.contracts = this.resident.contracts

                    this.contracts = this.contracts.map(contract => { 
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
                this.model.contract_id = null
                this.resident = this.residents.find(resident => resident.id == resident_id)
                // this.contracts = this.resident.contracts.filter( contract => contract.status == 1)
                this.contracts = this.resident.contracts

                this.contracts = this.contracts.map(contract => { 
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


                if(this.contracts.length == 1) {
                    this.model.contract_id = this.contracts[0].id
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

                    this.validationRules.contract_id = [{
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
                    ...mapActions(['getRequest', 'updateRequest', 'getResident', 'getRequestConversations', 'getAddress', 'getRequestTags',
                'createRequestTags', 'getTags', 'deleteRequestTag']),
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
                        this.showPayer = this.showQualification && resp.data.qualification == 5 ? true : false;
                        this.showCapturePhase =  resp.data.category.capture_phase == 1 || (resp.data.sub_category && resp.data.sub_category.capture_phase == 1) ? true : false;
                        this.showQualification =  resp.data.category.qualification == 1 || (resp.data.sub_category && resp.data.sub_category.qualification == 1) ? true : false;
                        this.showComponent =  resp.data.category.component == 1 || (resp.data.sub_category && resp.data.sub_category.component == 1) ? true : false;
                        this.showPayer = this.showQualification && resp.data.qualification == 5 ? true : false;
                        
                        const data = resp.data;

                        this.model = Object.assign({}, this.model, data);

                        this.$set(this.model, 'category_id', data.category.id);
                        if(data.sub_category)
                            this.$set(this.model, 'sub_category_id', data.sub_category.id);
                        this.$set(this.model, 'created_by', data.created_by);
                        this.$set(this.model, 'building', data.contract.building.name);
                        this.address = data.contract.address
                        //this.contracts = resp.data.resident.contracts.filter(item => item.status == 1)
                        this.model.contract_id = data.contract.id
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

                                    await this.fetchCurrentRequest();
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


