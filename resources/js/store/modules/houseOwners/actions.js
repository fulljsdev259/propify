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
}
