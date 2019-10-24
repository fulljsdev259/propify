<template>
    <el-form :model="model" :rules="validationRules" label-position="top"  ref="form" v-loading="loading">
        
        <el-row :gutter="20">
            <el-col :md="24">
                <el-dropdown split-button 
                            :disabled="true" 
                            type="info" 
                            trigger="click" 
                            @command="handleCommand">
                    Do you want to use global or assign?
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item command="global">Choose from global</el-dropdown-item>
                        <el-dropdown-item command="assign">Assign</el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </el-col>
        </el-row>

        <el-row v-for="category in categories" :key="category">
            <el-col :md="24">
                <el-form-item :label="'Email Receptionist of ' + $t(`models.request.category_list.${category}`)"
                            class="label-block">
                    <el-select
                        :loading="remoteLoading"
                        :placeholder="$t('general.placeholders.search')"
                        :remote-method="remoteSearchManagers"
                        class="custom-remote-select"
                        filterable
                        multiple
                        remote
                        reserve-keyword
                        style="width: 100%;"
                        v-model="toAssign"
                    >
                        <div class="custom-prefix-wrapper" slot="prefix">
                            <i class="el-icon-search custom-icon"></i>
                        </div>
                        <el-option
                            :key="manager.id"
                            :label="`${manager.first_name} ${manager.last_name}`"
                            :value="manager.id"
                            v-for="manager in toAssignList"/>
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
                remoteLoading: false,
                activeCommand: '',
            }
        },
        methods: {
            ...mapActions(['getPropertyManagers']),
            submit () {
                
                this.$refs.form.validate(async valid => {
                    if (valid) {
                        this.loading = true;  
                        
                    }
                })
            },
            async remoteSearchManagers(search) {
                if (search === '') {
                    this.resetToAssignList();
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getPropertyManagers({
                            get_all: true,
                            search
                        });

                        this.toAssignList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            handleCommand(command) {
                this.activeCommand = command
            },
        },
        async created () {
            this.categories = this.$constants.requests.categories_data.categories
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
        
        .switcher {
            /deep/ .el-form-item__content {
                display: flex;
                align-items: center;

                & > div {
                    flex: 1;
                    justify-content: flex-end;
                    text-align: end;
                    width: 130px
                }
                .el-select {
                    width: 130px
                }
            }
            &__label {
                text-align: left;
                line-height: 1.4em;
                color: #606266;
            }
            &__desc {
                margin-top: 0.5em;
                display: block;
                font-size: 0.9em;
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
