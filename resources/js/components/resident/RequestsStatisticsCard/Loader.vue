<template>
    <content-loader :width="320" :height="161" :speed="2" primaryColor="#f3f3f3" secondaryColor="#ecebeb">
        <rect x="0" y="0" rx="0" ry="0" width="160" height="80" />
		<rect x="161" y="0" rx="0" ry="0" width="160" height="80" />
		<rect x="0" y="81" rx="0" ry="0" width="160" height="80" />
		<rect x="161" y="81" rx="0" ry="0" width="160" height="80" />
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