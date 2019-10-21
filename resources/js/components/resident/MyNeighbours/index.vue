<template>
    <div>
        <loader v-if="loading" />
        <div class="placeholder" v-else-if="!loading && !groupedResidents">
            <img class="image" :src="require('img/5d4c33211edfc.png')" />
            <div class="content">
                <div class="title">{{$t('resident.no_data.neighbour')}}</div>
                <div class="description">{{$t('resident.no_data_info.neighbour')}}</div>
            </div>
        </div>
        <div class="my-neighbours" v-else>
            <el-input prefix-icon="el-icon-search" v-model="model" placeholder="Type to search through your neighbours..." clearable v-if="showSearch" />
            <el-timeline>
                <template v-for="(residents, letter) in groupedResidents">
                    <el-timeline-item :key="letter" class="letter" size="large">
                        <el-divider content-position="left">
                            {{letter}}
                        </el-divider>
                    </el-timeline-item>
                    <el-timeline-item v-for="{id, title, first_name, last_name, user} in residents" :key="id">
                        <ui-avatar :src="user.avatar" :size="36" :name="user.name" shadow="hover" />
                        <div class="content">
                            <!-- <div class="title">
                                {{title}}
                            </div> -->
                            <div class="name">
                                {{first_name}} {{last_name}}
                            </div>
                        </div>
                        <!-- <el-tooltip content="Send an email" v-if="title !== 'company'">
                            <el-button size="small" icon="el-icon-message" plain circle></el-button>
                        </el-tooltip> -->
                    </el-timeline-item>
                </template>
            </el-timeline>
        </div>
    </div>
</template>

<script>
    import Loader from './Loader'
    import {mapGetters} from 'vuex'
    import {EXTRA_LOADING_SECONDS} from 'config'

    export default {
        name: 'p-my-neighbours',
        components: {
            Loader
        },
        props: {
            limit: {
                type: Number,
                default: -1,
                validator: value => value >= -1
            },
            showSearch: {
                type: Boolean,
                default: false
            }
        },
        data () {
            return {
                model: '',
                loading: false,
                groupedResidents: null,
                timeout: null
            }
        },
        computed: {
            ...mapGetters({
                user: 'loggedInUser'
            })
        },
        async mounted () {
            const {resident} = this.user

            if (resident.building_id) {
                this.loading = true

                let {residents} = await this.$store.dispatch('getBuilding', {
                    id: resident.building_id
                })

                // exclude the loggedin resident
                residents = residents.filter(resident => resident.id != this.$store.getters.loggedInUser.resident.id) 

                if (this.limit > -1) {
                    residents = residents.slice(0, this.limit)
                }

                const unorderedList = residents.reduce((obj, resident) => {
                    obj[resident.first_name[0]] = obj[resident.first_name[0]] || []
                    obj[resident.first_name[0]].push(resident)

                    return obj
                }, {})

                this.groupedResidents = Object.keys(unorderedList)
                    .sort((a, b) => a.localeCompare(b))
                    .reduce((obj, key) => {
                        obj[key] = unorderedList[key].sort((a, b) => a.first_name.localeCompare(b.first_name))

                        return obj
                    }, {})

                this.timeout = setTimeout(() => this.loading = false, EXTRA_LOADING_SECONDS)
            }
        },
        beforeDestroy () {
            clearTimeout(this.timeout)
        }
    }
</script>

<style lang="scss" scoped>
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

    .my-neighbours {
        .el-input {
            margin-bottom: 32px;
        }

        .el-timeline {
            padding: 0;
            padding-top: 22px;

            .el-timeline-item {
                padding-bottom: 8px;

                &:not(.letter) {
                    &:hover :global(.el-timeline-item__wrapper) :global(.el-timeline-item__content) {
                        cursor: pointer;
                    }

                    :global(.el-timeline-item__wrapper) {
                        padding-left: 38px;
                    }

                }

                &:first-child {
                    :global(.el-timeline-item__node) {
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
                        &:not(:global(.el-timeline-item__node--large)) {
                            top: 0;
                        }
                    }
                }

                :global(.el-timeline-item__tail) {
                    border-left: none;
                }

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

                :global(.el-timeline-item__wrapper) {
                    padding-left: 0px;

                    :global(.el-timeline-item__content) {
                        display: flex;
                        align-items: center;
                        text-transform: capitalize;

                        .content {
                            flex: 1;
                            min-width: 0;
                            display: flex;
                            align-items: center;
                            flex-wrap: wrap;
                            margin-left: 16px;

                            .title,
                            .name {
                                width: 100%;
                                overflow: hidden;
                                min-width: 0;
                                text-overflow: ellipsis;
                                white-space: nowrap;
                            }

                            .title {
                                font-size: 14px;
                                font-weight: 300;
                                color: var(--color-text-placeholder);
                            }

                            .name {
                                color: var(--color-primary);
                                font-size: 16px;
                                font-weight: 600;
                                margin-right: 4px;
                            }
                        }

                        .el-divider {
                            background: linear-gradient(to right, #DCDFE6, rgba(#DCDFE6, 0) 56%);

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
                    }
                }
            }
        }
    }
</style>