<template>
    <div class="avatars-wrapper square-avatars">
        <span :key="index" v-for="(count, index) in counts">
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

    export default {
        name: 'PinboardCount',
        data() {
            return {
                counts: [
                    {
                        prop: 'comments_count',
                        background: '#ebb563',
                        color: '#fff',
                        label: 'models.pinboard.comments'
                    }, {
                        prop: 'likes_count',
                        background: '#67C23A',
                        color: '#fff',
                        label: 'models.pinboard.likes'
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
        },
        mounted() {
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