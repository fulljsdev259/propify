<template>
    <div class="avatars-wrapper square-avatars">
        <span :key="index" v-for="(count, index) in counts">
            <el-tooltip
                :content="`${count.label}: ${countsData[count.prop]}`"
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

    export default {
        name: 'ContractCount',
        data() {
            return {
                counts: [
                {
                    prop: 'total_rent_contracts_count',
                    background: '#aaa',
                    color: '#fff',
                    label: this.$t('models.tenant.status.total')
                }, {
                    prop: 'active_rent_contracts_count',
                    background: '#5fad64',
                    color: '#fff',
                    label: this.$t('models.tenant.status.active')
                }, {
                    prop: 'inactive_rent_contracts_count',
                    background: '#dd6161',
                    color: '#fff',
                    label: this.$t('models.tenant.status.not_active')
                }
                ]
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
                margin-right: -10px;
                border: 1px solid #fff;
                cursor: pointer;

                &:hover {
                    z-index: 999 !important;
                }
            }
        }
    }

</style>