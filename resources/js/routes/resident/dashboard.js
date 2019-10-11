import {isAuthenticatedGuard, isResidentGuard} from 'guards'
import VueRouterMultiguard from 'vue-router-multiguard'

export default {
    name: 'residentDashboard',
    path: 'dashboard',
    component: () => import ( /* webpackChunkName: "resident/dashboard/index" */ 'views/Resident/Dashboard'),
    beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isResidentGuard]),
    meta: {
        title: 'Dashboard'
    }
}