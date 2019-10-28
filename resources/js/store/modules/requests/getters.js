import {format} from 'date-fns'

export default {
    requests(state, getters, rootState) {
        const {application: {constants: {requests}}} = rootState;
        const storerequests = state.requests.data ? state.requests.data : [];

        return storerequests.map((request) => {
            //request.priority_label = requests.priority[request.priority];
            //request.internal_priority_label = requests.internal_priority[request.internal_priority];
            request.status_label = requests.status[request.status];
            

            request.qualification_label = request.qualification > 0 && request.qualification <= 5 ? requests.qualification[request.qualification] : "";
            request.resident_name = request.resident ? `${request.resident.first_name} ${request.resident.last_name}` : '';
            request.category_name = request.category.name;
            
            const assignedUsers = [...request.assignedUsers];

            request.assignedUsersCount = 0;
            if (assignedUsers.length) {
                request.assignedUsers = request.assignedUsers.splice(0, 2);
                if (assignedUsers.length > 2) {
                    request.assignedUsersCount = assignedUsers.length - 2;
                }
            }

            if (request.resident && request.resident.building && request.resident.building.address) {
                request.address = `${request.resident.building.address.street} ${request.resident.building.address.house_num}`;
                request.zip = `${request.resident.building.address.zip} ${request.resident.building.address.city}`;
            }

            return request;
        });
    },
    requestsMeta(state) {
        return _.omit(state.requests, 'data');
    },
    getRequestTemplatesWithId: state => id => state.templates[id]
}
