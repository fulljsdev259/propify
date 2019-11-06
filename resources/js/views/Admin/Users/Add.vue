<template>
    <div class="users-add" v-loading.fullscreen.lock="loading.state">
        <heading :title="$t('models.user.add_admin')" icon="icon-user" shadow="heavy">
            <add-actions :saveAction="submit" :role="this.$route.params.role" route="adminUsers" editRoute="adminUsersEdit"/>
        </heading>
        <div class="crud-view">
            <card>
                <el-form :model="model" label-width="192px" ref="form" style="max-width: 641px;">
                    <el-form-item :label="$t('general.name')" :rules="validationRules.name" prop="name">
                        <el-input type="text" v-model="model.name"/>
                    </el-form-item>
                    <el-form-item :label="$t('general.email')" :rules="validationRules.email" prop="email">
                        <el-input type="email"
                                  v-model="model.email"
                                  class="dis-autofill"
                                  readonly
                                  onfocus="this.removeAttribute('readonly');"
                        />
                    </el-form-item>
                    <el-form-item :label="$t('general.password')" :rules="validationRules.password" prop="password">
                        <el-input type="password"
                                  v-model="model.password"
                                  class="dis-autofill"
                                  readonly
                                  onfocus="this.removeAttribute('readonly');"
                        />
                    </el-form-item>
                    <el-form-item :label="$t('general.confirm_password')" :rules="validationRules.password_confirmation"
                                  prop="password_confirmation">
                        <el-input type="password" v-model="model.password_confirmation"/>
                    </el-form-item>
                    <el-form-item :label="$t('general.phone')" prop="phone">
                        <el-input type="text" v-model="model.phone"/>
                    </el-form-item>

                    <el-form-item :label="$t('general.language')" :rules="validationRules.language" prop="settings.language">
                        <select-language :activeLanguage.sync="model.settings.language"/>
                    </el-form-item>
                </el-form>
            </card>
        </div>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import AdminUsersMixin from 'mixins/adminUsersMixin';
    import AddActions from 'components/EditViewActions';
    import SelectLanguage from 'components/SelectLanguage';

    export default {
        name: 'AdminUsersAdd',
        mixins: [AdminUsersMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            AddActions,
            SelectLanguage
        },
       
    }
</script>

<style lang="scss" scoped>
    .users-add {
        .heading {
            margin-bottom: 20px;
        }
    }
</style>
