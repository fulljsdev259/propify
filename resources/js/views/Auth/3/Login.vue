<template>
    <el-form class='login2-container'>
        <div class="form-header">
            <p>{{ $t('auth.welcome_login') }}</p>
        </div>
        <el-form :model="model" ref="form">
            <el-form-item prop="email" :label="$t('general.email-')" :rules="validationRules.email" v-bind:class="[emailfocus, email_input]" id="email_input">
                <el-input
                    type="email" 
                    v-model="model.email" 
                    autocomplete="off"
                    @keyup.enter.native="submit"
                    @focus="focus('email')"
                    @blur="blur('email')"
                    v-bind:class="emailshow"
                ></el-input>
            </el-form-item>
            <el-form-item prop="password" :label="$t('general.password')" :rules="validationRules.password" v-bind:class="[pwdfocus, pwd_input]" id="pwd_input">
                <el-input
                    type="password" 
                    v-model="model.password" 
                    autocomplete="off"
                    @keyup.enter.native="submit"
                    @focus="focus('pwd')"
                    @blur="blur('pwd')"
                    v-bind:class="pwdshow"
                ></el-input>
            </el-form-item>
            <el-form-item>
                <el-row class="subact_wrap">
                <el-col :xl="12" :lg="12" :md="24" :sm="24" :xs="24" class="login-sidebar">
                    <el-checkbox>{{$t('general.stay_logged_in')}}</el-checkbox>
                </el-col>
                <el-col :xl="12" :lg="12" :md="24" :sm="24" :xs="24" class="login-sidebar pwd_reset">
                    <router-link :to="{name: 'newForgot'}">
                        <el-button type="text">
                            {{$t('general.forgot_password')}}
                        </el-button>
                    </router-link>
                </el-col>
                </el-row>
            </el-form-item>
            <el-form-item>
                <el-row>
                    <el-col :xl="8" :lg="12" :md="12" :sm="24" :xs="24" class="login-sidebar login_act">
                        <el-button type="info" plain class="text-center w90p" @click="submit" ref="prev">{{$t('general.login')}}</el-button>
                    </el-col>
                    <el-col :xl="8" :lg="12" :md="12" :sm="24" :xs="24" class="login-sidebar login_act register_act">
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
                pwdfocus: "",
                emailshow: 'input_hide',
	            email_input: 'email_input',
                pwd_input: 'pwd_input',
                pwdshow: 'input_hide'
            }
        },
        methods: {
            focus(obj) {
                if (obj == 'email') this.emailfocus = 'input_focus';
                else this.pwdfocus = 'input_focus';
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
            font-size: 16px;
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
        border: 2px solid rgba(0, 0, 0, 0.2);
        border-top-right-radius: 10px;
        border-top-left-radius: 10px;
        padding-top: 20px;
        padding-bottom: 15px;
    }
    .pwd_input{
        border: 2px solid rgba(0, 0, 0, 0.2);
        border-bottom-right-radius: 10px;
        border-bottom-left-radius: 10px;
        padding-top: 20px;
        padding-bottom: 15px;
        border-top: none;
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
            font-size: 32px;
            font-weight: 700;
        }
        @media screen and (max-width: 768px) {
            font-size: 25px !important;
        }
    }
    .el-form-item {
        &:nth-last-child(2) :global(.el-form-item__content) {
            margin-top: 50px;
            margin-bottom: 35px;
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
    .login2-container{font-family: 'RadikalW03', sans-serif;}
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
    .input_focus{
        border-left: 3px solid #6B0036 !important;
        margin-left: 0px !important;
        width: 99% !important;
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
                font-size: 15px;
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
            button {
                width: 87%;
                height: 55px;
                color: rgba(0, 0, 0, 0.8);
                background: white;
                font-size: 16px;
                font-weight: 700;
                border-radius: 8px;
            }
            .el-checkbox__label{
                font-size: 17px;
                color: rgba(0, 0, 0, 0.6);
                font-weight: 700;
            }
            .el-button--info.is-plain:focus, .el-button--info.is-plain:hover {
                background: #3D3F41;
                border-color: #3D3F41;
                color: #FFF;
            }
            .pwd_reset button {color: rgba(0, 0, 0, 0.6) !important;}
        }
    }
	.el-checkbox__inner::after{
		top: 3px !important;
		left: 7px !important;
		height: 10px;
	}
</style>
