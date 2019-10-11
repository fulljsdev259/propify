<template>
    <ui-card shadow="always">
        <content-loader :width="320" :height="262" :speed="2" primaryColor="#f3f3f3" secondaryColor="#ecebeb" preserveAspectRatio="xMinYMin meet" style="width: 320px;">
            <rect x="0" y="0" rx="4" ry="4" width="320" height="32" />
            <rect x="0" y="40" rx="4" ry="4" width="53" height="8" />
            <rect x="0" y="56" rx="4" ry="4" width="106" height="8" />
            <rect x="0" y="72" rx="4" ry="4" width="320" height="24" />
            <rect x="0" y="104" rx="4" ry="4" width="160" height="8" />
            <rect x="0" y="120" rx="4" ry="4" width="280" height="8" />
            <rect x="0" y="136" rx="4" ry="4" width="53" height="8" />
            <circle cx="16" cy="168" r="16" />
            <rect x="40" y="158" rx="4" ry="4" width="53" height="8" />
            <rect x="40" y="170" rx="4" ry="4" width="80" height="8" />
            <circle cx="16" cy="206" r="16" />
            <rect x="40" y="196" rx="4" ry="4" width="53" height="8" />
            <rect x="40" y="208" rx="4" ry="4" width="80" height="8" />
            <circle cx="16" cy="244" r="16" />
            <rect x="40" y="234" rx="4" ry="4" width="53" height="8" />
            <rect x="40" y="246" rx="4" ry="4" width="80" height="8" />
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