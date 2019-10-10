import {isAuthenticatedGuard, isResidentGuard} from 'guards'
import VueRouterMultiguard from 'vue-router-multiguard'

export default {
    name: 'residentRequests',
    path: 'requests',
    component: () => import ( /* webpackChunkName: "resident/requests/index" */ 'views/Resident/Requests/index'),
    beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isResidentGuard]),
    meta: {
        title: 'Requests'
    }
}