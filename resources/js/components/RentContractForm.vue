<template>
    <el-form :model="model" :rules="validationRules" label-position="top"  ref="form" v-loading="loading">

        <el-row :gutter="20">
            <el-col :md="12">
                <el-form-item prop="building_id" :label="$t('models.tenant.building.name')" class="label-block">
                    <el-select
                            :loading="remoteLoading"
                            :placeholder="$t('models.tenant.search_building')"
                            :remote-method="remoteRentContractdSearchBuildings"
                            @change="searchRentContractUnits(false)"
                            filterable
                            remote
                            reserve-keyword
                            style="width: 100%;"
                            v-model="model.building_id">
                        <el-option
                                :key="building.id"
                                :label="building.name"
                                :value="building.id"
                                v-for="building in buildings"/>
                    </el-select>
                </el-form-item>
            </el-col>
            <!-- <el-col :md="12" v-if="model.building_id">
                <el-form-item prop="unit_id" :label="$t('models.tenant.unit.name')"
                            class="label-block">
                    <el-select :placeholder="$t('models.tenant.search_unit')" 
                            style="display: block"
                            v-model="model.unit_id"
                            @change="changeRentContractUnit">
                        <el-option
                                :key="unit.id"
                                :label="unit.name"
                                :value="unit.id"
                                v-for="unit in units">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-col> -->
            <el-col :md="12" v-if="model.building_id">
                <el-form-item prop="unit_id" :label="$t('models.tenant.unit.name')"
                            class="label-block">
                    <el-select :placeholder="$t('models.tenant.search_unit')" 
                            style="display: block"
                            v-model="model.unit_id"
                            @change="changeRentContractUnit">
                        <el-option-group
                            v-for="group in units"
                            :key="group.label"
                            :label="group.label">
                            <el-option
                                v-for="item in group.options"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                                <span style="float: left">{{ item.name }}</span>
                                <rent-contract-count :countsData="item" style="float: right;"></rent-contract-count>
                            </el-option>
                        </el-option-group>
                        
                    </el-select>
                </el-form-item>
            </el-col>
        </el-row>
        <el-row :gutter="20" v-if="model.unit_id">
            <el-col :md="12">
                <el-form-item :label="$t('models.tenant.rent_type')"
                            prop="type"
                            class="label-block">
                    <!-- <el-select placeholder="Select" style="display: block" 
                                v-model="model.type">
                        <el-option
                                :key="type.value"
                                :label="type.name"
                                :value="type.value"
                                v-for="type in rent_types">
                        </el-option>
                    </el-select> -->
                    <el-select :placeholder="$t('models.unit.type.label')"
                                v-model="model.type">
                        <el-option
                                :key="key"
                                :label="$t('models.unit.type.' + value )"
                                :value="+key"
                                v-for="(value, key) in $constants.units.type">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col :md="12" v-if="model.unit_id">
                <el-form-item :label="$t('models.tenant.rent_duration')"
                            prop="duration"
                            class="label-block">
                    <el-select placeholder="Select" style="display: block" 
                                v-model="model.duration">
                        <el-option
                                :key="type.value"
                                :label="type.name"
                                :value="type.value"
                                v-for="type in rent_durations">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-col>
        </el-row>
        <el-row :gutter="20" v-if="model.unit_id">
            <el-col :md="12">
                <el-form-item :label="$t('models.tenant.rent_start')"
                        prop="start_date">
                    <el-date-picker
                            :picker-options="{disabledDate: disabledRentStart}"
                            :placeholder="$t('models.tenant.rent_start')"
                            format="dd.MM.yyyy"
                            style="width: 100%;"
                            type="date"
                            v-model="model.start_date"
                            value-format="yyyy-MM-dd"/>
                </el-form-item>
            </el-col>
            <el-col :md="12" v-if="model.unit_id && model.duration == 2">
                <el-form-item :label="$t('models.tenant.rent_end')">
                    <el-date-picker
                        :picker-options="{disabledDate: disabledRentEnd}"
                        :placeholder="$t('models.tenant.rent_end')"
                        format="dd.MM.yyyy"
                        style="width: 100%;"
                        type="date"
                        v-model="model.end_date"
                        value-format="yyyy-MM-dd"/>
                </el-form-item>
            </el-col>
        </el-row>
        <el-row :gutter="20" v-if="model.unit_id">
            <el-col :md="12">
                <el-form-item :label="$t('models.tenant.rentcontract_id')"
                                class="label-block">
                    <el-input
                        v-model="model.rent_contract_format"
                        :disabled="true">
                    </el-input>
                </el-form-item>
            </el-col>
            <el-col :md="12">
                <el-form-item :label="$t('models.tenant.status.label')" prop="status" class="label-block">
                    <el-select placeholder="Select" style="display: block" 
                                v-model="model.status">
                        <el-option
                                :key="status.value"
                                :label="status.name"
                                :value="status.value"
                                v-for="status in rentcontract_statuses">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-col>
        </el-row>
        <ui-divider v-if="model.unit_id" content-position="left">
            {{ $t('models.tenant.deposit_amount') }}
        </ui-divider>

        <el-row :gutter="20" v-if="model.unit_id">
            <el-col :md="12">
                <el-form-item :label="$t('models.tenant.deposit_amount')"
                                prop="deposit_amount">
                    <el-input type="text"
                            v-model="model.deposit_amount"
                    ></el-input>
                </el-form-item>
            </el-col>
            <el-col :md="12">
                <el-form-item :label="$t('models.tenant.type_of_deposit')"
                            prop="deposit_type">
                    <el-select placeholder="Select" style="display: block" 
                                v-model="model.deposit_type">
                        <el-option
                                :key="type.value"
                                :label="type.name"
                                :value="type.value"
                                v-for="type in deposit_types">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-col>
        </el-row>
        <ui-divider v-if="model.unit_id" content-position="left">
            {{ $t('general.monthly_rent_net') }}
        </ui-divider>
        <div class="el-table el-table--fit el-table--enable-row-hover el-table--enable-row-transition rent-data" 
                style="width: 100%;"
                v-if="model.unit_id && model.type < 3">
            <div class="el-table__header-wrapper">
                <table cellspacing="0" cellpadding="0" border="0" class="el-table__header">
                    <thead>
                        <tr>
                            <th class="data is-leaf">
                                <div class="cell">{{$t('general.monthly_rent_net')}}</div>
                            </th>
                            <th class="symbol is-leaf">
                                <div class="cell"></div>
                            </th>
                            <th class="data is-leaf">
                                <div class="cell">{{$t('models.tenant.maintenance')}}</div>
                            </th>
                            <th class="symbol is-leaf">
                                <div class="cell"></div>
                            </th>
                            <th class="data is-leaf">
                                <div class="cell">{{$t('models.tenant.gross_rent')}}</div>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="el-table__body-wrapper is-scrolling-none">
                <table cellspacing="0" cellpadding="0" border="0" class="el-table__body">
                    <tbody>
                        <tr>
                            <td class="data">
                                <div class="cell">
                                    <el-form-item 
                                        prop="monthly_rent_net">
                                        <el-input type="text"
                                                v-model="model.monthly_rent_net"
                                        >
                                            <template slot="prepend">CHF</template>
                                        </el-input>
                                    </el-form-item>
                                </div>
                            </td>
                            <td class="symbol">
                                <div class="cell">
                                    +
                                </div>
                            </td>
                            <td class="data">
                                <div class="cell">
                                    <el-form-item 
                                        prop="monthly_maintenance">
                                        <el-input type="text"
                                                v-model="model.monthly_maintenance"
                                        >
                                            <template slot="prepend">CHF</template>
                                        </el-input>
                                    </el-form-item>
                                </div>
                            </td>
                            <td class="symbol">
                                <div class="cell">
                                    =
                                </div>
                            </td>
                            <td class="data">
                                <div class="cell">
                                    <el-form-item >
                                        {{Number(model.monthly_rent_net) + Number(model.monthly_maintenance)}}
                                    </el-form-item>
                                    
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <el-row :gutter="20" v-if="model.unit_id && model.type >= 3">
            <el-col :md="8">
                <el-form-item :label="$t('general.monthly_rent_net')" prop="monthly_rent_net" class="label-block">
                    <el-input type="text"
                            v-model="model.monthly_rent_net"
                    >
                        <template slot="prepend">CHF</template>
                    </el-input>
                </el-form-item>
            </el-col>
        </el-row>
        <!-- <el-row :gutter="20" v-if="model.unit_id">
            <el-col :md="12">
                <el-form-item :label="$t('models.tenant.deposit_status.label')"
                                class="label-block">
                    <el-radio-group v-model="model.deposit_status">
                        <el-radio-button 
                            :key="status.value" 
                            :label="status.value" 
                            v-for="status in deposit_statuses"
                        >
                            {{status.name}}
                        </el-radio-button>
                    </el-radio-group>
                </el-form-item>
            </el-col>
        </el-row> -->
        <ui-divider v-if="model.unit_id" content-position="left">
            {{ $t('models.tenant.rent_contract_pdf') }}
        </ui-divider>
        <el-row :gutter="20"  v-if="model.unit_id">
            <el-col :md="24">
                <el-form-item>

                <el-table
                    :data="model.media"
                    style="width: 100%"
                    v-if="model.media.length"
                    class="rentcontract-file-table"
                    >
                    <el-table-column
                        :label="$t('models.rent_contract.filename')"
                        prop="name"
                    >
                        <template slot-scope="scope">
                            <a v-if="scope.row.url" :href="scope.row.url" target="_blank"><strong>{{scope.row.name}}</strong></a>
                            <span v-else><strong>{{scope.row.name}}</strong></span>
                        </template>
                    </el-table-column>
                    <el-table-column
                        align="right"
                    >
                        <template slot-scope="scope">
                            <el-tooltip
                                :content="$t('general.actions.delete')"
                                class="item" effect="light" 
                                placement="top-end">
                                    <el-button @click="deletePDFfromRentContract(scope.$index)" icon="ti-trash" size="mini" type="danger"/>
                            </el-tooltip>
                        </template>
                    </el-table-column>
                </el-table>

                <el-alert
                    :title="$t('models.request.pdf_only_desc')"
                    type="info"
                    show-icon
                    :closable="false"
                >
                </el-alert>

                <upload-rent-contract @fileUploaded="addPDFtoRentContract" class="upload-custom" acceptType=".pdf" drag multiple/>
                
                </el-form-item>
            </el-col>
        
        </el-row>
        
        <ui-divider></ui-divider>
        <div class="rentcontract-actions">
            <el-button type="primary" @click="submit" icon="ti-save" round>{{$t('general.actions.save')}}</el-button>
        </div>
        

        

    </el-form>
</template>

<script>
    import {displayError} from "../helpers/messages";
    import UploadRentContract from 'components/UploadRentContract';
    import RentContractCount from 'components/RentContractCount';
    import {mapActions, mapGetters} from 'vuex';

    export default {
        name: "RentContractForm",
        components: {
            UploadRentContract,
            RentContractCount
        },
        props: {
            mode: {
                type: String
            },
            tenant_id: {
                type: Number
            },
            data: {
                type: Object
            },
            edit_index: {
                type: Number
            },
            visible: {
                type: Boolean,
                default: false
            },
            used_units: {
                type: Array
            }
        },
        data () {
            return {
                remoteLoading: false,
                buildings: [],
                units: [],
                options: [],
                rent_durations: [],
                deposit_statuses: [],
                rentcontract_statuses: [],
                deposit_types: [],
                loading: false,
                model: {
                    type: '',
                    duration: '',
                    start_date: '',
                    end_date: '',
                    deposit_amount: '',
                    deposit_type: '',
                    monthly_rent_net: '',
                    monthly_maintenance: '',
                    status: 1,
                    deposit_status: 1,
                    monthly_rent_gross: '',
                    unit_id: '',
                    building_id: '',
                    media: [],
                    buildings: [],
                    units: [],
                },
                validationRules: {
                    building_id: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.tenant.building.name')})
                    }],
                    unit_id: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.tenant.unit.name')})
                    }],
                    deposit_amount: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.tenant.deposit_amount')})
                    }],
                    deposit_type: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.tenant.type_of_deposit')})
                    }],
                    start_date: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.tenant.rent_start')})
                    }],
                    type: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.tenant.rent_type')})
                    }],
                    duration: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.tenant.rent_duration')})
                    }],
                    status: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.tenant.status.label')})
                    }],
                    monthly_rent_net: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.tenant.gross_rent')})
                    }],
                    monthly_maintenance: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.tenant.maintenance')})
                    }],
                }
            }
        },
        methods: {
            submit () {
                
                this.$refs.form.validate(async valid => {
                    if (valid) {
                        this.loading = true;
                        const {...params} = this.model

                        if (this.tenant_id == undefined || this.tenant_id == 0) 
                        {
                            params.unit = this.units.find(item => item.id == this.model.unit_id)
                            params.building = this.buildings.find(item => item.id == this.model.building_id)
                            if (this.mode == "add") {
                                this.$emit('add-rent-contract', params)
                            }
                            else {
                                this.$emit('update-rent-contract', this.edit_index, params)
                            }
                            
                        }
                        else {
                            
                            params.tenant_id = this.tenant_id
                            params.unit = this.units.find(item => item.id == this.model.unit_id)
                            params.building = this.buildings.find(item => item.id == this.model.building_id)

                            if (this.mode == "add") {
                                const resp = await this.$store.dispatch('rentContracts/create', params);
                                this.$emit('add-rent-contract', resp.data)
                            }
                            else {
                                const resp = await this.$store.dispatch('rentContracts/update', params);
                                this.$emit('update-rent-contract', this.edit_index, params)
                            }
                        }

                        this.loading = false
                        this.$refs.form.resetFields()
                        this.$emit('update:visible', false);
                        
                    }
                })
            },
            disabledRentStart(date) {
                const d = new Date(date).getTime();
                const rentEnd = new Date(this.model.end_date).getTime();
                return d >= rentEnd;
            },
            disabledRentEnd(date) {
                const d = new Date(date).getTime();
                const rentStart = new Date(this.model.start_date).getTime();
                return d <= rentStart;
            },
            rentContractUploaded(file) {
                this.uploadMediaFile({
                    id: this.model.id,
                    media: file.src
                }).then(r => {                    
                    displaySuccess(r);

                    this.model.media.push(r.data);
                }).catch(err => {
                    displayError(err);
                });
            },
            async remoteRentContractdSearchBuildings(search) {
                if (search === '') {
                    this.buildings = [];
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getBuildings({get_all: true, search});
                        this.buildings = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            async searchRentContractUnits(shouldKeepValue) {
                if(!shouldKeepValue)
                    this.model.unit_id = '';
                try {
                    
                    // const resp = await this.getUnits({
                    //     get_all: true,
                    //     building_id: this.model.building_id
                    // });

                    // this.used_units.forEach(id => {
                    //     if(!this.model.unit || this.model.unit.id != id)
                    //         resp.data = resp.data.filter( item => item.id != id )
                    // })

                    // this.units = resp.data

                    const resp1 = await this.getUnits({
                        show_rent_contract_counts: true,
                        group_by_floor: true,
                        building_id: this.model.building_id
                    });

                    
                    let parent_obj = this
                    this.units = [];
                    for( var key in resp1.data) {
                        if( !resp1.data.hasOwnProperty(key)) continue;

                        let group_label = "";
                        if(key > 0)
                        {
                            group_label = key + ". " + this.$t('models.unit.floor_title.upper_ground_floor')
                        }
                        else if(key == 0)
                        {
                            group_label = this.$t('models.unit.floor_title.ground_floor')
                        }
                        else if(key < 0)
                        {
                            group_label = key + ". " + this.$t('models.unit.floor_title.under_ground_floor')
                        }
                        else if(key == 'attic')
                        {
                            group_label = this.$t('models.unit.floor_title.top_floor');
                        }
                        
                        var obj = resp1.data[key];


                        this.units.push( {
                            label : group_label,
                            options: obj
                        })

                    }

                    
                } catch (err) {
                    displayError(err);
                } finally {
                    this.remoteLoading = false;
                }
            },
            changeRentContractUnit() {
                let unit = this.units.find(item => item.id == this.model.unit_id)
                this.model.monthly_rent_net = unit.monthly_rent_net
                this.model.monthly_maintenance = unit.monthly_maintenance
                this.model.monthly_rent_gross = unit.monthly_rent_gross
                this.model.type = unit.type
                this.model.duration = 1
            },
            addPDFtoRentContract(file) {
                //let toUploadRentContractFile = {...file, url: URL.createObjectURL(file.raw)}
                let toUploadRentContractFile = {media : file.src, name: file.raw.name}
                this.model.media.push(toUploadRentContractFile)
            },
            deletePDFfromRentContract(index) {
                this.model.media.splice(index, 1)
            },
            ...mapActions(['getBuildings', 'getUnits']),
        },
        async created () {
            let parent_obj = this
            this.deposit_types = Object.entries(this.$constants.rentContracts.deposit_type).map(([value, label]) => ({value: +value, name: this.$t(`models.tenant.deposit_types.${label}`)}))
            this.rent_durations = Object.entries(this.$constants.rentContracts.duration).map(([value, label]) => ({value: +value, name: this.$t(`models.tenant.rent_durations.${label}`)}))
            this.deposit_statuses = Object.entries(this.$constants.rentContracts.deposit_status).map(([value, label]) => ({value: +value, name: this.$t(`models.tenant.deposit_status.${label}`)}));
            this.rentcontract_statuses = Object.entries(this.$constants.rentContracts.status).map(([value, label]) => ({value: +value, name: this.$t(`models.tenant.rent_status.${label}`)}));

            if(this.mode == "edit") {
                this.model = this.data

                
                if( this.model.unit )
                {
                    let key = this.model.unit.floor
                    let group_label = ""
                    if(key > 0)
                    {
                        group_label = key + ". " + this.$t('models.unit.floor_title.upper_ground_floor')
                    }
                    else if(key == 0)
                    {
                        group_label = this.$t('models.unit.floor_title.ground_floor')
                    }
                    else if(key < 0)
                    {
                        group_label = key + ". " + this.$t('models.unit.floor_title.under_ground_floor')
                    }
                    else if(key == 'attic')
                    {
                        group_label = this.$t('models.unit.floor_title.top_floor');
                    }
                    this.units.push({ label: group_label, options : [this.model.unit]})
                }

                if(this.model.building) {
                    this.buildings.push(this.model.building)
                    await this.remoteRentContractdSearchBuildings(this.model.building.name)
                    await this.searchRentContractUnits(true)
                }

            }
        }
    }
</script>

<style lang="scss" scoped>

    /deep/ .ui-divider {
        margin: 32px 16px 16px 0;
        
        i {
            padding-right: 0;
        }

        .ui-divider__content {
            left: 0;
            z-index: 0;
            padding-left: 0;
            font-size: 16px;
            font-weight: 700;
            color: var(--color-primary);
        }
    }
    .el-form-item {
        margin-bottom: 0;

        &.is-error {
            margin-bottom: 10px;
        }
    }
    /deep/ .rent-data {
        background: transparent;
        table {
            width: 100%;
            cursor: initial;
            background: transparent;
            thead, tbody {
                width: 100%;
                background: transparent;
                tr {
                    display: flex;
                    width: 100%;
                    background: transparent;
                    .data {
                        flex: 1;
                        display: flex;
                        align-items: center;
                        background: transparent;
                        .cell {
                            width: 100%;
                            text-align: left;
                            
                            .el-form-item {
                                margin-bottom: 0;

                                &.is-error {
                                    // margin-bottom: 27px;
                                }
                            }

                            /deep/ .el-input.el-input-group {
                                .el-input-group__prepend {
                                    padding: 2px 8px 0;
                                    font-weight: 600;
                                }
                                .el-input__inner {
                                    padding: 5px;
                                }
                            }
                        }
                    }
                    
                    .symbol {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 20px;
                        background: transparent;
                        .cell {
                            text-overflow: initial;
                            font-size: 16px;
                            padding: 0;
                        }
                    }

                    td {
                        padding: 25px 0;

                        .cell {
                            overflow: visible;
                        }
                    }

                    td:last-child .cell {
                        padding-left: 10px !important;
                        text-align: left;
                    }
                }
            }
        }
    }

    /deep/ .el-input.el-input-group {
        .el-input-group__prepend {
            padding: 2px 8px 0;
            font-weight: 600;
        }
        
    }
    
    .el-alert {
        line-height: 19px;
        margin-bottom: 10px;
    }

    /deep/ .rentcontract-actions {
        width: 100%;
        button {
            width: 100%;
            i {
                padding-right: 5px;
            }
        }
    }

    /deep/ .rentcontract-file-table {
        margin-bottom: 10px;
    }

    
</style>
