<template>
    
        <div class="residents-edit mb20 residents-edit-new">
            <div class="main-content">
                <heading :title="$t('models.resident.edit_title')" icon="icon-group">
                    <template slot="description" v-if="model.resident_format">
                        <div class="subtitle">{{model.resident_format}}</div>
                    </template>
                    <edit-actions :saveAction="submit" route="adminResidents"/>
                </heading>
                <el-row :gutter="20" class="crud-view">
                    <el-col>
                        <el-form :model="model" label-position="top" label-width="192px" ref="form">
                            <el-row  :gutter="20">
                                <el-col>
                                <el-card class="chart-card">
                                        <el-row :gutter="20">
                                            <h3 class="chart-card-header">
                                                <i class="ti-user"/>
                                            {{ $t('models.resident.personal_details_card') }}
                                            </h3>
                                        </el-row>
                                        <el-row :gutter="20">
                                            <el-col :md="4" class="resident_avatar">
                                                <cropper :resize="false" :viewportType="'circle'" @cropped="cropped"/>
                                                <img
                                                    src="~img/man.png"
                                                    class="user-image"
                                                    v-if="model.avatar==null && model.title == 'mr'"/>
                                                <img
                                                    src="~img/woman.png"
                                                    class="user-image"
                                                    v-else-if="model.avatar==null && model.title == 'mrs'"/>
                                                <img
                                                    src="~img/company.png"
                                                    class="user-image"
                                                    v-else-if="model.avatar==null && model.title == 'company'"/>
                                                <img :src="`/${user.avatar}?${Date.now()}`"
                                                    class="user-image"
                                                    v-if="avatar.length == 0 && user.avatar">

                                            </el-col>
                                            <el-col :md="20">
                                                    <el-col :md="6">
                                                        <el-form-item :label="$t('general.salutation')" :rules="validationRules.title"
                                                                    prop="title">
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
                                                    <el-col :md="6">
                                                        <el-form-item :label="$t('models.resident.company')" :rules="validationRules.company"
                                                                    prop="company"
                                                                    v-if="model.title === titles.company">
                                                            <el-input autocomplete="off" type="text" v-model="model.company"></el-input>
                                                        </el-form-item>
                                                    </el-col>
                                                    <el-col :md="6">
                                                        <el-form-item :label="$t('general.firstName')"
                                                                    :rules="validationRules.first_name"
                                                                    prop="first_name">
                                                            <el-input autocomplete="off" type="text" v-model="model.first_name"></el-input>
                                                        </el-form-item>
                                                    </el-col>
                                                    <el-col :md="6">
                                                        <el-form-item :label="$t('general.lastName')"
                                                                    :rules="validationRules.last_name"
                                                                    prop="last_name">
                                                            <el-input autocomplete="off" type="text" v-model="model.last_name"></el-input>
                                                        </el-form-item>
                                                    </el-col>
                                                    <el-col :md="6">
                                                        <el-form-item :label="$t('models.resident.birth_date')"
                                                                    :rules="validationRules.birth_date"
                                                                    prop="birth_date">
                                                            <el-date-picker
                                                                    :placeholder="$t('models.resident.birth_date')"
                                                                    format="dd.MM.yyyy"
                                                                    style="width: 100%;"
                                                                    type="date"
                                                                    v-model="model.birth_date"
                                                                    :picker-options="birthDatePickerOptions"
                                                                    value-format="yyyy-MM-dd"/>
                                                        </el-form-item>
                                                    </el-col>
                                                    <el-col :md="6">
                                                        <el-form-item :label="$t('general.language')" :rules="validationRules.language" 
                                                                prop="settings.language">
                                                            <select-language :activeLanguage.sync="model.settings.language" />
                                                        </el-form-item>
                                                    </el-col>
                                                    <el-col :md="6">
                                                        <el-form-item :label="$t('models.resident.nation')"
                                                                    prop="nation">
                                                            <el-select filterable
                                                                    clearable
                                                                    v-model="model.nation">
                                                                <el-option :key="country.id"
                                                                        :label="country.name"
                                                                        :value="country.id"
                                                                        v-for="country in countries"></el-option>
                                                            </el-select>
                                                        </el-form-item>
                                                    </el-col>
                                            </el-col>
                                        </el-row>
                                </el-card>
                                </el-col>
                                <el-col :md="12">
                                <el-card class="chart-card">
                                        <el-row :gutter="20">
                                            <h3 class="chart-card-header">
                                                <i class="ti-user"/>
                                                {{ $t('models.resident.contact_info_card') }}
                                            </h3>
                                            <el-col :md='12'>
                                                <el-form-item :label="$t('models.resident.mobile_phone')" prop="mobile_phone">
                                                    <el-input autocomplete="off" type="text"
                                                            v-model="model.mobile_phone"></el-input>
                                                </el-form-item>
                                            </el-col>
                                            <el-col :md='12'>
                                                <el-form-item :label="$t('models.resident.private_phone')" prop="private_phone">
                                                    <el-input autocomplete="off" type="text"
                                                            v-model="model.private_phone"></el-input>
                                                </el-form-item>
                                            </el-col>
                                        </el-row>
                                        <el-row class="last-form-row" :gutter="20">
                                            <el-col :md='12'>
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
                                            <el-col :md='12'>

                                            </el-col>
                                        </el-row>
                                </el-card>
                                </el-col>
                                <el-col :md='12'>
                                    <el-card class="chart-card">
                                        <el-row :gutter="20">
                                            <h3 class="chart-card-header">
                                                <i class="ti-user"/>
                                                {{ $t('models.resident.account_info_card') }}
                                            </h3>
                                            <el-col :md="12">
                                                <el-form-item :label="$t('general.email')" :rules="validationRules.email" prop="email">
                                                    <el-input autocomplete="off" type="email" v-model="model.email"></el-input>
                                                </el-form-item>
                                            </el-col>
                                            <el-col :md="12">
                                                <el-form-item :label="$t('models.resident.status.label')"
                                                            :rules="validationRules.status"
                                                            prop="status">
                                                    <el-select style="display: block" v-model="model.status">
                                                        <el-option
                                                                :key="k"
                                                                :label="$t(`models.resident.status.${status}`)"
                                                                :value="parseInt(k)"
                                                                v-for="(status, k) in constants.residents.status">
                                                        </el-option>
                                                    </el-select>
                                                </el-form-item>
                                            </el-col>
                                        </el-row>
                                        <el-row class="last-form-row" :gutter="20">
                                            <el-col :md="12">
                                                <el-form-item :label="$t('general.password')" :rules="validationRules.password"
                                                            prop="password">
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
                                                <el-form-item :label="$t('general.confirm_password')"
                                                            :rules="validationRules.password_confirmation"
                                                            prop="password_confirmation">
                                                    <el-input autocomplete="off" type="password"
                                                            v-model="model.password_confirmation"></el-input>
                                                </el-form-item>
                                            </el-col>
                                        </el-row>
                                    </el-card>
                                </el-col>
                            </el-row>
                        </el-form>
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-card :loading="loading" class="chart-card">
                                        <el-row :gutter="20">
                                            <h3 class="chart-card-header">
                                                <i class="icon-handshake-o ti-user icon "/>
                                                    &nbsp;{{ $t('models.resident.contract.title') }}
                                                <el-button style="float:right" type="primary" @click="toggleDrawer" icon="icon-plus" size="mini" round>{{$t('models.resident.contract.add')}}</el-button>    
                                            </h3>
                                            
                                        </el-row>
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

                                </el-card>
                            </el-col>

                        </el-row>

                    </el-col>
                </el-row>
            </div>
            <ui-drawer :visible.sync="visibleDrawer" :z-index="1" direction="right" docked>
                <ui-divider content-position="left"><i class="icon-handshake-o ti-user icon"></i> &nbsp;&nbsp;{{ $t('models.resident.contract.title') }}</ui-divider>
                
                <div class="content" v-if="visibleDrawer">
                    <contract-form v-if="editingContract" :hide-building-and-units="false" mode="edit" :data="editingContract" :resident_id="model.id" :visible.sync="visibleDrawer" :edit_index="editingContractIndex" @update-contract="updateContract" :used_units="used_units"/>
                    <contract-form v-else mode="add" :resident_id="model.id" :visible.sync="visibleDrawer" @add-contract="addContract" :used_units="used_units"/>
                </div>
            </ui-drawer>
        </div>
        
    
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import RawGridStatisticsCard from 'components/RawGridStatisticsCard';
    import CircularProgressStatisticsCard from 'components/CircularProgressStatisticsCard';
    import ColoredStatisticsCard from 'components/ColoredStatisticsCard.vue';
    import ProgressStatisticsCard from 'components/ProgressStatisticsCard.vue';
    import AdminResidentsMixin from 'mixins/adminResidentsMixin';
    import ContractForm from 'components/ContractForm';
    import {mapActions, mapGetters} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import Cropper from 'components/Cropper';
    import EditActions from 'components/EditViewActions';
    import SelectLanguage from 'components/SelectLanguage';

    const mixin = AdminResidentsMixin({
        mode: 'edit'
    });

    export default {
        mixins: [mixin],
        components: {
            Heading,
            Card,
            RawGridStatisticsCard,
            CircularProgressStatisticsCard,
            ColoredStatisticsCard,
            ProgressStatisticsCard,
            Cropper,
            EditActions,
            SelectLanguage,
            ContractForm
        },
        methods: {
            pickFile(){
                this.$refs.userImage.click()
            },
            onFilePicked(e){
                const files = e.target.files
                if(files[0]!==undefined){
                    this.model.avatar = files[0]
                    const fr = new FileReader()
                    fr.readAsDataURL(files[0])
                    fr.addEventListener('load', () => {
                        this.avatar = fr.result
                    })

                }
            },
            cropped(d) {
                this.avatar = d
            },
            ...mapActions(['deleteMediaFile', 'downloadResidentCredentials', 'sendResidentCredentials']),
            deleteMedia() {
                this.deleteMediaFile({
                    id: this.model.id,
                    media_id: this.lastMedia.id
                }).then(r => {
                    displaySuccess(r);

                    this.model.media.splice(-1, 1);
                }).catch(err => {
                    displayError(err);
                });
            },
            requestEditView(request) {
                this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id: request.id
                    }
                })
            },
            pinboardEditView(pinboard) {
                this.$router.push({
                    name: 'adminPinboardEdit',
                    params: {
                        id: pinboard.id
                    }
                })
            },
            listingEditView(listing) {
                this.$router.push({
                    name: 'adminListingsEdit',
                    params: {
                        id: listing.id
                    }
                })
            },
            async downloadCredentials() {
                this.loading.state = true;
                try {
                    const resp = await this.downloadResidentCredentials({id: this.model.id});
                    if (resp && resp.data) {
                        const url = window.URL.createObjectURL(new Blob([resp.data], {type: resp.headers['content-type']}));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', resp.headers['content-disposition'].split('filename=')[1]);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(url);
                    }
                } catch (e) {
                    displayError(e)
                } finally {
                    this.loading.state = false;
                }
            },
            async sendCredentials() {
                this.loading.state = true;
                try {
                    const resp = await this.sendResidentCredentials({id: this.model.id});
                    if (resp && resp.data) {
                        displaySuccess(resp.data);
                    }
                } catch (e) {
                    displayError(e);
                } finally {
                    this.loading.state = false;
                }
            },
            requestStatusBadge(status) {
                const colorObject = {
                    1: '#bbb',
                    2: '#ebb563',
                    3: '#ebb563',
                    4: '#67C23A',
                    5: '#ebb563',
                    6: '#67C23A'
                };

                return colorObject[status];
            },
            requestStatusLabel(status) {
                return this.$t(`models.request.status.${this.requestStatusConstants[status]}`)
            }
        },
        mounted() {
            this.$root.$on('changeLanguage', () => this.getCountries());
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
            lastMedia() {
                return this.model.media[this.model.media.length - 1];
            },
            requestStatusConstants() {
                return this.constants.requests.status
            },
            
        }
    }
</script>
<style lang="scss">
    .el-tabs--border-card {
        border-radius: 6px;
        .el-tabs__header {
            border-radius: 6px 6px 0 0;
        }
        .el-tabs__nav-wrap.is-top {
            border-radius: 6px 6px 0 0;
        }
    }

    .last-form-row {
        margin-bottom: -22px;
    }

    .residents-edit-new{

        .chart-card{

            .left-col{
                padding-left: 10px!important;
            }

            .right-col{
                padding-right: 10px!important;
            }
        }

        .user-image{
            max-width: 170px;
        }

        .chart-card-header{
            font-size: 16px;
            font-weight: 400;
            padding: 0 20px 16px;
            margin: -4px -10px 10px;
            border-bottom: 1px solid #EBEEF5;

            h3 {
                font-size: 24px;
                font-weight: 500;
            }

        }
    }
</style>

<style lang="scss" scoped>
    
    .residents-edit {
        overflow: hidden;
        flex: 1;

        .main-content { 
            overflow-x: hidden;
            overflow-y: scroll;
            height: 100%;
        }

        .heading {
            margin-bottom: 20px;
        }

        .chart-card{
            margin-bottom: 30px!important;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ededed;
            border-radius: 4px;
            box-shadow: 0 1px 1px 0 transparentize(#000, .2);
        }
        // .user-info {
        //     border-left: 2px dashed #ccc;
        // }
        > .el-row > .el-col {
            &:first-of-type .el-card:not(:last-of-type) {
                margin-bottom: 2em;
            }

            &:last-of-type {
                > *:not(:last-of-type) {
                    margin-bottom: 1em;
                }
            }
        }
        .edit-user-image{
            position: absolute;
            right: 0;
            bottom: 5px;
            font-size: 18px;
            background-color: transparentize(#000, .5);
            color: white;
            padding: 5px 10px;
            cursor: pointer;
        }
        #language_select {
            margin-left: 0px !important;
            margin-right: 0px !important;
        }
        .resident_avatar {
            img {
                border-radius: 50%;
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

            }
        }

        .contract-table {
            .clickable {
                display: block;
                width: 100%;
            }
        }
    }
</style>
