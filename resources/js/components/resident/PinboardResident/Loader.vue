<template>
    <content-loader :width="320" :height="32" :speed="2" primaryColor="#f3f3f3" secondaryColor="#ecebeb" preserveAspectRatio="xMinYMin meet" style="width: 320px;">
        <circle cx="16" cy="16" r="16" /> 
		<rect x="40" y="4" rx="4" ry="4" width="280" height="24" />
    </content-loader>
</template>
<script>
    import { ContentLoader } from "vue-content-loader"

    export default {
        inheritAttrs: false,
        components: {
            ContentLoader
        }
    }
</script>
