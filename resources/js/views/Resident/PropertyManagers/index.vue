<template>
    <div class="property-managers">
        <ui-heading icon="icon-users" :title="$t('resident.property_managers')" :description="$t('resident.heading_info.property_manager')">
        </ui-heading>
        <ui-divider />
        <loader v-if="loading" />
        <el-card v-else-if="!loading && !groupedManagers">
            <div class="placeholder" >
                <img class="image" :src="require('img/5d4c33211edfc.png')" />
                <div class="content">
                    <div class="title">{{$t('resident.no_data.property_manager')}}</div>
                    <div class="description">{{$t('resident.no_data_info.property_manager')}}</div>
                </div>
            </div>
        </el-card>
        <el-card v-else>
            <el-timeline>
                <template v-for="(managers, letter) in groupedManagers">
                    <el-timeline-item :key="letter" class="letter" size="large">
                        <el-divider content-position="left">
                            {{letter}}
                        </el-divider>
                    </el-timeline-item>
                    <el-timeline-item v-for="{id, slogan, user} in managers" :key="id">
                        <ui-avatar :size="40" :src="user.avatar" :name="user.name" shadow="hover" />
                        <div class="content">
                            <div class="name">
                                {{user.name}}
                            </div>
                            <div class="phone">
                                {{user.phone}}
                            </div>
                            <div class="email">
                                {{user.email}}
                            </div>
                            <div class="slogan">
                                {{slogan}}
                            </div>
                        </div>
                    </el-timeline-item>
                </template>
            </el-timeline>
        </el-card>
    </div>
</template>

<script>
import Heading from 'components/Heading'
    import Loader from './Loader'
    import {mapGetters} from 'vuex'

    export default {
        components: {
            Loader,
            Heading
        },
        props: {
            limit: {
                type: Number,
                default: -1,
                validator: value => value >= -1
            }
        },
        data () {
            return {
                loading: false,
                groupedManagers: null
            }
        },
        computed: {
            ...mapGetters({
                user: 'loggedInUser'
            }),

            isLoading () {
                return this.loading && !this.residents.length;
            }
        },
        async mounted () {
            const {resident} = this.user
            
            if (resident.building) {
                this.loading = true

                let {managers_last} = await this.$store.dispatch('getBuilding', {
                    id: resident.building.id
                })

                if (this.limit > -1) {
                    managers_last = managers_last.slice(0, this.limit)
                }

                const unorderedList = managers_last.reduce((obj, manager) => {
                    obj[manager.user.name[0]] = obj[manager.user[0]] || []
                    obj[manager.user.name[0]].push(manager)

                    return obj
                }, {})

                this.groupedManagers = Object.keys(unorderedList)
                    .sort((a, b) => a.localeCompare(b))
                    .reduce((obj, key) => {
                        obj[key] = unorderedList[key].sort((a, b) => a.user.name.localeCompare(b.user.name))

                        return obj
                    }, {})

                this.loading = false
            }
        }
    }
</script>

<style lang="scss" scoped>
    .property-managers {
        position: relative;

        &:before {
            content: '';
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background-image: url('~img/5d4c5257a99ab.png');
            background-position: top left;
            background-repeat: no-repeat;
            background-attachment: fixed;
            pointer-events: none;
            z-index: -1;
            filter: opacity(.048);
        }

        .placeholder {
            display: flex;
            padding: 16px;
            text-align: center;
            align-items: center;
            flex-direction: column;
            justify-content: center;

            .image {
                width: 256px;
            }

            .title {
                font-size: 20px;
                font-weight: 800;
                color: var(--color-primary);
            }

            .description {
                font-size: 14px;
                font-weight: 600;
                word-break: break-word;
                color: var(--color-text-placeholder);
            }
        }

        .el-card {
            background-color: transparentize(#fff, .28);
            max-width: 640px;

            .el-timeline {
                padding: 0;
                padding-top: 22px;

                .el-timeline-item {
                    padding-bottom: 1px;

                    &:not(.letter) {
                        :global(.el-timeline-item__wrapper) {
                            padding-left: 38px;
                        }
                    }

                    &:first-child {
                        :global(.el-timeline-item__node) {
                            display: none;

                            &:global(.el-timeline-item__node--large) {
                                top: 0;
                            }
                        }

                        :global(.el-timeline-item__wrapper) {
                            :global(.el-timeline-item__content) {
                                .el-divider {
                                    margin-top: 0;
                                }
                            }
                        }
                    }

                    &:last-child {
                        :global(.el-timeline-item__node) {
                            display: none;

                            &:not(:global(.el-timeline-item__node--large)) {
                                top: 0;
                            }
                        }
                    }

                    // &:not(.letter) {
                    //     &:hover :global(.el-timeline-item__wrapper) :global(.el-timeline-item__content) {
                    //         cursor: pointer;
                    //     }

                    //     :global(.el-timeline-item__wrapper) {
                    //         padding-left: 38px;
                    //     }

                    // }

                    :global(.el-timeline-item__node) {
                        position: relative;
                        display: none;

                        &:not(:global(.el-timeline-item__node--large)) {
                            top: 24px;
                        }

                        &:global(.el-timeline-item__node--large) {
                            top: 28px;
                        }
                    }

                    :global(.el-timeline-item__tail) {
                        border-left: none;
                    }
                    
                    :global(.el-timeline-item__wrapper) {
                        padding-left: 0;
                        
                        :global(.el-timeline-item__content) {
                            display: flex;
                            align-items: center;
                            text-transform: capitalize;

                            .el-divider {
                                background: linear-gradient(to right, #DCDFE6, transparent);

                                .el-divider__text {
                                    &.is-left {
                                        color: var(--color-primary);
                                        background-color: #fff;
                                        font-size: 20px;
                                        font-weight: 900;
                                        width: 32px;
                                        height: 32px;
                                        padding: 0;
                                        border-radius: 50%;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        left: 0;
                                        box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76)
                                    }
                                }
                            }

                            .ui-avatar {
                                align-self: flex-start;
                            }

                            .content {
                                margin-left: 8px;

                                .name {
                                    font-size: 18px;
                                    font-weight: 800;
                                }

                                .phone {
                                    color: var(--color-text-placeholder);
                                }

                                .email {
                                    color: var(--color-text-placeholder);
                                }

                                .slogan {
                                    color: var(--color-text-secondary);
                                    margin-top: 8px;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    @media only screen and (max-width: 676px) {
        .property-managers {
            /deep/ .ui-heading__content__description {
                display: none
            }
        }
    }
</style>