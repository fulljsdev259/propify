import AdminPermissions from 'boot/roles/admin'
import hasPermissionGuard from 'guards/hasPermissionGuard'
import VueRouterMultiguard from 'vue-router-multiguard'

export default [{
    path: 'tenants',
    component: {
        template: '<router-view />'
    },
    children: [{
        name: 'adminTenants',
        path: '/',
        component: () => import ( /* webpackChunkName: "admin/tenants/index" */ 'views/Admin/Tenants'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.list.resident)]),
        meta: {
            title: 'Tenants'
        }
    }, {
        name: 'adminTenantsAdd',
        path: 'add',
        component: () => import ( /* webpackChunkName: "admin/tenants/add" */ 'views/Admin/Tenants/Add'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.create.resident)]),
        props: {
            title: 'Add resident'
        },
        meta: {
            title: 'Add resident'
        }
    }, {
        name: 'adminTenantsEdit',
        path: ':id',
        component: () => import ( /* webpackChunkName: "admin/tenants/editNew" */ 'views/Admin/Tenants/Edit'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.update.resident)]),
        props: {
            title: 'Edit resident'
        },
        meta: {
            title: 'Edit resident'
        }
    }, {
        path: ':id/view',
        name: 'adminTenantsView',
        component: () => import ( /* webpackChunkName: "admin/tenants/view" */ 'views/Admin/Tenants/view'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.view.resident)]),
        props: {
            title: 'View resident'
        },
        meta: {
            title: 'View resident'
        }
    }]
}]