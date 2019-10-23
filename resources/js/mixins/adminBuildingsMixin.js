import {mapGetters, mapActions} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';
import ServicesTypes from 'mixins/methods/servicesTypes';
import { EventBus } from '../event-bus.js';

export default (config = {}) => {
    let mixin = {
        mixins: [ServicesTypes],
        props: {
            title: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                remoteLoading: false,
                assignmentTypes: ['managers'],
                assignmentType: 'managers',
                toAssignList: [],
                toAssign: '',
                toAssignProvider: '',
                toAssignProviderList: [],
                serviceCount: 0,
                statistics: {
                    raw: [{
                        icon: 'ti-user',
                        color: '#003171',
                        value: 0,
                        description: 'dashboard.residents.total_residents'
                    }, {
                        icon: 'ti-plus',
                        color: '#26A65B',
                        value: 0,
                        description: 'models.resident.status.active'
                    },{
                        icon: 'ti-plus',
                        color: '#26A65B',
                        value: 0,
                        description: 'models.resident.status.not_active'
                    }],
                    percentage: {
                        occupied_units: 0,
                        free_units: 0,
                    }
                },
                model: {
                    name: '',
                    description: '',
                    floor_nr: 1,
                    floor: [],
                    attic: false,
                    state_id: '',
                    city: '',
                    street: '',
                    house_num: '',
                    zip: '',
                    service_providers: []
                },
                validationRules: {
                    name: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.name')})
                    }],
                    // description: [{
                    //     required: true,
                    //     message: this.$t("models.building.validation.description.required")
                    // }],
                    floor_nr: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.building.floor_nr')})
                    }],
                    floor: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.building.floors')})
                    }],
                    state_id: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.state')})
                    }],
                    city: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.city')})
                    }],
                    street: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.street')})
                    }],
                    house_num: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.house_num')})
                    }],
                    zip: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.zip')})
                    }],
                },
                loading: {
                    state: false,
                    text: 'general.please_wait'
                },
                allServices: [],
                quarters: [],
                unitAutoCreate: false,
            };
        },
        methods: {
            ...mapActions(['getStates', 'getPropertyManagers','getQuarters','getUsers','assignBuildingProvider','getServices','assignServiceBuilding','unassignServiceBuilding']),
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
                        const buildingAssignee = await this.getBuildingAssignees({building_id: this.$route.params.id});                        
                        let exclude_ids = [];
                        if (this.assignmentType === 'managers') {
                            buildingAssignee.data.data.map(item => {
                                if(item.type === 'manager'){
                                    exclude_ids.push(item.edit_id);
                                }                                
                            })
                            resp = await this.getPropertyManagers({
                                get_all: true,
                                search,
                                exclude_ids
                            });
                        } else if(this.assignmentType === 'administrator'){
                            buildingAssignee.data.data.map(item => {
                                if(item.type === 'user'){                                    
                                    exclude_ids.push(item.edit_id);
                                }                                
                            })
                            resp = await this.getUsers({
                                get_all: true,
                                search,
                                exclude_ids,
                                role: 'administrator'
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
                    resp = await this.assignManagerToBuilding({
                        id: this.model.id,
                        toAssignId: this.toAssign   
                    });
                } else if (this.assignmentType === 'administrator') {
                    resp = await this.assignUsersToBuilding({
                        id: this.model.id,
                        toAssignId: this.toAssign
                    });
                }

                if (resp && resp.data) {             
                    displaySuccess(resp.data)                           
                    this.resetToAssignList();
                    this.$refs.assigneesList.fetch();                    
                }
            },
            async remoteSearchQuarters(search) {
                if (search === '') {
                    this.quarters = [];
                } else {
                    this.remoteLoading = true;

                    try {
                        const {data} = await this.getQuarters({get_all: true, search});

                        this.quarters = data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
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

                        const resp = await this.getServices({get_all: true, search});

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

                        const resp = await this.assignServiceBuilding({
                            id: this.toAssignProvider,
                            toAssignId: this.model.id
                        });

                        if (resp && resp.data && config.mode === 'edit') {                            
                            this.$refs.assignmentsProviderList.fetch(); 
                            this.resetToAssignProviderList();
                            this.serviceCount++;
                            displaySuccess(resp.data)                            
                        }

                        resolve(true);

                    } catch (e) {                        
                        displayError(e);
                        reject(false);
                    }
                })
            },
        },
        computed: {
            form() {
                return this.$refs.form;
            },
            ...mapGetters(['states'])
        }
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.methods = {
                    async submit(afterValid = false) {
                        const valid = await this.form.validate();
                        if (valid) {
                            this.loading.state = true;
                            try {
                                const {state_id, city, street, house_num, zip, ...restParams} = this.model;
                                const response = await this.createBuilding({
                                    address: {
                                        state_id,
                                        city,
                                        street,
                                        house_num,
                                        zip
                                    },
                                    ...restParams
                                });
                                displaySuccess(response);
                                this.form.resetFields();
                                if (!!afterValid) {
                                    afterValid(response);
                                } else {
                                    this.$router.push({
                                        name: 'adminBuildingsEdit',
                                        params: {id: response.data.id}
                                    })
                                }
                            } catch (err) {
                                displayError(err);
                            } finally {
                                this.loading.state = false;
                            }
                        }

                    },

                    ...mixin.methods,
                    ...mapActions(['createBuilding', 'createAddress'])
                };

                mixin.created = async function () {
                    await this.getStates();
                    // const {data} = await this.getServicesGroupedByCategory();
                    // this.allServices = data;
                };

                break;
            case 'edit':
                mixin.methods = {
                    submit() {
                        return new Promise((resolve, reject) => {
                            this.form.validate(async valid => {
                                if (!valid) {
                                    resolve(false);
                                    return false;
                                }
                                this.loading.state = true;
                                try {
                                    const {state_id, city, street, house_num, zip, ...restParams} = this.model;
                                    const data = await this.updateBuilding({
                                        address: {
                                            state_id,
                                            city,
                                            street,
                                            house_num,
                                            zip
                                        },
                                        ...restParams,
                                        service_providers: restParams.service_providers_ids
                                    });

                                    this.model.service_providers = data.data.service_providers;
                                    this.serviceCount = this.model.service_providers.length
                                    this.model.service_providers_ids = [];

                                    displaySuccess(data);
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
                    ...mapActions(['getBuilding', 'updateBuilding', 'updateAddress', 'getBuildingStatistics'])
                };

                mixin.created = async function () {
                    try {
                        this.loading.state = true;

                        await this.getStates();

                        // const {data} = await this.getServicesGroupedByCategory();
                        // this.allServices = data;

                        const {
                            address: {
                                state: {
                                    id: state_id
                                },
                                ...restAddress
                            },
                           
                            ...restData
                        } = await this.getBuilding({id: this.$route.params.id});
                        this.statistics.raw[0].value = restData.active_residents_count + restData.in_active_residents_count;
                        this.statistics.raw[1].value = restData.active_residents_count;
                        this.statistics.raw[2].value = restData.in_active_residents_count;

                        this.model = {state_id, ...restAddress, ...restData, service_providers_ids: []};
                        
                        this.serviceCount = this.model.service_providers.length
                        this.fileCount = this.model.media.length

                        
                        this.contractCount = this.model.contracts.length

                        this.model.residents = this.model.contracts.map(contract => contract.resident);

                        this.model.residents = this.model.residents.filter((item, index) => {
                            return this.model.residents.indexOf(item) === index
                        })
  

                        if (this.model.quarter) {
                            this.$set(this.model, 'quarter_id', this.model.quarter.id);
                            this.remoteSearchQuarters(`${this.model.quarter.name}`);
                        }

                        const {
                            data: {
                                free_units,
                                occupied_units,
                                total_residents,
                                total_units
                            }
                        } = await this.getBuildingStatistics({id: this.$route.params.id});


                        this.statistics.percentage.occupied_units = occupied_units;
                        this.statistics.percentage.free_units = free_units;
                    } catch (err) {
                        this.$router.replace({
                            name: 'adminBuildings'
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
