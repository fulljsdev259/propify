<template>
    <div class="residents-view">
        <heading :title="$t('models.resident.view_title')" icon="icon-group">
            <template slot="description" v-if="model.resident_format">
                <div class="subtitle">{{model.resident_format}}</div>
            </template>
            <el-form label-position="top" label-width="192px" ref="form">
                <el-form-item>
                    <el-button
                            @click="downloadCredentials"
                            size="mini"
                            type="primary"
                            round
                    >
                        {{$t('models.resident.download_credentials')}}
                    </el-button>
                    <el-button
                            @click="sendCredentials"
                            size="mini"
                            type="primary"
                            round
                    >
                        {{$t('models.resident.send_credentials')}}
                    </el-button>
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
                                    src="~img/man.png"
                                    class="user-image"
                                    v-if="model.avatar==null && model.title == 'mr'"/>
                                <img
                                    src="~img/woman.png"
                                    class="user-image"
                                    v-else-if="model.avatar==null && model.title == 'mrs'"/>
                                <img
                                    src="~img/company.png"
                                    class="user-image"
                                    v-else-if="model.avatar==null && model.title == 'company'"/>
                                <img
                                    style="width: 100%;"
                                    class="user-image"
                                    :src="`/${user.avatar}?${Date.now()}`"
                                    v-else
                                />
                            </el-col>
                            <el-col :md="7">
                                <h3 class="user-name">
                                    {{ model.first_name }} {{ model.last_name }} 
                                    <span 
                                        v-if="model.settings.language=='en'"
                                        class='flag-icon flag-icon-us'>
                                    </span>
                                    <span
                                        v-else
                                        :class="'flag-icon flag-icon-' + model.settings.language">
                                    </span>
                                </h3>
                                <p class="user-info text-secondary" v-if="model.title === titles.company">{{model.company}}</p>
                                <i class="icon-dot-circled" :class="[constants.residents.status[model.status] === 'active' ? 'icon-success' : 'icon-danger']"></i>
                                {{ constants.residents.status[model.status] ? $t('models.resident.status.' + constants.residents.status[model.status]) : ''}}
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
                                    <el-col v-if="((model.private_phone === '') || (model.private_phone === null))" :sm="16" :xs="12" class="text-secondary">
                                        {{$t('general.no_data')}}
                                    </el-col>
                                    <el-col v-else :sm="16" :xs="12" class="text-secondary">
                                        {{model.private_phone}}
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
                                        {{ new Date(model.birth_date) | formatDate }}
                                    </el-col>
                                </el-row>

                                <el-row :gutter="20">
                                    <el-col :sm="8" :xs="12">{{$t('models.resident.nation')}}:</el-col>
                                    <el-col :sm="16" :xs="12" class="text-secondary">
                                        <span v-if="countries.length > 0 && model.nation">
                                            {{ countries.find(element => +element.id === +model.nation).name }}
                                        </span>
                                    </el-col>
                                </el-row>
                            </el-col>
                        </el-row>
                    </el-card>
                </el-col>
                <el-col :md="7" class="left-card">
                   <el-card class="chart-card left-card-data-section">
                        <h3 class="right-card">
                            <i class="ti-user"/>
                            {{$t('models.resident.contract.title')}}
                        </h3>
                        <el-row :gutter="20">
                            <el-col :sm="8" :xs="12">{{$t('models.resident.contract.rent_start')}}:</el-col>
                            <el-col :sm="16" :xs="12" class="text-secondary" v-if="model.rent_start">
                                {{new Date(model.rent_start) | formatDate }}
                            </el-col>
                            <el-col :sm="16" :xs="12" class="text-secondary" v-else>
                               &#8203;
                            </el-col>

                            <el-col :sm="8" :xs="12">{{$t('models.resident.contract.rent_end')}}:</el-col>
                            <el-col :sm="16" :xs="12" class="text-secondary" v-if="model.rent_end">
                                {{  new Date(model.rent_end) | formatDate }}
                            </el-col>
                            <el-col :sm="16" :xs="12" class="text-secondary" v-else>
                                &#8203;
                            </el-col>

                            <el-col v-if="lastMedia" class="media">
                                <a :href="lastMedia.url" target="_blank"><strong>{{lastMedia.name}}</strong></a>
                            </el-col>

                            <el-col v-if="lastMedia">
                                <template v-if="lastMedia && lastMedia.name">
                                    <el-image :src="lastMedia.url" style="width: 100%" v-if="isFileImage(lastMedia)"/>
                                    <embed :src="lastMedia.url" style="width: 100%" v-else/>
                                </template>
                            </el-col>
                        </el-row>
                   </el-card>
                    <el-card class="chart-card left-card-data-section">
                        <h3 class="right-card">
                            <i class="icon-commerical-building icon"/>
                            {{$t('models.resident.building.name')}}
                        </h3>
                        <el-row :gutter="20">
                            <el-col :sm="8" :xs="12">{{$t('models.resident.building.name')}}:</el-col>
                            <el-col :sm="16" :xs="12" class="text-secondary">
                                {{
                                buildings.filter(building => building.id==model.building_id).length>0 ?
                                buildings.filter(building => building.id==model.building_id)[0].name
                                : '&nbsp;'
                                }}
                            </el-col>
                            <el-col :sm="8" :xs="12">{{$t('models.resident.unit.name')}}:</el-col>
                            <el-col :sm="16" :xs="12" class="text-secondary">
                                {{
                                units.filter(unit => unit.id==model.unit_id).length>0 ?
                                units.filter(unit => unit.id==model.unit_id)[0].name
                                : '&nbsp;'
                                }}
                            </el-col>

                            <el-col :sm="8" :xs="12">{{$t('general.street')}}:</el-col>
                            <el-col :sm="16" :xs="12" class="text-secondary">
                                {{
                                buildings.filter(building => building.id==model.building_id).length>0 ?
                                buildings.filter(building => building.id==model.building_id)[0].address.street
                                : '&nbsp;'
                                }}
                            </el-col>

                            <el-col :sm="8" :xs="12">{{$t('models.building.house_num')}}:</el-col>
                            <el-col :sm="16" :xs="12" class="text-secondary">
                                {{
                                buildings.filter(building => building.id==model.building_id).length>0 ?
                                buildings.filter(building => building.id==model.building_id)[0].address.house_num
                                : '&nbsp;'
                                }}
                            </el-col>

                            <el-col :sm="8" :xs="12">{{$t('general.zip')}}:</el-col>
                            <el-col :sm="16" :xs="12" class="text-secondary">
                                {{
                                buildings.filter(building => building.id==model.building_id).length>0 ?
                                buildings.filter(building => building.id==model.building_id)[0].address.zip
                                : '&nbsp;'
                                }}
                            </el-col>

                            <el-col :sm="8" :xs="12">{{$t('general.city')}}:</el-col>
                            <el-col :sm="16" :xs="12" class="text-secondary">
                                {{
                                buildings.filter(building => building.id==model.building_id).length>0 ?
                                buildings.filter(building => building.id==model.building_id)[0].address.city
                                : '&nbsp;'
                                }}
                            </el-col>
                        </el-row>
                        <el-divider style="margin: 0;"></el-divider>
                        <el-row :gutter="20">
                            <el-col :sm="12" :xs="12">{{$t('models.unit.type.label')}}:</el-col>
                            <el-col :sm="12" :xs="12" class="text-secondary">

                                {{
                                !!$constants.units.type[unit.type] ?
                                $t('models.unit.type.' + $constants.units.type[unit.type])
                                : '&nbsp;'
                                }}
                            </el-col>

                            <el-col :sm="12" :xs="12">{{$t('models.unit.room_no')}}:</el-col>
                            <el-col :sm="12" :xs="12" class="text-secondary">
                                {{unit.room_no}}&nbsp;
                            </el-col>

                            <el-col :sm="12" :xs="12">{{$t('general.monthly_rent_net')}}:</el-col>
                            <el-col :sm="12" :xs="12" class="text-secondary">
                                {{unit.monthly_rent_net}}&nbsp;
                            </el-col>

                            <el-col :sm="12" :xs="12">{{$t('models.unit.floor')}}:</el-col>
                            <el-col :sm="12" :xs="12" class="text-secondary">
                                {{unit.floor}}&nbsp;
                            </el-col>
                            
                            <el-col :sm="12" :xs="12">{{$t('models.unit.basement')}}:</el-col>
                            <el-col :sm="12" :xs="12">
                                <div v-if="unit.basement">
                                    <el-tag size="mini">{{$t('models.unit.basement')}}</el-tag>
                                </div>
                                <div v-else>
                                    &nbsp;
                                </div>
                            </el-col>

                            <el-col :sm="12" :xs="12">{{$t('models.unit.attic')}}:</el-col>
                            <el-col :sm="12" :xs="12">
                                <div v-if="unit.attic">
                                    <el-tag size="mini">{{$t('models.unit.attic')}}</el-tag>
                                </div>
                                <div v-else>
                                    &nbsp;
                                </div>
                            </el-col>
                        </el-row>
                    </el-card>
                </el-col>
                <el-col :md="17">
                    <el-row :gutter="20">
                        <el-col>
                           <el-card class="chart-card">
                                <h3 class="right-card">
                                    <i class="icon-chat-empty icon"/>
                                    {{ $t('general.requests') }}
                                </h3>
                                <Timeline
                                        :filterValue="model.id"
                                        :noDataMessage="$t('general.no_requests')"
                                        fetchAction="getRequests"
                                        filter="resident_id"
                                        v-if="model.id"
                                />
                           </el-card>
                        </el-col>
                        <el-col>
                            <el-card class="chart-card">
                                <h3 class="right-card">
                                    <i class="icon-megaphone-1 icon"/>
                                    {{ $t('models.resident.pinboard') }}
                                </h3>
                                <Timeline
                                        :filterValue="user.id"
                                        :noDataMessage="$t('general.no_pinboard')"
                                        fetchAction="getPinboardTruncated"
                                        filter="user_id"
                                        v-if="!_.isEmpty(user)"
                                />
                            </el-card>
                        </el-col>
                        <el-col>
                            <el-card class="chart-card">
                                <h3 class="right-card">
                                    <i class="icon-basket icon"/>
                                    {{ $t('models.resident.listings') }}
                                </h3>
                                <Timeline
                                        :filterValue="user.id"
                                        :noDataMessage="$t('general.no_listings')"
                                        fetchAction="getListings"
                                        filter="user_id"
                                        v-if="!_.isEmpty(user)"
                                />
                            </el-card>
                        </el-col>
                    </el-row>
                </el-col>
            </el-row>
        </div>
    </div>

</template>

<script>
    import Heading from "components/Heading";
    import AdminResidentsMixin from "mixins/adminResidentsMixin";
    import UnitsMixin from 'mixins/adminUnitsMixin';
    import RelationList from "components/RelationListing";
    import Timeline from "components/ResidentViewTimeline"
    import {mapActions, mapGetters} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import {format} from 'date-fns'

    const mixin = AdminResidentsMixin({
        mode: "view"
    });
    const unitsMixin = UnitsMixin({
        mode: 'add'
    });

    export default {
        mixins: [mixin, unitsMixin],
        components: {
            Heading,
            RelationList,
            Timeline
        },
        data() {
            return {
                requests: [],
                pinboard: [],
                listings: [],

            };
        },
        filters: {
            formatDate(date) {
                return format(date, 'DD.MM.YYYY')
            }
        },
        methods: {
            ...mapActions(['downloadResidentCredentials', 'sendResidentCredentials']),
            edit() {
                this.$router.push({
                    name: "adminResidentsEdit",
                    params: {
                        id: this.$route.params.id
                    }
                });
            },
            async downloadCredentials() {
                this.loading.state = true;
                try {
                    const resp = await this.downloadResidentCredentials({id: this.model.id});
                    if (resp && resp.data) {
                        const url = window.URL.createObjectURL(new Blob([resp.data], {type: resp.headers['content-type']}));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', resp.headers['content-disposition'].split('filename=')[1]);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(url);
                    }
                } catch (e) {
                    displayError(e)
                }
            },
            async sendCredentials() {
                try {
                    const resp = await this.sendResidentCredentials({id: this.model.id});                    
                    if (resp && resp.data) {
                        displaySuccess(resp.data);
                    }
                } catch (e) {
                    displayError(e);
                }
            },
            goToListing() {
                return this.$router.push({
                    name: 'adminResidents',
                    query: this.queryParams
                })
            },
        },
        mounted() {
            this.$root.$on('changeLanguage', () => this.getCountries());
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
    .residents-view {
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
