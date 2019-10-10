import {isAuthenticatedGuard, isResidentGuard} from 'guards'
import VueRouterMultiguard from 'vue-router-multiguard'

export default {
    path: 'my',
    component: {
        template: '<router-view />'
    },
    children: [{
        name: 'residentMyPersonal',
        path: 'personal',
        component: () => import ( /* webpackChunkName: "resident/my/personal" */ 'views/Resident/My/Personal'),
        meta: {
            title: 'My personal data'
        }
    }, {
        name: 'residentMyContracts',
        path: 'contracts',
        component: () => import ( /* webpackChunkName: "resident/my/contracts" */ 'views/Resident/My/Contracts'),
        meta: {
            title: 'My recent contracts'
        }
    }, {
        name: 'residentMyDocuments',
        path: 'documents',
        component: () => import ( /* webpackChunkName: "resident/my/documents" */ 'views/Resident/My/Documents'),
        meta: {
            title: 'My documents'
        }
    }, {
        name: 'residentMyContacts',
        path: 'contacts',
        component: () => import ( /* webpackChunkName: "resident/my/contacts" */ 'views/Resident/My/Contacts'),
        meta: {
            title: 'My contact persons'
        }
    }],
    beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isResidentGuard])
}