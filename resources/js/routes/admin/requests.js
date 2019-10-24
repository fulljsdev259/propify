import AdminPermissions from 'boot/roles/admin'
import {isAuthenticatedGuard, isAdminGuard, hasPermissionGuard} from 'guards'
import VueRouterMultiguard from 'vue-router-multiguard'


export default [{
    path: 'requests',
    component: {
        template: '<router-view />'
    },
    children: [{
        path: '/',
        name: 'adminRequests',
        component: () => import ( /* webpackChunkName: "admin/requests/index" */ 'views/Admin/Requests'),
        beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isAdminGuard, hasPermissionGuard(AdminPermissions.list.request)]),
        props: {
            
        },
        meta: {
            title: 'Requests',
        }
    },  {
        path: 'my',
        name: 'adminMyRequests',
        component: () => import ( /* webpackChunkName: "admin/requests/index" */ 'views/Admin/Requests'),
        beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isAdminGuard, hasPermissionGuard(AdminPermissions.list.request)]),
        props: {
            title: 'My Requests'
        },
        meta: {
            title: 'My Requests',
        }
    }, {
        path: 'mypending',
        name: 'adminMypendingRequests',
        component: () => import ( /* webpackChunkName: "admin/requests/index" */ 'views/Admin/Requests'),
        beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isAdminGuard, hasPermissionGuard(AdminPermissions.list.request)]),
        props: {
            title: 'My pending requests'
        },
        meta: {
            title: 'My Requests'
        }
    }, {
        path: 'notassigned',
        name: 'adminUnassignedRequests',
        component: () => import ( /* webpackChunkName: "admin/requests/index" */ 'views/Admin/Requests'),
        beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isAdminGuard, hasPermissionGuard(AdminPermissions.list.request)]),
        props: {
            title: 'Not Assigned'
        },
        meta: {
            title: 'Not Assigned'
        }
    }, {
        path: 'pending',
        name: 'adminAllpendingRequests',
        component: () => import ( /* webpackChunkName: "admin/requests/index" */ 'views/Admin/Requests'),
        beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isAdminGuard, hasPermissionGuard(AdminPermissions.list.request)]),
        props: {
            title: 'All pending requests'
        },
        meta: {
            title: 'All pending requests'
        }
    }, {
        path: 'add',
        name: 'adminRequestsAdd',
        component: () => import ( /* webpackChunkName: "admin/requests/add" */ 'views/Admin/Requests/Add'),
        beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isAdminGuard, hasPermissionGuard(AdminPermissions.create.request)]),
        props: {
            title: 'Add request'
        },
        meta: {
            title: 'Add Request'
        }
    }, {
        path: ':id',
        name: 'adminRequestsEdit',
        component: () => import ( /* webpackChunkName: "admin/requests/edit" */ 'views/Admin/Requests/Edit'),
        beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isAdminGuard /* , hasPermissionGuard(AdminPermissions.update.request) */]),
        props: {
            title: 'Edit request'
        },
        meta: {
            title: 'Edit Request'
        }
    }, {
        path: ':id/view',
        name: 'adminRequestsView',
        component: () => import ( /* webpackChunkName: "admin/requests/view" */ 'views/Admin/Requests/View'),
        beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isAdminGuard /* , hasPermissionGuard(AdminPermissions.update.request) */]),
        props: {
            title: 'View request'
        },
        meta: {
            title: 'View Request'
        }
    }]
}];

//beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isAdminGuard, hasPermissionGuard(AdminPermissions.update.request)]),