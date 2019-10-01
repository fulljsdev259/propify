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
    isTenant({loggedInUser}) {
        return !!loggedInUser.tenant;
    },
    Settings({Settings}) {
        return Settings;
    }
}
