import AdminPermissions from 'boot/roles/admin'
import hasPermissionGuard from 'guards/hasPermissionGuard'
import VueRouterMultiguard from 'vue-router-multiguard'

export default [{
    path: 'residents',
    component: {
        template: '<router-view />'
    },
    children: [{
        name: 'adminResidents',
        path: '/',
        component: () => import ( /* webpackChunkName: "admin/residents/index" */ 'views/Admin/Residents'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.list.resident)]),
        meta: {
            title: 'Residents'
        }
    }, {
        name: 'adminResidentsAdd',
        path: 'add',
        component: () => import ( /* webpackChunkName: "admin/residents/add" */ 'views/Admin/Residents/Add'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.create.resident)]),
        props: {
            title: 'Add resident'
        },
        meta: {
            title: 'Add resident'
        }
    }, {
        name: 'adminResidentsEdit',
        path: ':id',
        component: () => import ( /* webpackChunkName: "admin/residents/editNew" */ 'views/Admin/Residents/Edit'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.update.resident)]),
        props: {
            title: 'Edit resident'
        },
        meta: {
            title: 'Edit resident'
        }
    }, {
        path: ':id/view',
        name: 'adminResidentsView',
        component: () => import ( /* webpackChunkName: "admin/residents/view" */ 'views/Admin/Residents/view'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.view.resident)]),
        props: {
            title: 'View resident'
        },
        meta: {
            title: 'View resident'
        }
    }]
}]