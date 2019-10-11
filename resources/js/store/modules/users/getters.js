export default {
    users(state) {
        return state.users.data;
    },
    usersMeta(state) {
        return _.omit(state.users, 'data');
    },
    loggedIn(state) {
        return state.loggedIn;
    },
    allRoles(state) {
        return state.roles;
    },
    loggedInUser ({loggedInUser}) {
        return loggedInUser;
    },
    isResident({loggedInUser}) {
        return !!loggedInUser.resident;
    },
    Settings({Settings}) {
        return Settings;
    }
}
