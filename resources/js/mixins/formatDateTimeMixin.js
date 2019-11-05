import {format} from 'date-fns'

export default {
    methods: {
        formatDatetime (dateTime) {
            return this.$t('general.date_time_format', {
                date: format(dateTime, 'DD.MM.YYYY'),
                time: format(dateTime, 'HH:mm')
            })
        },
        splitDatetime (dateTime) {
            var res = dateTime.split(" ");
            return this.$t('general.date_time_format', {
                date: res[0],
                time: res[1]
            })
        },
        formatDayAtTimePeriod (dateTime1, dateTime2) {
            return this.$t('general.day_at_time_period', {
                date: format(dateTime1, 'DD.MM.YYYY'),
                time1: format(dateTime1, 'HH:mm'),
                time2: format(dateTime2, 'HH:mm'),
            })
        },
        formatTwoDaysAtTime (dateTime1, dateTime2) {
            return this.$t('general.two_days_at_time', {
                date1: format(dateTime1, 'DD.MM.YYYY'),
                time1: format(dateTime1, 'HH:mm'),
                date2: format(dateTime2, 'DD.MM.YYYY'),
                time2: format(dateTime2, 'HH:mm'),
            })
        },
    }
}
