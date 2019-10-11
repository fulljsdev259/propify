import {isAuthenticatedGuard, isResidentGuard} from 'guards'
import VueRouterMultiguard from 'vue-router-multiguard'

export default {
    name: 'cleanifyRequest',
    path: 'cleanify',
    component: () => import ( /* webpackChunkName: "resident/cleanify/index" */ 'views/Resident/Cleanify'),
    beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isResidentGuard]),
    meta: {
        title: 'Cleanify'
    },
}
