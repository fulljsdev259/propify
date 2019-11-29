<template>
    <div class="avatars-wrapper square-avatars">
        <span :key="index" v-for="(count, index) in counts" >

            <el-tooltip
                :content="`${$t(count.label)}: ${countsData[count.prop]}`"
                class="item"
                effect="light" placement="top"
                v-if="countsData[count.prop]"
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
        name: 'RelationCount',
        data() {
            return {
                // counts: [
                // {
                //     prop: 'total_relations_count',
                //     background: '#aaa',
                //     color: '#fff',
                //     label: this.$t('models.resident.relation.status_count.total')
                // }, {
                //     prop: 'active_relations_count',
                //     background: '#5fad64',
                //     color: '#fff',
                //     label: this.$t('models.resident.relation.status_count.active')
                // }, {
                //     prop: 'inactive_relations_count',
                //     background: '#dd6161',
                //     color: '#fff',
                //     label: this.$t('models.resident.relation.status_count.inactive')
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
            }
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
            counts() {
                if(this.constants.relations) {
                    return Object.entries(this.constants.relations.status).map(([value, label]) => ({
                        prop: label + '_relations_count',
                        background: this.constants.relations.status_colorcode[value],
                        color: '#fff',
                        label: `models.resident.relation.status.${label}`
                    }))
                }
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
                margin-right: 0px;
                border: 1px solid #fff;
                cursor: pointer;

                &:hover {
                    z-index: 999 !important;
                }
            }
        }
    }

</style>