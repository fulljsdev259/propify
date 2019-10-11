import LatestMyNeighboursCardError from './LatestMyNeighboursCard/Error'
import LatestMyNeighboursCardLoader from './LatestMyNeighboursCard/Loader'

import LatestPinboardCardError from './LatestPinboardCard/Error'
import LatestPinboardCardLoader from './LatestPinboardCard/Loader'

import LatestListingsCardError from './LatestListingsCard/Error'
import LatestListingsCardLoader from './LatestListingsCard/Loader'

import LatestPropertyManagersCardError from './LatestPropertyManagersCard/Error'
import LatestPropertyManagersCardLoader from './LatestPropertyManagersCard/Loader'

import LatestRequestsCardError from './LatestRequestsCard/Error'
import LatestRequestsCardLoader from './LatestRequestsCard/Loader'

import LikeError from './Like/Error'
import LikeLoader from './Like/Loader'

import LikesError from './Likes/Error'
import LikesLoader from './Likes/Loader'

import MyNeighboursError from './MyNeighbours/Error'
import MyNeighboursLoader from './MyNeighbours/Loader'

import PinboardAddCardError from './PinboardAddCard/Error'
import PinboardAddCardLoader from './PinboardAddCard/Loader'

import PinboardCardError from './PinboardCard/Error'
import PinboardCardLoader from './PinboardCard/Loader'

import PinboardNewResidentCardError from './PinboardNewResidentCard/Error'
import PinboardNewResidentCardLoader from './PinboardNewResidentCard/Loader'

import PinboardEditFormError from './PinboardEditForm/Error'
import PinboardEditFormLoader from './PinboardEditForm/Loader'

import PinboardDeleteModalError from './PinboardDeleteModal/Error'
import PinboardDeleteModalLoader from './PinboardDeleteModal/Loader'

import ListingAddFormError from './ListingAddForm/Error'
import ListingAddFormLoader from './ListingAddForm/Loader'

import ListingEditFormError from './ListingEditForm/Error'
import ListingEditFormLoader from './ListingEditForm/Loader'

import ListingCardError from './ListingCard/Error'
import ListingCardLoader from './ListingCard/Loader'

import ListingDetailsError from './ListingDetails/Error'
import ListingDetailsLoader from './ListingDetails/Loader'

import RateCardError from './RateCard/Error'
import RateCardLoader from './RateCard/Loader'

import RequestAddFormError from './RequestAddForm/Error'
import RequestAddFormLoader from './RequestAddForm/Loader'

import RequestCardError from './RequestCard/Error'
import RequestCardLoader from './RequestCard/Loader'

import RequestStatusChangeModalError from './RequestStatusChangeModal/Error'
import RequestStatusChangeModalLoader from './RequestStatusChangeModal/Loader'

import RequestsStatisticsCardError from './RequestsStatisticsCard/Error'
import RequestsStatisticsCardLoader from './RequestsStatisticsCard/Loader'

import RSSFeedError from './RSSFeed/Error'
import RSSFeedLoader from './RSSFeed/Loader'

import UserError from './User/Error'
import UserLoader from './User/Loader'

import UserNotificationsError from './UserNotifications/Error'
import UserNotificationsLoader from './UserNotifications/Loader'

import UserSettingsError from './UserSettings/Error'
import UserSettingsLoader from './UserSettings/Loader'

import WeatherCardError from './WeatherCard/Error'
import WeatherCardLoader from './WeatherCard/Loader'

import EmergencyCardError from './EmergencyCard/Error'
import EmergencyCardLoader from './EmergencyCard/Loader'

import PinboardResidentError from './PinboardResident/Error'
import PinboardResidentLoader from './PinboardResident/Loader'


export default {
    install (Vue) {
        Object.entries({
            latestMyNeighboursCard: () => ({
                component: import(/* webpackChunkName: "latestMyNeighboursCard" */ './LatestMyNeighboursCard'),
                loading: LatestMyNeighboursCardLoader,
                error: LatestMyNeighboursCardError,
                delay: 0,
                timeout: 8000
            }),
            latestPinboardCard: () => ({
                component: import(/* webpackChunkName: "latestPinboardCard" */ './LatestPinboardCard'),
                loading: LatestPinboardCardLoader,
                error: LatestPinboardCardError,
                delay: 0,
                timeout: 8000
            }),
            latestListingsCard: () => ({
                component: import(/* webpackChunkName: "latestListingsCard" */ './LatestListingsCard'),
                loading: LatestListingsCardLoader,
                error: LatestListingsCardError,
                delay: 0,
                timeout: 8000
            }),
            latestPropertyManagersCard: () => ({
                component: import(/* webpackChunkName: "latestPropertyManagersCard" */ './LatestPropertyManagersCard'),
                loading: LatestPropertyManagersCardLoader,
                error: LatestPropertyManagersCardError,
                delay: 0,
                timeout: 8000
            }),
            latestRequestsCard: () => ({
                component: import(/* webpackChunkName: "latestRequestsCard" */ './LatestRequestsCard'),
                loading: LatestRequestsCardLoader,
                error: LatestRequestsCardError,
                delay: 0,
                timeout: 8000
            }),
            like: () => ({
                component: import(/* webpackChunkName: "like" */ './Like'),
                loading: LikeLoader,
                error: LikeError,
                delay: 0,
                timeout: 8000
            }),
            likes: () => ({
                component: import(/* webpackChunkName: "likes" */ './Likes'),
                loading: LikesLoader,
                error: LikesError,
                delay: 0,
                timeout: 8000
            }),
            myNeighbours: () => ({
                component: import(/* webpackChunkName: "myNeighbours" */ './MyNeighbours'),
                loading: MyNeighboursLoader,
                error: MyNeighboursError,
                delay: 0,
                timeout: 8000
            }),
            pinboardAddCard: () => ({
                component: import(/* webpackChunkName: "pinboardAddCard" */ './PinboardAddCard'),
                loading: PinboardAddCardLoader,
                error: PinboardAddCardError,
                delay: 0,
                timeout: 8000
            }),
            pinboardCard: () => ({
                component: import(/* webpackChunkName: "pinboardCard" */ './PinboardCard'),
                // loading: PinboardCardLoader,
                error: PinboardCardError,
                delay: 0,
                timeout: 8000
            }),
            pinboardNewResidentCard: () => ({
                component: import(/* webpackChunkName: "pinboardNewResidentCard" */ './PinboardNewResidentCard'),
                // loading: PinboardNewResidentCardLoader,
                error: PinboardNewResidentCardError,
                delay: 0,
                timeout: 8000
            }),
            pinboardEditForm: () => ({
                component: import(/* webpackChunkName: "pinboardEditForm" */ './PinboardEditForm'),
                loading: PinboardEditFormLoader,
                error: PinboardEditFormError,
                delay: 0,
                timeout: 8000
            }),
            pinboardDeleteModal: () => ({
                component: import(/* webpackChunkName: "pinboardDeleteModal" */ './PinboardDeleteModal'),
                loading: PinboardDeleteModalLoader,
                error: PinboardDeleteModalError,
                delay: 0,
                timeout: 8000
            }),
            listingAddForm: () => ({
                component: import(/* webpackChunkName: "listingAddForm" */ './ListingAddForm'),
                loading: ListingAddFormLoader,
                error: ListingAddFormError,
                delay: 0,
                timeout: 8000
            }),
            listingEditForm: () => ({
                component: import(/* webpackChunkName: "listingEditForm" */ './ListingEditForm'),
                loading: ListingEditFormLoader,
                error: ListingEditFormError,
                delay: 0,
                timeout: 8000
            }),
            listingCard: () => ({
                component: import(/* webpackChunkName: "listingCard" */ './ListingCard'),
                loading: ListingCardLoader,
                error: ListingCardError,
                delay: 0,
                timeout: 8000
            }),
            listingDetails: () => ({
                component: import(/* webpackChunkName: "listingDetails" */ './ListingDetails'),
                loading: ListingDetailsLoader,
                error: ListingDetailsError,
                delay: 0,
                timeout: 8000
            }),
            rateCard: () => ({
                component: import(/* webpackChunkName: "rateCard" */ './RateCard'),
                loading: RateCardLoader,
                error: RateCardError,
                delay: 0,
                timeout: 8000
            }),
            requestCard: () => ({
                component: import(/* webpackChunkName: "requestCard" */ './RequestCard'),
                loading: RequestCardLoader,
                error: RequestCardError,
                delay: 0,
                timeout: 8000
            }),
            requestAddForm: () => ({
                component: import(/* webpackChunkName: "requestAddForm" */ './RequestAddForm'),
                loading: RequestAddFormLoader,
                error: RequestAddFormError,
                delay: 0,
                timeout: 8000
            }),
            requestStatusChangeModal: () => ({
                component: import(/* webpackChunkName: "requestAddForm" */ './RequestStatusChangeModal'),
                loading: RequestStatusChangeModalLoader,
                error: RequestStatusChangeModalError,
                delay: 0,
                timeout: 8000
            }),
            requestsStatisticsCard: () => ({
                component: import(/* webpackChunkName: "requestsStatisticsCard" */ './RequestsStatisticsCard'),
                loading: RequestsStatisticsCardLoader,
                error: RequestsStatisticsCardError,
                delay: 0,
                timeout: 8000
            }),
            rssFeed: () => ({
                component: import(/* webpackChunkName: "rssFeed" */ './RSSFeed'),
                loading: RSSFeedLoader,
                error: RSSFeedError,
                delay: 0,
                timeout: 8000
            }),
            user: () => ({
                component: import(/* webpackChunkName: "user" */ './User'),
                loading: UserLoader,
                error: UserError,
                delay: 0,
                timeout: 8000
            }),
            userNotifications: () => ({
                component: import(/* webpackChunkName: "userNotifications" */ './UserNotifications'),
                loading: UserNotificationsLoader,
                error: UserNotificationsError,
                delay: 0,
                timeout: 8000
            }),
            userSettings: () => ({
                component: import(/* webpackChunkName: "userSettings" */ './UserSettings'),
                loading: UserSettingsLoader,
                error: UserSettingsError,
                delay: 0,
                timeout: 8000
            }),
            weatherCard: () => ({
                component: import(/* webpackChunkName: "weatherCard" */ './WeatherCard'),
                loading: WeatherCardLoader,
                error: WeatherCardError,
                delay: 0,
                timeout: 8000
            }),
            emergencyCard: () => ({
                component: import(/* webpackChunkName: "weatherCard" */ './EmergencyCard'),
                loading: EmergencyCardLoader,
                error: EmergencyCardError,
                delay: 0,
                timeout: 8000
            }),
            pinboardResident: () => ({
                component: import(/* webpackChunkName: "weatherCard" */ './PinboardResident'),
                loading: PinboardResidentLoader,
                error: PinboardResidentError,
                delay: 0,
                timeout: 8000
            })
        }).forEach(([name, component]) => Vue.component(name, component))
    }
}