export default {
    houseOwners(state) {
        return state.houseOwners.data;
    },
    houseOwnersMeta(state) {
        return _.omit(state.houseOwners, 'data');
    }
}