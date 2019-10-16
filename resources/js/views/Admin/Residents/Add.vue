<template>
    <div class="residents-add">
        <div class="main-content">
        <heading :title="$t('models.resident.add')" icon="icon-group" shadow="heavy">
            <add-actions :saveAction="submit" editRoute="adminResidentsEdit" route="adminResidents"/>
        </heading>
        <div class="crud-view">
            <el-form :model="model" ref="form">
                <el-row :gutter="20">
                    <el-col :lg="12" :sm="24">
                        <card :loading="loading" :header="$t('models.resident.personal_details_card')">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :rules="validationRules.title"
                                                  :label="$t('general.salutation')"
                                                  prop="title"
                                                  class="label-block">
                                        <el-select placeholder="Select" style="display: block" v-model="model.title">
                                            <el-option
                                                    :key="title"
                                                    :label="$t(`general.salutation_option.${title}`)"
                                                    :value="title"
                                                    v-for="title in titles">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12" v-if="model.title === titles.company">
                                    <el-form-item :label="$t('models.resident.company')" :rules="validationRules.company"
                                                  prop="company">
                                        <el-input autocomplete="off" type="text" v-model="model.company"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.first_name')" :rules="validationRules.first_name"
                                                  prop="first_name">
                                        <el-input autocomplete="off" type="text" v-model="model.first_name"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.last_name')" :rules="validationRules.last_name"
                                                  prop="last_name">
                                        <el-input autocomplete="off" type="text" v-model="model.last_name"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.resident.birth_date')" :rules="validationRules.birth_date"
                                                  prop="birth_date">
                                        <el-date-picker
                                                format="dd.MM.yyyy"
                                                style="width: 100%;"
                                                type="date"
                                                v-model="model.birth_date"
                                                :picker-options="birthDatePickerOptions"
                                                value-format="yyyy-MM-dd"/>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.language')"
                                                  :rules="validationRules.language"
                                                  prop="settings.language"
                                                  class="label-block">
                                        <select-language :active-language.sync="model.settings.language"/>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item class="label-block"
                                                  :label="$t('models.resident.nation')"
                                                  prop="nation">
                                        <el-select filterable
                                                    clearable
                                                   style="display: block"
                                                   v-model="model.nation">
                                            <el-option :key="country.id"
                                                       :label="country.name"
                                                       :value="country.id"
                                                       v-for="country in countries"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item class="label-block"
                                                  :label="$t('models.resident.type.label')"
                                                  prop="type">
                                        <el-select style="display: block"
                                                   v-model="model.type">
                                            <el-option
                                                :key="k"
                                                :label="$t(`models.resident.type.${type}`)"
                                                :value="parseInt(k)"
                                                v-for="(type, k) in constants.residents.type">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </card>
                        <card class="mt15" :loading="loading" :header="$t('models.resident.contact_info_card')">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.email')" :rules="validationRules.email" prop="email" >
                                        <el-input autocomplete="off" type="email" v-model="model.email"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.resident.mobile_phone')" prop="mobile_phone">
                                        <el-input autocomplete="off" type="text" v-model="model.mobile_phone"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.resident.private_phone')" prop="private_phone">
                                        <el-input autocomplete="off" type="text" v-model="model.private_phone"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.resident.work_phone')" prop="work_phone">
                                        <el-input autocomplete="off"
                                                  type="text"
                                                  v-model="model.work_phone"
                                                  class="dis-autofill"
                                                  readonly
                                                  onfocus="this.removeAttribute('readonly');"
                                        ></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </card>
                    </el-col>
                    <el-col :lg="12" :sm="24">
                        <card :loading="loading" :header="$t('models.resident.account_info_card')">
                            <!--                            <el-form-item :label="$t('models.user.profile_image')">-->
                            <!--                                <cropper :resize="false" :viewportType="'circle'" @cropped="cropped"/>-->
                            <!--                            </el-form-item>-->

                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.password')" :rules="validationRules.password" prop="password">
                                        <el-input autocomplete="off"
                                                  type="password"
                                                  v-model="model.password"
                                                  class="dis-autofill"
                                                  readonly
                                                  onfocus="this.removeAttribute('readonly');"
                                        ></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.confirm_password')" :rules="validationRules.password_confirmation"
                                                  prop="password_confirmation">
                                        <el-input autocomplete="off" type="password"
                                                  v-model="model.password_confirmation"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </card>
                        
                        
                        <card class="mt15 contract-box">
                            <template slot="header">
                                
                                {{ $t('models.resident.contract.title') }}
                                <el-button style="float:right" type="primary" @click="toggleDrawer" icon="icon-plus" size="mini" round>{{$t('models.resident.contract.add')}}</el-button>    
                            
                            </template>
                            
                                <el-table
                                    :data="model.contracts"
                                    style="width: 100%"
                                    class="contract-table"
                                    >
                                    <el-table-column
                                        :label="$t('models.resident.contract.contract_id')"
                                        prop="id"
                                    >
                                        <template slot-scope="scope">
                                            <span class="clickable" @click="editContract(scope.$index)">{{scope.row.contract_format}}</span>
                                        </template>
                                    </el-table-column>
                                    <el-table-column
                                        :label="$t('models.resident.building.name')"
                                        prop="building.name"
                                    >
                                    </el-table-column>
                                    <el-table-column
                                        :label="$t('models.resident.unit.name')"
                                        prop="unit.name"
                                    >
                                    </el-table-column>
                                    <el-table-column
                                        align="right"
                                    >
                                        <template slot-scope="scope">
                                            <el-tooltip
                                                :content="$t('general.actions.edit')"
                                                class="item" effect="light" 
                                                placement="top-end">
                                                    <el-button @click="editContract(scope.$index)" icon="ti-pencil" size="mini" type="success"/>
                                            </el-tooltip>
                                            <el-tooltip
                                                :content="$t('general.actions.delete')"
                                                class="item" effect="light" 
                                                placement="top-end">
                                                    <el-button @click="deleteContract(scope.$index)" icon="ti-trash" size="mini" type="danger"/>
                                            </el-tooltip>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            
                        </card>
                    </el-col>
                </el-row>
            </el-form>
        </div>
        </div>
        <ui-drawer :visible.sync="visibleDrawer" :z-index="1" direction="right" docked>
            <ui-divider content-position="left"><i class="icon-handshake-o ti-user icon"></i> &nbsp;&nbsp;{{ $t('models.resident.contract.title') }}</ui-divider>
            <div class="content" v-if="visibleDrawer">
                <contract-form v-if="editingContract" mode="edit" :data="editingContract" :resident_type="model.type" :resident_id="model.id" :visible.sync="visibleDrawer" :edit_index="editingContractIndex" @update-contract="updateContract" :used_units="used_units"/>
                <contract-form v-else mode="add" :resident_type="model.type" :resident_id="model.id" :visible.sync="visibleDrawer" @add-contract="addContract" :used_units="used_units"/>
            </div>
        </ui-drawer>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import AdminResidentsMixin from 'mixins/adminResidentsMixin';
    import Cropper from 'components/Cropper';
    import AddActions from 'components/EditViewActions';
    import SelectLanguage from 'components/SelectLanguage';
    import ContractForm from 'components/ContractForm';
    import {mapActions, mapGetters} from 'vuex';

    export default {
        mixins: [AdminResidentsMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            Cropper,
            ContractForm,
            AddActions,
            SelectLanguage,
        },
        mounted() {
            this.$root.$on('changeLanguage', () => this.getCountries());
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            })
        }
    }
</script>

<style lang="scss">
    .label-block .el-form-item__label {
        display: block;
        float: none;
        text-align: left;
    }
</style>

<style lang="scss" scoped>
    .residents-add {
        overflow: hidden;
        height: 100%;
        flex: 1;

        .main-content { 
            overflow-x: hidden;
            overflow-y: scroll;
            height: 100%;
        }

        .heading {
            margin-bottom: 20px;
        }

        

        /deep/ .contract-box.el-card {
            .el-card__header {
                display: block;
            }

            .contract-table {
                .clickable {
                    display: block;
                    width: 100%;
                }
            }
        }

        

        .ui-drawer {
            .ui-divider {
                margin: 32px 16px 0 16px;
                i {
                    padding-right: 0;
                }

                /deep/ .ui-divider__content {
                    left: 0;
                    z-index: 1;
                    padding-left: 0;
                    font-size: 16px;
                    font-weight: 700;
                    color: var(--color-primary);
                }
            }
            .content {
                height: calc(100% - 70px);
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                padding: 16px;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-box-orient: vertical;
                -webkit-box-direction: normal;
                -ms-flex-direction: column;
                flex-direction: column;
                position: relative;

                .ui-divider {
                    margin: 32px 16px 16px 0;
                }
                
            }

            .chart-card-header{
                font-size: 16px;
                font-weight: 400;
                padding: 10px 20px;
                margin: 0;
                border-bottom: 1px solid #EBEEF5;

                h3 {
                    font-size: 24px;
                    font-weight: 500;
                }

            }
        }
    }

    .group-name {
        width: 192px;
        text-align: right;
        padding-right: 10px;
        box-sizing: border-box;
        font-size: 16px;
        font-weight: bold;
        color: #6AC06F;
    }

    .mb15 {
        margin-bottom: 15px;
    }

</style>
