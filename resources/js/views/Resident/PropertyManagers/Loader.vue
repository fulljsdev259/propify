<template>
    <content-loader :width="320" :height="320" :speed="2" primaryColor="#f3f3f3" secondaryColor="#ecebeb" preserveAspectRatio="xMinYMin meet" style="width: 320px;">
        <rect x="8" y="20" rx="1" ry="1" width="2" height="324" /> 
		<circle cx="9.5" cy="20" r="7" /> 
		<circle cx="42" cy="20" r="20" /> 
		<circle cx="9" cy="76" r="6" /> 
		<circle cx="42" cy="76" r="20" /> 
		<rect x="70" y="72" rx="0" ry="0" width="160" height="8" /> 
		<circle cx="9.5" cy="132" r="7" /> 
		<circle cx="42" cy="132" r="20" /> 
		<circle cx="9" cy="188" r="6" /> 
		<circle cx="42" cy="188" r="20" /> 
		<rect x="70" y="184" rx="0" ry="0" width="213" height="8" /> 
		<circle cx="9.5" cy="244" r="7" /> 
		<circle cx="42" cy="244" r="20" /> 
		<circle cx="9" cy="300" r="6" /> 
		<circle cx="42" cy="300" r="20" /> 
		<rect x="70" y="296" rx="0" ry="0" width="185" height="8" />
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