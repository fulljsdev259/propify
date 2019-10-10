import {format} from 'date-fns'

export default {
    requests(state, getters, rootState) {
        const {application: {constants: {requests}}} = rootState;
        const storerequests = state.requests.data ? state.requests.data : [];

        return storerequests.map((request) => {
            request.priority_label = requests.priority[request.priority];
            request.internal_priority_label = requests.internal_priority[request.internal_priority];
            request.status_label = requests.status[request.status];
            

            request.qualification_label = request.qualification > 0 && request.qualification <= 5 ? requests.qualification[request.qualification] : "";
            request.tenant_name = request.tenant ? `${request.tenant.first_name} ${request.tenant.last_name}` : '';
            request.category_name = request.category.name;
            request.parent_category_name = request.category.parent_id ? request.category.parentCategory.name : '';

            const assignedUsers = [...request.assignedUsers];

            request.assignedUsersCount = 0;
            if (assignedUsers.length) {
                request.assignedUsers = request.assignedUsers.splice(0, 2);
                if (assignedUsers.length > 2) {
                    request.assignedUsersCount = assignedUsers.length - 2;
                }
            }

            if (request.tenant && request.tenant.building && request.tenant.building.address) {
                request.address = `${request.tenant.building.address.street} ${request.tenant.building.address.house_num}`;
                request.zip = `${request.tenant.building.address.zip} ${request.tenant.building.address.city}`;
            }

            return request;
        });
    },
    requestsMeta(state) {
        return _.omit(state.requests, 'data');
    },
    getRequestTemplatesWithId: state => id => state.templates[id]
}
