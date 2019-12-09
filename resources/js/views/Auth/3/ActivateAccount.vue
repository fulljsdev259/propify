<template>
    <div class="activate2-container">
        <el-form :model="model" ref="form">
            <router-link :to="{name: 'login'}">
                <el-button type="text">
                    <i class="el-icon-back"></i>{{$t('general.back')}}
                </el-button>
            </router-link>
            <h2>{{$t('general.activate_account')}}</h2>            
            <p>{{$t('general.activate_info')}}</p>
            <el-row type="flex">
                <el-col :span="12">
                    <el-form-item prop="email" :label="$t('general.email')" :rules="validationRules.email">
                        <el-input type="email" v-model="model.email" autocomplete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item prop="act_code" :label="$t('general.activate_code')" :rules="validationRules.act_code">
                        <el-input type="text" v-model="model.act_code" autocomplete="off"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row type="flex">
                <el-col :span="12">
                    <el-form-item prop="password" :label="$t('general.password')" :rules="validationRules.password">
                        <el-input autocomplete="off" type="password" v-model="model.password"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item prop="password_confirmation" :label="$t('general.confirm_password')" :rules="validationRules.password_confirmation">
                        <el-input autocomplete="off" type="password"
                                    v-model="model.password_confirmation"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-form-item prop="terms" :rules="validationRules.terms">
                <el-checkbox v-model="model.terms"></el-checkbox><div>{{$t('general.activate_terms_condition_1')}}</div>
            </el-form-item>    
            <el-form-item>
                <el-button type="primary" class="text-center w100p" @click="submit">{{$t('general.activate')}}</el-button>
            </el-form-item>
        </el-form>
        
    </div>
</template>
<script>
    import PasswordValidatorMixin from 'mixins/passwordValidatorMixin';
    import authActivateMixin from 'mixins/authActivateMixin';

    export default {
        mixins: ['PasswordValidatorMixin', authActivateMixin],
   
    }
</script>
<style lang="scss" scoped>
    .activate2-container {
        width: 100%;
        .el-form .el-row {
            .el-col {
                &:nth-child(2) {
                    margin-left: 25px;
                }
                div {
                    margin: 0;
                }
            }
            
        }
        h2 {
            font-weight: normal;
            margin: 0 !important;
        }
        p {
            margin: 5px;
        }
        .el-form-item {
            margin-bottom: 10px !important;
            &:nth-of-type(1) {
                margin-top: 20px;
            }
            &:nth-last-child(2), :nth-last-child() :global(.el-form-item__content) {
                display: flex;
                align-items: center;
                :global(.el-checkbox) {
                    flex: 1;
                    margin: 0;
                }
            }
            &:nth-last-of-type(1) {
                margin-top: 10px;
            }
            .el-button {
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

            .el-button {
                width: 100%;
            }
        }
    }
</style>
<style lang="scss">
    .activate2-container {
        .el-form-item {
            margin-bottom: 10px !important;
        }
        .el-form-item:nth-last-of-type(2) {
            div.el-form-item__content {
                line-height: 1.5 !important;
                display: flex;
                div {
                    margin-left: 20px;
                    width: 100%;
                    word-break: break-all;
                }
            }
        }
    }
</style>
