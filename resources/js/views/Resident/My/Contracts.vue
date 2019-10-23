<template>
    <placeholder :size="256" :src="require('img/5cf66b5b3c55f.png')" v-if="!loading.visible && !contracts">
        There is no contract available.
        <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</small>
    </placeholder>
    <div class="contracts" v-else-if="contracts">
        <ui-heading icon="ti-book" :title="$t('resident.my_contract')" :description="$t('resident.heading_info.my_contract')">
        </ui-heading>
        <ui-divider />
        <el-row :gutter="12">
            <el-col :md="12" :key="contract.id" v-for="contract in contracts">
                <el-card>
                    <el-divider class="column-divider" content-position="left">{{$t('resident.building')}}</el-divider>
                    <div class="name-line">
                        <b>{{$t('resident.name')}}: </b>
                        <div>
                            <div>{{contract.address.street}} {{contract.address.house_num}}</div>
                            <div>{{contract.address.zip}} {{contract.address.city}}</div>
                        </div>
                    </div>
                    <el-divider class="column-divider" content-position="left">{{$t('resident.unit')}}</el-divider>
                    <div>
                        <b>{{$t('resident.type')}}:</b>
                        {{$t('models.unit.type.' + $constants.units.type[contract.unit.type])}}
                    </div>
                    <div>
                        <b>{{$t('resident.unit_number')}}:</b>
                        {{contract.unit.room_no}}
                    </div>
                    <div>
                        <b>{{$t('resident.floor')}}:</b>
                        {{contract.unit.floor}}
                    </div>
                    <div v-if="contract.unit.basement">
                        <b>Basement:</b>
                        Yes
                    </div>
                    <div v-if="contract.unit.attic">
                        <b>Attic:</b>
                        Yes
                    </div>
                    <div>
                        <b>{{$t('resident.monthly_rent_net')}}:</b>
                        {{contract.monthly_rent_net}}
                    </div>
                    <div>
                        <b>{{$t('general.maintenance')}}:</b>
                        {{contract.monthly_maintenance}}
                    </div>
                    <div>
                        <b>{{$t('general.gross_rent')}}:</b>
                        {{contract.monthly_rent_gross}}
                    </div>
                    <template v-if="contract.rent_start">
                        <el-divider content-position="left">{{$t('resident.rent_date')}}</el-divider>
                        <el-tag class="rent" type="warning" disable-transitions>
                            {{$t('resident.start_date')}}:
                            <el-tag type="warning" effect="plain" disable-transitions>{{contract.rent_start | formatDate}}</el-tag>
                            <template v-if="contract.rent_end">
                                End date: <el-tag type="warning" effect="plain" disable-transitions>{{contract.rent_end | formatDate}}</el-tag>
                            </template>
                        </el-tag>
                    </template>
                    <template v-if="contract.media">
                        <el-divider content-position="left">{{$t('resident.contract_file')}}</el-divider>
                        <template  v-for="file in contract.media">
                            <embed :key="file.id" :src="file.url" style="height: 200px;"/>
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
                contract: null,
                contracts: null,
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
            //     text: this.$t('resident.fetching_message.contract')
            // })

            this.contracts = this.$store.getters.loggedInUser.resident.contracts
            // try {
            //     const {data: {unit, media, address, rent_start, rent_end, }} = await this.$store.dispatch('myTenancy')

            //     this.contract = {unit, address, rent_start, rent_end}

            //     if (media.length) {
            //         this.contract.file = media[media.length - 1]
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

    

   .placeholder {
        height: 100% !important;
        font-size: 20px;
        color: var(--color-main-background-darker);

        small {
            font-size: 72%;
            color: var(--primary-color-lighter);
        }
    }
    .contracts {

        .heading {
            margin-bottom: 24px;
            
            .description {
                color: darken(#fff, 40%);
            }
        }

        .el-divider--horizontal {
            margin: 20px 0;
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
        .contracts {
            /deep/ .ui-heading__content__description {
                display: none
            }
        }
    }

    @media only screen and (max-width: 991px) {
        .contracts {
            .el-col:not(:last-child) {
                margin-bottom: 10px;
            }
        }
    }
</style>