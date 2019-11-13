<template>
    <el-form :model="model" :rules="validationRules" label-position="top" ref="form" v-loading="loading">
        <el-row :gutter="20">
            <el-col :md="24">
                <el-form-item 
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
                <el-form-item 
                            :rules="validationRules.category"
                            prop="category">
                    <el-select :placeholder="$t('general.placeholders.select')" style="display: block" 
                                v-model="model.category">
                        <el-option
                                :key="category.id"
                                :label="$t(`models.request.category_list.${category.name}`)"
                                :value="category.id"
                                v-for="category in categories">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col :md="12">
                <el-form-item 
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
                <el-form-item 
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
                <el-form-item 
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
        <el-row :gutter="20" style="margin-top: 10px;">
            <el-col :md="24" class="drawer-actions">
                <el-button type="primary" @click="submit" icon="ti-save" round>&nbsp;{{ $t('general.actions.save') }}</el-button>
            </el-col>
        </el-row>
        

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
            },
            mode: {
                type: String
            },
            editing_index: {
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
                    selectedWorkflowBuildingData: [],
                    workflowBuildingList: [],
                    workflowToUserList: [],
                    selectedWorkflowToUser: [],
                    selectedWorkflowToUserData: [],
                    workflowCcUserList: [],
                    selectedWorkflowCcUser: [],
                    selectedWorkflowCcUserData: [],
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
                'getAllAdminsForQuarter']),
            async submit () {
                try {
                    const valid = await this.$refs.form.validate();
                    if (valid) {

                        this.model.selectedWorkflowBuilding.map( building_id => {
                            let item = this.model.workflowBuildingList.find(item => item.id == building_id)
                            this.model.selectedWorkflowBuildingData.push(item)
                        })
                        
                        this.model.categoryData = this.categories.find(item => item.id == this.model.category)

                        let to_users = []

                        let cc_users = []

                        this.model.selectedWorkflowToUser.map( user_id => {
                            let item = this.model.workflowToUserList.find(item => item.id == user_id)
                            to_users.push(item)
                        })

                        this.model.selectedWorkflowCcUser.map( user_id => {
                            let item = this.model.workflowCcUserList.find(item => item.id == user_id)
                            cc_users.push(item)
                        })

                        let payload = {
                            title: this.model.title,
                            category_id: this.model.category,
                            category: this.model.categoryData,
                            building_ids: this.model.selectedWorkflowBuilding,
                            buildings: this.model.selectedWorkflowBuildingData,
                            to_user_ids: this.model.selectedWorkflowToUser,
                            to_users: to_users,
                            cc_user_ids: this.model.selectedWorkflowCcUser,
                            cc_users: cc_users
                        }

                        if(this.mode == 'add') {
                            this.$emit('add-workflow', payload);
                        }
                        else {
                            this.$emit('update-workflow', this.editing_index, payload);
                        }
                        // const resp = await this.saveBuildingEmailReceptionists(payload)

                        // if(resp.success)
                        // {
                        //     displaySuccess(resp);

                        // }


                        
                        this.$emit('update:visible', false);

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
                        const resp = await this.getAllAdminsForQuarter({quarter_id: this.quarter_id})

                        this.model.workflowToUserList = resp;
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
                        const resp = await this.getAllAdminsForQuarter({quarter_id: this.quarter_id})
                        this.model.workflowCcUserList = resp;
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
        
            this.loading = true;
            
            this.categories = this.$constants.requests.categories_data.tree


            console.log(this.data);

            if(this.mode == 'edit') {
                this.model.title = this.data.title
                this.model.category = this.data.category_id
                this.model.selectedWorkflowBuilding = this.data.building_ids
                this.model.selectedWorkflowToUser = this.data.to_user_ids
                this.model.selectedWorkflowCcUser = this.data.cc_user_ids
            }
            
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
            
            display: flex;
            justify-content: flex-end;

        }
    }
</style>
