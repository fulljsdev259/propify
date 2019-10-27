export default {
    services(state) {
        if (state.services.data) {
            return state.services.data.map((service) => {
                service.addr = service.address.street;
                service.cty = `${service.address.zip} ${service.address.city}`;

                return service;
            });
        }

        return state.services;
    },
    servicesMeta(state) {
        return _.omit(state.services, 'data');
    }
}
