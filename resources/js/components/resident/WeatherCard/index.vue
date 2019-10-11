<template>
    <ui-card shadow="always">
        <loader v-if="loading && !data" />
        <div v-else-if="!loading && !data">No data available.</div><!-- TODO - change content to something nicer -->
        <div v-else>
            <div :class="`owi owi-${data.weather[0].icon}`"></div>
            <div class="city">{{data.name}}</div>
            <div class="temperature">
                <div class="celsius">
                    {{data.main.temp | celsius}}
                </div>
                <div class="minmax">
                    <div>{{$t('components.resident.weatherWidget.minTemp')}} {{data.main.temp_min | celsius}}</div>
                    <div>{{$t('components.resident.weatherWidget.maxTemp')}} {{data.main.temp_max | celsius}}</div>
                </div>
            </div>
            <div class="description">{{data.weather[0].description}}</div>
            <div class="extra">
                <div class="content">
                    <div class="title">{{$t('components.resident.weatherWidget.wind')}}</div>
                    {{data.wind.speed}}m/s
                </div>
                <div class="content">
                    <div class="title">{{$t('components.resident.weatherWidget.cloudiness')}}</div>
                    {{data.clouds.all}}%
                </div>
                <div class="content">
                    <div class="title">{{$t('components.resident.weatherWidget.humidity')}}</div>
                    {{data.main.humidity}}%
                </div>
                <div class="content">
                    <div class="title">{{$t('components.resident.weatherWidget.pressure')}}</div>
                    {{data.main.pressure}}hPa
                </div>
            </div>
        </div>
    </ui-card>
</template>

<script>
    import loader from './Loader'
    import {EXTRA_LOADING_SECONDS} from 'config'
    import 'open-weather-icons/dist/css/open-weather-icons.css'

    export default {
        name: 'p-weather-card',
        components: {
            loader
        },
        data () {
            return {
                data: null,
                timeout: null,
                loading: false
            }
        },
        filters: {
            celsius (value) {
                return ~~(value - 273.15)
            }
        },
        async mounted () {
            this.loading = true

            const {data} = await this.axios.get('pinboard/weather.json')

            this.data = data
            this.timeout = setTimeout(() => this.loading = false, EXTRA_LOADING_SECONDS)
        },
        beforeDestroy () {
            clearTimeout(this.timeout)
        }
    }
</script>

<style lang="sass" scoped>
    .ui-card
        &:before
            content: ''
            position: absolute
            top: 0
            left: 0
            background-image: url('~img/1057121917.png')
            background-repeat: no-repeat
            background-position: center center
            background-size: cover
            width: 100%
            height: 100%
            pointer-events: none
            z-index: 1
            filter: opacity(.48)

        /deep/ .ui-card__body
            .owi
                font-size: 112px
                color: var(--primary-color)

            .city
                color: var(--primary-color)
                font-size: 32px
                font-weight: 800
                overflow: hidden
                text-overflow: ellipsis
                white-space: nowrap

            .temperature
                display: flex
                align-items: center
                font-size: 36px
                font-weight: 100
                line-height: 1.0

                .celsius
                    color: var(--primary-color)
                    display: flex

                    &:after
                        content: '\00b0' + 'C'
                        letter-spacing: -2px

                .minmax
                    display: flex
                    flex-direction: column
                    margin-left: 8px
                    font-size: 44%
                    font-weight: lighter
                    color: var(--color-text-secondary)
                    text-transform: lowercase

                    div:after
                        content: '\00b0' + 'C'
                        letter-spacing: -2px
                        text-transform: uppercase

            .description
                font-size: 18px
                font-weight: 300
                color: var(--color-text-secondary)
                margin: 8px 0

            .extra
                display: flex
                align-items: center

                .content
                    color: var(--color-text-placeholder)

                    .title
                        font-weight: 600
                        color: var(--color-primary)
                        text-transform: capitalize

                    &:not(:last-child)
                        margin-right: 8px
</style>