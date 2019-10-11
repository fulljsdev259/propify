import {isAuthenticatedGuard, isResidentGuard} from 'guards'
import VueRouterMultiguard from 'vue-router-multiguard'

export default {
    path: 'pinboard',
    component: {
        template: '<router-view />'
    },
    children: [{
        name: 'residentPinboards',
        path: '/',
        component: () => import ( /* webpackChunkName: "resident/pinboard/index" */ 'views/Resident/Pinboard'),
        meta: {
            title: 'Pinboards'
        }
    }, {
        name: 'residentPinboard',
        path: ':id',
        component: () => import ( /* webpackChunkName: "resident/pinboard/detail" */ 'views/Resident/Pinboard/Detail'),
        meta: {
            title: 'Pinboard'
        }
    }],
    beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isResidentGuard]),
}