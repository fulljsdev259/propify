import AdminPermissions from 'boot/roles/admin'
import hasPermissionGuard from 'guards/hasPermissionGuard'
import VueRouterMultiguard from 'vue-router-multiguard'

export default [{
    path: 'activity',
    component: {
        template: '<router-view />'
    },
    children: [{
        name: 'adminActivityList',
        path: '/',
        component: () => import ( /* webpackChunkName: "admin/buildings/index" */ 'views/Admin/Activity'),
        beforeEnter: VueRouterMultiguard([hasPermissionGuard(AdminPermissions.list.audit)]),
        meta: {
            title: 'Activity'
        }
    }]
}]