<template>
    <el-form class='login2-container'>
        <div class="form-header">
            <p>{{ $t('auth.welcome_login') }}</p>
        </div>
        <el-form :model="model" ref="form">
            <el-form-item prop="email" :label="$t('general.email-')" :rules="validationRules.email" v-bind:class="[email_input, emailfocus, emailfocus1]" id="email_input">
                <el-input
                    type="email" 
                    v-model="model.email" 
                    autocomplete="off"
                    @keyup.enter.native="submit"
                    @focus="focus('email')"
                    v-bind:class="emailshow"
                ></el-input>
            </el-form-item>
            <el-form-item prop="password" :label="$t('general.password')" :rules="validationRules.password" v-bind:class="[pwd_input, pwdfocus, pwdfocus1]" id="pwd_input">
                <el-input
                    type="password" 
                    v-model="model.password" 
                    autocomplete="off"
                    @keyup.enter.native="submit"
                    @focus="focus('pwd')"
                    v-bind:class="pwdshow"
                ></el-input>
            </el-form-item>
            <el-form-item>
                <el-row class="subact_wrap">
                <el-col :xl="12" :lg="12" :md="24" :sm="24" :xs="24" class="login-sidebar logged_in">
                    <el-checkbox>{{$t('general.stay_logged_in')}}</el-checkbox>
                </el-col>
                <el-col :xl="12" :lg="12" :md="24" :sm="24" :xs="24" class="login-sidebar pwd_reset">
                    <router-link :to="{name: 'newForgot'}">
                        {{$t('general.forgot_password')}}
                    </router-link>
                </el-col>
                </el-row>
            </el-form-item>
            <el-form-item>
                <el-row :gutter="20">
                    <el-col :xl="8" :lg="8" :md="12" :sm="24" :xs="24" class="login-sidebar login_act login-button">
                        <el-button type="info" plain class="text-center w90p" @click="submit" ref="prev">{{$t('general.login')}}</el-button>
                    </el-col>
                    <el-col :xl="8" :lg="8" :md="12" :sm="24" :xs="24" class="login-sidebar login_act register_act login-button">
                        <router-link :to="{name: 'nwewActivateAccount'}" class="el-menu-item-link">
                            <el-button type="info" plain class="text-center w90p" ref="prev">{{$t('general.register')}}</el-button>
                        </router-link>
                    </el-col>
                </el-row>
                <div class="login_footer">
                    <h5>{{$t('general.agree')}}</h5>
                    <h5><a href="#">{{$t('general.protection')}}</a> {{$t('general.agreed')}}</h5>
                </div>
            </el-form-item>
        </el-form>
    </el-form>
</template>
<script>
    import loginMixin from 'mixins/authLoginMixin';

    export default {
        mixins: [loginMixin],
        data() {
            return {
                emailfocus: "",
                emailfocus1: "",
                pwdfocus: "",
                pwdfocus1: "",
                emailshow: 'input_hide',
	            email_input: 'email_input',
                pwd_input: 'pwd_input',
                pwdshow: 'input_hide'
            }
        },
        methods: {
            focus(obj) {
                if (obj == 'email') {
                    this.emailfocus = 'input_focus';
                    this.emailfocus1 = 'input_focus1';
                    this.pwdfocus = '';
                }
                else {
                    this.pwdfocus = 'input_focus_pwd';
                    this.pwdfocus1 = 'input_focus_pwd1';
                    this.emailfocus = '';
                }
            },
            blur(obj) {
                if (obj == 'email') this.emailfocus = '';
                else this.pwdfocus = '';
            }
        },
        mounted() {
            document.querySelector('#email_input').addEventListener('click', () => {
                this.emailshow = 'input_show';
                setTimeout('document.querySelector("#email_input input").focus()',100);
            });
            document.querySelector('#pwd_input').addEventListener('click', () => {
                this.pwdshow = 'input_show';
                setTimeout('document.querySelector("#pwd_input input").focus()',100);
            });
        }
    }
</script>
<style lang="scss" scoped>
.login_act{
    button{
        @media screen and (max-width: 768px) {
            width: 100% !important;
        }
    }
}
    .login_footer{
        margin-top: 90px;
        h5{
            margin: 0;
            font-size: 14px;
            color: rgba(0, 0, 0, 0.3);
            line-height: 1.2;
            a{
                color: rgba(0, 0, 0, 0.7);
            }
        }
        @media screen and (max-width: 768px) {
            margin-top: 50px;
        }
    }
    .el-form{
        // padding-top: 30px;
    }
    .el-form-item__label{
        font-size: 16px;
    }
    .email_input{
        border: 2px solid rgba(0, 0, 0, 0.1);
        border-top-right-radius: 10px;
        border-top-left-radius: 10px;
        padding-top: 2px;
        padding-bottom: 20px;
        background-color: var(--color-white);
    }
    .pwd_input{
        border: 2px solid rgba(0, 0, 0, 0.1);
        border-bottom-right-radius: 10px;
        border-bottom-left-radius: 10px;
        padding-bottom: 20px;
        border-top: none;
        background-color: var(--color-white);
    }
    .el-form-item {
        .el-form-item__label{
            padding-left: 30px;
            font-size: 16px;
            color: rgba(0, 0, 0, 0.5);
            font-weight: 700;
        }
    }
    .form-header {
        padding-bottom: 30px;
        h3 {
            font-size: 18.48px;
            font-weight: 500;
            margin-top: 0;
            margin-bottom: 14px;
        }
        p {
            margin-top: 20px;
            font-family: 'Radikal Thin';
            font-weight: 700;
            font-size: 26px;
            color: var(--text-color);
        }
        @media screen and (max-width: 768px) {
            font-size: 25px !important;
        }
    }
    .el-form-item {
        &:nth-last-child(2) :global(.el-form-item__content) {
            margin-top: 50px;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            :global(.el-checkbox) {
                flex: 1;
                margin: 0;
            }
        }
        .el-form-item__label {
            color: rgba(0, 0, 0, 0.6);
            line-height: 24px;
            font-size: 15px;
        }
    }
    .el-menu-item-link {
        margin-bottom: 5%;
        width: calc(100% - 6em);
    }
   
</style>
<style lang="scss">
    .el-form-item__label{
        padding-top: 18px !important;
    }
    .login2-container{font-family: 'Radikal', sans-serif;}
    .subact_wrap{
        width: 100%;
    }
    .register_act{
        @media screen and (max-width: 768px) {
            margin-top: 30px;
        }
    }
    .pwd_reset{
        text-align: right;
        @media screen and (max-width: 768px) {
            text-align: left;
        }
        button {
            text-align: right;
            height: 30px !important;
            padding: 0 !important;
            @media screen and (max-width: 768px) {
                text-align: left;
            }
        }
    }
    .input_show{
        display: block;
    }
    .input_hide{
        display: none;
    }
    .input_focus1 {
        padding-bottom: 0 !important;
    }
    .input_focus{
        .el-form-item__content{
            margin-bottom: -1.5px;
            padding-bottom: 5px !important;
            border-left: 3px solid #6B0036 !important;
            border-top-left-radius: 3.5px;
            margin-left: -1px !important;
            width: 99.1% !important;
            @media screen and (max-width: 768px) {
                width: 98.3% !important;
            }
            @media screen and (max-width: 603px) {
                width: 99% !important;
            }
            @media screen and (max-width: 460px) {
                width: 98.7% !important;
            }
        }
        
    }
    .el-input__inner{
        border-radius: 10px;
    }
    .input_focus_pwd1 {
        padding-bottom: 0 !important;
    }
    .input_focus_pwd{
        .el-form-item__content{
            margin-top: -1.5px;
            padding-bottom: 5px !important;
            border-left: 3px solid #6B0036 !important;
            border-bottom-left-radius: 3.5px;
            margin-left: -1px !important;
            width: 99.1% !important;
            @media screen and (max-width: 768px) {
                width: 98.3% !important;
            }
            @media screen and (max-width: 603px) {
                width: 99% !important;
            }
            @media screen and (max-width: 460px) {
                width: 98.7% !important;
            }
        }
        
    }
    .el-checkbox__inner{
        width: 21px !important;
        height: 21px !important;
        border: 1px solid var(--border-color-base) !important;
	border-radius: 6px !important;
    }
    .login2-container {
        position: relative;
        width: 100%;

        .el-form-item {
            margin-bottom: 0 !important;
            .el-form-item__label {
                color: rgba(0, 0, 0, 0.8);
                line-height: 24px;
                font-size: 13px;
                padding-left: 21px;
                
            }
            .el-form-item__label:before{
                content: '' !important;
            }
            input {
                padding-left: 42px;
            }
            .el-input__icon {
                color: rgba(0, 0, 0, 0.4);
                padding-left: 2px;
            }
            .el-input__inner{
                border: none;
                padding-left: 25px;
                color: black;
                font-weight: 700;
                font-size: 17px;
            }
            .el-form-item__error{
                padding-left: 25px;
                padding-bottom: 10px;
                position: relative !important;
            }
            .el-button {
                padding: 18px 20px;
                width: 100%;
                color: rgba(0, 0, 0, 0.8);
                background: var(--color-white);
                font-size: 14px;
                font-weight: 700;
                border-radius: 8px;
                
                &:not(:nth-of-type(1)) {
                    margin-left: 25px;
                }
            }
            .el-checkbox__label{
                font-size: 14px;
                color: var(--color-text-regular)
            }
            .el-button--info.is-plain:focus, .el-button--info.is-plain:hover {
                background: #3D3F41;
                border-color: #3D3F41;
                color: #FFF;
            }
            .pwd_reset {
                a {
                    text-decoration: none;
                    color: var(--color-text-regular);
                }
            }
        }
    }
	.el-checkbox__inner::after{
		top: 3px !important;
		left: 7px !important;
		height: 10px;
	}
</style>
