import {mapGetters, mapActions} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';
import { EventBus } from '../event-bus.js';

export default (config = {}) => {
    let mixin = {
        mixins: [],
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
                assignment_types: [],
                userAssignmentType: null,
                serviceAssignmentTypes: ['providers'],
                toAssignList: [],
                toAssign: '',
                toAssignProvider: '',
                toAssignProviderList: [],
                serviceCount: 0,
                types:[],
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
                    quarter_id: '',
                    name: '',
                    description: '',
                    floor_nr: 1,
                    floor: [],
                    under_floor: 0,
                    attic: false,
                    state_id: '',
                    city: '',
                    street: '',
                    house_num: '',
                    zip: '',
                    types: [],
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
                    under_floor: [{
                        pattern: /^[0-3]$/,
                        message: this.$t('validation.digits_between', {
                            attribute: this.$t('models.building.under_floor'),
                            min: 0,
                            max: 3,
                        })
                    }],
                    state_id: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.state')})
                    }],
                    types: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.building.type')})
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
                old_model: null,
            };
        },
        methods: {
            ...mapActions([
                'getStates', 
                'getQuarters'
            ]),
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
            async getTypes() {
                this.types = [];
                this.types = Object.entries(this.$constants.buildings.type).map(([value, label]) => ({value: +value, name: this.$t(`models.quarter.types.${label}`)}))

                // for(let item in this.$constants.buildings.type) {
                //     this.types.push({
                //         id: item,
                //         name: this.$t(`models.quarter.types.${this.$constants.buildings.type[item]}`),
                //     })
                // }
            },
        },
        computed: {
            form() {
                return this.$refs.form;
            },
            ...mapGetters(['states'])
        },
        watch: {
            "$i18n.locale": {
                immediate: true,
                handler(val) {
                    this.getTypes();
                }
            }
        },
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.methods = {
                    submit(afterValid = false) {
                        return new Promise(async (resolve, reject) => {
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
                                        // this.$router.push({
                                        //     name: 'adminBuildingsEdit',
                                        //     params: {id: response.data.id}
                                        // })
                                        resolve(response);
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

                    ...mixin.methods,
                    ...mapActions(['createBuilding', 'createAddress'])
                };

                mixin.created = async function () {
                    await this.getStates();
                };

                break;
            case 'edit':
                mixin.methods = {
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
                                    this.old_model = this.model;

                                    //this.model.service_providers = data.data.service_providers;
                                    //this.serviceCount = this.model.service_providers.length
                                    this.model.service_providers_ids = [];
                                    if(this.$refs.auditList){
                                        this.$refs.auditList.fetch();
                                    }
                                    displaySuccess(data);
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

                    ...mixin.methods,
                    ...mapActions(['getBuilding', 'updateBuilding', 'updateAddress', 'getBuildingStatistics'])
                };

                mixin.created = async function () {
                    try {
                        this.loading.state = true;

                        await this.getStates();
                        this.assignment_types = Object.entries(this.$constants.quarters.assignment_type).map(([value, label]) => ({value: +value, name: this.$t(`models.quarter.assignment_types.${label}`)}))
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
                        

                        //this.serviceCount = this.model.service_providers.length
                        this.fileCount = this.model.media.length
                        
                        this.relationCount = this.model.relations.length

                        this.model.residents = this.model.relations.map(relation => relation.resident);

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
