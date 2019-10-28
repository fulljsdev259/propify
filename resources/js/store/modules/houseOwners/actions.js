import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    getHouseOwners({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('propertyManagers', payload))
                .then(({data: r}) => {
                    if (r && !payload.disableCommit) {
                        commit('SET_HOUSE_OWNERS', r.data)
                    }
                    if (!payload.get_all) {
                        r.data.data = r.data.data.map((manager) => {
                            manager.name = `${manager.first_name} ${manager.last_name}`;
                            return manager
                        });
                    }
                    if(payload.get_all) {
                        r.data = r.data.map((manager) => {
                            manager.name = `${manager.first_name} ${manager.last_name}`;
                            return manager
                        });
                    }
                    resolve(r)
                })
                .catch(({response: {data: err}}) => reject(err)));
    },
    createHouseOwner({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.post('propertyManagers', payload).then((response) => {
                resolve(response.data);
            }).catch(({response: {data: err}}) => reject(err));
        });
    },
    updateHouseOwner({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.put(`propertyManagers/${payload.id}`, payload).then((response) => {
                resolve(response.data);
            }).catch(({response: {data: err}}) => reject(err));
        });
    },
    getHouseOwner({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.get(`propertyManagers/${payload.id}`).then((response) => {
                resolve(response.data);
            }).catch(({response: {data: err}}) => reject(err));
        });
    },
    deleteHouseOwner({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`propertyManagers/${payload.id}`).then((response) => {
                resolve(response.data)
            }).catch(({response: {data: err}}) => reject(err))
        })
    },
    // assignQuarter({}, payload) {
    //     return new Promise((resolve, reject) => {
    //         axios.post(`propertyManagers/${payload.id}/quarters/${payload.toAssignId}`, {}).then((resp) => {
    //             resolve(resp.data);
    //         }).catch(({response: {data: err}}) => reject(err))
    //     });
    // },
    // assignBuilding({}, payload) {
    //     return new Promise((resolve, reject) => {
    //         axios.post(`propertyManagers/${payload.id}/buildings/${payload.toAssignId}`, {}).then((resp) => {
    //             resolve(resp.data);
    //         }).catch(({response: {data: err}}) => reject(err))
    //     });
    // },
    // unassignBuilding({}, payload) {
    //     return new Promise((resolve, reject) => {
    //         axios.delete(`propertyManagers/${payload.id}/buildings/${payload.toAssignId}`).then((response) => {
    //             resolve(response.data)
    //         }).catch(({response: {data: err}}) => reject(err))
    //     })
    // },
    // unassignQuarter({}, payload) {
    //     return new Promise((resolve, reject) => {
    //         axios.delete(`propertyManagers/${payload.id}/quarters/${payload.toAssignId}`).then((response) => {
    //             resolve(response.data)
    //         }).catch(({response: {data: err}}) => reject(err))
    //     })
    // },
    // batchDeleteHouseOwners({commit}, payload) {
    //     return new Promise((resolve, reject) => {
    //         axios.delete(`propertyManagers/batchDelete`, {data: payload}).then((response) => {
    //             resolve(response.data)
    //         }).catch(({response: {data: err}}) => reject(err))
    //     })
    // },
    // getAssignments({}, payload) {
    //     return new Promise((resolve, reject) => {
    //         axios.get(buildFetchUrl(`propertyManagers/${payload.manager_id}/assignments`, payload)).then((response) => {
    //             response.data.data.data = response.data.data.data.map((building) => {
    //                 if (building.type === 'building') {
    //                     building.aType = 1;
    //                     building.assignmentType = 'building';
    //                 } else {
    //                     building.aType = 2;
    //                     building.assignmentType = 'quarter';
    //                 }
    //                 return building;
    //             });
    //             resolve(response.data);
    //         }).catch(({response: {data: err}}) => reject(err));
    //     });
    // },
    // getIDsAssignmentsCount({}, payload) {       
    //     return new Promise((resolve, reject) => {
    //         axios.post(`propertyManagers/idsassignments`, payload).then((response) => {    
    //             resolve(response.data);
    //         }).catch(({response: {data: err}}) => reject(err));
    //     });
    // }
}
