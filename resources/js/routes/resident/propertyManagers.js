import {isAuthenticatedGuard, isResidentGuard} from 'guards'
import VueRouterMultiguard from 'vue-router-multiguard'

export default {
    name: 'residentPropertyManagers',
    path: 'property-managers',
    component: () => import ( /* webpackChunkName: "resident/propertyManagers/index" */ 'views/Resident/PropertyManagers'),
    beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isResidentGuard]),
    meta: {
        title: 'Property managers'
    },
}
