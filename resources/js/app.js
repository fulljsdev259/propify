require('./bootstrap');

import 'open-weather-icons/dist/css/open-weather-icons.css'
import 'flag-icon-css/css/flag-icon.min.css';

import Vue from 'vue';
import axios from '@/axios'
import ElementUI from 'element-ui';
import VueI18n from 'vue-i18n';
import store from './store/index';
import router from './routes';
import VueCroppie from 'vue-croppie';
import VueUid from 'vue-uid';
import can from 'middlewares/can';
import permissions from 'middlewares/permissions';
import Sticky from 'vue-sticky-directive';
import VueVirtualScroller from 'vue-virtual-scroller'
import ReadMore from 'vue-read-more'
import VueDebounce from 'vue-debounce'
import VueAxios from 'vue-axios'

import 'vue-virtual-scroller/dist/vue-virtual-scroller.css'
import 'element-ui/lib/theme-chalk/index.css';


Vue.component('media', () => import(/* webpackChunkName: "media" */ 'components/Media'))
Vue.component('chat', () => import(/* webpackChunkName: "chat" */ 'components/Chat'))
Vue.component('comment', () => import(/* webpackChunkName: "comment" */ 'components/Comment'))
Vue.component('comments', () => import(/* webpackChunkName: "comments" */ 'components/Comments'))
Vue.component('add-comment', () => import(/* webpackChunkName: "add-comment" */ 'components/AddComment'))



Vue.use(VueUid);
Vue.use(Sticky)
Vue.use(VueVirtualScroller)

Vue.use(VueI18n)
Vue.use(ReadMore)
Vue.use(VueDebounce)

Vue.use(VueAxios, axios)
Vue.directive('click-outside', {
    bind: function (el, binding, vnode) {
      el.clickOutsideEvent = function (event) {
        // here I check that click was outside the el and his childrens
        if (!(el == event.target || el.contains(event.target))) {
          // and if it did, call method provided in attribute value
          vnode.context[binding.expression](event);
        }
      };
      document.body.addEventListener('click', el.clickOutsideEvent)
    },
    unbind: function (el) {
      document.body.removeEventListener('click', el.clickOutsideEvent)
    },
  });
import messages from './lang/index';


// Import top level component
import App from './App.vue';

import VueSweetalert2 from 'vue-sweetalert2';

const i18n = new VueI18n({
    locale: 'de', // set locale
    fallbackLocale: 'de',
    messages, // set locale messages
});

import 'sass/element.variables.scss';

Vue.use(ElementUI, {
    i18n: (key, value) => i18n.t(key, value)
});

Vue.use(VueSweetalert2, {
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
});
Vue.use(VueCroppie);

//add lodash from window to vue too
Object.defineProperty(Vue.prototype, '_', { value: window._ });
Object.defineProperty(Vue.prototype, '$can', { value: can });
Object.defineProperty(Vue.prototype, '$permissions', { value: permissions });


export default (async() => {
    let instance = null;

    try {
        await store.dispatch('application/constants');

        if (localStorage.token) {
            const me = await store.dispatch('me');

            if (me.data && me.data.settings && me.data.settings.language) {
                i18n.locale = me.data.settings.language;
            }
        }

        instance = new Vue({
            el: '#app',
            i18n,
            store,
            router,
            render: h => h(App),
            beforeMount() {
                this.$store.watch(state => state.users.loggedInUser, data => {
                    if (data && data.settings && data.settings.language) {
                        this.$i18n.locale = data.settings.language;
                    }
                });
            },
        });
    } catch (err) {
        alert('The application could not be initialized. Please try again.');
    }

    return instance;
})();
