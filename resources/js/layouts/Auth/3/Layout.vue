<template>
    <el-container>      
        <el-row class="sub_container">  
            <div class="languageswitcher">
                <div class="item">
                    <locale-switcher />
                </div>
            </div>    
            <el-col :xl="10" :lg="11" :md="12" :sm="12" :xs="12" class="login-sidebar">
                <el-aside>
                    <div class="login-pane">
                        <router-link to="/login" class="logo w-100">
                            <img src="../../../../img/logo1.png" v-show="resident_logo_src"/>
                        </router-link>
                        <router-view></router-view>
                    </div>
                </el-aside>
            </el-col>
            <el-col :xl="14" :lg="13" :md="12" :sm="12" :xs="12" class="login-content">
            </el-col>
        </el-row>
    </el-container>
</template>
<script>
    export default {
        data() {
            return {
                resident_logo_src: '',
                logo_src: '',
                phone: '',
                street: '',
                zip: '',
                city: ''
            }
        },
        beforeCreate() {
            document.getElementById('viewport').setAttribute('content', 'width=device-width, initial-scale=1.0');
        },
        mounted () {
            // this.resident_logo_src = "/" + this.$constants.logo.resident_logo;
            this.resident_logo_src = "../../../../img/logo1.png";
            this.logo_src = "/" + this.$constants.logo.logo;
            this.phone = this.$constants.details.phone;
            this.street = this.$constants.details.street;
            this.zip = this.$constants.details.zip;
            this.city = this.$constants.details.city;
        }
    }
</script>
<style lang="scss">
    @media screen and (max-width: 603px) {
        .login-content{
            width: 0% !important;
        }
        .login-sidebar{
            width: 100% !important;
        }
    }
     .languageswitcher{
            position: absolute;
            top: 40px;
            right: 40px;
            z-index: 3000;
            @media screen and (max-width: 1024px) { 
                top: 20px;
                right: 20px;
            }
    }
    @media screen and (max-width: 554px) {
        .el-button {
            width: 100% !important;
        }
    }
</style>
<style lang="scss" scoped>
    .sub_container{
        width: 100%;
    }
    .el-col {
        height: 100%;
    }

    .login-pane {
        a {
            margin: 0 !important;
        }
    }
    .languageswitcher {
        position: absolute;
        top: 40px;
        right: 40px;
        z-index: 3000;
    }

    .logo{
        width: 100%;
    }
    
    .el-container {
        height: 100%;
        position:relative;
        @media screen and (max-width: 768px) {
            .logo{
                img{
                    width: 200px !important;
                }
            }
        }
        @media screen and (min-width: 554px) {
            background-color:#515862;
            &:before{
                background-size: cover;
                background-repeat: no-repeat;
                position:absolute;
                width: 100%;
                height: 100%;
                content: '';
            }
            .login-content {
                background-size: cover;
                background-repeat: no-repeat;
                background-image: url('../../../../img/login_background.png');
            }
        } 
        @media screen and (max-height: 768px) {
            height: auto;
            .logo {
                margin-bottom: 0px !important;
                width: 100%;
            }
            @media screen and (max-width: 554px) {
                .logo {
                    margin-bottom: 15px !important;
                }
            }
            @media screen and (min-width: 554px) {
                .login-content {
                    padding-top: 85px;
                }
            }
        }
        
        .company-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding-bottom: 15px;
            margin-top: 25px;
            .phone-number {
                line-height: 1.5;
            }
            .bottom-logo{
                margin-top: 5px;
                margin-bottom: 5px;
            }
            .address {
                font-size: 10px;
                line-height: 2;
            }
        }            

        .login-sidebar {
            
            @media screen and (max-width: 554px) {
                width: 100% !important;
                height: auto;
                background-color: #fafafa;
                margin-top: 20px;
                padding-bottom: 20px;
                .company-info {
                    display: none;
                }
            }
            .el-aside {
                margin: 0;
                padding: 0;
                height: 100%;
                border-radius: 0;
                width: 100% !important;

                @media screen and (max-height: 950px) {
                    padding-top: 0;
                    padding-bottom: 0;
                }
                background-color: #ffffff;
                @media screen and (min-width: 554px) {
                    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19),
                    0 6px 6px rgba(0, 0, 0, 0.23);
                }
                @media screen and (max-width: 1200px) {
                    width: 100% !important;
                    margin: 0px;
                    height: 100%;
                    border-radius: 0;
                } 
                @media screen and (max-width: 992px) {
                    padding: 0 20px;
                }
                @media screen and (min-width: 1400px) {
                    .company-info {
                        display: none;
                    }
                }
                .login-pane {
                    padding: 100px;

                    flex: 1;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    flex-direction: column;
                    .logo {
                        margin: 1em auto;
                        img {
                            @media screen and (max-width: 736px) {
                            }
                        }
                        @media screen and (max-width: 850px) {
                            margin: 20px auto 1em;
                        }
                        @media screen and (max-width: 554px) {
                            margin: 0 auto 1em;
                        }
                    }
                    @media screen and (max-width: 850px) {
                        justify-content: flex-start;
                    }
                    @media screen and (max-width: 768px) {
                        padding: 50px 15px 15px 15px !important;
                    }
                 }
                &:before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    background-image: url("~img/5c3b59fab215e.png");
                    filter: opacity(.024) sepia(100%);
                    z-index: -1;
                    width: 100%;
                    height: 100%;
                }
               
            }
        }
        .login-content {
            @media screen and (max-width: 554px) {
                width: 100% !important;
                height: auto;
            }
            .company-info {
                padding-top: 20px;
                @media screen and (min-width: 554px) {
                    display: none;
                }
            }
            .el-main {
                position: relative;
                padding: 8em;
                display: flex;
                align-items: flex-end;
                height: 100%;
                @media screen and (max-width: 1368px) {
                    padding: 4em;
                }
                @media screen and (max-width: 1200px) {
                    align-items: center;
                }
                @media screen and (max-width: 1024px) {
                    align-items: center;
                }
                @media screen and (max-width: 992px) {
                    padding: 0 20px;
                }
                &:before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    background-repeat: no-repeat;
                    background-size: cover;
                    z-index: -1;
                    filter: opacity(.05);
                    width: 100%;
                    height: 100%;
                }
                &:after {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    background-repeat: no-repeat;
                    background-size: 88em;
                    background-position: left calc(100% + 7em);
                    z-index: 1;
                    opacity: .32;
                    width: 100%;
                    height: 100%;
                }
                .content {
                    position: relative;
                    z-index: 2;
                    h1 {
                        font-size: 2.5em;
                        color: var(--primary-color);
                        margin-top: 0;
                        margin-bottom: .5em;
                        text-shadow: 0 0 3px #fff;
                    }
                    p {
                        font-size: 16px;
                        color: #fff;
                    }
                    @media screen and (max-width: 1024px) {
                        h1 {
                            font-size: 26px;
                            line-height: 1.2;
                        }
                    }
                    @media screen and (max-width: 554px) {
                        h1 {
                            text-align: center;
                            font-size: 22px;
                        }
                        p {
                            color: #515862;
                        }
                    }
                    .el-row {
                        margin-top: 4em;
                        margin-bottom: 4em;
                        @media screen and (max-width: 1368px) {
                            margin-top: 2em;
                            margin-bottom: 2em;
                        }
                        &:last-of-type{
                            margin-bottom: 0;
                        }
                        .el-col {
                            &:not(:last-of-type) {
                                margin-bottom: 2em;
                            }
                            display: flex;
                            align-items: center;
                            @media screen and (max-width: 768px) {
                                align-items: flex-start;
                            }
                            i {
                                width: 72px;
                                height: 72px;
                                flex-shrink: 0;
                                display: block;
                                border-radius: 12px;
                                font-size: 2.4em;
                                text-align: center;
                                line-height: 72px;
                                color: #fff;
                                background-color: var(--primary-color) !important;
                                box-shadow: 0 10px 20px rgba(0,0,0,0.19),
                                0 6px 6px rgba(0,0,0,0.23);
                                margin-right: .5em;
                            }
                            .content {
                                h3 {
                                    font-size: 1.2em;
                                    margin: 0;
                                    margin-bottom: .32em;
                                    color: var(--primary-color);
                                    text-shadow: 0 0 3px #fff;
                                }
                                p {
                                    margin: 0;
                                    font-size: 14px;
                                    color: #fff;
                                    @media screen and (max-width: 554px) {
                                        color: #515862;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

</style>
<style lang="less">
    @media screen and (max-height: 950px) {
        .activate-account-container {
            .el-form-item__label {
                line-height: 20px !important;
            }
        }
    }
</style>
