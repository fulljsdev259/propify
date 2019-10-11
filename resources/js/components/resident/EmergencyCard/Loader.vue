<template>
    <content-loader :width="320" :height="128" :speed="2" primaryColor="#f3f3f3" secondaryColor="#ecebeb" preserveAspectRatio="xMinYMin meet" style="width: 320px;">
        <rect x="0" y="0" rx="4" ry="4" width="96" height="96" />
        <rect x="112" y="0" rx="4" ry="4" width="208" height="16" />
        <rect x="112" y="28" rx="4" ry="4" width="53" height="24" />
        <rect x="112" y="64" rx="4" ry="4" width="208" height="32" />
    </content-loader>
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