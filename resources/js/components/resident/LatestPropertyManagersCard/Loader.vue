<template>
    <content-loader :width="320" :height="234" :speed="2" primaryColor="#f3f3f3" secondaryColor="#ecebeb" preserveAspectRatio="xMinYMin meet" style="width: 320px;margin: 0 auto; display: block;"> 
        <circle cx="160" cy="48" r="48" /> 
		<rect x="80" y="112" rx="4" ry="4" width="160" height="28" /> 
		<rect x="107" y="148" rx="4" ry="4" width="106" height="16" /> 
		<rect x="120" y="172" rx="4" ry="4" width="80" height="8" /> 
		<rect x="53.5" y="196" rx="4" ry="4" width="213" height="8" /> 
		<rect x="0" y="210" rx="4" ry="4" width="320" height="8" /> 
		<rect x="107" y="226" rx="0" ry="0" width="106" height="8" />
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

<style lang="scss" scoped>
    .el-card {
        :global(.el-card__body) {
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }
</style>