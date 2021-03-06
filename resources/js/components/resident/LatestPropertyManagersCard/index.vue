<template>
    <ui-card shadow="always">
        <template #header>
            <i class="icon-users"></i> {{$t('resident.property_managers')}}
            <el-button type="text" @click="$router.push({name: 'residentPropertyManagers'})">{{$t('resident.actions.view_all')}}</el-button>
        </template>
        <loader v-if="loading" />
        <template v-else-if="!loading && managers">
            <el-carousel :autoplay="false" :arrow="managers.length > 1 ? 'hover': 'never'" indicator-position="none" height="256" v-if="managers.length">
                <el-carousel-item v-for="manager in managers" :key="manager.id">
                    <ui-avatar :size="96" :src="manager.avatar" :name="manager.name" shadow="hover" />
                    <div class="name">
                        {{manager.name}}
                    </div>
                    <div class="email">
                        {{manager.email}}
                    </div>
                    <div class="phone">
                        {{manager.phone}}
                    </div>
                    <div class="slogan">
                        {{manager.slogan}}
                    </div>
                </el-carousel-item>
            </el-carousel>
            <div class="placeholder" v-else>
                <img class="image" :src="require('img/5cae67303cdad.png')" />
                <div class="title">There are no property managers...</div>
                <div class="description">Every property manager will be list here...</div>
            </div>
        </template>
    </ui-card>
</template>

<script>
    import Loader from './Loader'
    import Placeholder from 'components/Placeholder'
    import {mapGetters} from 'vuex'
    import {EXTRA_LOADING_SECONDS} from 'config'

    export default {
        name: 'p-latest-property-managers-card',
        components: {
            Placeholder,
            Loader
        },
        data () {
            return {
                loading: false,
                managers: [],
                timeout: null
            }
        },
        computed: {
            ...mapGetters({
                user: 'loggedInUser'
            }),

            isLoading () {
                return this.loading && !this.managers.length;
            }
        },
        async mounted () {
            this.loading = true

            let resp = await this.$store.dispatch('getMyPropertyManagers')

            this.managers = (resp && resp.data) ? resp.data : []

            this.managers.map( item => item.name = item.first_name + ' ' + item.last_name)

            this.timeout = setTimeout(() => this.loading = false, EXTRA_LOADING_SECONDS)
        },
        beforeDestroy () {
            clearTimeout(this.timeout)
        }
    }
</script>

<style lang="sass" scoped>
    .ui-card
        /deep/ .ui-card__header
            .el-button
                padding: 0
                margin-left: auto

                /deep/ [class*=icon] + span
                    margin-left: 5px

        /deep/ .ui-card__body
            .el-carousel
                @media screen and (max-width: 414px)
                    /deep/ .el-carousel__container
                        height: 205px
                /deep/ .el-carousel__item
                    display: flex
                    flex-direction: column
                    align-content: center
                    justify-content: center
                    text-align: center

                    .ui-avatar
                        margin: 0 auto 16px auto

                    .name
                        font-size: 24px
                        font-weight: 800
                        color: var(--color-primary)

                    .email
                        font-size: 16px
                        font-weight: 400
                        color: var(--color-text-placeholder)

                    .phone
                        font-size: 16px
                        font-weight: 400
                        color: var(--color-text-secondary)

                    .slogan
                        color: var(--color-text-placeholder)
                        margin-top: 16px
                        overflow: hidden
                        display: -webkit-box
                        -webkit-line-clamp: 3
                        -webkit-box-orient: vertical

        .placeholder
            display: flex
            padding: 16px
            text-align: center
            align-items: center
            flex-direction: column
            justify-content: center

            .image
                width: 256px

            .title
                font-size: 20px
                font-weight: 800
                color: var(--color-primary)

            .description
                font-size: 14px
                font-weight: 600
                word-break: break-word
                color: var(--color-text-placeholder)
</style>