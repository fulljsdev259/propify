import {mapGetters, mapActions} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';
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
                toAssignList: [],
                buildings: [],
                buildingId: '',
                buildingName: '',
                model: {
                    resident_id: '',
                    name: '',
                    type: null,
                    room_no: '',
                    monthly_rent_net: '',
                    monthly_maintenance: '',
                    monthly_rent_gross: '',
                    floor: '',
                    sq_meter: '',
                    attic: false,
                    building_id: this.$route.params.id,
                    selected_resident: '',
                    residents: [],
                    media: [],
                    contracts: [],
                },
                validationRules: {
                    resident_id: [{
                        required: false,
                        message: this.$t('validation.required',{attribute: this.$t('general.resident')})
                    }],
                    name: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.name')})
                    }],
                    type: [{
                        required: true
                    }],
                    room_no: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.unit.room_no')})
                    }],
                    monthly_rent_net: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.gross_rent')})
                    }],
                    monthly_maintenance: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.maintenance')})
                    }],
                    floor: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.unit.floor')})
                    }, {
                        validator: this.validateFloor
                    }],
                    building_id: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.unit.building')})
                    }]
                },
                loading: {
                    state: false,
                    text: 'general.please_wait'
                },
                requestColumns: [{
                    prop: 'category.name',
                    label: 'models.request.category'
                }],
                requestActions: [{
                    width: '180px',
                    buttons: [{
                        title: 'general.actions.edit',
                        type: 'primary',
                        onClick: this.requestEditView
                    }]
                }],
                toAssign: '',
                addedAssigmentList: [],
                media: [],
                
            }
        },
        methods: {
            ...mapActions(['getResidents', 'getBuildings']),
            requestEditView(row) {
                return this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id: row.id
                    }
                });
            },
            async remoteSearchBuildings(search) {
                if (search === '') {
                    this.buildings = [];
                } else {
                    this.remoteLoading = true;

                    try {
                        const {data} = await this.getBuildings({get_all: true, search});

                        this.buildings = data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            async remoteSearchResidents(search) {
                if (search === '') {
                    this.toAssignList = [];
                } else {
                    this.remoteLoading = true;

                    try {
                        const {data} = await this.getResidents({get_all: true, search});

                        this.toAssignList = data;
                        this.toAssignList.forEach(t => t.name = `${t.first_name} ${t.last_name}`);
                        this.toAssignList = this.toAssignList.filter((resident) => {
                            return this.addedAssigmentList.filter(obj => obj.id === resident.id).length === 0;
                        });
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            async assignResident(newResident) {
                if (!this.toAssign || !this.model.id) {
                    return false;
                }
                let resp; 
                try {
                    resp = await axios.post(`units/`+ this.model.id + `/assignees/` + this.toAssign);
                    this.addedAssigmentList.push(newResident);
                    this.addedAssigmentList.map((user) => {
                        if (user.status == 1) {
                            user.statusString = 'Active';
                        } else {
                            user.statusString = 'Not Active';
                        }
                        user.name = user.first_name + " " + user.last_name;
                        return user;
                    });
                } catch {
                    console.log(e);
                }

                if (resp && resp.data) {
                    this.toAssign = '';
                    this.toAssignList = [];
                    this.$refs.assigneesList.fetch();
                    displaySuccess(resp.data)
                }
            },
            notifyUnassignment(resident) {
                this.$confirm(this.$t(`general.swal.confirm_change.title`), this.$t('general.swal.confirm_change.warning'), {
                    confirmButtonText: this.$t(`general.swal.confirm_change.confirm_btn_text`),
                    cancelButtonText: this.$t(`general.swal.confirm_change.cancel_btn_text`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading.status = true;

                        let resp;

                        try {
                            resp = await axios.delete(`units/`+ this.model.id + `/assignees/` + resident.id);
                            this.addedAssigmentList.forEach(element => {
                                if(element.id === resident.id) {
                                    let index = this.addedAssigmentList.indexOf(element);
                                    this.addedAssigmentList.splice(index, 1);
                                }
                            });
                        } catch {
                            console.log(e);
                        }

                        if (resp && resp.data) {
                            this.$refs.assigneesList.fetch();
                            displaySuccess(resp.data)
                        }

                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.loading.status = false;
                    }
                }).catch(async () => {
                    this.loading.status = false;
                });
            },
            validateFloor(rule, value, callback) {
                if(this.model.floor <= -4) {
                    callback(new Error(this.$t('validation.gte.numeric',{attribute: this.$t('models.unit.floor'), value: '-3'})));
                }
                callback();
            }
        },
        computed: {
            form() {
                return this.$refs.form;
            },
            rooms() {
                let rooms = [];

                for (let i = 1; i <= 6.5; i += .5) {
                    rooms.push({
                        value: i,
                        label: i
                    });
                }

                return rooms;
            }
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
                                const response = await this.createUnit(this.model);
                                displaySuccess(response);
                                

                                this.form.resetFields();
                                if (!!afterValid) {
                                    afterValid(response);
                                } else {
                                    this.$router.push({
                                        name: 'adminUnitsEdit',
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
                    ...mapActions(['createUnit'])
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

                                    this.model.monthly_rent_gross = ( Number(model.monthly_rent_net) + Number(model.monthly_maintenance) ).toFixed(2)
                                    const resp = await this.updateUnit(this.model)
                                    displaySuccess(resp);
                                    resolve(true);
                                    return resp;
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
                    ...mapActions(['getUnit', 'updateUnit', 'getBuilding'])
                };

                mixin.created = async function () {
                    try {
                        this.loading.state = true;

                        this.model = await this.getUnit({id: this.$route.params.id});

                        this.contractCount = this.model.contracts.length

                        this.addedAssigmentList = [];
                        this.addedAssigmentList = this.model.contracts.map(contract => contract.resident);

                        this.addedAssigmentList.map((user) => {
                            if (user.status == 1) {
                                user.statusString = 'Active';
                            } else {
                                user.statusString = 'Not Active';
                            }
                            user.name = user.first_name + " " + user.last_name;
                            return user;
                        });
                        
                        this.residentCount = this.addedAssigmentList.length

                        // if (this.model.resident) {
                        //     this.$set(this.model, 'resident_id', this.model.resident.id);
                        //     this.remoteSearchResidents(`${this.model.resident.first_name}`);
                        // }
                        if(this.model.building.attic == false)
                            this.model.attic = false;
                        this.buildings.push(this.model.building);
                        // if (config.withRelation && this.model.building_id) {
                        //     const building = await this.getBuilding({id: this.model.building_id});
                        //     this.remoteSearchBuildings(`${building.name}`);
                        // }
                        
                        this.fileCount = this.model.media ? this.model.media.length : 0
                        
                       
                    } catch (err) {
                        this.$router.replace({
                            name: 'adminUnits'
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
