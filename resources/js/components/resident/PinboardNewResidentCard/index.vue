<template>
    <el-card>
        <ui-avatar :src="data.user.avatar" :name="data.user.name" :size="48" shadow="always" />
        <div class="user">{{data.user.name}} {{$t('resident.new_neighbour_msg')}}</div>
        <likes type="pinboard" layout="row" :data="data.likes" :suffix="data.likes.length > 1 ? $t('resident.welcome_neighbour_msg_multiple') : $t('resident.welcome_neighbour_msg')" />
        <like :id="data.id" :name="data.user.name" type="pinboard" :icons="{like: 'icon-handshake-o', unlike: 'icon-handshake-o'}" :show-text="false" :show-welcome-text="true"/> <!-- :icons="{like: 'icon-heart', unlike: 'icon-heart-empty'}" -->
        
    </el-card>
</template>

<script>
    import AgoMixin from 'mixins/agoMixin'

    export default {
        name: 'p-pinboard-new-resident-card',
        mixins: [
            AgoMixin
        ],
        props: {
            data: {
                type: Object,
                required: true
            }
        }
    }
</script>

<style lang="scss" scoped>
    .el-card {
        overflow: visible;
        margin-top: 20px;
        margin-bottom: 0;

        :global(.el-card__body) {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-left: 0;
            padding-right: 0;

            .ui-avatar {
                margin-top: -40px;
                border: 4px transparentize(#fff, .28) solid;
            }

            .user {
                font-size: 18px;
                font-weight: 600;
                color: var(--primary-color);
                padding: 10px;
            }

            .content {
                font-size: 14px;
                font-weight: 500;

                b {
                    font-size: 18px;
                    color: var(--color-primary);
                }
            }

            :global(.like) {
                margin-bottom: -30px;
                width: 100%;
                padding: 10px;
                :global(.el-button) {
                    font-size: 15px;
                    width: 100%;
                    height: 40px;
                    padding: 0;
                    background-color: #fff;
                    color: var(--primary-color);
                    border-top: 1px var(--border-color-base) solid;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 0;
                    font-family: inherit;
                    //box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
                    /deep/ span {
                        padding-left: 10px;
                    }
                }
            }
        }
    }
</style>