<template>
    <div class="activate-account-container">
        <el-form :model="model" ref="form">       
            <router-link :to="{name: 'login'}">
                    <el-button type="text">
                        <i class="el-icon-back"></i>{{$t('general.back')}}
                    </el-button>
                </router-link>
            <h2>{{$t('general.activate_account')}}</h2>
            <p>{{$t('general.activate_info')}}</p>
             <el-row :gutter="10">
                <el-col :xl="12" :lg="12" :md="12" :sm="24" :xs="24">
                    <el-form-item prop="email" :label="$t('general.email')" :rules="validationRules.email">
                        <el-input type="email" v-model="model.email" autocomplete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xl="12" :lg="12" :md="12" :sm="24" :xs="24">
                    <el-form-item prop="act_code" :label="$t('general.activate_code')" :rules="validationRules.act_code">
                        <el-input type="text" v-model="model.act_code" autocomplete="off"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row :gutter="10">
                <el-col :xl="12" :lg="12" :md="12" :sm="24" :xs="24">
                    <el-form-item prop="password" :label="$t('general.password')" :rules="validationRules.password">
                        <el-input autocomplete="off" type="password" v-model="model.password"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xl="12" :lg="12" :md="12" :sm="24" :xs="24">
                    <el-form-item prop="password_confirmation" :label="$t('general.confirm_password')" :rules="validationRules.password_confirmation">
                        <el-input autocomplete="off" type="password"
                                    v-model="model.password_confirmation"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-form-item prop="terms" :rules="validationRules.terms">
                <el-checkbox v-model="model.terms">{{$t('general.activate_terms_condition_1')}}</el-checkbox>
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
    .activate-account-container {
        h2 {
            font-weight: normal;
        }
        a .el-button {
            text-align: left;
        }
  
    }
    .el-form-item {
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
</style>
