<template>
    <div class="my-neighbours">
        <ui-heading icon="icon-group" :title="$t('resident.my_neighbours')" :description="$t('resident.heading_info.my_neighbour')">
        </ui-heading>
        <!-- <heading icon="icon-group" :title="$t('resident.my_neighbours')">
            <div slot="description">Et aut cum ut earum. Et aperiam ut possimus explicabo. Modi dolores in odit id fuga maxime aperiam dolor.</div>
        </heading> -->
        <ui-divider />
        <el-card>
            <my-neighbours />
        </el-card>
    </div>
</template>

<script>
    import Heading from 'components/Heading'
    export default {
        components: {
            Heading
        },
    }
</script>

<style lang="sass" scoped>
    .my-neighbours
        position: relative

        &:before
            content: ''
            width: 100%
            height: 100%
            position: fixed
            top: 0
            left: 0
            background-image: url('~img/5d4c33211edfc.png')
            background-position: top left
            background-repeat: no-repeat
            background-attachment: fixed
            pointer-events: none
            z-index: -1
            filter: opacity(.048)
        

        .el-card
            max-width: 640px
            background-color: transparentize(#fff, .28)
        
    @media only screen and (max-width: 676px)
        .my-neighbours
            /deep/ .ui-heading__content__description
                display: none
</style>