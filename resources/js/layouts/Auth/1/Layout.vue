<template>
    <el-container>      
        <el-row>  
            <div class="languageswitcher">
                <div class="item">
                    <locale-switcher />
                </div>
            </div>    
            <el-col :xl="7" :lg="7" :md="12" :sm="12" :xs="12" class="login-sidebar">
                <el-aside width="90%">
                    <div class="login-pane">
                        <router-link to="/login" class="logo">
                            <img :src="resident_logo_src" v-show="resident_logo_src"/>
                        </router-link>
                        <router-view></router-view>
                    </div>
                    <div class="company-info">
                        <div class="phone-number">
                            SUPPORT
                            <br>
                            <b>{{ phone }}</b>
                        </div>
                        <div class="bottom-logo">
                            <img :src="logo_src" v-show="logo_src"/>
                        </div>
                        <div class="address">
                            Fortimo AG - {{ street }} {{ zip }} {{ city }}
                        </div>
                    </div>
                </el-aside>
            </el-col>
            <el-col :xl="17" :lg="17" :md="12" :sm="12" :xs="12" class="login-content">
                <el-main>
                    <div class="content">
                        <h1>{{ $t('auth.title') }}</h1>
                        <p>
                            {{ $t('auth.intro') }}
                        </p>
                        <el-row :gutter="24">
                            <el-col :xl="12" :lg="12" :md="24" :sm="24" :xs="24">
                                <i class="icon-vcard" />
                                <div class="content">
                                    <h3>{{ $t('auth.benefit_1.title') }}</h3>
                                    <p>
                                        {{ $t('auth.benefit_1.description') }}
                                    </p>
                                </div>
                            </el-col>
                            <el-col :xl="12" :lg="12" :md="24" :sm="24" :xs="24">
                                <i class="icon-megaphone-1" />
                                <div class="content">
                                    <h3>{{ $t('auth.benefit_2.title') }}</h3>
                                    <p>
                                        {{ $t('auth.benefit_2.description') }}
                                    </p>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row :gutter="24">
                            <el-col :xl="12" :lg="12" :md="24" :sm="24" :xs="24">
                                <i class="icon-chat-empty" />
                                <div class="content">
                                    <h3>{{ $t('auth.benefit_3.title') }}</h3>
                                    <p>
                                        {{ $t('auth.benefit_3.description') }}
                                    </p>
                                </div>
                            </el-col>
                            <el-col :xl="12" :lg="12" :md="24" :sm="24" :xs="24">
                                <i class="icon-basket" />
                                <div class="content">
                                    <h3>{{ $t('auth.benefit_4.title') }}</h3>
                                    <p>
                                        {{ $t('auth.benefit_4.description') }}
                                    </p>
                                </div>
                            </el-col>
                        </el-row>
                    </div>
                </el-main>
                <div class="company-info">
                    <div class="phone-number">
                        SUPPORT
                        <br>
                        <b>071112244</b>
                    </div>
                    <div class="bottom-logo">
                        <img :src="logo_src" v-show="logo_src"/>
                    </div>
                    <div class="address">
                        Fortimo AG - Botsford Trace 3172 New Bartonstad
                    </div>
                </div>
                 
            </el-col>
        </el-row>
    </el-container>
</template>
<script>
    export default {
        data() {
            return {
                resident_logo_src: '',
                logo_scr: '',
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
            this.resident_logo_src = "/" + this.$constants.logo.resident_logo;
            this.logo_src = "/" + this.$constants.logo.logo;
            this.phone = this.$constants.details.phone;
            this.street = this.$constants.details.street;
            this.zip = this.$constants.details.zip;
            this.city = this.$constants.details.city;
        }
    }
</script>
<style lang="scss">
     .languageswitcher{
            position: absolute;
            top: 40px;
            right: 40px;
            z-index: 3000;
            @media screen and (max-width: 554px) { 
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
    .el-col {
        height: 100%;
    }
    
    .el-container {
        height: 100%;
        position:relative;
        @media screen and (min-width: 554px) {
            background-color:#515862;
            &:before{
                background-size: cover;
                background-repeat: no-repeat;
                background-image: url(https://www.fortimo.ch/assets/images/a/7059-180618-aussenbilder-1-3e87cb84.jpg);
                position:absolute;
                filter:opacity(0.5);
                width: 100%;
                height: 100%;
                content: '';
            }
        } 
        .company-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding-bottom: 15px;
            .phone-number {
                line-height: 1.5;
            }
            .bottom-logo{
                margin-top: 15px;
                margin-bottom: 15px;
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
                background-color: #fff;
                margin-top: 150px;
                .company-info {
                    display: none;
                }
            }
            .el-aside {
                position: relative;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 3em;
                margin: 40px 0 40px 40px;
                height: calc(100% - 80px);

                @media screen and (max-height: 950px) {
                    padding-top: 0;
                    padding-bottom: 0;
                }
                background-color: #ffffff;
                border-radius: 12px;
                @media screen and (min-width: 554px) {
                    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19),
                    0 6px 6px rgba(0, 0, 0, 0.23);
                }
                @media screen and (max-width: 1200px) {
                    width: 100% !important;
                    margin: 0px;
                    height: 100%;
                    border-radius: 0;
                    background-color: rgba(255, 255, 255, 0.5);
                } 
                @media screen and (max-width: 992px) {
                    padding: 0 20px;
                }
                .login-pane {
                    flex: 1;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    flex-direction: column;
                    .logo {
                        margin: 1em auto;
                        img {
                            width: 130px;
                        }
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
                    padding: 20px;
                    align-items: center;
                }
                @media screen and (max-width: 1024px) {
                    align-items: center;
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
                    @media screen and (max-width: 554px) {
                        padding-top: 20px;
                        h1 {
                            text-align: center;
                            font-size: 28px;
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
