import Layout from 'layouts/ResidentLayout'
import MyRoutes from 'routes/resident/my'
import PinboardRoutes from 'routes/resident/pinboard'
import RequestsRoutes from 'routes/resident/requests'
import SettingsRoutes from 'routes/resident/settings'
import DashboardRoutes from 'routes/resident/dashboard'
import ListingRoutes from 'routes/resident/listing'
import CleanifyRoutes from 'routes/resident/cleanify'
import MyNeighboursRoutes from 'routes/resident/myNeighbours'
import PropertyManagersRoutes from 'routes/resident/propertyManagers'

export default [{
    path: '/',
    component: Layout,
    children: [
        MyRoutes,
        PinboardRoutes,
        RequestsRoutes,
        SettingsRoutes,
        DashboardRoutes,
        CleanifyRoutes,
        ListingRoutes,
        MyNeighboursRoutes,
        PropertyManagersRoutes
    ]
}]
