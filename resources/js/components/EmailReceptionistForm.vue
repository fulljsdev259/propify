<template>
    <el-form :model="model" :rules="validationRules" label-position="top"  ref="form" v-loading="loading">
        
        <el-row :gutter="20" v-if="isBuilding && quarter_id">
            <el-col :md="24">
                <el-dropdown split-button 
                            :disabled="true" 
                            type="primary" 
                            trigger="click" 
                            @command="handleCommand">
                    {{$t('general.email_receptionist.info_desc')}} ?
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item command="global">{{$t('general.email_receptionist.global')}}</el-dropdown-item>
                        <el-dropdown-item command="assign">{{$t('general.email_receptionist.assign')}}</el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </el-col>
        </el-row>
        <template v-if="activeCommand == 'assign'">
            <el-row v-for="(category, $index) in categories" :key="category.id">
                <el-col :md="24">
                    <el-form-item :label="$t('general.email_receptionist.email_receptionist_of', {category: $t(`models.request.category_list.${category.name}`) })"
                                class="label-block">
                        <el-select
                            :loading="remoteLoading"
                            :placeholder="$t('general.placeholders.search')"
                            :remote-method="data => remoteSearchManagers(data, $index)"
                            class="custom-remote-select"
                            filterable
                            multiple
                            remote
                            reserve-keyword
                            style="width: 100%;"
                            v-model="assign[$index]"
                        >
                            <div class="custom-prefix-wrapper" slot="prefix">
                                <i class="el-icon-search custom-icon"></i>
                            </div>
                            <el-option
                                :key="manager.id"
                                :label="`${manager.first_name} ${manager.last_name}`"
                                :value="manager.id"
                                v-for="manager in assignList[$index]"/>
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
    import {displayError} from "../helpers/messages";
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
            }
        },
        data () {
            return {
                remoteLoading: false,
                loading: false,
                model: {
                    phone_number: '',
                    activate_emergency: false,
                    time_schedule: ''
                },
                categories: [],
                validationRules: {
                    phone_number: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.emergency.phone_number')})
                    }],
                    time_schedule: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.emergency.time_schedule')})
                    }],
                },
                toAssignList: '',
                toAssign: [],
                assignList: '',
                assign: [],
                remoteLoading: false,
                activeCommand: '',
                global: '',
            }
        },
        methods: {
            ...mapActions(['getPropertyManagers']),
            submit () {
                if(this.activeCommand == 'global')
                    this.global = 1
                else if(this.activeCommand == 'assign')
                    this.global = 0
                // let service_provider_ids = this.toAssign
                
                

                // this.processAssignment = false;
                // this.closeModal();
                // this.fetchMore();
                // if(resp.data.success)
                //     displaySuccess(resp.data.message);
                let categories = [];
                for(let i = 0; i < this.categories.length; i ++)
                {
                    categories.push({
                        quarter_id : 1,
                        category_id : this.categories[i].id,
                        property_manager_ids: this.assign[i]
                    })
                    console.log('category, assign', this.categories[i].name, this.assign[i])
                    
                }
             
                // const resp = await this.saveEmailReceptionist({
                //     request_ids, 
                //     service_provider_ids
                // })
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

                        this.assignList[index] = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            resetToAssignList(index) {
                this.assignList[index] = [];
                this.assign[index] = '';
            },
            handleCommand(command) {
                this.activeCommand = command
            },
        },
        async created () {
            let categories = this.$constants.requests.categories_data.categories

            this.categories = Object.keys(categories).map(key => {
                return { id : key, name : categories[key] }
            })

            if(!this.isBuilding)
                this.activeCommand = 'assign'
            else if(this.isBuilding && !this.quarter_id)
                this.activeCommand = 'assign'

            this.assignList = []
            for(let i = 0; i < this.categories.length; i ++)
            {
                this.assign.push([])
                this.assign[i] = []
            }
            
        }
    }
</script>

<style lang="scss" scoped>
    .el-form {
        display: flex;
        flex-direction: column;
        height: 100%;
        
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
