<template>
    <el-form :model="resetPassword" @submit.native.prevent="" ref="resetPasswordEmailForm">        
        <router-link :to="{name: 'login'}">
            <el-button type="text">
                <i class="el-icon-back"></i>{{$t('general.back')}}
            </el-button>
        </router-link>
        <h2>{{$t('general.reset_password')}}</h2>
        <p>{{$t('general.forgot_password_info')}}</p>
        <el-form-item :label="$t('general.email')" :rules="validationRules.email" prop="email">
            <el-input autocomplete="off" type="email" v-model="resetPassword.email"></el-input>
        </el-form-item>
        <el-form-item v-loading.lock="loading">
            <el-button @click.prevent="submitResetPasswordForm" class="text-center w100p" type="primary">
                {{$t('general.reset_password_mail')}}
            </el-button>
        </el-form-item>
    </el-form>
</template>
<script>
    import authForgotPasswordMixin from 'mixins/authForgotPasswordMixin';

    export default {
        mixins: [authForgotPasswordMixin]
    }
</script>
<style lang="scss" scoped>
    .el-form {
        a .el-button {
            text-align: left;
        }
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
        .el-menu-item-link {
            line-height: 1;
            text-align: center;
            text-decoration: none;
            color: #909399;

            i {
                margin-bottom: 5px;
                margin-right: 10px;
            }
        }
    }
</style>
