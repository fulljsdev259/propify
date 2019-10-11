<template>
    <div class="settings" :style="{'overflow': main_drawer?'hidden':'inherit'}">
        <heading :title="$t('settings.title')" class="custom-heading" icon="ti-settings" shadow="heavy" />

        <el-tabs class="settings-tabs" tab-position="left" v-model="activeName">
            <el-tab-pane name="settings">
                <template slot="label"><i class="icon icon-cog"></i>{{$t('settings.settings')}}</template>

                <div class="dashboard-tabpanel dashboard-tabpanel_left">
                    <el-tabs type="border-card" v-model="activeSettingsName">
                        <el-tab-pane :label="$t('settings.settings')" name="settings_settings">
                            <el-button class="save-tab" @click="saveSettings('SettingsSettingsForm')" icon="ti-save" type="primary">
                                {{$t('general.actions.save')}}
                            </el-button>
                            <el-form :model="model" :rules="validationRules"
                                     ref="SettingsSettingsForm">
                                <el-row :gutter="20">
                                    <el-col :md="12">

                                        <el-card :header="$t('general.actions.view')">
                                            <el-row :gutter="20">
                                                <el-col :md="12">
                                                    <el-form-item :label="$t('general.name')" :rules="validationRules.name" prop="name">
                                                        <el-input type="text" v-model="model.name"></el-input>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :md="12">
                                                    <el-form-item :label="$t('general.email')" :rules="validationRules.email" prop="email">
                                                        <el-input type="email" v-model="model.email"></el-input>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row :gutter="20">
                                                <el-col :md="12">
                                                    <el-form-item :label="$t('general.phone')" prop="phone">
                                                        <el-input type="string" v-model="model.phone"></el-input>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :md="12">
                                                    <el-form-item :label="$t('general.street')" :rules="validationRules.street"
                                                                  prop="address.street">
                                                        <el-input autocomplete="off" type="text" v-model="model.address.street"></el-input>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row :gutter="20">
                                                <el-col :md="4">
                                                    <el-form-item :label="$t('general.zip')" :rules="validationRules.zip" prop="address.zip">
                                                        <el-input autocomplete="off" type="text" v-model="model.address.zip"></el-input>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :md="8">
                                                    <el-form-item :label="$t('general.city')" :rules="validationRules.city"
                                                                  prop="address.city">
                                                        <el-input autocomplete="off" type="text" v-model="model.address.city"></el-input>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :md="12">
                                                    <el-form-item :rules="validationRules.state_id"
                                                                  prop="address.state.id">
                                                        <label class="card-label">{{$t('general.state')}}</label>
                                                        <el-select :placeholder="$t('general.state')" style="display: block"
                                                                   v-model="model.address.state.id">
                                                            <el-option :key="state.id" :label="state.name" :value="state.id"
                                                                       v-for="state in states"></el-option>
                                                        </el-select>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                        </el-card>

                                        <el-card :header="$t('settings.pdf')">
                                            <el-form-item class="switcher" prop="blank_pdf">
                                                <label class="switcher__label">
                                                    <span class="switcher__label-title">{{$t('settings.blank_pdf')}}</span>
                                                    <span class="switcher__label-desc">{{$t('settings.blank_pdf_desc')}}</span>
                                                </label>
                                                <el-switch v-model="model.blank_pdf"/>
                                            </el-form-item>
                                            <el-form-item prop="pdf_font_family">
                                                <label class="card-label">
                                                    {{$t('settings.font_family')}}
                                                </label>
                                                <el-select
                                                           style="display: block"
                                                           v-model="model.pdf_font_family">
                                                    <el-option :key="font.id"
                                                               :style="`font-family: '${font.label}';`"
                                                               :label="font.label"
                                                               :value="font.label"
                                                               v-for="font in fonts"></el-option>
                                                </el-select>
                                            </el-form-item>
                                        </el-card>
                                    </el-col>
                                    <el-col :md="12">
                                    <!--                        <el-card class="mb20">-->
                                    <!--                            <el-form :model="model" size="mini"-->
                                    <!--                            >-->
                                    <!--                                <div :key="schedule.day" :md="12"-->
                                    <!--                                     class="day-wrapper mb20" v-for="schedule in model.opening_hours ">-->
                                    <!--                                    <div class="day-name">-->
                                    <!--                                        <div class="group-name">{{$t(`general.days.${schedule.day}`)}}</div>-->
                                    <!--                                        <el-switch-->
                                    <!--                                            size="mini"-->
                                    <!--                                            v-model="schedule.closed"-->
                                    <!--                                        >-->
                                    <!--                                        </el-switch>-->
                                    <!--                                    </div>-->
                                    <!--                                    <el-time-picker-->
                                    <!--                                        :end-placeholder="$t('settings.endTime')"-->
                                    <!--                                        :range-separator="$t('settings.to')"-->
                                    <!--                                        :start-placeholder="$t('settings.startTime')"-->
                                    <!--                                        format="HH:mm"-->
                                    <!--                                        is-range-->
                                    <!--                                        style="width: 100%"-->
                                    <!--                                        v-model="schedule.time"-->
                                    <!--                                        value-format="HH:mm">-->
                                    <!--                                    </el-time-picker>-->
                                    <!--                                </div>-->
                                    <!--                            </el-form>-->
                                    <!--                        </el-card>-->

                                    <el-card :header="$t('settings.settings')">
                                        <!-- <el-form-item :label="$t('settings.quarter_enable')" prop="quarter_enable">
                                            <el-switch v-model="model.quarter_enable"/>
                                        </el-form-item>
                                        <el-form-item :label="$t('settings.listing_approval_enable')"
                                                      prop="listing_approval_enable">
                                            <el-switch v-model="model.listing_approval_enable"/>
                                        </el-form-item> -->
                                        <el-form-item class="switcher"
                                                      prop="pinboard_approval_enable">
                                            <label class="switcher__label">
                                                <span class="switcher__label-title">{{$t('settings.pinboard_approval_enable')}}</span>
                                                <span class="switcher__label-desc">{{$t('settings.pinboard_approval_enable_desc')}}</span>
                                            </label>
                                            <el-switch v-model="model.pinboard_approval_enable"/>
                                        </el-form-item>
                                        <el-form-item class="switcher"
                                                      prop="contact_enable">
                                            <label class="switcher__label">
                                                <span class="switcher__label-title">{{$t('settings.contact_enable')}}</span>
                                                <span class="switcher__label-desc">{{$t('settings.contact_enable_desc')}}</span>
                                            </label>
                                            <el-switch v-model="model.contact_enable"/>
                                        </el-form-item>
                                        <el-row :gutter="20">
                                            <el-col :md="12">
                                                <el-form-item :label="$t('settings.comment_update_timeout')"
                                                              :rules="validationRules.comment_update_timeout"
                                                              prop="comment_update_timeout">
                                                    <el-input autocomplete="off" type="number"
                                                              v-model="model.comment_update_timeout"></el-input>
                                                </el-form-item>
                                            </el-col>
                                        </el-row>
                                    </el-card>

                                    <el-card :header="$t('settings.email')">
                                        <el-row :gutter="20">
                                            <el-col :md="12">
                                                <el-form-item :label="$t('settings.mail_from_name.label')"
                                                              prop="mail_from_name"
                                                              :rules="validationRules.mail_from_name"
                                                >
                                                    <el-input autocomplete="off" type="text"
                                                              v-model="model.mail_from_name"></el-input>
                                                </el-form-item>
                                            </el-col>
                                            <el-col :md="12">
                                                <el-form-item :label="$t('settings.mail_from_address.label')"
                                                              prop="mail_from_address"
                                                              :rules="validationRules.mail_from_address"
                                                >
                                                    <el-input autocomplete="off" type="text"
                                                              v-model="model.mail_from_address"></el-input>
                                                </el-form-item>
                                            </el-col>
                                        </el-row>
                                        <el-row :gutter="20">
                                            <el-col :md="12">
                                                <el-form-item :label="$t('settings.mail_host.label')"
                                                              prop="mail_host"
                                                              :rules="validationRules.mail_host"
                                                >
                                                    <el-input autocomplete="off"
                                                              type="url"
                                                              v-model="model.mail_host"
                                                              class="dis-autofill"
                                                              readonly
                                                              onfocus="this.removeAttribute('readonly');"
                                                    ></el-input>
                                                </el-form-item>
                                            </el-col>
                                            <el-col :md="12">
                                                <el-form-item :label="$t('settings.mail_port.label')"
                                                              prop="mail_port"
                                                              :rules="validationRules.mail_port"
                                                >
                                                    <el-input autocomplete="off" type="text"
                                                              v-model="model.mail_port"></el-input>
                                                </el-form-item>
                                            </el-col>
                                        </el-row>
                                        <el-row :gutter="20">
                                            <el-col :md="12">
                                                <el-form-item required
                                                >
                                                    <label class="card-label">{{$t('settings.mail_encryption')}}</label>
                                                    <el-select :placeholder="$t('settings.mail_encryption')" style="display: block"
                                                               v-model="model.mail_encryption">
                                                        <el-option :key="item.id"
                                                                   :label="item"
                                                                   :value="item"
                                                                   v-for="item in mailEncryption"></el-option>
                                                    </el-select>
                                                </el-form-item>
                                            </el-col>
                                            <el-col :md="12">
                                                <el-form-item :label="$t('settings.mail_username.label')"
                                                              prop="mail_username"
                                                              :rules="validationRules.mail_username"
                                                >
                                                    <el-input autocomplete="off" type="text"
                                                              v-model="model.mail_username"></el-input>
                                                </el-form-item>
                                            </el-col>
                                        </el-row>
                                        <el-row :gutter="20">
                                                <el-col :md="12">
                                                    <el-form-item :label="$t('settings.mail_password.label')"
                                                                  :rules="validationRules.mail_password"
                                                                  prop="mail_password"
                                                    >
                                                        <el-input autocomplete="new-password"
                                                                  show-password
                                                                  v-model="model.mail_password"
                                                        ></el-input>
                                                    </el-form-item>
                                                </el-col>
                                                 <el-col :md="12">
                                                    <el-form-item :rules="validationRules.mail_powered_by"
                                                                  prop="email_powered_by">
                                                        <label class="card-label">{{$t('settings.mail_powered_by.label')}}</label>
                                                        <el-select :placeholder="$t('general.state')" style="display: block"
                                                                   v-model="model.email_powered_by">
                                                            <el-option :label="$t('general.placeholders.select')" value=""></el-option>
                                                            <el-option :key="item.label+item.value" :label="$t('settings.powered_by')+' '+item.label" :value="item.value"
                                                                       v-for="item in mail_powered_by"></el-option>
                                                        </el-select>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                    </el-card>
                                </el-col>
                                </el-row>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane :label="$t('settings.micro_apps')" name="microApps">
                            <el-button class="save-tab" @click="saveSettings('microAppsSettingsForm')" icon="ti-save"
                                       type="primary">
                                {{$t('general.actions.save')}}
                            </el-button>
                            <el-form :model="model" :rules="validationRules"
                                     ref="microAppsSettingsForm">
                                <el-row :gutter="20">
                                    <el-col :md="8">
                                        <el-card class="listing-card card-boxs">
                                            <span @click="Iframe_drawer" class="icon-cog"></span>
                                            <el-form-item :label="$t('settings.iframe_enable')" class="switcher switcher-block" prop="contact_enable">
                                                <span class="switcher__desc">{{ $t('settings.iframe_enable_desc')}}</span>
                                                <el-switch 
                                                    v-model="model.iframe_enable"
                                                    />
                                            </el-form-item>
                                        </el-card>
                                    </el-col>
                                    <!-- <el-col :md="8">
                                        <el-card class="listing-card card-boxs">
                                            <span @click="Gocaution_drawer" class="icon-cog" style="display:none"></span>
                                            <el-form-item :label="$t('settings.gocaution.label')" class="switcher switcher-block">
                                                <span class="switcher__desc">{{$t('settings.gocaution.description')}}</span>
                                                <el-switch 
                                                v-model="model.gocaution_enable"
                                                />
                                            </el-form-item>
                                        </el-card>
                                    </el-col>
                                    <el-col :md="8">
                                        <el-card class="listing-card card-boxs">
                                            <span @click="Cleanify_drawer" class="icon-cog"></span>
                                           <el-form-item :label="$t('settings.cleanify_email.label')"
                                                        :rules="validationRules.cleanify_email" prop="cleanify_email" class="switcher switcher-block">
                                                <span class="switcher__desc">{{$t('settings.cleanify_email.description')}}</span>
                                                <el-switch 
                                                v-model="model.cleanify_enable"
                                                />
                                                
                                            </el-form-item>
                                        </el-card>
                                    </el-col> -->
                                </el-row>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane :label="$t('settings.theme')" name="theme">
                            <el-button class="save-tab" @click="saveSettings('themeSettingsForm')" icon="ti-save"
                                       type="primary">
                                {{$t('general.actions.save')}}
                            </el-button>
                            <el-form :model="model" :rules="validationRules"
                                     ref="themeSettingsForm">
                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <el-card>
                                            <el-form-item :label="$t('models.user.logo')">
                                                <!-- <cropper @cropped="setLogoUpload"/> -->
                                                <!-- <img :src="SettingsLogo" ref="SettingsLogo"
                                                     v-show="SettingsLogo || model.logo_upload"
                                                     width="300px"> -->
                                                <upload-document @fileUploaded="setAvatarLogoUpload" class="drag-custom" drag/>
                                                <img :src="logo_upload_img"
                                                     v-show="logo_upload_img"
                                                     >
                                                <img :src="SettingsLogo" ref="SettingsLogo"
                                                     v-show="SettingsLogo && !logo_upload_img"
                                                    >
                                                
                                            </el-form-item>
                                            <el-form-item :label="$t('models.user.circle_logo')">
                                                <upload-document @fileUploaded="setCircleLogoUpload" class="drag-custom" drag/>
                                                <img :src="circle_logo_upload_img"
                                                     v-show="circle_logo_upload_img"
                                                    >
                                                <img :src="SettingsCircleLogo" ref="SettingsCircleLogo"
                                                     v-show="SettingsCircleLogo && !circle_logo_upload_img"
                                                    >
                                            </el-form-item>
                                            <!-- <el-form-item :label="$t('models.user.favicon_icon')">
                                                <upload-document @fileUploaded="setFaviconIconUpload" class="drag-custom" drag/>
                                                <img :src="favicon_icon_upload_img"
                                                     v-show="favicon_icon_upload_img"
                                                    >
                                                <img :src="SettingsFaviconIcon" ref="SettingsFaviconIcon"
                                                     v-show="SettingsFaviconIcon && !favicon_icon_upload_img"
                                                    >
                                            </el-form-item> -->
                                            <el-form-item :label="$t('models.user.resident_logo')">
                                                <upload-document @fileUploaded="setResidentLogoUpload" class="drag-custom" drag/>
                                                <img :src="resident_logo_upload_img"
                                                     v-show="resident_logo_upload_img"
                                                    >
                                                <img :src="SettingsResidentLogo" ref="SettingsResidentLogo"
                                                     v-show="SettingsResidentLogo && !resident_logo_upload_img"
                                                     >
                                            </el-form-item>
                                            <el-form-item :label="$t('settings.primary_color')">
                                                <el-color-picker
                                                        size="medium"
                                                        v-model="model.primary_color"></el-color-picker>
                                            </el-form-item>
                                            <!-- <el-form-item :label="$t('settings.accent_color')">
                                                <el-color-picker
                                                        size="medium"
                                                        v-model="model.accent_color">
                                                </el-color-picker>
                                            </el-form-item> -->
                                        </el-card>
                                    </el-col>
                                </el-row>
                            </el-form>
                        </el-tab-pane>
                    </el-tabs>
                </div>

            </el-tab-pane>
            <el-tab-pane name="requests">
                <template slot="label"><i class="icon icon-chat-empty"></i>{{$t('general.requests')}}</template>

<!--                <heading :title="$t('general.requests')" class="custom-heading" icon="ti-settings" shadow="heavy" />-->

                <div class="dashboard-tabpanel dashboard-tabpanel_left">
                    <el-tabs type="border-card" v-model="activeRequestName">
<!--                        <el-tab-pane :label="$t('settings.categories')" name="categories">-->
<!--                            <CategoriesListing/>-->
<!--                        </el-tab-pane>-->
                        <el-tab-pane :label="$t('settings.templates')" name="templates">
                            <TemplatesListing v-if="!templateEditMode" :onClick="handleEditClick"/>
                            
                            <TemplateEdit v-else />
                        </el-tab-pane>
                    </el-tabs>
                </div>
            </el-tab-pane>
            <el-tab-pane name="residents">
                <template slot="label"><i class="icon icon-group"></i>{{$t('settings.residents_portal')}}</template>

                <div class="dashboard-tabpanel dashboard-tabpanel_left">
                    <el-tabs type="border-card" v-model="activeResidentsName">
                        <el-tab-pane :label="$t('settings.login_variations')" name="login_variations">
                            <el-button class="save-tab" @click="saveSettings('residentsLoginVariationsForm')" icon="ti-save"
                                       type="primary">
                                {{$t('general.actions.save')}}
                            </el-button>

                            <el-form :model="model" :rules="validationRules"
                                     ref="residentsLoginVariationsForm">

                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <el-card>
                                            <el-radio-group class="login-radio-group" v-model="model.login_variation">
                                                <el-row :gutter="20">
                                                    <el-col :md="12">
                                                        <el-radio class="login-radio" :label="1">
                                                            <div class="login-card">
                                                                <div class="login-card__img"></div>
                                                                <div class="login-card__content">
                                                                    <div class="login-card__title">{{$t('settings.login_variation')}} 1</div>
                                                                </div>
                                                            </div>
                                                        </el-radio>
                                                    </el-col>
                                                    <el-col :md="12">
                                                        <el-radio class="login-radio" :label="2">
                                                            <div class="login-card">
                                                                <div class="login-card__img"></div>
                                                                <div class="login-card__content">
                                                                    <div class="login-card__title">{{$t('settings.login_variation')}} 2</div>
                                                                </div>
                                                            </div>
                                                        </el-radio>
                                                    </el-col>
                                                </el-row>
                                            </el-radio-group>
                                            <el-form-item v-if="model.login_variation === 1"
                                                          class="switcher mt-20"
                                                          prop="login_variation_1_slider"
                                            >
                                                <label class="switcher__label">{{$t('settings.login_variation_slider')}}</label>
                                                <el-switch v-model="model.login_variation_1_slider"/>
                                            </el-form-item>
                                            <el-form-item v-if="model.login_variation === 2"
                                                          class="switcher mt-20"
                                                          prop="login_variation_2_slider"
                                            >
                                                <label class="switcher__label">
                                                    <span class="switcher__label-title">{{$t('settings.login_variation_slider')}}</span>
                                                </label>
                                                <el-switch v-model="model.login_variation_2_slider"/>
                                            </el-form-item>
                                            
                                        </el-card>
                                    </el-col>
                                </el-row>
                            </el-form>
                        </el-tab-pane>
                    </el-tabs>
                </div>
            </el-tab-pane>
        </el-tabs>
        <ui-drawer :visible.sync="main_drawer" :z-index="1" direction="right" docked>
            <el-tabs type="card" stretch>
                <el-tab-pane  name="iframe" v-if="Iframe_drawer_val">
                    <div slot="label"><i class="icon-cog"></i> <label class="switcher__label">{{$t('settings.iframe_enable')}}</label> </div>
                </el-tab-pane>
                <el-tab-pane name="gocaution" v-if="Gocaution_drawer_val">
                    <div slot="label"><i class="icon-cog"></i><label class="switcher__label">{{$t('settings.gocaution.label')}}</label> </div>
                    
                </el-tab-pane>
                <el-tab-pane name="cleanify" v-if="Cleanify_drawer_val">
                    <div slot="label"><i class="icon-cog"></i><label class="switcher__label">{{$t('settings.cleanify_email.label')}}</label> </div>
                    <!-- <el-input type="email" v-model="model.cleanify_email"></el-input> -->
                </el-tab-pane>
                <div :style="{'display':Iframe_drawer_val?'block':'none'}">
                        <el-form :model="model" :rules="iFrameRules" ref="microAppsSettingsForm_iframe" class="demo-ruleForm">
                            <el-form-item :label="$t('settings.iframe_url.label')"  prop="iframe_url">
                                <el-input v-model="model.iframe_url"></el-input>
                            </el-form-item>
                        </el-form>    
                        <div class="drawer-btn-sec"> 
                            <el-button class="save-tab" @click="saveSettings('microAppsSettingsForm_iframe')" icon="ti-save"
                                    type="primary">
                                {{$t('general.actions.save')}}
                            </el-button> 
                        </div> 
                </div> 
                <div :style="{'display':Cleanify_drawer_val?'block':'none'}">
                    <el-form :model="model" :rules="cleanifyRules" ref="microAppsSettingsForm_cleanify" class="demo-ruleForm">
                        <el-form-item :label="$t('settings.cleanify_url.label')"  prop="cleanify_email">
                            <el-input type="email" v-model="model.cleanify_email"></el-input>
                        </el-form-item>
                    </el-form>
                    <div class="drawer-btn-sec"> 
                        <el-button class="save-tab" @click="saveSettings('microAppsSettingsForm_cleanify')" icon="ti-save"
                                type="primary">
                            {{$t('general.actions.save')}}
                        </el-button> 
                    </div> 
                </div> 
                <!-- <div class="drawer-btn-sec"> 
                    <el-button class="save-tab" @click="saveSettings('microAppsSettingsForm')" icon="ti-save"
                            type="primary">
                        {{$t('general.actions.save')}}
                    </el-button> 
                </div>  -->
            </el-tabs>
        </ui-drawer>
    </div>
</template>
<script>
    import Heading from 'components/Heading';
    import Cropper from 'components/Cropper';
    import UploadDocument from 'components/UploadDocument';
    import {mapActions} from 'vuex';
    import {displayError, displaySuccess} from 'helpers/messages';
    import CategoriesListing from './Categories'
    import globalFunction from "helpers/globalFunction";
    import TemplatesListing from '../TemplatesNew'
    import TemplateEdit from '../TemplatesNew/Edit';

    export default {
        name: 'AdminProfile',
        mixins: [globalFunction],
        components: {
            Heading,
            Cropper,
            UploadDocument,
            CategoriesListing,
            TemplatesListing,
            TemplateEdit
        },
        data() {
            return {
                model: {
                    name: '',
                    email: '',
                    phone: '',
                    blank_pdf: true,
                    quarter_enable: true,
                    logo: '',
                    circle_logo: '',
                    favicon_icon: '',
                    resident_logo: '',
                    logo_upload: '',
                    resident_logo: '',
                    circle_logo_upload: '',
                    favicon_icon_upload: '',
                    resident_logo_upload: '',
                    resident_logo_upload: '',
                    listing_approval_enable: true,
                    pinboard_approval_enable: false,
                    comment_update_timeout: 60,
                    iframe_url: '',
                    mail_from_name: '',
                    mail_from_email: '',
                    mail_host: '',
                    mail_port: '',
                    mail_username: '',
                    mail_password: '',
                    mail_powered_by: '',
                    cleanify_email: '',
                    address: {
                        state: {}
                    },
                    contact_enable: false,
                    accent_color: '',
                    primary_color: '',
                    login_variation: '',
                    login_variation_2_slider: false,
                    pdf_font_family: '',
                    visibleDrawer: false,
                    drawerTabsModel: 'iframe',
                    iframe_url: '',
                },
                iFrameRules: {
                    iframe_url: [  
                        { required: true, message: this.$t("settings.iframe_url.validation"), trigger: 'blur' },
                        { type: 'url', message: this.$t("settings.iframe_url.label") },
                    ]
                },    
                cleanifyRules:{
                    cleanify_email:[
                        { required: true, message: this.$t("settings.cleanify_url.validation"), trigger: 'blur' },
                        { type: 'email', message: this.$t("settings.cleanify_url.label") },
                    ]
                },
                Iframe_drawer_val:false,
                Gocaution_drawer_val: false,
                Cleanify_drawer_val: false,
                main_drawer:false,
                direction: 'rtl',
                appName : '',
                logo_upload_img: '',
                circle_logo_upload_img: '',
                favicon_icon_upload_img: '',
                resident_logo_upload_img: '',
                activeName: 'settings',
                activeSettingsName: 'settings_settings',
                activeRequestName: 'templates',
                activeResidentsName: 'login_variations',
                templateEditMode: false,
                states: [],
                mail_powered_by: [
                    {
                        label: 'Propify (Propify AG)',
                        value: 0
                    }, {
                        label: 'Fortimo (Fortimo AG)',
                        value: 1
                    }
                ],
                mailEncryption: [
                    'tls',
                    'ssl'
                ],
                fonts: [{
                    label: 'Arial'
                }, {
                    label: 'Times New Roman'
                }]
            }
        },
        async created() {
            await this.fetchSettings();
            

            if (this.$route.query.tab) {
                this.goToTab(this.$route.query.tab);
            }
        },
        mounted() {
            this.model.accent_color = this.model.accent_color ?
                this.model.accent_color :
                this.$constants.colors.accent_color;

            this.model.primary_color = this.model.primary_color ?
                this.model.primary_color :
                this.$constants.colors.primary_color;

            this.$root.$on('changeLanguage', () => this.fetchSettings());
        },
        computed: {
            SettingsLogo() {
                return this.model.logo ? `/${this.model.logo}?${Date.now()}` : '';
            },
            SettingsCircleLogo() {
                return this.model.circle_logo ? `/${this.model.circle_logo}?${Date.now()}` : '';
            },
            SettingsFaviconIcon() {
                return this.model.favicon_icon ? `/${this.model.favicon_icon}?${Date.now()}` : '';
            },
            SettingsResidentLogo() {
                return this.model.resident_logo ? `/${this.model.resident_logo}?${Date.now()}` : '';
            },
            validationRules() {
                setTimeout(() => {this.validateForm('SettingsSettingsForm')}, 0);
                return {
                    email: [{
                        required: true,
                        message: this.$t("general.email_validation.required")
                    },
                        {
                            type: 'email',
                            message: this.$t("general.email_validation.email")
                        }
                    ],
                    name: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('general.name')})
                    }],
                    iframe_url: [{
                        type: 'url',
                        message: this.$t('validation.url',{attribute: this.$t('settings.iframe_url.label')})
                    }],
                    cleanify_email: [{
                        type: 'email',
                        message: this.$t('validation.email',{attribute: this.$t('settings.cleanify_email.label')})
                    }],
                    mail_from_name: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('settings.mail_from_name.label')})
                    }],
                    mail_from_address: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('settings.mail_from_address.label')})
                    },
                        {
                            type: 'email',
                            message: this.$t('validation.email',{attribute: this.$t('settings.mail_from_address.label')})
                        }
                    ],
                    mail_host: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('settings.mail_host.label')})
                    }],
                    mail_port: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('settings.mail_port.label')})
                    }],
                    mail_username: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('settings.mail_username.label')})
                    }],
                    mail_password: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('settings.mail_password.label')})
                    }],
                    mail_powered_by: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('settings.mail_powered_by.label')})
                    }],
                }
            }
        },
        methods: {
            ...mapActions(['getSettings', 'updateSettings', 'getStates']),
            handleEditClick(id) {
                this.templateEditMode = true;
            },
            goToTab(tabName) {
                this.activeName = tabName;
            },
            validateForm(formName) {
                this.$refs[formName].clearValidate();
            },
            fetchSettings() {
                this.getStates().then((resp) => {
                    this.states = resp.data;
                });
                this.getSettings().then((resp) => {
                    this.model = Object.assign({}, this.model, resp.data);
                    this.$root.$emit('fetch_logo', this.model.logo);
                    try {
                        this.$set(this.model, 'opening_hours', JSON.parse(this.model.opening_hours));
                    } catch (e) {

                    }

                }).catch((error) => {
                    displayError(error);
                });
            },
            saveSettings(form) {
                // this.main_drawer = false;
                // console.log("assas");
                // return;
                //console.log('=='); console.log(form);
                this.$refs[form].validate((valid) => {
                    if (valid) {
                        this.model.primary_color_lighter = this.getLightenDarkenColor(this.model.primary_color, 90) + '59'
                        this.updateSettings(this.model).then((resp) => {
                            
                            this.fetchSettings();
                            displaySuccess(resp);
                            //this.main_drawer = false;
                            var v = this;
                            setTimeout(function () {
                                v.main_drawer = false;
                                document.getElementsByTagName('footer')[0].style.display = "block";
                            }, 3000);

                            // this.main_drawer = false;
                            
                        }).catch((error) => {
                            displayError(error);
                        });
                    }
                });
            },
            setLogoUpload(image) {
                this.model.logo_upload = image;
            },
            setAvatarLogoUpload(image) {
                this.model.logo_upload = image.src;
                this.logo_upload_img = "data:image/png;base64," + image.src;
            },
            setCircleLogoUpload(image) {
                this.model.circle_logo_upload = image.src;
                this.circle_logo_upload_img = "data:image/png;base64," + image.src;
            },
            setFaviconIconUpload(image) {
                this.model.favicon_icon_upload = image.src;
                this.favicon_icon_upload_img = "data:image/png;base64," + image.src;
            },
            setResidentLogoUpload(image) {
                this.model.resident_logo_upload = image.src;
                this.resident_logo_upload_img = "data:image/png;base64," + image.src;
            },
            toggleDrawer () {
                this.visibleDrawer = !this.visibleDrawer
            },
            toggleDrawer () {
                this.visibleDrawer = !this.visibleDrawer
            },
            openIframeDrawer (val) {
                if (!this.visibleDrawer || this.drawerTabsModel === 'iframe') {
                    this.toggleDrawer()
                }
                this.drawerTabsModel = 'iframe'
            },
            openGocautionDrawer (val) {
                if (!this.visibleDrawer || this.drawerTabsModel === 'gocaution') {
                    this.toggleDrawer()
                }
                this.drawerTabsModel = 'gocaution'
            },
            openCleanifyDrawer(val) {
                if (!this.visibleDrawer || this.drawerTabsModel === 'gocaution') {
                    this.toggleDrawer()
                }
                this.drawerTabsModel = 'cleanify'
            },
             Iframe_drawer(val){
                if(val)
                {                    
                    this.main_drawer = true;
                    this.Iframe_drawer_val = true;
                    this.Gocaution_drawer_val = false;
                    this.Cleanify_drawer_val = false;
                    this.appName = 'Iframe';
                    document.getElementsByTagName('footer')[0].style.display = "none";
                }
            },
            Gocaution_drawer(val){
                if(val)
                {
                    this.main_drawer = true;
                    this.Iframe_drawer_val = false;
                    this.Gocaution_drawer_val = true;
                    this.Cleanify_drawer_val = false;
                    this.appName = 'Gocaution';
                    document.getElementsByTagName('footer')[0].style.display = "none";
                }
            },
            Cleanify_drawer(val){
                if(val)
                {
                    this.main_drawer = true;
                    this.Iframe_drawer_val = false;
                    this.Gocaution_drawer_val = false;
                    this.Cleanify_drawer_val = true;
                    this.appName = 'Cleanify';
                    document.getElementsByTagName('footer')[0].style.display = "none";
                }
            }
        },
        watch: {
            activeName(newTab, oldTab) {
                this.$router.push({
                    name: 'adminSettings',
                    query: {
                        tab: newTab
                    }
                });
            },
            'main_drawer': {
                immediate: false,
                handler (state) {
                    // TODO - auto blur container if visible is true first
                    if (!state) {
                        document.getElementsByTagName('footer')[0].style.display = "block";
                    }
                }
            },
            "$route.query": {
                immediate: true,
                handler({page, per_page}, prevQuery) {
                    if(this.$route.name == "login") {
                        return;
                    }
                    
                    if(this.$route.query.template == undefined)
                        this.templateEditMode = false;
                    
                }
            }
        }
    }

</script>

<style lang="scss">
    .settings {
        flex: 1;
    }

    .dashboard-tabpanel {
        .el-tabs--border-card > .el-tabs__header .el-tabs__item{
            flex-basis: 0;
            -webkit-box-flex: 1;
            flex-grow: 1;
            text-align: center;
            color: #495057;
            cursor: pointer;
            font-weight:400;
            -webkit-box-align: center;
            align-items: center;
            text-align: center;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 0 13px !important;

            &.is-active, &:hover{
                background: var(--primary-color);
                //border-radius: 120px;
                border-right-color: none;
                border-left-color: none;
                -ms-flex-positive: 1;
                color: #fff !important;
                transition: background-color .3s ease,color .3s ease !important;
            }

            &:hover{
                background: var(--primary-color-lighter);;
            }

            &:first-child {
                border-top-left-radius: 5px;
                border-bottom-left-radius: 5px;
            }

            &:last-child {
                border-top-right-radius: 5px;
                border-bottom-right-radius: 5px;
            }
        }
        .el-tabs__nav {
            float: none;
            text-align: center;
            border-radius: 120px;
            padding: .75rem;
            display: flex;
            flex-wrap: wrap;
            width: fit-content;
            margin: 1.5rem 0 1.5em auto;

            @media screen and (max-width: 1000px) {
                margin: 1.5rem auto;
            }
        }
        .el-tabs--border-card{
            background:none;
        }
        .el-tabs--border-card{
            border: none;
            background: none;
            box-shadow: none;
        }
        .el-tabs--border-card > .el-tabs__header{
            border-bottom: none !important;
            background: none !important;
        }
        .chart-card{
            //height: 420px;

            overflow: visible;

            .el-card__header {
                padding: 15px;
                font-size: 15px;
            }

            .dashboard-table {
                position: relative;

                .link-container {
                    position: absolute;
                    top: -55px;
                    right: 0px;
                    text-align: right;
                    padding: 20px 15px;
                    font-size: 14px;

                    a {
                        text-decoration: none;
                        color: #525252;

                        &:hover {
                            color: #303133;
                        }
                    }
                }
            }

            .chart-filter {
                display: flex;
                align-items: center;

                &.in-toolbar {
                    position: absolute;
                    top: -42px;
                    right: 50px;

                    background-color: transparent;
                    border-bottom: none;
                    padding: 0;
                }

                .el-radio-button__inner {
                    padding: 8px 12px;
                    font-weight: 400;
                }

                .el-date-editor {
                    width: 135px;

                    .el-input__inner {
                        height: 33px;
                        line-height: 33px;
                    }

                    .el-input__icon {
                        display: flex;
                        justify-content: center;
                        align-items: center;

                        &.el-range__close-icon {
                            display: none;
                        }
                    }
                }

                .el-date-editor--week {
                    width: 160px;

                    input {
                        text-align: center;
                        padding-right: 10px;
                    }

                    .el-input__suffix {
                        display: none;
                    }
                }

                .el-range-editor {
                    width: 250px;
                    padding: 0 0 0 7px;
                    height: 32px;
                    line-height: 32px;

                    .el-range-separator {
                        width: 6%;
                    }
                }
            }

            .apexcharts-toolbar {
                // margin-top: -88px;
                margin-top: -38px;
                margin-right: 7px;
                .apexcharts-menu.open {
                    right: 7px;
                }
            }

            .apexcharts-legend.center.position-bottom {
                padding-top: 10px;
            }

            .el-tabs {
                .el-tabs__header {
                    margin-bottom: 0;
                    .el-tabs__nav {
                        margin: 0;
                        padding: 6px 0;
                        margin-left: 15px;

                        .el-tabs__item {
                            font-size: 15px;
                            font-weight: 400;
                        }
                    }
                }

                .el-tabs__content {
                    overflow: visible;
                }
            }
        }
    }

    .mt-20 {
        margin-top: 20px;
    }

    .settings-tabs.el-tabs.el-tabs--left {
        overflow: auto;
        &::-webkit-scrollbar {
            width: 0;
            height: 0;
        }
        height: calc(100% - 100px);
        > .el-tabs__header.is-left {
            margin-top: 20px;
            margin-left: 20px;
            position: sticky;
            top: 0;
            min-width: 200px;
            height: 100%;
            box-shadow: inset 7px 0 5px -7px rgba(0,0,0,0.2);
            border-radius: 10px;
            background: #fff;
            .el-tabs__nav-wrap {
                &:after {
                    background: transparent;
                }
            }
            .el-tabs__active-bar {
                display: none;
            }
            .el-tabs__item.is-left {
                height: 50px;
                line-height: 50px;
                border-bottom: 1px #ebebeb solid;
                text-align: left;
                .icon {
                    margin-right: 8px;
                }
                &:hover {
                    background: var(--primary-color-lighter);
                }
            }
        }
    }

    .dashboard-tabpanel_left {
        .el-tabs__nav {
            margin: 1.5rem auto 1.5em 0;
        }
        .el-tabs__content {
            overflow: visible;
        }
        .save-tab {
            position: absolute;
            top: -72px;
            right: 20px;
        }
    }

    .switcher {
        .el-form-item__content {
            display: flex;
            align-items: center;
        }
        &.switcher-block {
            .el-form-item__content {
                display: block;
                span {
                    display: block;
                }
            }
        }
        &__label {
            line-height: 1.4em;
            color: #606266;
        }
        &__label-title {
            display: flex;
            align-items: center;
            min-height: 40px;
        }
        &__label-desc {
            margin-top: 0.5em;
            display: block;
            font-size: 0.9em;
        }
        .el-switch {
            margin-top: 10px;
            margin-left: auto;
        }
    }

    .el-radio-group.login-radio-group {
        display: block;
    }

    .el-radio.login-radio {
        display: block;
        .el-radio__input {
            display: none;
        }
        .el-radio__label {
            display: block;
            padding: 0;
        }
        &.is-checked {
            .login-card {
                box-shadow: none;
                border: 1px var(--primary-color) solid;
            }
        }
    }

    .login-card {
        background-color: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        border: 1px #e0e0e0 solid;
        border-radius: 6px;
        transition: all 0.3s;
        &__img {
            min-height: 100px;
            overflow: hidden;
            border-bottom: 1px #e0e0e0 solid;
        }
        &__content {
            padding: 8px;
            white-space: normal;
        }
        &__title {
            line-height: 1.4em;
            font-weight: bold;
        }
    }

    .dis-autofill input {
        cursor: text;
    }

    .settings {
        min-height: calc(100% - 56px - 2rem);

        .requestCategories {
            .heading {
                padding: 0;
                position: absolute;
                top: -82px;
                right: 20px;
            }
            .list-table {
                padding: 0;
            }
        }

        .templates {
            .list-table {
                padding: 0;
            }
        }

        .custom-heading {
            margin-bottom: 20px;
        }

        .el-card {
            border-radius: 6px;
            box-shadow: 0 1px 3px transparentize(#000, .88),
            0 1px 2px transparentize(#000, .76);
            position: relative;

            + .el-card {
                margin-top: 20px;
            }

            .el-form {
                /*max-width: 512px;*/

                .el-button :global([class*="ti"]) {
                    margin-right: 8px;
                }
            }

            .card-label {
                float: none;
                text-align: left;
                color: #606266;
                line-height: 40px;
            }

            .el-form-item:only-child {
                margin-bottom: 0;
            }
        }
    }

    .group-name {
        text-align: left;
        padding-right: 10px;
        box-sizing: border-box;
        font-size: 16px;
        font-weight: bold;
        color: #6AC06F;
        text-transform: capitalize;
        width: 100px;
    }

    .day-wrapper {
        display: flex;
        justify-content: space-between;
    }

    .day-name {
        display: flex;
        align-items: center;
        margin-right: 20px;
    }

    .listing-card .el-form-item {
        .el-form-item__label {
            display: block;
            margin-bottom: 5px;
            line-height: 20px;
            float: none;
            text-align: left;
        }
        .el-form-item__content {
            line-height: 28px;
        }
    }
    .card-boxs label {
        text-align: center !important;
        display: block !important;
        font-size: 15px;
        font-weight: 600;
        padding: 0;
    }
    .card-boxs span.switcher__desc {
    text-align: center;
    font-weight: normal;
    margin-top: 10px;
    line-height: 20px;
    font-size: 13px;
    color: #333;
}
    .card-boxs div {
        text-align: center;
    }
    .card-boxs label p {
        margin: 0 0 10px;
    }
    .card-boxs .el-input {
        margin-top: 12px;
    }
    .ui-drawer {
        .el-tabs {
            height: 100%;
            display: flex;
            flex-direction: column;

            &.el-tabs--card :global(.el-tabs__header) {
                :global(.el-tabs__nav-wrap) {
                    :global(.el-tabs__nav-scroll) {
                        :global(.el-tabs__nav) {
                            border: 0;
                        }
                    }
                }
            }

            :global(.el-tabs__header) {
                margin-bottom: 0;
            }

            :global(.el-tabs__content) {
                padding: 16px;
                height: 100%;
                display: flex;
                flex-direction: column;
                overflow-y: auto;
            }
        }
    }
    .switcher-frist .el-switch {
        margin-top: 10px;
    }
   .card-boxs .icon-menu:after {
        width: auto;
        position: absolute;
        top: -22px;
        left: -10px;
        font-size: 20px;
        z-index: 9;
        content: "\2630";
        font-weight: bold;
        color: #000;
    }
    div#pane-microApps .el-card__body span.icon-cog {
        float: right;
        cursor: pointer;
        visibility: hidden;
        opacity: 0;
        transition: all .5s ease .0s;
        -webkit-transition: all .5s ease .0s;
        -ms-transition: all .5s ease .0s;
        -moz-transition: all .5s ease .0s;
        position: absolute;
        top: 10px;
        right: 12px;
    }
    div#pane-microApps .el-card__body {
        padding-top: 30px;
    }
    .drawer-save {
        float: right !important;
        width: 100px !important;
        min-width: 100px;
        margin-top: 15px;
    }
    div#pane-microApps .card-boxs:hover .el-card__body span.icon-cog {
        opacity: 1 !important;
        visibility: visible !important;
    }
    .ui-drawer .drawer-save {
        float: none !important;
        width: auto !important;
        min-width: 80px;
        margin-top: 15px;
        padding: 6px 15px;
    }
    .ui-drawer .drawer-btn-sec {
        position: absolute;
        width: 94%;
        bottom: 20px;
        text-align: center;
    }
    .ui-drawer.ui-drawer i.icon-cog {
        margin-right: 7px;
    }
    .ui-drawer .drawer-save i.ti-save {
        font-size: 12px;
        margin-right: 3px;
    }

</style>