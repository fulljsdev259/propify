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
                assignmentTypes: ['managers', 'administrator'],
                assignmentType: 'managers',
                toAssignList: [],
                toAssign: '',
                statistics: {
                    raw: [{
                        icon: 'ti-user',
                        color: '#003171',
                        value: 0,
                        description: 'dashboard.tenants.total_tenants'
                    }, {
                        icon: 'ti-plus',
                        color: '#26A65B',
                        value: 0,
                        description: 'models.tenant.status.active'
                    },{
                        icon: 'ti-plus',
                        color: '#26A65B',
                        value: 0,
                        description: 'models.tenant.status.not_active'
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
                        message: this.$t("models.building.validation.name.required")
                    }],
                    // description: [{
                    //     required: true,
                    //     message: this.$t("models.building.validation.description.required")
                    // }],
                    floor_nr: [{
                        required: true,
                        message: this.$t("models.building.validation.floor_nr.required")
                    }],
                    floor: [{
                        required: true,
                        message: this.$t("models.unit.validation.floor.required")
                    }],
                    state_id: [{
                        required: true,
                        message: this.$t('models.address.validation.state.required')
                    }],
                    city: [{
                        required: true,
                        message: this.$t('models.address.validation.city.required')
                    }],
                    street: [{
                        required: true,
                        message: this.$t('models.address.validation.street.required')
                    }],
                    house_num: [{
                        required: true,
                        message: this.$t('models.address.validation.house_num.required')
                    }],
                    zip: [{
                        required: true,
                        message: this.$t('models.address.validation.zip.required')
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
            ...mapActions(['getStates', 'getPropertyManagers','getServicesGroupedByCategory', 'getQuarters','getUsers']),
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
                                        params: {id: resp.data.id}
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
                    const {data} = await this.getServicesGroupedByCategory();
                    this.allServices = data;
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
                                    EventBus.$emit('service-get-counted', this.model.service_providers.length);
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

                        const {data} = await this.getServicesGroupedByCategory();
                        this.allServices = data;

                        const {
                            address: {
                                state: {
                                    id: state_id
                                },
                                ...restAddress
                            },
                           
                            ...restData
                        } = await this.getBuilding({id: this.$route.params.id});
                        this.statistics.raw[0].value = restData.active_tenants_count + restData.in_active_tenants_count;
                        this.statistics.raw[1].value = restData.active_tenants_count;
                        this.statistics.raw[2].value = restData.in_active_tenants_count;                        

                        this.model = {state_id, ...restAddress, ...restData, service_providers_ids: []};
                        EventBus.$emit('service-get-counted', this.model.service_providers.length);
                        EventBus.$emit('file-get-counted', this.model.media.length);                        
                        EventBus.$emit('tenant-get-counted', this.model.tenants.length);

                        if (this.model.quarter) {
                            this.$set(this.model, 'quarter_id', this.model.quarter.id);
                            this.remoteSearchQuarters(`${this.model.quarter.name}`);
                        }

                        const {
                            data: {
                                free_units,
                                occupied_units,
                                total_tenants,
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
