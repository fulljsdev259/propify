<template>
    <content-loader :width="320" :height="352" :speed="2" primaryColor="#f3f3f3" secondaryColor="#ecebeb" preserveAspectRatio="xMinYMin meet" style="width: 320px;">
        <circle cx="21" cy="21" r="21" />
        <rect x="53" y="9" rx="4" ry="4" width="80" height="8" />
        <rect x="53" y="21" rx="4" ry="4" width="53" height="8" />
        <rect x="0" y="58" rx="4" ry="4" width="160" height="8" />
        <rect x="0" y="74" rx="4" ry="4" width="266" height="8" />
        <rect x="0" y="90" rx="4" ry="4" width="106" height="8" />
        <rect x="0" y="106" rx="4" ry="4" width="40" height="8" />
        <rect x="48" y="106" rx="4" ry="4" width="53" height="8" />
        <circle cx="16" cy="138" r="16" />
        <rect x="40" y="134" rx="4" ry="4" width="160" height="8" />
        <circle cx="16" cy="178" r="16" />
        <rect x="40" y="174" rx="4" ry="4" width="106" height="8" />
        <circle cx="16" cy="214" r="16" />
        <rect x="40" y="210" rx="4" ry="4" width="280" height="8" />
        <circle cx="16" cy="254" r="16" />
        <rect x="40" y="250" rx="4" ry="4" width="160" height="8" />
        <circle cx="16" cy="294" r="16" />
        <rect x="40" y="290" rx="4" ry="4" width="80" height="8" />
        <circle cx="16" cy="334" r="16" />
        <rect x="40" y="322" rx="4" ry="4" width="280" height="24" />
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