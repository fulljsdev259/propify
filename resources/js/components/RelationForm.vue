<template>
    <el-form :model="model" :rules="validationRules" label-position="top"  ref="form" v-loading="loading">

        <el-row :gutter="20">
            <el-col :md="24">
                <el-form-item :label="$t('models.resident.relation.type.label')"
                            prop="type"
                            class="label-block">
                    <el-select :placeholder="$t('models.resident.relation.placeholder.type')"
                                style="display: block;"
                                v-model="model.type">
                        <el-option
                                :key="key"
                                :label="$t('models.resident.relation.type.' + value )"
                                :value="+key"
                                v-for="(value, key) in $constants.relations.type"
                                v-if="key != 3">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col :md="12">
                <el-form-item prop="quarter_id" :label="$t('models.resident.quarter.name')" class="label-block">
                    <!-- <el-select
                            :loading="remoteLoading"
                            :placeholder="$t('models.resident.search_quarter')"
                            :remote-method="remoteRelationSearchQuarters"
                            @change="searchRelationUnits(false)"
                            filterable
                            remote
                            reserve-keyword
                            style="width: 100%;"
                            v-model="model.quarter_id">
                        <el-option
                                :key="quarter.id"
                                :label="quarter.name"
                                :value="quarter.id"
                                v-for="quarter in quarters"/>
                    </el-select> -->
                    <multi-select
                        :type="quarterFilter.key"
                        :name="quarterFilter.name"
                        :data="quarterFilter.data"
                        :maxSelect="1"
                        :showMultiTag="true"
                        :selectedOptions="[model.quarter_id]"
                        @select-changed="handleSelectChange($event, 'quarter')"
                    >
                    </multi-select>
                </el-form-item>
            </el-col>
            <el-col :md="12">
                <el-form-item v-if="mode == 'add'"
                            :label="$t('models.resident.unit.name')"
                            prop="unit_id" 
                            class="label-block">
                    <!-- <multi-select
                        :type="unitFilter.key"
                        :name="unitFilter.name"
                        :data="unitFilter.data"
                        :filter="unitFilter"
                        :selectedOptions="model.unit_id"
                        @select-changed="handleSelectChange($event, 'unit')"
                    >
                    </multi-select> -->
                    <!-- <el-select :placeholder="$t('models.resident.search_unit')" 
                            style="display: block"
                            v-model="model.unit_id"
                            filterable 
                            clearable
                            multiple
                            reserve-keyword
                            @change="changeRelationUnit">
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
                                <span style="float: right">{{ translateUnitType(item.type) }}</span>
                                
                            </el-option>
                        </el-option-group>
                    </el-select> -->
                    <multi-select 
                        :key="units.length"
                        :type="unitFilter.key"
                        :name="unitFilter.name"
                        :data="unitFilter.data"
                        showMultiTag
                        showGroup
                        :maxSelect="1"
                        :selectedOptions="[model.unit_id]"
                        @select-changed="model.unit_id=$event, changeRelationUnit()"
                    >

                    </multi-select>
                </el-form-item>
                <el-form-item v-if="mode == 'edit'"
                            :label="$t('models.resident.unit.name')"
                            prop="unit_id" 
                            class="label-block">
                    <!-- <multi-select
                        :type="unitFilter.key"
                        :name="unitFilter.name"
                        :data="unitFilter.data"
                        :filter="unitFilter"
                        :selectedOptions="[model.unit_id]"
                        @select-changed="handleSelectChange($event, 'unit')"
                    >
                    </multi-select> -->
                    <!-- <el-select :placeholder="$t('models.resident.search_unit')" 
                            style="display: block"
                            v-model="model.unit_id"
                            @change="changeRelationUnit">
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
                                <span style="float: right">{{ translateUnitType(item.type) }}</span>
                            </el-option>
                        </el-option-group>
                    </el-select> -->
                    <multi-select 
                        :key="units.length"
                        :type="unitFilter.key"
                        :name="unitFilter.name"
                        :data="unitFilter.data"
                        showMultiTag
                        showGroup
                        :maxSelect="1"
                        :selectedOptions="[model.unit_id]"
                        @select-changed="model.unit_id=$event, changeRelationUnit()"
                    >

                    </multi-select>
                </el-form-item>
            </el-col>
            
            <el-col :md="12">
                <el-form-item :label="$t('models.resident.relation.start_date')"
                        prop="start_date">
                    <el-date-picker
                            :picker-options="{disabledDate: disabledRentStart}"
                            :placeholder="$t('models.resident.relation.start_date')"
                            format="dd.MM.yyyy"
                            style="width: 100%;"
                            type="date"
                            v-model="model.start_date"
                            @change="changeStartDate"
                            value-format="yyyy-MM-dd"/>
                </el-form-item>
            </el-col>
            <el-col :md="12">
                <el-form-item :label="$t('models.resident.relation.end_date')">
                    <el-date-picker
                        :picker-options="{disabledDate: disabledRentEnd}"
                        :placeholder="$t('models.resident.relation.end_date')"
                        format="dd.MM.yyyy"
                        style="width: 100%;"
                        type="date"
                        v-model="model.end_date"
                        value-format="yyyy-MM-dd"/>
                </el-form-item>
            </el-col>
   
            <!-- <el-col :md="24" v-if="showResidents">
                <el-form-item :label="$t('general.resident')" prop="resident_ids">
                    <el-select
                        :loading="remoteLoading"
                        :placeholder="$t('models.request.placeholders.resident')"
                        :remote-method="remoteSearchResidents"
                        filterable 
                        remote
                        multiple
                        reserve-keyword
                        style="width: 100%;"
                        v-model="model.resident_ids">
                        <el-option
                            :key="resident.id"
                            :label="resident.name"
                            :value="resident.id"
                            v-for="resident in residents"/>
                    </el-select>
                </el-form-item>
            </el-col> -->
            <!-- <el-col :md="12" v-if="showResident && model.unit_id">
                <el-form-item :label="$t('general.resident')" prop="resident_id">
                    <el-select
                        :loading="remoteLoading"
                        :placeholder="$t('models.request.placeholders.resident')"
                        :remote-method="remoteSearchResidents"
                        @change="changeResident"
                        filterable 
                        remote
                        reserve-keyword
                        style="width: 100%;"
                        v-model="model.resident_id">
                        <el-option
                            :key="resident.id"
                            :label="resident.name"
                            :value="resident.id"
                            v-for="resident in residents"/>
                    </el-select>
                </el-form-item>
            </el-col> -->
        
            <!-- <el-col :md="12" v-if="model.unit_id">
                <el-form-item :label="$t('models.resident.relation.type.label')"
                            prop="type"
                            class="label-block">
                    <el-select :placeholder="$t('models.unit.type.label')"
                                style="display: block;"
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
            <el-col :md="12" v-if="model.unit_id && resident_type_check == 1">
                <el-form-item :label="$t('models.resident.relation.duration')"
                            prop="duration"
                            class="label-block">
                    <el-select :placeholder="$t('general.placeholders.select')" style="display: block" 
                                v-model="model.duration">
                        <el-option
                                :key="type.value"
                                :label="type.name"
                                :value="type.value"
                                v-for="type in durations">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-col> -->

            

            <!-- <el-col :md="12" v-if="model.unit_id && resident_type_check == 1">
                <el-form-item :label="$t('models.resident.status.label')" prop="status" class="label-block">
                    <el-select :placeholder="$t('general.placeholders.select')" style="display: block" 
                                v-model="model.status">
                        <el-option
                                :key="status.value"
                                :label="status.name"
                                :value="status.value"
                                v-for="status in relation_statuses">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-col> -->
        </el-row>
        <template><!--v-if="resident_type_check == 1"-->
        <!-- <ui-divider v-if="model.unit_id" content-position="left">
            {{ $t('models.resident.relation.deposit_amount') }}
        </ui-divider>

        <el-row :gutter="20" v-if="model.unit_id">
            <el-col :md="12">
                <el-form-item :label="$t('models.resident.relation.deposit_amount')"
                                prop="deposit_amount">
                    <el-input type="text"
                            v-model="model.deposit_amount"
                    ></el-input>
                </el-form-item>
            </el-col>
            <el-col :md="12">
                <el-form-item :label="$t('models.resident.relation.type_of_deposit')"
                            prop="deposit_type">
                    <el-select :placeholder="$t('general.placeholders.select')" style="display: block" 
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
                                <div class="cell">{{$t('general.maintenance')}}</div>
                            </th>
                            <th class="symbol is-leaf">
                                <div class="cell"></div>
                            </th>
                            <th class="data is-leaf">
                                <div class="cell">{{$t('general.gross_rent')}}</div>
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
                                        <el-input type="number"
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
                                        <el-input type="number"
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
                                        {{( Number(model.monthly_rent_net) + Number(model.monthly_maintenance) ).toFixed(2)}}
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
        </el-row> -->
        <!-- <el-row :gutter="20" v-if="model.unit_id">
            <el-col :md="12">
                <el-form-item :label="$t('models.resident.relation.deposit_status.label')"
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
        <!-- <ui-divider v-if="model.unit_id" content-position="left">
            {{ $t('models.resident.relation.relation_pdf') }}
        </ui-divider> -->
        <el-row :gutter="20"  v-if="model.unit_id" class="media-box">
            <el-col :md="24">
                <el-form-item>

                

                <!-- <el-alert
                    :title="$t('models.resident.relation.pdf_only_desc')"
                    type="info"
                    show-icon
                    :closable="false"
                >
                </el-alert> -->

                <upload-relation @fileUploaded="addPDFtoRelation" class="upload-custom" acceptType=".jpg,.jpeg,.png,.gif,.bmp,.pdf,.JPG,.JPEG,.PBG,.GIF,.BMP,.PDF" drag multiple/>
                
                <relation-file-table :media="model.media" @delete="deletePDFfromRelation"></relation-file-table>
                
                </el-form-item>
            </el-col>
            <!-- <el-col :md="12">
                <el-form-item :label="$t('models.resident.relation.pdf_select_types.label')"
                            prop="type"
                            class="label-block">
                    <el-select :placeholder="$t('models.resident.relation.pdf_select_types.label')"
                                style="display: block;"
                                v-model="model.choose_pdf">
                        <el-option
                                :key="value"
                                :label="$t('models.resident.relation.pdf_select_types.' + value )"
                                :value="value"
                                v-for="value in pdf_select_types">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-col> -->
        </el-row>
        </template>
        <!-- <ui-divider style="margin-top: 16px;"></ui-divider> -->
        <div class="relation-form-actions">
            <div class="button-group">
                <el-button type="danger" v-if="edit_index != undefined" @click="$emit('delete-relation', edit_index)" icon="ti-trash" >
                    {{$t('general.actions.delete')}}
                </el-button>
                <el-button type="primary" v-if="resident_id == undefined" @click="submit" icon="ti-save">
                    {{ edit_index == undefined ? $t('general.actions.add') : $t('general.actions.edit')}}
                </el-button>
                <el-button type="primary" v-else @click="submit" icon="ti-save" >{{$t('general.actions.save')}}</el-button>
            </div>
        </div>
        

        

    </el-form>
</template>

<script>
    import {displayError} from "../helpers/messages";
    import UploadRelation from 'components/UploadRelation';
    import RelationFileTable from 'components/RelationFileTable';
    import RelationCount from 'components/RelationCount';
    import {mapActions, mapGetters} from 'vuex';
    import MultiSelect from 'components/Select';
    import globalFunction from "helpers/globalFunction";

    export default {
        name: "RelationForm",
        components: {
            UploadRelation,
            RelationCount,
            RelationFileTable,
            MultiSelect
        },
        mixins: [globalFunction],
        props: {
            mode: {
                type: String
            },
            resident_type: {
                type: Number
            },
            resident_id: {
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
            },
            hideBuildingAndUnits: {
                type: Boolean,
                default: false
            },
            hideBuilding: {
                type: Boolean,
                default: false
            },
            showResident: {
                type: Boolean,
                default: false
            },
            quarter_id: {
                type: Number
            },
            unit_id: {
                type: Number
            },
        },
        data () {
            return {
                remoteLoading: false,
                quarters: [],
                buildings: [],
                units: [],
                options: [],
                durations: [],
                deposit_statuses: [],
                relation_statuses: [],
                deposit_types: [],
                residents: [],
                loading: false,
                pdf_select_types: ["all", "existing"],
                model: {
                    resident_id: '',
                    resident_ids: [],
                    type: '',
                    duration: '',
                    start_date: '',
                    end_date: '',
                    deposit_amount: 0,
                    deposit_type: 1,
                    monthly_rent_net: 0,
                    monthly_maintenance: 0,
                    status: '',
                    deposit_status: 1,
                    monthly_rent_gross: 0,
                    unit_id: (this.mode == 'add' ? [] : ''),
                    quarter_id: '',
                    media: [],
                    buildings: [],
                    units: [],
                },
                validationRules: {
                    quarter_id: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.resident.quarter.name')})
                    }],
                    unit_id: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.resident.unit.name')})
                    }],
                    resident_ids: [],
                    deposit_amount: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.resident.relation.deposit_amount')})
                    }],
                    deposit_type: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.resident.relation.type_of_deposit')})
                    }],
                    start_date: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.resident.relation.start_date')})
                    }],
                    type: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.resident.relation.type.label')})
                    }],
                    duration: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.resident.relation.duration')})
                    }],
                    status: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.resident.status.label')})
                    }],
                    monthly_rent_net: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.monthly_rent_net')})
                    }],
                    monthly_maintenance: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.maintenance')})
                    }],
                },
                original_unit_id : 0,
                isFuture: false
            }
        },
        computed: {
            unitFilter() {
                return {
                        name: this.$t('models.resident.search_unit'),
                        type: 'group-select',
                        key: 'name',
                        data: this.units,
                        remoteLoading: false,
                        fetch: this.searchRelationUnits
                }
            },
            quarterFilter() {
                return {
                        name: this.$t('models.resident.search_quarter'),
                        type: 'select',
                        key: 'name',
                        data: this.quarters,
                        remoteLoading: false,
                        fetch: this.fetchRemoteQuarters
                }
            },
            showResidents() {
                let flag = false
                // if(this.mode == 'add') {
                //     if(this.model.unit_id) {
                //         this.model.unit_id.forEach(every_unit_id => {
                //             let found = this.units.find(item => item.id == every_unit_id && item.type >= 1 && item.type <= 2)
                //             if(found)
                //                 flag = true
                //         })
                //     }
                // }
                // else {
                //     if(this.model.unit_id) {
                //         let found = this.units.find(item => item.id == this.model.unit_id && item.type >= 1 && item.type <= 2)
                //         if(found)
                //             flag = true
                //     }
                // }
                if(this.mode == 'add') {
                    if(this.model.unit_id) {
                        this.model.unit_id.forEach(every_unit_id => {
                            this.units.forEach(group => {
                                let found = group.options.find(item => item.id == every_unit_id && item.type >= 1 && item.type <= 2)
                                if(found)
                                    flag = true
                            })
                        })
                    }
                }
                else {
                    if(this.model.unit_id) {
                        this.units.forEach(group => {
                            let found = group.options.find(item => item.id == this.model.unit_id && item.type >= 1 && item.type <= 2)
                            if(found)
                                flag = true
                        })
                    }
                }
                return flag;
            }
        },
        methods: {
            submit () {
                
                this.$refs.form.validate(async valid => {
                    if (valid) {
                        this.loading = true;
                        this.model.monthly_rent_gross = (Number(this.model.monthly_rent_net) + Number(this.model.monthly_maintenance)).toFixed(2)
                        const {...params} = this.model

                        
                        if(!this.showResident)
                            params.resident_id = this.resident_id

                        if (params.resident_id == undefined || params.resident_id == 0) 
                        {

                            if(this.mode == 'add') {
                                params.unit_id.forEach(every_unit_id => {
                                    this.units.forEach(group => {
                                    let found = group.options.find(item => item.id == every_unit_id)
                                    if(found)
                                            params.units.push(found)
                                    })

                                })
                            }
                            else {
                                this.units.forEach(group => {
                                    let found = group.options.find(item => item.id == this.model.unit_id)
                                    if(found)
                                        params.unit = found
                                })
                                
                            }
                            
                            let end_date = null
                            if(params.end_date)
                                end_date = new Date(params.end_date).getTime();
                            const today = new Date().getTime();

                            if(!end_date || end_date == today)
                                params.status = 1
                            else if(end_date && end_date > today)
                                params.status = 2
                            else if(end_date && end_date < today)
                                params.status = 3

                            params.quarter = this.quarters.find(item => item.id == this.model.quarter_id)
                            
                            if (this.mode == "add") {
                                if(params.unit_id.length > 1)
                                {
                                    params.unit_id.forEach(every_unit_id => {
                                        let every_obj = Object.assign({}, params)
                                        every_obj.unit_id = every_unit_id
                                        every_obj.unit = params.units.find(item => item.id == every_unit_id)
                                        this.$emit('add-relation', every_obj)
                                    })
                                }
                                else {
                                    this.$emit('add-relation', params)
                                }
                                
                            }
                            else {
                                this.$emit('update-relation', this.edit_index, params)
                            }
                            
                        }
                        else {
                            
                            this.units.forEach(group => {
                                let found = group.options.find(item => item.id == this.model.unit_id)
                                if(found)
                                    params.unit = found
                            })
                            params.quarter = this.quarters.find(item => item.id == this.model.quarter_id)

                            params.status = 1
                            if (this.mode == "add") {
                                const resp = await this.$store.dispatch('relations/create', params);
                                this.$emit('add-relation', resp.data)
                            }
                            else {
                                const resp = await this.$store.dispatch('relations/update', params);
                                this.$emit('update-relation', this.edit_index, resp.data)
                            }
                        }

                        this.loading = false
                        this.$refs.form.resetFields()
                        this.$emit('update:visible', false);
                        
                    }
                })
            },
            handleSelectChange(val, filter) {
                if(filter == 'unit') {
                    this.model.unit_id = val
                } else if(filter == 'quarter') {
                    if(val.length == 1) {
                        this.model.quarter_id = val[0]
                        this.searchRelationUnits(false)
                    }
                    else {
                        this.model.quarter_id = null
                        this.model.unit_id = this.mode == 'add' ? [] : ''
                        this.units = []
                    }
                        
                }
            },
            disabledRentStart(date) {
                const d = new Date(date).getTime();
                if(!this.model.end_date)
                    return false
                const rentEnd = new Date(this.model.end_date).getTime();
                return d >= rentEnd;
            },
            disabledRentEnd(date) {
                const d = new Date(date).getTime();
                if(!this.model.start_date)
                    return false
                const rentStart = new Date(this.model.start_date).getTime();
                return d <= rentStart;
            },
            changeStartDate(date) {
                const start_date = new Date(date).getTime();
                const today = new Date().getTime();

                this.isFuture = start_date > today
                // if(this.isFuture)
                //     this.model.status = 2
            },
            async remoteSearchResidents(search) {
                if (search === '') {
                    this.residents = [];
                } else {
                    this.remoteLoading = true;

                    try {
                        const {data} = await this.getResidents({get_all: true, search});
                        this.residents = data;
                        this.residents.forEach(t => t.name = `${t.first_name} ${t.last_name}`);
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            async remoteRelationSearchQuarters(search) {
                if (search === '') {
                    this.quarters = [];
                } else {
                    this.remoteLoading = true;

                    try {
                        let resp = await this.getQuarters({get_all: true, search});

                        this.quarters = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            async remoteRelationSearchBuildings(search) {
                if (search === '') {
                    this.buildings = [];
                } else {
                    this.remoteLoading = true;

                    try {
                        let resp = null
                        if(this.quarter_id) {
                            resp = await this.getBuildings({get_all: true, quarter_id: this.quarter_id, search});
                        }
                        else {
                            resp = await this.getBuildings({get_all: true, search});
                        }

                        resp.data.map(building => {
                            building.name = building.address.street + ' ' + building.address.house_num
                        })
                        
                        this.buildings = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            async searchRelationUnits(shouldKeepValue) {
                if(!shouldKeepValue) {
                    this.model.unit_id = this.mode == 'add' ? [] : '';
                }
                try {

                    const resp1 = await this.getUnits({
                        group_by_building: true,
                        quarter_id: this.model.quarter_id
                    });
                    
                    let parent_obj = this
                    this.units = [];
                    for( var key in resp1.data) {
                        if( !resp1.data.hasOwnProperty(key)) continue;

                        let group_label = key
                        
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
            // async searchRelationUnits(shouldKeepValue) {
            //     if(!shouldKeepValue) {
            //         this.model.unit_id = this.mode == 'add' ? [] : '';
            //     }
            //     try {

            //         const resp = await this.getUnits({
            //             quarter_id: this.model.quarter_id
            //         });
                    
            //         let parent_obj = this
            //         this.units = resp.data.data

            //     } catch (err) {
            //         displayError(err);
            //     } finally {
            //         this.remoteLoading = false;
            //     }
            // },
            translateUnitType(type) {
                return this.$t(`models.unit.type.${this.$constants.units.type[type]}`);
            },
            changeRelationUnit() {

                let unit = null
                this.units.forEach(group => {
                    let found = group.options.find(item => item.id == this.model.unit_id)
                    if(found)
                        unit = found
                })

                // if(unit)
                // {
                //     this.model.monthly_rent_net = unit.monthly_rent_net
                //     this.model.monthly_maintenance = unit.monthly_maintenance
                //     this.model.monthly_rent_gross = unit.monthly_rent_gross
                //     this.model.type = unit.type
                //     this.model.duration = 1
                // }
            },
            addPDFtoRelation(file) {
                //let toUploadRelationFile = {...file, url: URL.createObjectURL(file.raw)}
                let toUploadRelationFile = {media : file.src, name: file.raw.name}
                this.model.media.push(toUploadRelationFile)
            },
            deletePDFfromRelation(index) {
                this.model.media.splice(index, 1)
            },
            async fetchRemoteQuarters(search = '') {
                const quarters = await this.getQuarters({get_all: true, search})

                return quarters.data
            },
            ...mapActions(['getQuarters', 'getBuildings', 'getUnits', 'getResidents']),
        },
        async created () {

            this.loading = true;

            this.quarters = await this.fetchRemoteQuarters();

            //this.$refs.form.clearValidate('unit_id')

            let parent_obj = this
            this.deposit_types = Object.entries(this.$constants.relations.deposit_type).map(([value, label]) => ({value: +value, name: this.$t(`models.resident.relation.deposit_types.${label}`)}))
            //this.durations = Object.entries(this.$constants.relations.duration).map(([value, label]) => ({value: +value, name: this.$t(`models.resident.relation.durations.${label}`)}))
            this.deposit_statuses = Object.entries(this.$constants.relations.deposit_status).map(([value, label]) => ({value: +value, name: this.$t(`models.resident.relation.deposit_status.${label}`)}));
            this.relation_statuses = Object.entries(this.$constants.relations.status).map(([value, label]) => ({value: +value, name: this.$t(`models.resident.relation.status.${label}`)}));


            if(this.mode == "edit") {
                this.model = Object.assign({}, this.data)

                if(this.data.residents) {
                    this.model.resident_ids = Object.assign({}, this.data.residents.map(item => item.id))
                    this.model.residents.forEach(t => t.name = `${t.first_name} ${t.last_name}`);
                    this.residents = this.model.residents
                    
                }
                    
                if(this.model.quarter)
                    this.model.quarter_id = this.model.quarter.id
                
                
                // if(this.model.resident)
                // {
                //     this.residents.push(this.model.resident)
                //     this.residents.forEach(t => t.name = `${t.first_name} ${t.last_name}`);
                // }

                
                if(!this.model.media)
                    this.model.media = []


                const start_date = new Date(this.model.start_date).getTime();
                const today = new Date().getTime();

                this.isFuture = start_date > today
                
                this.original_unit_id = this.data.unit_id

                if( !this.hideBuildingAndUnits ) {
                    if( this.model.unit )
                    {
                        let group_label = "Quarter"
                        this.units.push({ label: group_label, options : [this.model.unit]})
                    }
                    if(this.model.quarter) {
                        //this.quarters.push(this.model.quarter)
                        //await this.remoteRelationSearchQuarters(this.model.quarter.name)
                        await this.searchRelationUnits(true)
                    }
                }

            }

            if(this.model.unit_id == null)
                this.model.unit_id = []

            this.loading = false;
        },
        mounted() {
            this.$refs.form.$el.focus()
        },
    }
</script>

<style lang="scss" scoped>

    .el-form {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
        
    /deep/ .ui-divider {
        margin: 32px 16px 16px 0;
        margin-left: 10px;
        margin-right: 10px;
        width: inherit;
        
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
        height: 270px;

        .el-table__body-wrapper {
            height: 100%;
        }

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
                        padding: 27px 0;

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
    

    /deep/ .relation-form-actions {
        // position: absolute;
        //width: 100%;
        margin-top: 30px;
        margin-left: 10px;
        margin-right: 10px;
        display: flex;
        flex-direction: column;
        //flex-grow: 1;
        justify-content: flex-end;


        .button-group {
            display: flex;
            justify-content: flex-end;

            button {
                //width: 100%;
                i {
                    padding-right: 5px;
                }
            }
        }
        
    }

    /deep/ .el-tag {
        background-color: var(--primary-color);
        color: white;
        border-radius: 6px;
        font-size: 12px;
        
        margin: 0;
        padding: 0;
        padding-left: 10px;
        padding-right: 20px;
        height: 35px;
        line-height: 35px;

        i {
            color: white;
            background: transparent;
            font-size: 17px;
            font-weight: 600;
        }
    }

    /deep/ .el-tag.el-tag--info .el-tag__close {
        color: white;
        background: transparent;
    }

    /deep/ .el-dropdown .el-button span.el-tag i.el-tag__close {
        right: 0;
        line-height: 1.4;
        font-size: 14px;
        font-weight: 700;
        color: var(--color-white);
    }

    .media-box {
        margin-top: 30px;

        .el-form-item {
            /deep/ .el-form-item__content {
                display: flex;
            }
        }
    }
</style>
