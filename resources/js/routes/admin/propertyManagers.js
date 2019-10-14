import AdminPermissions from 'boot/roles/admin'
import hasPermissionGuard from 'guards/hasPermissionGuard'
import VueRouterMultiguard from 'vue-router-multiguard'

export default [{
    path: 'property-managers',
    component: {
        template: '<router-view />'
    },
    children: [{
        name: 'adminPropertyManagers',
        path: '/',
        component: () => import ( /* : "admin/propertyManagers/index" */ 'views/Admin/PropertyManagers'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.list.propertyManager)]),
        props: {
            title: 'List property manager'
        },
        meta: {
            title: 'List Property Manager'
        }
    }, {
        name: 'adminPropertyManagersAdd',
        path: 'add',
        component: () => import ( /* : "admin/propertyManagers/add" */ 'views/Admin/PropertyManagers/Add'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.create.propertyManager)]),
        props: {
            title: 'Add property manager'
        },
        meta: {
            title: 'Add Property Manager'
        }
    }, {
        name: 'adminPropertyManagersEdit',
        path: ':id',
        component: () => import ( /* : "admin/propertyManagers/edit" */ 'views/Admin/PropertyManagers/Edit'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.update.propertyManager)]),
        props: {
            title: 'Edit property manager'
        },
        meta: {
            title: 'Edit Property Manager'
        }
    }]
}];