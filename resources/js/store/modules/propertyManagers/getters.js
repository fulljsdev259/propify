export default {
    propertyManagers(state) {
        if(state.propertyManagers.data)
            return state.propertyManagers.data;

        return state.propertyManagers;
    },
    propertyManagersMeta(state) {
        return _.omit(state.propertyManagers, 'data');
    }
}