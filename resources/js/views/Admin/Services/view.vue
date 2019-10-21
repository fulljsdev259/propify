<template>
    <div class="services-view">
        <heading :title="$t('models.service.view_title')" icon="icon-group">
            <template slot="description" v-if="model.service_provider_format">
                <div class="subtitle">{{model.service_provider_format}}</div>
            </template>
            <el-form label-position="top" label-width="192px" ref="form">
                <el-form-item>
                    <el-button
                            @click="edit"
                            icon="ti-pencil"
                            round
                            size="mini"
                            type="primary"
                    ><span>&nbsp;</span>{{$t('general.actions.update')}}
                    </el-button>
                    <el-button
                            @click="goToListing"
                            size="mini"
                            type="warning"
                            round
                    > {{this.$t('general.actions.close')}}
                    </el-button>
                </el-form-item>
            </el-form>
        </heading>
        <div class="body">
            <el-row :gutter="30">
                <el-col>
                    <el-card class="chart-card">
                        <el-row :gutter="20" class="main-section">
                            <el-col :md="4">

                                <img
                                    style="width: 100%;"
                                    class="user-image"
                                    :src="`/${user.avatar}?${Date.now()}`"
                                    
                                />
                            </el-col>
                            <el-col :md="7">
                                <h3 class="user-name">
                                    {{ model.name }} 
                                    <span 
                                        v-if="model.settings.language=='en'"
                                        class='flag-icon flag-icon-us'>
                                    </span>
                                    <span
                                        v-else
                                        :class="'flag-icon flag-icon-' + model.settings.language">
                                    </span>
                                </h3>
                            </el-col>
                            <el-col :md="13" class="info">
                                <el-row :gutter="20">
                                    <el-col :sm="8" :xs="12">{{$t('models.resident.mobile_phone')}}:</el-col>
                                    <el-col v-if="((model.mobile_phone === '') || (model.mobile_phone === null))" :sm="16" :xs="12" class="text-secondary">
                                        {{$t('general.no_data')}}
                                    </el-col>
                                    <el-col v-else :sm="16" :xs="12" class="text-secondary">
                                        {{model.mobile_phone}}
                                    </el-col>
                                </el-row>

                                <el-row :gutter="20">
                                    <el-col :sm="8" :xs="12">{{$t('models.resident.private_phone')}}:</el-col>
                                    <el-col v-if="((user.phone === '') || (user.phone === null))" :sm="16" :xs="12" class="text-secondary">
                                        {{$t('general.no_data')}}
                                    </el-col>
                                    <el-col v-else :sm="16" :xs="12" class="text-secondary">
                                        {{user.phone}}
                                    </el-col>                                    
                                </el-row>

                                <el-row :gutter="20">
                                    <el-col :sm="8" :xs="12">{{$t('models.resident.work_phone')}}:</el-col>
                                    <el-col v-if="((model.work_phone === '') || (model.work_phone === null))" :sm="16" :xs="12" class="text-secondary">
                                        {{$t('general.no_data')}}
                                    </el-col>
                                    <el-col v-else :sm="16" :xs="12" class="text-secondary">
                                        {{model.work_phone}}
                                    </el-col>                                    
                                </el-row>

                                <el-row :gutter="20">
                                    <el-col :sm="8" :xs="12">{{$t('general.email')}}:</el-col>
                                    <el-col :sm="16" :xs="12" class="text-secondary">
                                       {{ model.email}}
                                    </el-col>
                                </el-row>

                                <el-row :gutter="20">
                                    <el-col :sm="8" :xs="12">{{$t('models.resident.birth_date')}}:</el-col>
                                    <el-col :sm="16" :xs="12" class="text-secondary">
                                        <!-- {{ new Date(model.birth_date) | formatDate }} -->
                                    </el-col>
                                </el-row>

                            </el-col>
                        </el-row>
                    </el-card>
                </el-col>
            </el-row>
        </div>
    </div>

</template>

<script>
    import Heading from "components/Heading";
    import AdminServicesMixin from "mixins/adminServicesMixin";
    import RelationList from "components/RelationListing";
    
    import {mapActions, mapGetters} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import {format} from 'date-fns'

    const mixin = AdminServicesMixin({
        mode: "view"
    });

    export default {
        mixins: [mixin],
        components: {
            Heading,
            RelationList,
        },
        data() {
            return {
            };
        },
        filters: {
            formatDate(date) {
                return format(date, 'DD.MM.YYYY')
            }
        },
        methods: {
            edit() {
                this.$router.push({
                    name: "adminServicesEdit",
                    params: {
                        id: this.$route.params.id
                    }
                });
            },
            goToListing() {
                return this.$router.push({
                    name: 'adminServices',
                    query: this.queryParams
                })
            },
        },
        mounted() {
            
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
            lastMedia() {
                return this.model.media ? this.model.media[this.model.media.length - 1] : null;
            },
        }
    };

</script>

<style lang="scss" scoped>
   .main-section {
        padding: 8px 0px 0 0 !important;
        margin-left: -5px !important;
    }
    .services-view {
        .heading {
            margin-bottom: 20px;
        }

        .body {
            padding: 0 20px;
            .chart-card{
                margin-bottom: 30px !important;
                padding: 10px;
                height: 100%;

                h3 {
                    font-size: 24px;
                    font-weight: 500;
                }

                .right-card {
                    color: var(--text-color);
                    font-size: 18px;
                    font-weight: 400;
                    margin-bottom: 10px;
                    margin-top: 0;
                }

                .user-name {
                    margin-top: 5px;
                    margin-bottom: 0px;
                    span {
                        margin-left: 10px;
                        font-size: 16px;
                    }
                }

                .user-info {
                    font-size: 13px;
                    margin-top: 0;
                    margin-bottom: 15px;
                }

                .info {
                    border-left: 2px dashed #ccc;
                    padding-left: 30px !important;
                    margin-top: 5px;

                    .el-row:not(:first-child) {
                        margin-top: 10px;
                    }

                    .el-col:nth-last-of-type(1),
                    .el-col:nth-last-of-type(2) {
                        padding-bottom: 0 !important;
                    }
                    a{
                        text-decoration: none;
                        color: #009ce7;
                    }
                }

                .left-card-row {
                    .el-col {
                        font-size: 18px;
                        margin-bottom: 10px;
                    }
                }
                &__body {
                padding: 10px 0 0 0 !important;
            }
            }

            .user-image {
                border-radius: 50%;
                max-height: 150px !important;
                max-width: 150px !important;
            }

            .media {
                margin-top: 20px;
            }

            .media-image {
                margin-top: 30px;
                height: 200px !important;
                max-width: 100% !important;
            }
        }

        .mb-10 {
            margin-bottom: 15px;
        }
        .text-secondary {
            color: #909399;
        }
        .icon-success {
            color: #5fad64;
        }
        .icon-danger {
            color: #dd6161;
        }
    }

</style>
