<template >
    <ui-card shadow="always">
        <template #header>
            <i class="icon-group"></i> {{$t('resident.my_neighbours')}}
            <el-button type="text" @click="$router.push({name: 'residentMyNeighbours'})">{{$t('resident.actions.view_all')}}</el-button>
        </template>
        <my-neighbours ref="my-neighbours" :limit="limit" />
    </ui-card>
</template>

<script>
    export default {
        name: 'p-latest-my-neighbours-card',
        props: {
            limit: {
                type: Number,
                default: -1,
                validator: value => value >= -1
            }
        }
    }
</script>

<style lang="sass" scoped>
    .ui-card
        /deep/ .ui-card__header
            .el-button
                padding: 0
                margin-left: auto

                /deep/ [class*=icon] + span
                    margin-left: 5px
        /deep/ .ui-card__body
            .my-neighbours
                max-height: 400px
                overflow-y: auto;
</style>