<template>
    <div class="greeting">
        {{greeting}} <strong style="color: var(--primary-color);">{{user.name}}</strong>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        props: {
            afternoonHour: {
                type: Number,
                default: 12
            },
            eveningHour: {
                type: Number,
                default: 19
            }
        },
        data () {
            return {
                timeOfDay: null
            }
        },
        computed: {
            ...mapGetters({
                user: 'loggedInUser'
            }), 

            greeting () {
                const currentHour = (new Date()).getHours()

                let greeting

                if (currentHour >= this.afternoonHour && currentHour <= this.eveningHour) {
                    greeting = this.$t('resident.good_afternoon')

                    this.timeOfDay = 'day'
                } else if (currentHour >= this.afternoonHour) {
                    greeting = this.$t('resident.good_evening')

                    this.timeOfDay = 'night'
                } else {
                    greeting = this.$t('resident.good_morning')

                    this.timeOfDay = 'day'
                }

                return greeting
            }
        }
    }
</script>