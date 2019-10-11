import {isAuthenticatedGuard, isResidentGuard} from 'guards'
import VueRouterMultiguard from 'vue-router-multiguard'

export default {
    name: 'residentSettings',
    path: 'settings',
    component: () => import ( /* webpackChunkName: "resident/settings/index" */ 'views/Resident/Settings'),
    beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isResidentGuard]),
    meta: {
        title: 'Settings'
    }
}