<template>
    <div class="units-edit listing-edit mb20">
        <heading :title="$t('models.listing.edit_title')" icon="icon-basket" shadow="heavy" style="margin-bottom: 20px;">
            <edit-actions :saveAction="submit" :deleteAction="deleteListing" route="adminListings"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-col :md="12">
                <card :loading="loading">
                    <el-form :model="model" label-position="top" label-width="192px" ref="form">
                        <el-row :gutter="20">
                            <el-col :lg="12">
                                <el-form-item :label="$t('models.listing.status.label')">
                                    <el-select class="custom-select" v-model="model.status">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.listing.status.${status}`)"
                                            :value="parseInt(key)"
                                            v-for="(status, key) in listingConstants.status">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :lg="12">
                                <el-form-item :label="$t('models.listing.type.label')">
                                    <el-select class="custom-select" v-model="model.type">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.listing.type.${type}`)"
                                            :value="parseInt(key)"
                                            v-for="(type, key) in listingConstants.type">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :lg="12">
                                <el-form-item :label="$t('models.listing.visibility.label')">
                                    <el-select class="custom-select" v-model="model.visibility">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.listing.visibility.${visibility}`)"
                                            :value="parseInt(key)"
                                            v-for="(visibility, key) in listingConstants.visibility">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :lg="12" v-if="model.type !== 4">
                                <el-form-item :label="$t('models.listing.price')" :rules="validationRules.price"
                                              prop="price">
                                    <el-row :gutter="5">
                                        <el-col :span="16">
                                            <el-input type="text" v-model="price.integer">
                                                <span slot="prepend">CHF</span>
                                            </el-input>
                                        </el-col>
                                        <el-col :span="1">.</el-col>
                                        <el-col :span="7">
                                            <el-input type="text" v-model="price.decimals" maxlength="2" />
                                        </el-col>
                                    </el-row>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-tabs type="card" v-model="activeName">
                            <el-tab-pane :label="$t('general.admin_menu.requests')" name="description">    
                                <el-row>
                                    <el-col>
                                        <el-form-item :label="$t('models.listing.listing_title')" :rules="validationRules.title"
                                                    prop="title">
                                            <el-input type="text" v-model="model.title"/>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                                <el-row class="last-form-row">
                                    <el-col>
                                        <el-form-item :label="$t('general.content')" :rules="validationRules.content"
                                                      prop="content"
                                                      :key="editorKey">
                                            <yimo-vue-editor
                                                    :config="editorConfig"
                                                    v-model="model.content"/>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                            </el-tab-pane>
                            <el-tab-pane :label="$t('models.request.images')" name="image">
                                <span slot="label">
                                    <el-badge :value="mediaCount" :max="99" class="admin-layout">{{ $t('models.request.images') }}</el-badge>
                                </span>
                                <card :loading="loading">
                                    <!-- <upload-document @fileUploaded="uploadFiles" class="drag-custom" drag multiple/>
                                    <div class="mt15">
                                        <request-media :data="[...model.media, ...media]" @deleteMedia="deleteMedia"
                                                                    v-if="media.length || (model.media && model.media.length)"></request-media>
                                    </div> -->
                                    <ui-media-gallery :files="model.media.map(({url}) => url)" @delete-media="deleteMediaByIndex"/>
                                    <el-alert
                                        :title="$t('general.upload_all_desc')"
                                        type="info"
                                        show-icon
                                        :closable="false"
                                    >
                                    </el-alert>
                                    <media-uploader ref="media" :id="listing_id" :audit_id="audit_id" type="listings" layout="grid" v-model="media" :upload-options="uploadOptions" />
                                </card>
                            </el-tab-pane>
                        </el-tabs>                    
                    </el-form>
                </card>
            </el-col>
            <el-col :md="12">
                <card :loading="loading" class="contact-info-card">
                    <el-row  :gutter="30" class="contact-info-card-row">
                        <el-col class="contact-info-card-col" :md="8">
                            <span class="custom-label">
                                <i class="icon-user"></i>&nbsp;{{$t('general.user')}}
                            </span>
                            <span class="custom-value" v-if="model.user">
                                <router-link :to="{name: 'adminUsersEdit', params: {id: model.user.id}}" class="resident-link">
                                    <avatar :size="30"
                                            :src="'/' + model.user.avatar"
                                            v-if="model.user.avatar"></avatar>
                                    <avatar :size="28"
                                            :username="model.user.first_name ? `${model.user.first_name} ${model.user.last_name}`: `${model.user.name}`"
                                            backgroundColor="rgb(205, 220, 57)"
                                            color="#fff"
                                            v-if="!model.user.avatar"></avatar>
                                    <span>{{model.user.name}}</span>
                                </router-link>                            
                            </span>
                        </el-col>

                        <el-col class="contact-info-card-col" :md="8">
                            <span class="custom-label">
                                <i class="icon-contacts"></i>&nbsp;{{$t('models.listing.contact')}}
                            </span>
                            <span>
                                {{model.contact}}
                                </span>
                        </el-col>

                        <el-col class="contact-info-card-col" :md="8">
                            <span class="custom-label">
                                <i class="icon-paper-plane"></i>&nbsp;{{$t('models.listing.published_at')}}
                            </span>
                            <span class="custom-value" v-if="model.published_at">
                                    {{model.published_at}}
                                </span>
                            <span v-else>-</span>
                        </el-col>
                        
                        <!-- <el-col :md="12">
                            <span class="custom-label">{{$t('models.listing.contact')}}</span>
                            <br>
                            <span>
                                {{model.contact}}
                                </span>
                        </el-col> -->
                    </el-row>
                     <el-row  :gutter="30" class="contact-info-card-row">
                        <el-col class="contact-info-card-col" :md="8">
                            <span class="custom-label">
                                <i class="icon-thumbs-up"></i>&nbsp;{{$t('models.listing.likes')}}
                            </span>
                            <span class="custom-value">
                                {{model.likes_count}}
                            </span>
                        </el-col>

                        <el-col class="contact-info-card-col" :md="8">
                            <span class="custom-label">
                                <i class="icon-chat"></i>&nbsp;{{$t('models.listing.comments')}}
                            </span>
                            <span class="custom-value">
                                {{model.comments_count}}
                            </span>
                        </el-col>
                        <el-col class="contact-info-card-col" :md="8"></el-col>
                        <!--
                        <el-col :md="8">
                            <span class="custom-label">{{$t('models.listing.comments')}}</span>
                            <br>
                            <span class="custom-value">
                                {{model.comments_count}}
                            </span>
                        </el-col>

                        <el-col :md="8">
                            <span class="custom-label">{{$t('models.listing.published_at')}}</span>
                            <br>
                            <span class="custom-value" v-if="model.published_at">
                                    {{model.published_at}}
                                </span>
                            <span v-else>-</span>
                        </el-col>-->
                    </el-row> 
                </card>
                <card class="mt15" v-if="model.id" :header="$t('models.listing.comments')">
                    <chat :id="model.id" type="listing"/>
                </card>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import {mapActions} from 'vuex';
    import ListingsMixin from 'mixins/adminListingsMixin';
    import EditActions from 'components/EditViewActions';
    import {Avatar} from 'vue-avatar';
    import EditorConfig from 'mixins/adminEditorConfig';

    const mixin = ListingsMixin({mode: 'edit'});

    export default {
        mixins: [
            mixin,
            EditorConfig
        ],
        components: {
            EditActions,
            Avatar,
        },
        data() {
            return {
                activeName: 'description',
            }
        },
        methods: {            
            ...mapActions([
                "deleteListing"
            ])
        },
        computed: {
            mediaCount() {
                if(this.model.media) {
                    return this.model.media.length;
                } else {
                    return 0;
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .el-tabs--border-card {
        border-radius: 6px;
        .el-tabs__header {
            border-radius: 6px 6px 0 0;
        }
        .el-tabs__nav-wrap.is-top {
            border-radius: 6px 6px 0 0;
        }
    }

    .last-form-row {
        margin-bottom: -22px;
    }

    .custom-select {
        display: block;
    }

    .custom-label {
        color: var(--primary-color);
    }

    .mb20 {
        margin-bottom: 20px;
    }
    .contact-info-card {
        .contact-info-card-row {
            display: flex;
            border-bottom: 1px solid #EBEEF5;
            margin-left: 0 !important;
            margin-right: 0 !important;
            &:first-child {
                .contact-info-card-col {
                    padding-top: 0;
                }
            }
            &:last-child {
                border-bottom: 0;
                .contact-info-card-col {
                    padding-bottom: 0;
                }
            }
            .contact-info-card-col {
                &:first-child {
                    padding-left: 0 !important;
                }
                &:last-child {
                    padding-right: 0 !important;
                }
            }
        }
        
        .contact-info-card-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-right: 1px solid #EBEEF5;
            min-height: 57px;
            padding-bottom: 10px;
            padding-top: 10px;
            &:last-child {
                border: none;
            }
        }
    }

    .custom-label {
        color: var(--primary-color);
        display: inline-block;
        margin-bottom: 10px;
    }

    .custom-value {        
        line-height: 28px;
    }

    .resident-link {
        display: flex;
        align-items: center;
        color: var(--primary-color);
        text-decoration: none;

        &:hover {
            color: var(--primary-color-lighter);
        }
        & > span {
            margin-left: 5px;
        }
    }

    .ui-media-gallery {
        margin-bottom: 10px;
    }

</style>
<style lang="scss">
    .listing-edit {
        .el-input-group--prepend .el-input-group__prepend {
            padding: 0 10px;
            font-weight: bold;
        }
        .el-card .el-card__body, .el-card .el-card__header {
            padding: 20px !important;
        }
        #tab-image {
            padding-right: 40px !important;
        }
    }
</style>
