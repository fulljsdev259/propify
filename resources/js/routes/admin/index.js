import {isAuthenticatedGuard, isAdminGuard, isServiceGuard, isManagerGuard} from 'guards'
import VueRouterMultiguard from 'vue-router-multiguard'

import permissions from 'middlewares/permissions';
import Layout from 'layouts/AdminLayout';
import Dashboard from 'views/Admin';
import Profile from 'views/Admin/Profile';
import Settings from 'views/Admin/Settings';

import BuildingsRoutes from 'routes/admin/buildings';
import UnitsRoutes from 'routes/admin/units';
import ResidentsRoutes from 'routes/admin/residents';
import UsersRoutes from 'routes/admin/users';
import ServicesRoutes from 'routes/admin/services';
import PinbordRoutes from 'routes/admin/pinboard';
import QuartersRoutes from 'routes/admin/quarters';
import RequestsRoutes from 'routes/admin/requests';
import PropertyManagersRoutes from 'routes/admin/propertyManagers';
import HouseOwnersRoutes from 'routes/admin/houseOwners';
import ListingsRoutes from 'routes/admin/listings';
import TemplatesRoutes from 'routes/admin/templates';


export default [{
    path: '/admin',
    component: Layout,
    children: [{
        path: '/',
        name: 'admin',
        component: Dashboard,
        beforeEnter: VueRouterMultiguard([isServiceGuard, isManagerGuard]),
        meta: {
            breadcrumb: 'Home'
        },
    }, {
        path: 'dashboard',
        name: 'adminDashboard',
        component: Dashboard,
        beforeEnter: VueRouterMultiguard([isServiceGuard, isManagerGuard]),
        meta: {
            breadcrumb: 'Home'
        },
    // }, {
    //     path: 'profile',
    //     name: 'adminProfile',
    //     component: Profile,
    //     meta: {
    //         breadcrumb: 'Profile'
    //     }
    }, {
        path: 'settings',
        name: 'adminSettings',
        component: Settings,
        meta: {
            permission: permissions.view.Settings,
            breadcrumb: 'Settings',
            title: 'Settings'
        },
    },
        ...UsersRoutes,
        ...ServicesRoutes,
        ...ResidentsRoutes,
        ...BuildingsRoutes,
        ...PinbordRoutes,
        ...UnitsRoutes,
        ...QuartersRoutes,
        ...RequestsRoutes,
        ...PropertyManagersRoutes,
        ...HouseOwnersRoutes,
        // ...ListingsRoutes,
        ...TemplatesRoutes,
    ],
    beforeEnter: VueRouterMultiguard([isAuthenticatedGuard, isAdminGuard]),
}];
