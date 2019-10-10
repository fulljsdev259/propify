<template>
    <placeholder :size="256" :src="require('img/5cf66b5b3c55f.png')" v-if="!loading.visible && !contract">
        There is no contract available.
        <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</small>
    </placeholder>
    <div class="contracts" v-else-if="contract">
        <ui-heading icon="ti-book" :title="$t('tenant.my_contract')" :description="$t('tenant.heading_info.my_contract')">
        </ui-heading>
        <ui-divider />
        <el-card>
            <el-divider class="column-divider" content-position="left">{{$t('tenant.building')}}</el-divider>
            <div>
                <b>{{$t('tenant.name')}}: </b>
                <div>{{contract.address.street}} {{contract.address.house_num}}</div>
                <div>{{contract.address.zip}} {{contract.address.city}}</div>
            </div>
            <el-divider class="column-divider" content-position="left">{{$t('tenant.unit')}}</el-divider>
            <div>
                <b>{{$t('tenant.type')}}:</b>
                {{$t('models.unit.type.' + $constants.units.type[contract.unit.type])}}
            </div>
            <div>
                <b>{{$t('tenant.unit_number')}}:</b>
                {{contract.unit.room_no}}
            </div>
            <div>
                <b>{{$t('tenant.floor')}}:</b>
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
                <b>{{$t('tenant.monthly_rent_net')}}:</b>
                {{contract.unit.monthly_rent_net}}
            </div>
            <template v-if="contract.rent_start">
                <el-divider content-position="left">{{$t('tenant.rent_date')}}</el-divider>
                <el-tag class="rent" type="warning" disable-transitions>
                    {{$t('tenant.start_date')}}:
                    <el-tag type="warning" effect="plain" disable-transitions>{{contract.rent_start | formatDate}}</el-tag>
                    <template v-if="contract.rent_end">
                        End date: <el-tag type="warning" effect="plain" disable-transitions>{{contract.rent_end | formatDate}}</el-tag>
                    </template>
                </el-tag>
            </template>
            <template v-if="contract.file">
                <el-divider content-position="left">{{$t('tenant.contract_file')}}</el-divider>
                <el-image :src="contract.file.url" v-if="isFileImage(contract.file)" />
                <embed :src="contract.file.url" v-else />
            </template>
        </el-card>
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
                loading: {
                    visible: true
                }
            }
        },
        methods: {
            isFileImage (file) {
                const ext = file.name.split('.').pop()

                return ['jpg', 'jpeg', 'gif', 'bmp', 'png'].includes(ext);
            },
        },
        async mounted () {
            this.loading = this.$loading({
                target: this.$el.parentElement,
                text: this.$t('tenant.fetching_message.contract')
            })

            try {
                const {data: {unit, media, address, contract, rent_start, rent_end}} = await this.$store.dispatch('myTenancy')

                this.contract = {unit, address, rent_start, rent_end}

                if (media.length) {
                    this.contract.file = media[media.length - 1]
                }
            } catch (error) {
                displayError(error)
            } finally {
                this.loading.close()
            }
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
</style>