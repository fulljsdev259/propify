<template>
    <content-loader :width="320" :height="144" :speed="2" primaryColor="#f3f3f3" secondaryColor="#ecebeb" preserveAspectRatio="xMinYMin meet" style="width: 320px;">
        <circle cx="20" cy="20" r="20" />
		<rect x="48" y="8" rx="4" ry="4" width="80" height="8" />
		<rect x="48" y="20" rx="4" ry="4" width="106" height="8" />
		<circle cx="20" cy="72" r="20" />
		<rect x="48" y="62" rx="4" ry="4" width="186" height="8" />
		<rect x="48" y="74" rx="4" ry="4" width="99" height="8" />
		<circle cx="20" cy="122" r="20" />
		<rect x="48" y="110" rx="4" ry="4" width="53" height="8" />
		<rect x="48" y="122" rx="4" ry="4" width="240" height="8" />
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