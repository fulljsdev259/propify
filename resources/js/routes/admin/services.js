import AdminPermissions from 'boot/roles/admin'
import hasPermissionGuard from 'guards/hasPermissionGuard'
import VueRouterMultiguard from 'vue-router-multiguard'

export default [{
    path: 'services',
    component: {
        template: '<router-view />'
    },
    children: [{
        name: 'adminServices',
        path: '/',
        component: () => import ( /* webpackChunkName: "admin/services/index" */ 'views/Admin/Services'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.list.provider)]),
        props: {
            title: 'Services'
        },
        meta: {
            title: 'Services'
        }
    }, {
        name: 'adminServicesAdd',
        path: 'add',
        component: () => import ( /* webpackChunkName: "admin/services/add" */ 'views/Admin/Services/Add'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.create.provider)]),
        props: {
            title: 'Add service'
        },
        meta: {
            title: 'Add Service'
        }
    }, {
        name: 'adminServicesEdit',
        path: ':id',
        component: () => import ( /* webpackChunkName: "admin/services/edit" */ 'views/Admin/Services/Edit'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.update.provider)]),
        props: {
            title: 'Edit service'
        },
        meta: {
            title: 'Edit Service'
        }
    }]
}]