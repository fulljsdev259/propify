// this will be deleted - its an workaround for now - ARGHHH
import './window'

import 'flag-icon-css/css/flag-icon.min.css'
import 'vue-virtual-scroller/dist/vue-virtual-scroller.css'

import App from '@/App.vue'

import axios from 'boot/axios'
import {DEFAULT_FALLBACK_LOCALE} from 'config'
import {AdminPermissions, ResidentPermissions} from 'boot/roles'

import store from 'store'
import router from 'routes'
import messages from 'lang/index'
import hasPermission from 'helpers/hasPermission'
import UIComponents from 'components/ui'
import CommonComponents from 'components/common'
import ResidentComponents from 'components/resident'

import Vue from 'vue'
import VueUid from 'vue-uid'
import VueI18n from 'vue-i18n'
import VueAxios from 'vue-axios'
import ElementUI from 'element-ui'
import VueCroppie from 'vue-croppie'
import ReadMore from 'vue-read-more'
import VueDebounce from 'vue-debounce'
import Sticky from 'vue-sticky-directive'
import VueSweetalert2 from 'vue-sweetalert2'
import AnimatedNumber from 'animated-number-vue'
import VueVirtualScroller from 'vue-virtual-scroller'
import VueLocalStorage from 'vue-localstorage'
import {VueResponsiveComponents, ResponsiveMixin} from 'vue-responsive-components'

Vue.use(Sticky)
Vue.use(VueUid)
Vue.use(VueI18n)
Vue.use(ReadMore)
Vue.use(VueCroppie)
Vue.use(VueDebounce)
Vue.use(UIComponents)
Vue.use(VueAxios, axios)
Vue.use(CommonComponents)
Vue.use(ResidentComponents)
Vue.use(VueVirtualScroller)
Vue.use(VueLocalStorage, {name: 'localStorage', bind: true})
Vue.use(VueSweetalert2, {confirmButtonColor: '#3085d6', cancelButtonColor: '#d33'})

const i18n = new VueI18n({messages, locale: DEFAULT_FALLBACK_LOCALE, fallbackLocale: DEFAULT_FALLBACK_LOCALE})

Vue.use(ElementUI, {i18n: (key, value) => i18n.t(key, value)})

Vue.component('AnimatedNumber', AnimatedNumber)

// TODO - temporary workaround - will be using this later https://github.com/leonardovilarinho/vue-acl
Vue.prototype.$can = hasPermission
Vue.prototype.$permissions = AdminPermissions
// ...really?! - keeping it to not break things within admin yet - will be removed
Vue.prototype._ = window._

Vue.config.performance = true
Vue.config.productionTips = false

Vue.mixin({
    data () {
        return {
            get authorizationToken () {
                return localStorage.token
            }
        }
    }
})

export default new Vue({
    el: '#app',
    i18n,
    store,
    router,
    mixins: [ResponsiveMixin],
    render: h => h(App),
    mounted () {
        this.$i18n.locale = this.$store.state.application.locale
        this.$store.watch(({application}) => application.locale, locale => this.$i18n.locale = locale)
    }
})