<template>
    <el-form :model="model" :rules="validationRules" label-position="top" ref="form" v-loading="loading">
        <el-row :gutter="20">
            <el-col :md="24">
                <el-form-item :label="$t('general.title')"
                            :rules="validationRules.title"
                            prop="title">
                    <el-input type="text"
                            v-model="model.title"
                    ></el-input>
                </el-form-item>
            </el-col>
        </el-row>
        <el-row :gutter="20">
            <el-col :md="12">
                <el-form-item :label="$t('models.request.category')"
                            :rules="validationRules.category"
                            prop="category">
                    <el-select :placeholder="$t('general.placeholders.select')" style="display: block" 
                                v-model="model.category">
                        <el-option
                                :key="category.value"
                                :label="$t(`models.request.category_list.${category.name}`)"
                                :value="category.value"
                                v-for="category in categories">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col :md="12">
                <el-form-item :label="$t('models.resident.building.name')"
                    :rules="validationRules.building"
                    prop="selectedWorkflowBuilding"
                    class="label-block"
                    >
                    <el-select
                        :loading="remoteLoading"
                        :placeholder="$t('general.placeholders.search')"
                        :remote-method="remoteSearchBuildings"
                        class="custom-remote-select"
                        filterable
                        multiple
                        remote
                        reserve-keyword
                        style="width: 100%;"
                        v-model="model.selectedWorkflowBuilding"
                    >
                        <div class="custom-prefix-wrapper" slot="prefix">
                            <i class="el-icon-search custom-icon"></i>
                        </div>
                        <el-option
                            :key="building.id"
                            :label="`${building.name}`"
                            :value="building.id"
                            v-for="building in model.workflowBuildingList"/>
                    </el-select>
                </el-form-item>
            </el-col>
        </el-row>
        <el-row :gutter="20">
            <el-col :md="12">
                <el-form-item :label="$t('models.request.mail.to')"
                            :rules="validationRules.category"
                            prop="selectedWorkflowToUser">
                    <el-select
                        :loading="remoteLoading"
                        :placeholder="$t('general.placeholders.search')"
                        :remote-method="remoteSearchToUsers"
                        class="custom-remote-select"
                        filterable
                        multiple
                        remote
                        reserve-keyword
                        style="width: 100%;"
                        v-model="model.selectedWorkflowToUser"
                    >
                        <div class="custom-prefix-wrapper" slot="prefix">
                            <i class="el-icon-search custom-icon"></i>
                        </div>
                        <el-option
                            :key="user.id"
                            :label="`${user.name}`"
                            :value="user.id"
                            v-for="user in model.workflowToUserList"/>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col :md="12">
                <el-form-item :label="$t('models.request.mail.cc')"
                    :rules="validationRules.building"
                    prop="selectedWorkflowCcUser"
                    class="label-block"
                    >
                    <el-select
                        :loading="remoteLoading"
                        :placeholder="$t('general.placeholders.search')"
                        :remote-method="remoteSearchCcUsers"
                        class="custom-remote-select"
                        filterable
                        multiple
                        remote
                        reserve-keyword
                        style="width: 100%;"
                        v-model="model.selectedWorkflowCcUser"
                    >
                        <div class="custom-prefix-wrapper" slot="prefix">
                            <i class="el-icon-search custom-icon"></i>
                        </div>
                        <el-option
                            :key="user.id"
                            :label="`${user.name}`"
                            :value="user.id"
                            v-for="user in model.workflowCcUserList"/>
                    </el-select>
                </el-form-item>
            </el-col>
        </el-row>
        
        <div class="drawer-actions">
            <el-button type="primary" @click="submit" icon="ti-save" round>{{$t('general.actions.save')}}</el-button>
        </div>
        

    </el-form>
</template>

<script>
    import {displayError, displaySuccess} from "../helpers/messages";
    import {mapActions, mapGetters} from 'vuex';

    export default {
        name: "WorkflowForm",
        props: {
            data: {
                type: Object
            },
            visible: {
                type: Boolean,
                default: false
            },
            quarter_id: {
                type: Number
            }
        },
        data () {
            return {
                remoteLoading: false,
                loading: false,
                global: '',
                model: {
                    title: '',
                    category: null,
                    assignList: '',
                    assign: [],
                    selectedWorkflowBuilding: [],
                    workflowBuildingList: [],
                    workflowToUserList: [],
                    selectedWorkflowToUser: [],
                    workflowCcUserList: [],
                    selectedWorkflowCcUser: [],
                },
                categories: [],
                validationRules: {
                    title: [{
                        required: true,
                        message: this.$t('models.quarter.required')
                    }],
                    category: [{
                        required: true,
                        message: this.$t('models.quarter.required')
                    }],
                    building: [{
                        required: true,
                        message: this.$t('models.quarter.required')
                    }],
                },
                remoteLoading: false,
                
                
                
            }
        },
        methods: {
            ...mapActions([
                'getPropertyManagers', 
                'getBuildings',
                'getUsers']),
            async submit () {
                try {
                    const valid = await this.$refs.form.validate();
                    if (valid) {
                        // let global_email_receptionist = null
                        // if(this.activeCommand == 'global')
                        //     global_email_receptionist = true
                        // else if(this.activeCommand == 'assign')
                        //     global_email_receptionist = false
                        
                        // let categories = [];
                        // for(let i = 0; i < this.categories.length; i ++)
                        // {
                        //     categories.push({
                        //         category : +this.categories[i].id,
                        //         property_manager_ids: this.model.assign[i]
                        //     })

                        // }

                        // if(this.isBuilding == true) {

                        //     let payload = {
                        //         building_id : this.building_id,
                        //         global_email_receptionist,
                        //         categories
                        //     }
                            

                        //     const resp = await this.saveBuildingEmailReceptionists(payload)

                        //     if(resp.success)
                        //     {
                        //         displaySuccess(resp);
                        //         const data = await this.getBuilding({id: this.$route.params.id});

                        //         if(data.has_email_receptionists) {
                        //             this.$emit('update-has-email-receptionists', true);
                        //         }
                        //     }
                        //     this.$emit('update:visible', false);
                        // }
                        // else {
                        //     let payload = {
                        //         quarter_id : this.quarter_id,
                        //         categories: categories
                        //     }

                        //     const resp = await this.saveQuarterEmailReceptionists(payload)

                        //     if(resp.success)
                        //         displaySuccess(resp);
                            
                        //     this.$emit('update:visible', false);
                        // }
                        
                    }
                }
                catch(err) {
                    console.log(err)
                }
            },
            async remoteSearchManagers(search, index) {
                if (search === '') {
                    this.resetToAssignList(index);
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getPropertyManagers({
                            get_all: true,
                            search
                        });

                        this.model.assignList[index] = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            resetToAssignList(index) {
                this.model.assignList[index] = [];
                this.model.assign[index] = '';
            },
            async remoteSearchBuildings(search) {
                if (search === '') {
                    this.resetBuildingList();
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getBuildings({
                            get_all: true,
                            quarter_id: this.quarter_id,
                            search
                        });

                        this.model.workflowBuildingList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            resetBuildingList() {
                this.model.workflowBuildingList = [];
                this.model.selectedWorkflowBuilding = [];
            },
            async remoteSearchToUsers(search) {
                if (search === '') {
                    this.resetToUserList();
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getUsers({
                            get_all: true,
                            get_role: true,
                            search,
                            roles: ['manager', 'administrator', 'provider']
                        });


                        this.model.workflowToUserList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            resetToUserList() {
                this.model.workflowToUserList = [];
                this.model.selectedWorkflowToUser = [];
            },
            async remoteSearchCcUsers(search) {
                if (search === '') {
                    this.resetCcUserList();
                } else {
                    this.remoteLoading = true;

                    try {
                       const resp = await this.getUsers({
                            get_all: true,
                            get_role: true,
                            search,
                            roles: ['manager', 'administrator', 'provider']
                        });

                        this.model.workflowCcUserList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            resetCcUserList() {
                this.model.workflowCcUserList = [];
                this.model.selectedWorkflowCcUser = [];
            },
            
        },
        async created () {
            let categories = this.$constants.requests.categories_data.categories
            this.loading = true;
            this.categories = Object.keys(categories).map(key => {
                return { id : key, name : categories[key] }
            })

            
            this.loading = false;
            
        }
    }
</script>

<style lang="scss" scoped>
    .el-form {
        display: flex;
        flex-direction: column;
        height: 100%;
        
        .el-select {
            width: 100%;
        }
        
        /deep/ .ui-divider {
            margin: 32px 16px 16px 0;
            
            i {
                padding-right: 0;
            }

            .ui-divider__content {
                left: 0;
                z-index: 1;
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

            &.info-label {
                /deep/ .el-form-item__label {
                    line-height: 2em;
                    margin-bottom: 1em;
                }
            }
        }

        /deep/ .el-input.el-input-group {
            .el-input-group__prepend {
                padding: 2px 8px 0;
                font-weight: 600;
            }
            
        }
        
        .el-dropdown {
            width: 100%;
            

            /deep/ .el-button-group {
                width: 100%;

                
                .el-button:first-child {
                    width: calc(100% - 30px);
                    border-radius: 20px;
                    border-top-right-radius: unset;
                    border-bottom-right-radius: unset;
                }

                .el-button:last-child {
                    border-radius: 20px;
                    border-top-left-radius: unset;
                    border-bottom-left-radius: unset;
                }
            }
        }

        /deep/ .drawer-actions {
            width: 100%;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;

            button {
                width: 100%;
                i {
                    padding-right: 5px;
                }
            }
        }
    }
</style>
