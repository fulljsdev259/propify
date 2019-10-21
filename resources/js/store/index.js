import Vue from 'vue'
import Vuex from 'vuex'

// These wil be depracated soon - awful structure - yet keeping to not cause breaking changes
import UsersStore from 'store/modules/users'
import UnitsStore from 'store/modules/units'
import ResidentsStore from 'store/modules/residents'
import TemplatesStore from 'store/modules/templates'
import ServicesStore from 'store/modules/services'
import RequestsStore from 'store/modules/requests'
import RequestCategoriesStore from 'store/modules/requestCategories'
import PropertyManagersStore from 'store/modules/propertyManagers'
import HouseOwnersStore from 'store/modules/houseOwners'
import ListingsStore from 'store/modules/listings'
import PinboardStore from 'store/modules/pinboard'
import NotificationsStore from 'store/modules/notifications'
import MediaStore from 'store/modules/media'
import QuartersStore from 'store/modules/quarters'
import DashboardStore from 'store/modules/dashboard'
import CommentsStore from 'store/modules/comments'
import CleanifyStore from 'store/modules/cleanify'
import BuildingsStore from 'store/modules/buildings'
import ApplicationStore from 'store/modules/application'
import AddressesStore from 'store/modules/addresses'
import TagsStore from 'store/modules/tags'


// new ones - this will stay in the future, the above one will be removed at some point
import NewPinboardStore from 'store/modules/newPinboard'
import NewRequestsStore from 'store/modules/newRequests'
import NewListingsStore from 'store/modules/newListings'
import ContractsStore from 'store/modules/contracts'
import EmergencyStore from 'store/modules/emergency'

import createPersistedState from 'vuex-persistedstate'


Vue.use(Vuex)

export default new Vuex.Store({
    state: {

        allLanguages: ['en', 'fr', 'de', 'it']
    },
    mutations: {},
    actions: {},
    getters: {
        getAllAvailableLanguages (state) {
            return state.allLanguages
        }
    },
    modules: {
        users: UsersStore,
        residents: ResidentsStore,
        buildings: BuildingsStore,
        units: UnitsStore,
        addresses: AddressesStore,
        services: ServicesStore,
        pinboard: PinboardStore,
        quarters: QuartersStore,
        requests: RequestsStore,
        requestCategories: RequestCategoriesStore,
        propertyManagers: PropertyManagersStore,
        houseOwners: HouseOwnersStore,
        listings: ListingsStore,
        templates: TemplatesStore,
        cleanify: CleanifyStore,
        application: {
            namespaced: true,
            ...ApplicationStore
        },
        notifications: NotificationsStore,
        media: {
            namespaced: true,
            ...MediaStore
        },
        TagsStore,

        // this will stay in the future only
        comments: CommentsStore,
        newPinboard: NewPinboardStore,
        newListings: NewListingsStore,
        newRequests: NewRequestsStore,
        contracts: ContractsStore,
        emergency: EmergencyStore,
    },
    plugins: [createPersistedState({
        key: 'state',
        paths: ['application.locale']
    })],
})