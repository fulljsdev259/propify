<template>
    <login1 v-if="this.$constants.login.variation == 1"/>
    <login2 v-else-if="this.$constants.login.variation==2"/>
    <login3 v-else-if="this.$constants.login.variation==3"/>
</template>
<script>
    import login1 from 'views/Auth/1/Login';
    import login2 from 'views/Auth/2/Login';
    import login3 from 'views/Auth/3/Login';

    export default {
        components: {
            login1,
            login2,login3
        }
    }
</script>
<style lang="scss" scoped>
  
</style>
