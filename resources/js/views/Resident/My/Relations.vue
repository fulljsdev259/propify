<template>
    <placeholder :size="256" :src="require('img/5cf66b5b3c55f.png')" v-if="!loading.visible && !relations">
        There is no relation available.
        <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</small>
    </placeholder>
    <div class="relations" v-else-if="relations">
        <ui-heading icon="ti-book" :title="$t('resident.my_relation')" :description="$t('resident.heading_info.my_relation')">
        </ui-heading>
        <ui-divider />
        <el-row :gutter="12">
            <el-col :md="12" :key="relation.id" v-for="relation in relations">
                <el-card>
                    <el-divider class="column-divider" content-position="left">{{$t('resident.building')}}</el-divider>
                    <div class="name-line">
                        <b>{{$t('resident.name')}}: </b>
                        <div>
                            <div>{{relation.address.street}} {{relation.address.house_num}}</div>
                            <div>{{relation.address.zip}} {{relation.address.city}}</div>
                        </div>
                    </div>
                    <el-divider class="column-divider" content-position="left">{{$t('resident.unit')}}</el-divider>
                    <div class="item">
                        <b>{{$t('resident.type')}}:</b>
                        {{$t('models.unit.type.' + $constants.units.type[relation.unit.type])}}
                    </div>
                    <div class="item">
                        <b>{{$t('resident.unit_number')}}:</b>
                        {{relation.unit.room_no}}
                    </div>
                    <!-- <div class="item">
                        <b>{{$t('resident.floor')}}:</b>
                        {{relation.unit.floor}}
                    </div>
                    <div v-if="relation.unit.basement" class="item">
                        <b>Basement:</b>
                        Yes
                    </div>
                    <div v-if="relation.unit.attic" class="item">
                        <b>Attic:</b>
                        Yes
                    </div>
                    <div class="item">
                        <b>{{$t('resident.monthly_rent_net')}}:</b>
                        {{relation.monthly_rent_net}}
                    </div>
                    <div class="item">
                        <b>{{$t('general.maintenance')}}:</b>
                        {{relation.monthly_maintenance}}
                    </div>
                    <div class="item">
                        <b>{{$t('general.gross_rent')}}:</b>
                        {{relation.monthly_rent_gross}}
                    </div> -->
                    <!-- <template v-if="relation.start_date">
                        <el-divider content-position="left">{{$t('resident.rent_date')}}</el-divider>
                        <el-tag class="rent" type="warning" disable-transitions>
                            {{$t('resident.start_date')}}:
                            <el-tag type="warning" effect="plain" disable-transitions>{{relation.start_date | formatDate}}</el-tag>
                            <template v-if="relation.end_date">
                                End date: <el-tag type="warning" effect="plain" disable-transitions>{{relation.end_date | formatDate}}</el-tag>
                            </template>
                        </el-tag>
                    </template> -->
                    <template v-if="relation.media">
                        <el-divider content-position="left">{{$t('resident.relation_file')}}</el-divider>
                        <template  v-for="file in relation.media">
                            <embed :key="file.id" :src="file.url" style="height: 500px;"/>
                            <!-- <el-image :key="file.id" :src="file.url" v-if="isFileImage(file)" />
                            <embed :key="file.id" :src="file.url" v-else /> -->
                        </template>
                    </template>
                </el-card>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import Heading from 'components/Heading'
    import Placeholder from 'components/Placeholder'
    import {displayError} from 'helpers/messages'
    import {format} from 'date-fns'
    import VueSticky from 'vue-sticky'

    export default {
        components: {
            Heading,
            Placeholder
        },
        directives: {
            sticky: VueSticky
        },
        filters: {
            formatDate (date) {
                return format(date, 'DD.MM.YYYY')
            }
        },
        data () {
            return {
                relation: null,
                relations: null,
                loading: {
                    visible: true
                }
            }
        },
        methods: {
            isFileImage (file) {
                const ext = file.file_name.split('.').pop()

                return ['jpg', 'jpeg', 'gif', 'bmp', 'png'].includes(ext);
            },
        },
        async mounted () {
            // this.loading = this.$loading({
            //     target: this.$el.parentElement,
            //     text: this.$t('resident.fetching_message.relation')
            // })

            this.relations = this.$store.getters.loggedInUser.resident.relations
            // try {
            //     const {data: {unit, media, address, start_date, end_date, }} = await this.$store.dispatch('myTenancy')

            //     this.relation = {unit, address, start_date, end_date}

            //     if (media.length) {
            //         this.relation.file = media[media.length - 1]
            //     }
            // } catch (error) {
            //     displayError(error)
            // } finally {
            //     this.loading.close()
            // }
        }
    }
</script>

<style lang="scss" scoped>

    @media screen and (max-width: 812px) {
        .item {
            width: 49%;
            display: inline-block;
        }
    }

   .placeholder {
        height: 100% !important;
        font-size: 20px;
        color: var(--color-main-background-darker);

        small {
            font-size: 72%;
            color: var(--primary-color-lighter);
        }
    }
    .relations {

        .heading {
            margin-bottom: 24px;
            
            .description {
                color: darken(#fff, 40%);
            }
        }

        .el-divider--horizontal {
            margin: 20px 0;
        }

        .el-row {
            .el-col {
                margin-bottom: 1em;
            }
        }

        .name-line {
            display: flex;

            b {
                margin-right: 5px;
            }
        }

        &:not(.empty):before {
            content: '';
            position: fixed;
            bottom: 0;
            right: 0;
            background-image: url('~img/5d066fc2eaf44.png');
            background-repeat: no-repeat;
            background-position: 100% 100%;
            width: 100%;
            height: 100%;
            opacity: .16;
            pointer-events: none;
        }

        .el-card {
            position: relative;
            z-index: 1;
            max-width: 1024px;

            :global(.el-card__body) {
                padding: 16px;

                > div {
                    &.el-divider {
                        :global(.el-divider__text.is-left) {
                            font-size: 16px;
                            left: 0;
                            padding-left: 0;
                        }
                    }

                    &:not(.el-divider):not(.el-image) {
                        &:not(:last-child) {
                            margin-bottom: 8px;
                        }
                    }
                }

                > .el-tag {
                    width: 100%;
                    height: auto;
                    padding: 4px 8px;
                    font-size: 14px;

                    .el-tag {
                        height: auto;
                        font-size: 14px;
                        font-weight: bold;
                        line-height: 1.8;
                        border-radius: 12px;
                    }
                }
                
                embed {
                    width: 100%;
                    height: 100vh;
                }
            }
        }
        
        
    }

    @media only screen and (max-width: 676px) {
        .relations {
            /deep/ .ui-heading__content__description {
                display: none
            }
        }
    }

    @media only screen and (max-width: 991px) {
        .relations {
            .el-col:not(:last-child) {
                margin-bottom: 10px;
            }
        }
    }
</style>