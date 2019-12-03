<template>
    <el-form :model="model" :rules="validationRules" label-position="top" ref="form" v-loading="loading" :class="mode == 'edit' ? 'edit-workflow-form' : ''">
        <el-row :gutter="20" style="margin-left: 0; margin-right: 0">
            <el-col :md="24">
                <el-form-item 
                            :rules="validationRules.required"
                            prop="title">
                    <el-input type="text" :placeholder="$t('models.quarter.workflow.placeholders.title')"
                            v-model="model.title"
                    ></el-input>
                </el-form-item>
            </el-col>
        </el-row>
        <el-row :gutter="20" style="margin-left: 0; margin-right: 0">
            <el-col :md="12">
                <el-form-item 
                            :rules="validationRules.required"
                            prop="category_id">
                    <el-select :placeholder="$t('models.quarter.workflow.placeholders.category')" style="display: block" 
                                v-model="model.category_id">
                        <el-option
                                :key="category.id"
                                :label="$t(`models.request.category_list.${category.name}`)"
                                :value="category.id"
                                v-for="category in categories">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-col>
            <!-- <el-col :md="12">
                <el-form-item 
                    :rules="validationRules.required"
                    prop="selectedWorkflowBuilding"
                    class="label-block"
                    >
                    <el-select
                        :loading="remoteLoading"
                        :placeholder="$t('models.quarter.workflow.placeholders.building')"
                        :remote-method="remoteSearchBuildings"
                        class="custom-remote-select"
                        filterable
                        multiple
                        clearable
                        remote
                        reserve-keyword
                        style="width: 100%;"
                        v-model="model.selectedWorkflowBuilding"
                    >
                        <el-option
                            :key="building.id"
                            :label="`${building.address.house_num}`"
                            :value="building.id"
                            v-for="building in model.workflowBuildingList"/>
                    </el-select>
                </el-form-item>
            </el-col> -->
            <el-col :md="12">
                <el-form-item 
                    :rules="validationRules.required"
                    prop="selectedWorkflowBuilding"
                    class="label-block"
                >
                    <multi-select
                        :type="buildingFilter.key"
                        :name="buildingFilter.name"
                        :data="buildingFilter.data"
                        :bgColor="'#DABFCD'"
                        showMultiTag
                        :selectedOptions="model.selectedWorkflowBuilding"
                        @select-changed="handleSelectChange($event, 'building')"
                    >
                    </multi-select>
                </el-form-item>
            </el-col>
        </el-row>
        <!-- <el-row :gutter="20" style="margin-left: 0; margin-right: 0">
            <el-col :md="12">
                <el-form-item 
                            :rules="validationRules.required"
                            prop="selectedWorkflowToUser">
                    <el-select
                        :loading="remoteLoading"
                        :placeholder="$t('models.quarter.workflow.placeholders.to_user')"
                        :remote-method="remoteSearchToUsers"
                        class="custom-remote-select"
                        filterable
                        multiple
                        clearable
                        remote
                        reserve-keyword
                        style="width: 100%;"
                        v-model="model.selectedWorkflowToUser"
                    >
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
                    prop="selectedWorkflowCcUser"
                    class="label-block"
                    >
                    <el-select
                        :loading="remoteLoading"
                        :placeholder="$t('models.quarter.workflow.placeholders.cc_user')"
                        :remote-method="remoteSearchCcUsers"
                        class="custom-remote-select"
                        filterable
                        multiple
                        clearable
                        remote
                        reserve-keyword
                        style="width: 100%;"
                        v-model="model.selectedWorkflowCcUser"
                    >
                        <el-option
                            :key="user.id"
                            :label="`${user.name}`"
                            :value="user.id"
                            v-for="user in model.workflowCcUserList"/>
                    </el-select>
                </el-form-item>
            </el-col>
        </el-row> -->
        <el-row :gutter="20" style="margin-left: 0; margin-right: 0">
            <el-col :md="12">
                <el-form-item 
                    :rules="validationRules.required"
                    prop="selectedWorkflowToUser"
                >
                    <multi-select
                        :type="toUserFilter.key"
                        :name="toUserFilter.name"
                        :data="toUserFilter.data"
                        :bgColor="'#DABFCD'"
                        showMultiTag
                        :selectedOptions="model.selectedWorkflowToUser"
                        @select-changed="handleSelectChange($event, 'to_user')"
                    >
                    </multi-select>
                </el-form-item>
            </el-col>
            <el-col :md="12">
                <el-form-item 
                    prop="selectedWorkflowCcUser"
                    class="label-block"
                    >
                    <multi-select
                        :type="ccUserFilter.key"
                        :name="ccUserFilter.name"
                        :data="ccUserFilter.data"
                        :bgColor="'#DABFCD'"
                        showMultiTag
                        :selectedOptions="model.selectedWorkflowCcUser"
                        @select-changed="handleSelectChange($event, 'cc_user')"
                    >
                    </multi-select>
                </el-form-item>
            </el-col>
        </el-row>
        <el-row :gutter="20" style="margin-top: 10px; margin-left: 0; margin-right: 0">
            <el-col :md="24" class="drawer-actions">
                <el-button type="default" size="mini" icon="icon-cancel" @click="close" class="round-btn">&nbsp;{{ $t('models.quarter.workflow.close') }}</el-button>
                <el-button v-if="mode=='edit'" type="danger" size="mini" icon="icon-trash-empty" @click="deleteItem" class="round-btn">&nbsp;{{ $t('models.quarter.workflow.delete') }}</el-button>
                <!-- <el-tooltip
                        :content="$t('models.quarter.workflow.tooltips.save')"
                        class="item" effect="light" placement="top-end"
                    > -->
                <el-button type="primary" size="mini" @click="submit" icon="icon-floppy" class="round-btn">&nbsp;{{ $t('general.actions.save') }}</el-button>
                <!-- </el-tooltip> -->
            </el-col>
        </el-row>
        

    </el-form>
</template>

<script>
    import {displayError, displaySuccess} from "../helpers/messages";
    import {mapActions, mapGetters} from 'vuex';
    import MultiSelect from 'components/Select';

    export default {
        name: "WorkflowForm",
        components: {
            MultiSelect,
        },
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
                    category_id: null,
                    selectedWorkflowBuilding: [],
                    workflowBuildingList: [],
                    workflowToUserList: [],
                    selectedWorkflowToUser: [],
                    workflowCcUserList: [],
                    selectedWorkflowCcUser: [],
                },
                categories: [],
                validationRules: {
                    required: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                },
                remoteLoading: false,
                buildings:[],
                users: []
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
                        let buildings = []
                        let to_users = []
                        let cc_users = []

                        let category = this.categories.find(item => item.id == this.model.category_id)

                        // this.model.selectedWorkflowBuilding.map( building_id => {
                        //     let item = this.model.workflowBuildingList.find(item => item.id == building_id)
                        //     buildings.push(item)
                        // })
                        
                        // this.model.selectedWorkflowToUser.map( user_id => {
                        //     let item = this.model.workflowToUserList.find(item => item.id == user_id)
                        //     to_users.push(item)
                        // })

                        // this.model.selectedWorkflowCcUser.map( user_id => {
                        //     let item = this.model.workflowCcUserList.find(item => item.id == user_id)
                        //     cc_users.push(item)
                        // })

                        this.model.selectedWorkflowBuilding.map( building_id => {
                            let item = this.buildings.find(item => item.id == building_id)
                            buildings.push(item)
                        })
                        
                        this.model.selectedWorkflowToUser.map( user_id => {
                            let item = this.users.find(item => item.id == user_id)
                            to_users.push(item)
                        })

                        this.model.selectedWorkflowCcUser.map( user_id => {
                            let item = this.users.find(item => item.id == user_id)
                            cc_users.push(item)
                        })
                        
                        let payload = {
                            title: this.model.title,
                            category_id: this.model.category_id,
                            category: category,
                            building_ids: this.model.selectedWorkflowBuilding,
                            buildings: buildings,
                            to_user_ids: this.model.selectedWorkflowToUser,
                            to_users: to_users,
                            cc_user_ids: this.model.selectedWorkflowCcUser,
                            cc_users: cc_users
                        }

                        if(this.id) 
                        {
                            payload.id = this.id
                        }

                        if(this.mode == 'add') {
                            this.$emit('add-workflow', payload);
                        }
                        else {
                            this.$emit('update-workflow', this.editing_index, payload);
                        }
                    }
                }
                catch(err) {
                    console.log(err)
                }
            },
            close() {
                if(this.mode == 'add') {
                    this.$emit('cancel-add-workflow')
                }
                else {
                    this.$emit('cancel-edit-workflow', this.editing_index)
                }
            },
            deleteItem() {
                if(this.mode == 'edit') {
                    this.$emit('delete-workflow', this.editing_index)
                }
            },
            handleSelectChange(val, filter) {
                if(filter == 'building') {
                    this.model.selectedWorkflowBuilding = val
                }
                else if(filter == 'to_user') {
                    this.model.selectedWorkflowToUser = val
                }
                else if(filter == 'cc_user') {
                    this.model.selectedWorkflowCcUser = val
                }
                
            },
            async fetchRemoteBuildings(search = '') {
                let buildings = await this.getBuildings({get_all: true, quarter_id: this.quarter_id, search});

                buildings.data.map(building => {
                    building.name = building.address.street + ' ' + building.address.house_num
                })
                return buildings.data
            },
            async fetchRemoteUsers(search = '') {
                const users = await this.getAllAdminsForQuarter({quarter_id: this.quarter_id, search})

                return users
            },
            async remoteSearchBuildings(search) {
                if (search === '') {
                    this.resetBuildingList();
                } else {
                    this.remoteLoading = true;

                    try {
                        let resp = await this.getBuildings({
                            get_all: true,
                            quarter_id: this.quarter_id,
                            search
                        });

                        resp.data.map(building => {
                            building.name = building.address.street + ' ' + building.address.house_num
                        })
                        

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
                        const resp = await this.getAllAdminsForQuarter({quarter_id: this.quarter_id, search})

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
                        const resp = await this.getAllAdminsForQuarter({quarter_id: this.quarter_id, search})
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
        computed: {
            buildingFilter() {
                return {
                        name: this.$t('models.quarter.workflow.placeholders.building'),
                        type: 'select',
                        key: 'house_num',
                        data: this.buildings,
                        remoteLoading: false,
                        fetch: this.fetchRemoteBuildings
                }
            },
            toUserFilter() {
                return {
                        name: this.$t('models.quarter.workflow.placeholders.to_user'),
                        type: 'select',
                        key: 'name',
                        data: this.users,
                        remoteLoading: false,
                        fetch: this.fetchRemoteUsers
                }
            },
            ccUserFilter() {
                return {
                        name: this.$t('models.quarter.workflow.placeholders.cc_user'),
                        type: 'select',
                        key: 'name',
                        data: this.users,
                        remoteLoading: false,
                        fetch: this.fetchRemoteUsers
                }
            }
        },
        async created () {
            this.loading = true;
            
            this.buildings = await this.fetchRemoteBuildings();
            this.buildings.map(building => {
                building.name = building.address.house_num;
                if(building.address)
                    building.house_num = building.address.house_num
            })
            this.users = await this.fetchRemoteUsers();
            this.categories = this.$constants.requests.categories_data.tree

            if(this.mode == 'edit') {
                this.model.title = this.data.title
                this.model.category_id = this.data.category_id
                this.$set(this.model, 'selectedWorkflowBuilding', this.data.building_ids)
                this.$set(this.model, 'selectedWorkflowToUser', this.data.to_user_ids)
                this.$set(this.model, 'selectedWorkflowCcUser', this.data.cc_user_ids)
                this.model.workflowBuildingList = this.data.buildings
                this.model.workflowToUserList = this.data.to_users
                this.model.workflowCcUserList = this.data.cc_users
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
                margin-bottom: 20px;
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
            padding-bottom: 10px;
            margin-top: 30px;
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
            height: 30px;
            line-height: 30px;

            i {
                color: white;
                background: transparent;
                font-size: 17px;
                font-weight: 600;
            }
        }

        /deep/ .el-select__tags {
            padding-left: 5px;

            input {
                margin-left: 0;
            }

        }

        /deep/ input {
            border: none;
            background: #f6f5f7;
        }

        .el-select.custom-remote-select {

            /deep/ .el-input__suffix {
                i.el-select__caret {
                    height: 41px;
                    display: block;
                    &::after {
                        color:#565556;
                        content: "\E6DB";
                        display: block !important;
                        position: absolute;
                        left: 5px;
                    }
                    &::before {
                        color:#565556;
                    }
                }

                .el-icon-circle-close::after {
                    display: none !important;
                }
                .el-icon-circle-close::before {
                    display: none !important;
                }
            }

            /deep/ .el-select__input {
                background: transparent;
                padding-left: 10px;
            }
        }

        /deep/ .el-row {
            .el-col {
                margin-bottom: 3px;
            }
            
        }
        

        /deep/ .el-tag.el-tag--info .el-tag__close:hover {
            background: transparent;
        }

        

    }

    
    /deep/ .el-select .el-input .el-select__caret {
        color: #565556;
    }

    .el-col {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    @media only screen and (min-width:992px){
        .el-row {
            .el-col-md-12:first-child {
                padding-right: 5px !important;
            }

            .el-col-md-12:last-child {
                padding-left: 5px !important;
            }
        }
    }
    
    .ti-save {
        margin-right: 5px;
    }
    
</style>
