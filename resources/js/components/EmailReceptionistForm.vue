<template>
    <el-form :model="model" :rules="validationRules" label-position="top" ref="form" v-loading="loading">
        
        <el-row :gutter="20" v-if="isBuilding && quarter_id">
            <el-col :md="24">
                <el-form-item :label="$t('general.email_receptionist.info_desc')"
                        class="label-block info-label"
                >
                    <el-select
                        :placeholder="$t('general.placeholders.search')"
                        class="custom-select"
                        v-model="activeCommand"
                    >
                        <el-option
                            :label="$t('general.email_receptionist.global')"
                            value="global">
                        </el-option>
                        <el-option
                            :label="$t('general.email_receptionist.assign')"
                            value="assign">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-col>
        </el-row>
        <template v-if="!loading && activeCommand == 'assign'">
            <el-row v-for="(category, $index) in categories" :key="category.id">
                <el-col :md="24">
                    <el-form-item :label="$t('general.email_receptionist.email_receptionist_of', {category: $t(`models.request.category_list.${category.name}`) })"
                        :rules="validationRules.assign"
                        :prop="'assign.' + $index"
                        class="label-block"
                        >
                        <el-select
                            :loading="remoteLoading"
                            :placeholder="$t('general.placeholders.search')"
                            :remote-method="data => remoteSearchManagers(data, $index)"
                            class="custom-remote-select"
                            filterable
                            multiple
                            remote
                            clearable
                            reserve-keyword
                            style="width: 100%;"
                            v-model="model.assign[$index]"
                        >
                            <div class="custom-prefix-wrapper" slot="prefix">
                                <i class="el-icon-search custom-icon"></i>
                            </div>
                            <el-option
                                :key="manager.id"
                                :label="`${manager.first_name} ${manager.last_name}`"
                                :value="manager.id"
                                v-for="manager in model.assignList[$index]"/>
                        </el-select>
                    </el-form-item>
                </el-col>
            </el-row>
        </template>
        <div class="drawer-actions">
            <el-button type="primary" @click="submit" icon="ti-save" round>{{$t('general.actions.save')}}</el-button>
        </div>
        

    </el-form>
</template>

<script>
    import {displayError, displaySuccess} from "../helpers/messages";
    import {mapActions, mapGetters} from 'vuex';

    export default {
        name: "EmailReceptionistForm",
        props: {
            data: {
                type: Object
            },
            visible: {
                type: Boolean,
                default: false
            },
            isBuilding: {
                type: Boolean,
                default: false
            },
            quarter_id: {
                type: Number
            },
            building_id: {
                type: Number
            },
            is_global: {
                type: Boolean,
                default: true
            },
        },
        data () {
            return {
                remoteLoading: false,
                loading: false,
                global: '',
                model: {
                    assignList: '',
                    assign: [],
                },
                categories: [],
                validationRules: {
                    assign: [{
                        required: true,
                        message: this.$t('models.quarter.required')
                    }],
                },
                remoteLoading: false,
                activeCommand: 'global',
                
            }
        },
        methods: {
            ...mapActions(['getPropertyManagers', 'getQuarterEmailReceptionists', 'saveQuarterEmailReceptionists', 'getBuildingEmailReceptionists', 'saveBuildingEmailReceptionists', 'getBuilding']),
            async submit () {
                try {
                    const valid = await this.$refs.form.validate();
                    if (valid) {
                        let global_email_receptionist = null
                        if(this.activeCommand == 'global')
                            global_email_receptionist = true
                        else if(this.activeCommand == 'assign')
                            global_email_receptionist = false
                        
                        let categories = [];
                        for(let i = 0; i < this.categories.length; i ++)
                        {
                            categories.push({
                                category : +this.categories[i].id,
                                property_manager_ids: this.model.assign[i]
                            })

                        }

                        if(this.isBuilding == true) {

                            let payload = {
                                building_id : this.building_id,
                                global_email_receptionist,
                                categories
                            }
                            

                            const resp = await this.saveBuildingEmailReceptionists(payload)

                            if(resp.success)
                            {
                                displaySuccess(resp);
                                const data = await this.getBuilding({id: this.$route.params.id});

                                if(data.has_email_receptionists) {
                                    this.$emit('update-has-email-receptionists', true);
                                }
                            }
                            this.$emit('update:visible', false);
                        }
                        else {
                            let payload = {
                                quarter_id : this.quarter_id,
                                categories: categories
                            }

                            const resp = await this.saveQuarterEmailReceptionists(payload)

                            if(resp.success)
                                displaySuccess(resp);
                            
                            this.$emit('update:visible', false);
                        }
                        
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
            handleCommand(command) {
                this.activeCommand = command
            },
        },
        async created () {
            let categories = this.$constants.requests.categories_data.categories
            this.loading = true;
            this.categories = Object.keys(categories).map(key => {
                return { id : key, name : categories[key] }
            })

            if(!this.isBuilding)
                this.activeCommand = 'assign'
            else if(this.isBuilding && !this.quarter_id)
                this.activeCommand = 'assign'
            else if(this.isBuilding && !this.is_global)
                this.activeCommand = 'assign'

            this.model.assignList = []
            for(let i = 0; i < this.categories.length; i ++)
            {
                this.model.assign.push([])
                this.model.assign[i] = []
            }

            if(this.isBuilding == true) {
                const resp = await this.getBuildingEmailReceptionists({building_id: this.building_id})
                if(resp.data.email_receptionists.length > 0) {
                    for(let i = 0; i < this.categories.length; i ++)
                    {
                        this.model.assign[i] = resp.data.email_receptionists[i].property_managers.map(item => item.id)
                        this.model.assignList[i] = resp.data.email_receptionists[i].property_managers
                    }
                }
            }
            else {
                const resp = await this.getQuarterEmailReceptionists({quarter_id: this.quarter_id})
                if(resp.data.email_receptionists.length > 0) {
                    for(let i = 0; i < this.categories.length; i ++)
                    {
                        this.model.assign[i] = resp.data.email_receptionists[i].property_managers.map(item => item.id)
                        this.model.assignList[i] = resp.data.email_receptionists[i].property_managers
                    }
                }
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
