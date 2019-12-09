<template>
    <el-form :model="resetPassword" ref="resetPasswordForm" :rules="validationRules">
        <h2>{{$t('general.change_password')}}</h2>
        <p>{{$t('general.change_password_info')}}</p>
        <el-form-item prop="password" :label="$t('general.password')">
            <el-input type="password" v-model="resetPassword.password" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item prop="password_confirmation" :label="$t('general.confirm_password')">
            <el-input type="password" v-model="resetPassword.password_confirmation" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item v-loading="loading">
            <el-button type="primary"  class="text-center w100p" @click="submitResetPasswordForm">
                {{$t('general.reset_password')}}
            </el-button>
        </el-form-item>
        <el-form-item>
            <router-link :to="{name: 'login'}" class="w100p">
                <el-button class="text-center w100p">{{$t('general.back_to_login')}}</el-button>
            </router-link>
        </el-form-item>
    </el-form>
</template>
<script>
    import authResetPasswordMixin from 'mixins/authResetPasswordMixin';
    export default {
        mixins: [authResetPasswordMixin]
    }
</script>
<style lang="scss" scoped>
    .el-form {
        .el-form-item {
            &:last-of-type :global(.el-form-item__content) {
                display: flex;
                align-items: center;
                .el-checkbox {
                    flex: 1;
                    margin: 0;
                }
            }
            a, .el-button {
                width: 100%;
            }
        }
    }
</style>
