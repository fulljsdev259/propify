<template>
    <ui-card icon="icon-chat-empty" :title="$t('resident.requests')" shadow="always">
        <loader v-if="loading && !data" style="grid-column: -1 / 1;" />
        <div class="placeholder" v-else-if="!loading && !data">
            <img class="image" :src="require('img/5c9d48f15dd1a.png')" />
            <div class="content">
                <div class="title">No requests available yet.</div>
                <div class="description">Et aut cum ut earum. Et aperiam ut possimus explicabo. Modi dolores in odit id fuga maxime aperiam dolor.</div>
            </div>
        </div>
        <template v-else>
            <div class="column">
                <div class="icon-lock-open"></div>
                <div class="title">
                    {{$t('resident.request_status.opened')}}
                </div>
                <animated-number :value="data.opened_requests_count" :formatValue="formatNumber" :delay="0" :duration="960" :complete="onAnimatedNumberCompleted" />
            </div>
            <div class="column">
                <div class="icon-stopwatch"></div>
                <div class="title">
                    {{$t('resident.request_status.pending')}}
                </div>
                <animated-number :value="data.pending_requests_count" :formatValue="formatNumber" :delay="480" :duration="960" :complete="onAnimatedNumberCompleted" />
            </div>
            <div class="column">
                <div class="icon-ok"></div>
                <div class="title">
                    {{$t('resident.request_status.done')}}
                </div>
                <animated-number :value="data.done_requests_count" :formatValue="formatNumber" :delay="960" :duration="960" :complete="onAnimatedNumberCompleted" />
            </div>
            <div class="column">
                <div class="icon-lock"></div>
                <div class="title">
                    {{$t('resident.request_status.archived')}}
                </div>
                <animated-number :value="data.archived_requests_count" :formatValue="formatNumber" :delay="1440" :duration="960" :complete="onAnimatedNumberCompleted" />
            </div>
        </template>
    </ui-card>
</template>

<script>
    import Loader from './Loader'
    import {EXTRA_LOADING_SECONDS} from 'config'
    import {mapGetters} from 'vuex'

    export default {
        name: 'p-requests-statistics-card',
        components: {
            Loader
        },
        data () {
            return {
                data: null,
                loading: false,
                timeout: null
            }
        },
        methods: {
            formatNumber (value) {
                return value.toFixed(0)
            },
            onAnimatedNumberCompleted ({animatables}) {
                // animatables[0].target.$el.parentElement.style.fontSize = '28px'
            }
        },
        computed: {
            ...mapGetters({
                user: 'loggedInUser'
            })
        },
        async mounted () {
            this.loading = true

            const {data} = await this.axios.get(`residents/${this.user.resident.id}/statistics`)

            this.data = data.data
            this.timeout = setTimeout(() => this.loading = false, EXTRA_LOADING_SECONDS)
        },
        beforeDestroy () {
            clearTimeout(this.setimeout)
        }
    }
</script>

<style lang="sass" scoped>
    .ui-card
        /deep/ .ui-card__body
            padding: 0
            display: grid
            grid-gap: 1px
            grid-template-columns: repeat(2, 1fr)

            .column
                box-shadow: 0 0 1px var(--border-color-base)
                color: var(--primary-color)
                text-align: center
                font-size: 32px
                padding: 16px
                display: flex
                flex-direction: column
                align-items: center
                justify-content: center
                transition: font-size .56s ease

                [class*=icon]
                    font-size: 24px
                    width: 48px
                    height: 48px
                    color: var(--primary-color)
                    display: flex
                    align-items: center
                    justify-content: center
                    border-radius: 50%
                    border: 8px var(--primary-color) solid
                    box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76)

                .title
                    font-size: 18px
                    font-weight: 300
                    color: var(--color-text-placeholder)
                    padding: 8px
                    text-transform: capitalize
</style>