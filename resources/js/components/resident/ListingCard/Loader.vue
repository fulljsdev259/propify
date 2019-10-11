<template>
    <ui-card>
        <content-loader :width="320" :height="380" :speed="2" primaryColor="#f3f3f3" secondaryColor="#ecebeb" preserveAspectRatio="xMinYMin meet">
            <rect x="0" y="0" rx="4" ry="4" width="320" height="320" /> 
            <rect x="0" y="328" rx="4" ry="4" width="106" height="12" /> 
            <rect x="0" y="348" rx="4" ry="4" width="80" height="12" /> 
            <rect x="0" y="368" rx="4" ry="4" width="160" height="12" />
        </content-loader>
    </ui-card>
</template>

<script>
    import {ContentLoader} from "vue-content-loader"

    export default {
        inheritAttrs: false,
        components: {
            ContentLoader
        }
    }
</script>

<style lang="sass" scoped>
    .ui-card
        /deep/ .ui-card__body
            padding: 0
</style>