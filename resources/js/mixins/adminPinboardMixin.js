import {mapActions} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';
import Heading from 'components/Heading';
import Card from 'components/Card';
import uuid from "uuid/v1";
import UploadDocument from 'components/UploadDocument';
import Media from 'components/RequestMedia';
import RequestMedia from 'components/RequestMedia';

// TODO make a common mixin for pinboard and listings mixins(media upload at least)
export default (config = {}) => {
    let mixin = {
        components: {
            Heading,
            Card,
            UploadDocument,
            Media,
            RequestMedia
        },
        props: {
            title: {
                type: String
            }
        },
        data() {
            return {
                remoteLoading: false,
                model: {
                    content: '',
                    visibility: 1,
                    status: 1,
                    type: '',
                    sub_type: 1,
                    media: [],
                    published_at: '',
                    user_id: '',
                    announcement: '',
                    notify_email: false,
                    category: '',
                    execution_period: 2,
                    is_execution_time: false,
                    execution_start: null,
                    execution_end: null,
                    category_image: true,
                    building_ids: [],
                    quarter_ids: [],
                    provider_ids: [],
                },
                validationRules: {
                    content: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    type: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    visibility: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    status: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    published_at: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    execution_start: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    execution_end: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                },
                loading: {
                    state: false,
                    text: 'general.please_wait'
                },
                media: [],
                assignmentTypes: ['quarter', 'building'],
                assignmentType: 'quarter',
                toAssign: '',
                toAssignList: [],
                addedAssigmentList: [],
                addedProviderAssigmentList: [],
                toAssignProvider: '',
                toAssignProviderList: [],
                types: [],
                rolename: '',
                datePickerKey: 0,
                justBlurred: '',
                executionStartTime: '00:00:00',
                executionEndTime: '23:59:00',
                uploadOptions: {
                    drop: true,
                    multiple: true,
                    draggable: true,
                    hideUploadButton: true,
                    extensions: 'vnd.openxmlformats-officedocument.wordprocessingml.document,vnd.openxmlformats-officedocument.spreadsheetml.sheet,pdf,png,jpeg,jpg',
                    hideSelectFilesButton: false
                },
                pinboard_id: null,
                audit_id: null,
                onload_notify_email: null
            }
        },
        computed: {
            form() {
                return this.$refs.form;
            },
            mediaFiles() {
                return [...this.model.media, ...this.media];
            },
            pinboardConstants() {
                return this.$constants.pinboard
            }
        },
        mounted() {
        },
        methods: {
            ...mapActions(['uploadPinboardMedia', 'deletePinboardMedia', 'getBuildings', 'getQuarters', 'assignPinboardBuilding',
                'assignPinboardQuarter', 'getServices', 'assignPinboardProvider']),
            translateType(type) {
                return this.$t(`general.assignment_types.${type}`);
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
                        await this.uploadPinboardMedia({
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
                        message: this.$t('models.pinboard.media.removed')
                    });
                } else {
                    const resp = await this.deletePinboardMedia({
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
                const resp = await this.deletePinboardMedia({
                    id: this.model.id,
                    media_id: this.model.media[index].id
                });
                if(this.$refs.auditList){
                    this.$refs.auditList.fetch();
                }
                this.model.media.splice(index, 1)                 
                displaySuccess(resp);
            },
            async remoteSearchBuildings(search) {
                if (search === '') {
                    this.resetToAssignList();
                } else {
                    this.remoteLoading = true;

                    try {
                        let resp = [];
                        if (this.assignmentType == 'building') {
                            resp = await this.getBuildings({
                                get_all: true,
                                search,
                                exclude_ids: this.model.building_ids.join(),
                                exclude_quarter_ids: this.model.quarter_ids.join()
                            });
                        } else {
                            resp = await this.getQuarters({
                                get_all: true,
                                search,
                                exclude_ids: this.model.quarter_ids.join()
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
            async attachAddedAssigmentList(assigmentId) {
                let assigment = this.toAssignList.filter(n => n.id === assigmentId)[0];

                if (!!assigment) {
                    this.addedAssigmentList.push({
                        id: assigmentId,
                        name: assigment.name,
                        type: this.assignmentType
                    });
                }

                this.model.building_ids = this.addedAssigmentList.filter(item => item['type'] === 'building').map((building) => building.id);
                this.model.quarter_ids = this.addedAssigmentList.filter(item => item['type'] === 'quarter').map((quarter) => quarter.id);

                this.toAssign = '';
                this.toAssignList = [];
            },
            async attachAddedProviderAssigmentList(assigmentId) {
                let assigment = this.toAssignProviderList.filter(n => n.id === assigmentId)[0];

                if (!!assigment) {
                    this.addedProviderAssigmentList.push({
                        id: assigmentId,
                        name: assigment.name,
                        type: 'providers'
                    });
                }

                this.model.provider_ids = this.addedProviderAssigmentList.filter(item => item['type'] === 'providers').map((provider) => provider.id);

                this.toAssignProvider = '';
                this.toAssignProviderList = [];
            },

            attachBuilding() {
                return new Promise(async (resolve, reject) => {
                    if (!this.toAssign || (!this.model.id && config.mode === 'edit')) {
                        return false;
                    }

                    try {

                        let resp;

                        if (this.assignmentType === 'building') {
                            resp = await this.assignPinboardBuilding({
                                id: this.model.id,
                                toAssignId: this.toAssign
                            });
                            this.model.building_ids.push(this.toAssign);
                        } else {
                            resp = await this.assignPinboardQuarter({
                                id: this.model.id,
                                toAssignId: this.toAssign
                            });
                            this.model.quarter_ids.push(this.toAssign);
                        }

                        if (resp && resp.data && config.mode === 'edit') {
                            this.$refs.assignmentsList.fetch();
                            if(this.$refs.auditList){
                                this.$refs.auditList.fetch();
                            }
                            this.toAssign = '';                            
                            displaySuccess(resp)
                        }

                        resolve(true);

                    } catch (e) {
                        if (e.response && !e.response.data.success) {
                            displayError(resp)
                        }

                        reject(false);
                    }
                })
            },
            resetToAssignList() {
                this.toAssignList = [];
                this.toAssign = '';
            },
            resetToAssignProviderList() {
                this.toAssignProviderList = [];
                this.toAssignProvider = '';
            },
            async remoteSearchProviders(search) {
                if (search === '') {
                    this.resetToAssignProviderList();
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getServices({
                            get_all: true,
                            search
                        });

                        resp.data = resp.data.filter((provider) => {
                            return !this.model.provider_ids.includes(provider.id)
                        });

                        this.toAssignProviderList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            attachProvider() {
                return new Promise(async (resolve, reject) => {
                    if (!this.toAssignProvider || (!this.model.id && config.mode === 'edit')) {
                        reject(false);
                        return false;
                    }

                    try {

                        const resp = await this.assignPinboardProvider({
                            id: this.model.id,
                            toAssignId: this.toAssignProvider
                        });

                        this.model.provider_ids.push(this.toAssignProvider);

                        if (resp && resp.data && config.mode === 'edit') {
                            this.$refs.assignmentsProviderList.fetch();
                            if(this.$refs.auditList){
                                this.$refs.auditList.fetch();
                            }
                            this.toAssignProvider = '';
                            displaySuccess(resp)
                        }

                        resolve(true);

                    } catch (e) {                        
                        displayError(e);
                        reject(false);
                    }
                })
            },
            resetExecutionTime() {
                this.model.execution_start
                    ? this.model.execution_start = this.model.execution_start.split(' ')[0] + ' 00:00:00'
                    : '';
                this.model.execution_end
                    ? this.model.execution_end = this.model.execution_end.split(' ')[0] + ' 00:00:00'
                    : '';
            },
            executionPeriodChange() {
                this.resetExecutionDateTime();
                this.model.execution_end = null;
            },
            isExecutionTimeChange() {
                if (this.model.execution_period == 1) {
                    this.model.is_execution_time
                        ? this.setExecutionDateTime()
                        : (() => {
                            this.resetExecutionDateTime();
                            this.model.execution_end = null;
                        })()
                } else if (this.model.execution_period == 2) {
                    this.model.is_execution_time
                        ? ''
                        : this.resetExecutionTime();
                }
                this.reinitDatePickers();
            },
            resetExecutionDateTime() {
                this.executionStartTime = '00:00:00';
                this.executionEndTime = '23:59:00';
                this.setExecutionDateTime();
            },
            setExecutionDateTime() {
                this.model.execution_end = this.model.execution_start;

                this.executionStartTime = this.executionStartTime || '00:00:00';
                this.executionEndTime = this.executionEndTime || '23:59:00';

                this.model.execution_start
                    ? this.model.execution_start = this.model.execution_start.split(' ')[0] + ' ' + this.executionStartTime
                    : '';
                this.model.execution_end
                    ? this.model.execution_end = this.model.execution_start.split(' ')[0] + ' ' + this.executionEndTime
                    : '';
            },
            setJustBlurred(elementRef) {
                this.justBlurred = elementRef;
                setTimeout(() => {
                    this.justBlurred = ''
                }, 10);
            },
            reinitDatePickers() {
                ++this.datePickerKey;

                if(this.justBlurred !== '') {
                    this.$nextTick(() => this.$refs[this.justBlurred].focus());
                }
            },
            changeSubType(val) {
                if(val == 3 && this.model.category == '')
                this.model.category = 1;
            }
        }
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['createPinboard']),
                    submit(afterValid = false) {
                        return new Promise(async (resolve, reject) => {
                            const valid = await this.form.validate();
                            
                            if (!valid) {
                                return false;
                            }

                            this.loading.state = true;

                            try {
                                this.model.announcement = this.model.type == 3 ? true : false;
                                
                                this.model.media = this.media.map(item => item.file.src)
                                const resp = await this.createPinboard(this.model);

                                if (resp && resp.data.id) {
                                    this.$set(this.model, 'id', resp.data.id);
                                    //await this.uploadNewMedia(resp.data.id, resp.data.audit_id);
                                    if (this.toAssign) {
                                        await this.attachBuilding();
                                    }

                                    if (this.toAssignProvider) {
                                        await this.attachProvider();
                                    }
                                }
                                this.form.resetFields();

                                this.media = [];
                                displaySuccess(resp);
                                if (!!afterValid) {
                                    afterValid(resp);
                                } else {
                                    // this.$router.push({
                                    //     name: 'adminServicesEdit',
                                    //     params: {id: resp.data.id}
                                    // })
                                    resolve(resp);
                                }
                                return resp;
                            } catch (err) {
                                displayError(err);
                                reject(resp);
                            } finally {
                                this.loading.state = false;
                            }
                        });
                    },

                };

                break;
            case 'edit':
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['getPinboard', 'updatePinboard']),
                    submit() {
                        return new Promise((resolve, reject) => {
                            this.form.validate(async valid => {
                                if (!valid) {
                                    resolve(false);
                                    return false;
                                }

                                try {
                                    this.loading.state = true;
                                    this.model.announcement = this.model.type == 3 ? true : false;

                                    await this.uploadNewMedia(this.model.id);

                                    const resp = await this.updatePinboard(this.model);
                                    this.onload_notify_email = this.model.notify_email;

                                    this.model = Object.assign({}, this.model, resp.data);
                                    this.media = [];

                                    displaySuccess(resp);
                                    if(this.$refs.auditList){
                                        this.$refs.auditList.fetch();
                                    }
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
                    async fetchCurrentPinboard() {
                        const {
                            ...restData
                        } = await this.getPinboard({id: this.$route.params.id});

                        this.model = {
                            execution_period: this.model.execution_period,
                            is_execution_time: this.model.is_execution_time,
                            execution_start: this.model.execution_start,
                            execution_end: this.model.execution_end,

                            ...restData
                        };

                        this.model.building_ids = this.model.buildings.map((building) => building.id);
                        this.model.quarter_ids = this.model.quarters.map((quarter) => quarter.id);
                        this.model.provider_ids = this.model.providers.map((provider) => provider.id);

                        this.model.execution_start ? this.executionStartTime = this.model.execution_start.split(' ')[1] : '';
                        this.model.execution_end ? this.executionEndTime = this.model.execution_end.split(' ')[1] : '';

                        this.onload_notify_email = this.model.notify_email;

                        return this.model;
                    }
                };

                mixin.created = async function () {
                    try {
                        this.loading.state = true;
                        await this.fetchCurrentPinboard();
                        if (this.model.type === 2) {
                            this.$router.replace({
                                name: 'adminPinboard'
                            });
                        }
                        this.resetExecutionTime();
                    } catch (err) {
                        this.$router.replace({
                            name: 'adminPinboard'
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
