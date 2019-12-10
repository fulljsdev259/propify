<template>
    <div class="all_wrap">
        <auth3 v-if="this.$router.currentRoute.name == 'newlogin' || this.$router.currentRoute.name == 'nwewActivateAccount' || this.$router.currentRoute.name == 'newForgot'" />
        <div v-else>
		    <auth1 v-if="this.$constants.login.variation == 1" />
            <auth2 v-else-if="this.$constants.login.variation == 2" />
	    </div>
    </div>
</template>
<script>
    import auth1 from 'layouts/Auth/1/Layout';
    import auth2 from 'layouts/Auth/2/Layout';
    import auth3 from 'layouts/Auth/3/Layout';

export default {
   components: {
       auth1,
       auth2,
       auth3
   }
}
</script>
<style scoped>
    .all_wrap {
        height: 100%;
    }
</style>