import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';
import { EventBus } from '../../../event-bus.js';
export default {
    getQuarters({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('quarters', payload))
                .then(({data: r}) => (r && commit('SET_QUARTERS', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    getQuarter(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.get(`quarters/${id}`)
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    createQuarter(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('quarters', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    updateQuarter(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.put(`quarters/${id}`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteQuarter(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.delete(`quarters/${id}`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteQuarterWithIds({}, payload) {        
        return new Promise((resolve, reject) => {
            axios.post(`quarters/deletewithids`, {ids: _.map(payload, 'id')}).then((resp) => {                
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    uploadQuarterFile(_, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`quarters/${payload.id}/media`, {...payload})
                .then((resp) => {
                    resolve({
                        success: true,
                        message: resp.data.message,
                        media: resp.data.data
                    });
                }).catch(({response: {data: err}}) => reject(err))
        })
    },
    deleteQuarterFile(_, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`quarters/${payload.id}/media/${payload.media_id}`)
                .then((resp) => {
                    resolve(resp.data);
                }).catch(({response: {data: err}}) => reject(err))
        });
    },
    assignManagerToQuarter({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`quarters/${payload.id}/managers`, {managerIds: [payload.toAssignId]})
                .then((resp) => {
                    resolve(resp.data);
                }).catch(({response: {data: err}}) => reject(err))
        });
    },
    getAllAdminsForQuarter(_, {quarter_id, is_get_function, search}) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl(`alladmins`, {exclude_assignees_quarter_id: quarter_id, function: is_get_function, search: search}))
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    assignUserToQuarter({}, {id, ...payload}) {
        return new Promise((resolve, reject) => {
            axios.post(`quarters/${id}/users`, payload)
                .then((resp) => {
                    resolve(resp.data);
                }).catch(({response: {data: err}}) => reject(err))
        });
    },
    assignUsersToQuarter({}, {id, ...payload}) {
        return new Promise((resolve, reject) => {
            axios.post(`quarters/${id}/users/mass-assign`, payload.user_params)
                .then((resp) => {
                    resolve(resp.data);
                }).catch(({response: {data: err}}) => reject(err))
        });
    },
    unassignQuarterAssignee(_, {assignee_id}) {
        return new Promise((resolve, reject) => {
            axios.delete(`quarters-assignees/${assignee_id}`).then((resp) => {
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    getQuarterAssignees({commit}, payload) {        
        return new Promise((resolve, reject) => {
                axios.get(buildFetchUrl(`quarters/${payload.quarter_id}/assignees`, payload))
                    .then(({data: r}) => {
                        if (!Array.isArray(r.data.data)) {
                            r.data.data = Object.values(r.data.data);                            
                        }                        
                        if(r.data.total){
                            EventBus.$emit('assignee-get-counted', r.data.total);
                        }                        
                        r.data.data = r.data.data.map((user) => {
                            if(user.type == 'user'){
                                user.uType = 3;
                            } if(user.type == 'manager'){
                                user.uType = 2;
                            }
                            return user;
                        });

                        resolve(r);
                    }).catch(({response: {data: err}}) => reject(err));
            }
        );
    },
    getQuarterEmailReceptionists({commit}, {quarter_id}) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl(`quarters/${quarter_id}/email-receptionists`))
                .then(({data: r}) => (r && resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    saveQuarterEmailReceptionists({commit}, {quarter_id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.post(buildFetchUrl(`quarters/${quarter_id}/email-receptionists`), restPayload.categories)
                .then(({data: r}) => (r && resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    saveQuarterWorkflow({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.post(`workflows`, payload)
                .then(({data: r}) => (r && resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    updateQuarterWorkflow({commit}, {id, ...payload}) {
        return new Promise((resolve, reject) =>
            axios.put(`workflows/${id}`, payload)
                .then(({data: r}) => (r && resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteQuarterWorkflow({commit}, {id}) {
        return new Promise((resolve, reject) =>
            axios.delete(`workflows/${id}`)
                .then(({data: r}) => (r && resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    
}
