import AdminPermissions from 'boot/roles/admin'
import hasPermissionGuard from 'guards/hasPermissionGuard'
import VueRouterMultiguard from 'vue-router-multiguard'

export default [{
    path: 'house-owners',
    component: {
        template: '<router-view />'
    },
    children: [{
        name: 'adminHouseOwners',
        path: '/',
        component: () => import ( /* : "admin/houseOwners/index" */ 'views/Admin/HouseOwners'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.list.propertyManager)]),
        props: {
            title: 'List house owner'
        },
        meta: {
            title: 'List House Owner'
        }
    }, {
        name: 'adminHouseOwnersAdd',
        path: 'add',
        component: () => import ( /* : "admin/houseOwners/add" */ 'views/Admin/HouseOwners/Add'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.create.propertyManager)]),
        props: {
            title: 'Add house owner'
        },
        meta: {
            title: 'Add House Owner'
        }
    }, {
        name: 'adminHouseOwnersEdit',
        path: ':id',
        component: () => import ( /* : "admin/houseOwners/edit" */ 'views/Admin/HouseOwners/Edit'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.update.propertyManager)]),
        props: {
            title: 'Edit house owner'
        },
        meta: {
            title: 'Edit House Owner'
        }
    }]
}];