import Vue from 'vue'
import store from 'store'
import {DEFAULT_FALLBACK_LOCALE} from 'config'

import AuthRoutes from 'routes/auth'
import AdminRoutes from 'routes/admin'
import ResidentRoutes from 'routes/resident'
import NotFound from 'routes/404'
import Landing from 'views/Landing'
import VueRouter from 'vue-router'
import {TYPES} from 'store/modules/application'

Vue.use(VueRouter)

let originalTitle

const Router = new VueRouter({
    routes: [{
        path: '/',
        component: Landing,
    }, ...AuthRoutes, ...ResidentRoutes, ...AdminRoutes, ...NotFound],
    mode: 'history'
})

Router.beforeEach(async (to, from, next) => {
    if (!Object.keys(store.state.application.constants).length) {
        await store.dispatch(`application/${TYPES.actions.getConstants}`)

        document.documentElement.style.setProperty('--primary-color', store.state.application.constants.colors.primary_color) // this will be removed
        document.documentElement.style.setProperty('--color-primary', store.state.application.constants.colors.primary_color)
        document.documentElement.style.setProperty('--primary-color-lighter', store.state.application.constants.colors.primary_color_lighter)

        if (!store.state.application.locale) {
            const [lang, locale] = (((navigator.userLanguage || navigator.language).replace('-', '_')).toLowerCase()).split('_')

            store.dispatch('application/setLocale', Object.keys(store.state.application.constants.app.languages).includes(lang) ? lang : DEFAULT_FALLBACK_LOCALE)
        }

        Vue.prototype.$constants = store.state.application.constants
    }

    if (localStorage.token) {
        if (!store.getters.loggedIn) {
            await store.dispatch('me')
        }
    }

    if (!originalTitle) {
        originalTitle = document.title
    }

    if (to.meta.title) {
        const {meta} = to.matched.slice().reverse().find(({meta}) => meta && meta.title)

        if (meta) {
            document.title = `${originalTitle} - ${meta.title}`
        }
    }

    next()
})

export default Router