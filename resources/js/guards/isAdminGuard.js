import store from 'store'

export default (to, from, next) => store.getters.loggedInUser.roles.findIndex(({name}) => name === 'resident') == -1 ? next() : next({name: 'residentDashboard'})


//export default (to, from, next) => store.getters.loggedInUser.roles.findIndex(({name}) => name === 'super_admin') > -1 ? next() : next({name: 'residentDashboard'})

//export default (to, from, next) => store.getters.loggedInUser.roles.findIndex(({name}) => name === 'super_admin' || name === 'administrator') > -1 ? next() : next({name: 'residentDashboard'})