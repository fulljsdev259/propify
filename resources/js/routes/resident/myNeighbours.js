import {isAuthenticatedGuard, isResidentGuard} from 'guards'
import VueRouterMultiguard from 'vue-router-multiguard'

export default {
    name: 'residentMyNeighbours',
    path: 'my-neighbours',
    component: () => import ( /* webpackChunkName: "resident/myNeighbours/index" */ 'views/Resident/MyNeighbours'),
    beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isResidentGuard]),
    meta: {
        title: 'My neighbours'
    },
}
