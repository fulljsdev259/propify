<template>
    <div class="users-edit" v-loading.fullscreen.lock="loading.state">
        <heading :title="$t('models.user.edit_admin')" icon="icon-user">
            <edit-actions :saveAction="submit" :deleteAction="deleteUser" :role="this.$route.params.role" route="adminUsers" :queryParams="queryParams" shadow="heavy"/>
        </heading>
        <el-row class="crud-view">
            <el-form :model="model" label-position="top" label-width="192px" ref="form">
                <el-col :md="12" id="left_card">
                    <card>
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('general.name')" :rules="validationRules.name" prop="name">
                                    <el-input type="text" v-model="model.name"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('general.email')" :rules="validationRules.email"
                                            prop="email">
                                    <el-input type="email"
                                              v-model="model.email"
                                              class="dis-autofill"
                                              readonly
                                              onfocus="this.removeAttribute('readonly');"
                                    />
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('general.password')" :rules="validationRules.password"
                                            autocomplete="off"
                                            prop="password">
                                    <el-input type="password"
                                              v-model="model.password"
                                              class="dis-autofill"
                                              readonly
                                              onfocus="this.removeAttribute('readonly');"
                                    />
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('general.confirm_password')"
                                            :rules="validationRules.password_confirmation"
                                            prop="password_confirmation">
                                    <el-input type="password" v-model="model.password_confirmation"/>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('general.phone')" prop="phone">
                                    <el-input type="text" v-model="model.phone"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12" id="right_card">
                                <el-form-item style="margin-bottom: 0;" :label="$t('general.language')" :rules="validationRules.language"
                                              prop="settings.language">
                                    <select-language :activeLanguage.sync="model.settings.language"/>
                                </el-form-item>
                            </el-col>
                        </el-row>

                    </card>
                </el-col>
            </el-form>
        </el-row>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import {mapActions} from 'vuex';
    import AdminUsersMixin from 'mixins/adminUsersMixin';
    import EditActions from 'components/EditViewActions';
    import SelectLanguage from 'components/SelectLanguage';

    export default {
        name: 'AdminUsersEdit',
        mixins: [AdminUsersMixin({
            mode: 'edit'
        })],
        components: {
            Heading,
            Card,
            EditActions,
            SelectLanguage
        },
        methods: {
            ...mapActions(['deleteUser']),
        }
    }
</script>

<style lang="scss" scoped>
    .last-form-row {
        margin-bottom: -22px;
    }

    .users-edit {
        .heading {
            margin-bottom: 20px;
        }
        .el-form {
            #left_card {
                padding-left: 1%;
                padding-right: 1%;
            }
            #right_card {
                padding-left: 1%;
                padding-right: 1%;
            }
        }
    }
</style>

