import Layout from 'layouts/Auth/Layout'
import isGuestGuard from 'guards/isGuestGuard'
import VueRouterMultiguard from 'vue-router-multiguard'

export default [{
    path: '/',
    component: Layout,
    children: [{
        name: 'login',
        path: 'login',
        component: () =>
            import ( /* webpackChunkName: "auth/1/login" */ 'views/Auth/all/Login'),
        beforeEnter: VueRouterMultiguard([isGuestGuard]),
        meta: {
            title: 'Login'
        }
    }, {
        path: 'newlogin',
        component: () =>
            import ( /* webpackChunkName: "auth/1/autoLogin" */ 'views/Auth/all/newLogin'),
        name: 'newlogin',
        meta: {
            title: 'New Login'
        }
    }, {
        path: 'autologin',
        component: () =>
            import ( /* webpackChunkName: "auth/1/autoLogin" */ 'views/Auth/all/AutoLogin'),
        name: 'autoLogin',
        meta: {
            title: 'Auto Login'
        }
    }, {
        name: 'forgot',
        path: 'forgot',
        component: () =>
            import ( /* webpackChunkName: "auth/1/forgotPassword" */ 'views/Auth/all/ForgotPassword'),
        beforeEnter: VueRouterMultiguard([isGuestGuard]),
        meta: {
            title: 'Forgot Password'
        }
    }, {
        name: 'resetPassword',
        path: 'reset-password',
        component: () =>
            import ( /* webpackChunkName: "auth/1/resetPassword" */ 'views/Auth/all/ResetPassword'),
        beforeEnter: VueRouterMultiguard([isGuestGuard]),
        meta: {
            title: 'Reset Password'
        }
    }, {
        path: 'activate',
        component: () =>
            import ( /* webpackChunkName: "auth/1/activateAccount" */ 'views/Auth/all/ActivateAccount'),
        name: 'activateAccount',
        beforeEnter: VueRouterMultiguard([isGuestGuard]),
        meta: {
            title: 'Activate Account',
        }
    }, {
        name: 'newForgot',
        path: 'newForgot',
        component: () =>
            import ( /* webpackChunkName: "auth/1/forgotPassword" */ 'views/Auth/3/ForgotPassword'),
        beforeEnter: VueRouterMultiguard([isGuestGuard]),
        meta: {
            title: 'Forgot Password'
        }
    }, {
        path: 'newActivate',
        component: () =>
            import ( /* webpackChunkName: "auth/1/activateAccount" */ 'views/Auth/3/ActivateAccount'),
        name: 'nwewActivateAccount',
        beforeEnter: VueRouterMultiguard([isGuestGuard]),
        meta: {
            title: 'Activate Account',
        }
    }]
}]