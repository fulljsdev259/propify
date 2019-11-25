<template>
    <div class="avatars-wrapper square-avatars">
        <span :key="index" v-for="(count, index) in counts">
            <el-tooltip
                :content="`${$t(count.label)}: ${countsData[count.prop]}`"
                class="item"
                effect="light" placement="top"
            >
                <avatar 
                    :background-color="count.background"
                    :color="count.color"
                    :initials="` ${countsData[count.prop]}`"
                    :size="30"
                    :style="{'z-index': (800 - index)}"
                    :username="`${countsData[count.prop]}`"
                />
            </el-tooltip>
        </span>
    </div>
</template>
<script>
    import {Avatar} from 'vue-avatar';
    import {mapGetters} from 'vuex'

    export default {
        name: 'RequestCount',
        data() {
            return {
                // counts: [
                // {
                //     prop: 'requests_received_count',
                //     background: '#bbb',
                //     color: '#fff',
                //     label: 'models.request.status.received'
                // }, {
                //     prop: 'requests_assigned_count',
                //     background: '#ebb563',
                //     color: '#fff',
                //     label: 'models.request.status.assigned'
                // }, {
                //     prop: 'requests_in_processing_count',
                //     background: '#ebb563',
                //     color: '#fff',
                //     label: 'models.request.status.in_processing'
                // }, {
                //     prop: 'requests_reactivated_count',
                //     background: '#ebb563',
                //     color: '#fff',
                //     label: 'models.request.status.reactivated'
                // }, {
                //     prop: 'requests_done_count',
                //     background: '#67C23A',
                //     color: '#fff',
                //     label: 'models.request.status.done'
                // }, {
                //     prop: 'requests_archived_count',
                //     background: '#67C23A',
                //     color: '#fff',
                //     label: 'models.request.status.archived'
                // }
                // ]
            }
        },
        props: {
            countsData: {
                type: Object,
                default: () => {
                    return null;
                }
            },
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
            counts() {
                if(this.constants.requests)
                    return Object.entries(this.constants.requests.status).map(([value, label]) => ({
                        prop: 'requests_' + label + '_count',
                        background: this.constants.requests.status_colorcode[value],
                        color: '#fff',
                        label: `models.request.status.${label}`
                    }))
                return []
            }
        },
        components: {
            Avatar
        }
        
    }
</script>
<style lang="scss" scoped>
    .avatars-wrapper {
        display: flex;

        & > span {
        }

        .vue-avatar--wrapper {
            font-size: 12px !important;
        }
    }

    .square-avatars {
        flex-wrap: wrap;

        & > span {
            margin-bottom: 2px;

            & > div {
                position: relative;
                margin-right: 2px;
                cursor: pointer;

                &:hover {
                    z-index: 999 !important;
                }
            }
        }
    }

</style>