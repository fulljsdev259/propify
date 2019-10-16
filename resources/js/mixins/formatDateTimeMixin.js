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
        }
    }
}