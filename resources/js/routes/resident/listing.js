import {isAuthenticatedGuard, isResidentGuard} from 'guards'
import VueRouterMultiguard from 'vue-router-multiguard'

export default {
    name: 'residentListing',
    path: 'listing',
    component: () => import ( /* webpackChunkName: "resident/listing/index" */ 'views/Resident/Listing'),
    beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isResidentGuard]),
    meta: {
        title: 'Listing'
    }
}