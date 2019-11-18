import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';
import { EventBus } from '../../../event-bus.js';

export default {
    getBuildings({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('buildings', payload))
                .then(({data: r}) => (r && commit('SET_BUILDINGS', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    getBuilding(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.get(`buildings/${id}`)
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    createBuilding(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('buildings', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    updateBuilding(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.put(`buildings/${id}`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteBuilding(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.delete(`buildings/${id}`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    getBuildingStatistics(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.get(`buildings/${id}/statistics`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    uploadBuildingFile(_, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`buildings/${payload.id}/media`, {...payload})
                .then((resp) => {
                    resolve({
                        success: true,
                        message: resp.data.message,
                        media: resp.data.data
                    });
                }).catch(({response: {data: err}}) => reject(err))
        })
    },
    deleteBuildingFile(_, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`buildings/${payload.id}/media/${payload.media_id}`)
                .then((resp) => {
                    resolve(resp.data);
                }).catch(({response: {data: err}}) => reject(err))
        });
    },
    unassignProviderToBuilding({}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`buildings/${payload.id}/service/${payload.toAssignId}`).then((resp) => {                
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    getAllAdminsForBuilding(_, {building_id, search}) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl(`alladmins`, {exclude_assignees_building_id: building_id, search: search}))
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    assignUserToBuilding({}, {id, ...payload}) {
        return new Promise((resolve, reject) => {
            axios.post(`buildings/${id}/users`, payload)
                .then((resp) => {
                    resolve(resp.data);
                }).catch(({response: {data: err}}) => reject(err))
        });
    },
    assignProviderToBuilding({}, payload){
        return new Promise((resolve, reject) => {
            axios.post(`buildings/${payload.id}/service/${payload.toAssignId}`)
                .then((resp) => {                    
                    resolve(resp.data);
                }).catch(({response: {data: err}}) => reject(err))
        });
    },   
    assignManagerToBuilding({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`buildings/${payload.id}/propertyManagers`, {managerIds: [payload.toAssignId]})
                .then((resp) => {
                    resolve(resp.data);
                }).catch(({response: {data: err}}) => reject(err))
        });
    },    
    assignUsersToBuilding({}, payload) {

        return new Promise((resolve, reject) => {
            axios.post(`buildings/${payload.id}/users`, {userIds: [payload.toAssignId]})
                .then((resp) => {
                    resolve(resp.data);
                }).catch(({response: {data: err}}) => reject(err))
        });
    },
    unassignBuildingAssignee(_, {assignee_id}) {
        return new Promise((resolve, reject) => {
            axios.delete(`buildings-assignees/${assignee_id}`).then((resp) => {
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    deleteBuildingWithIds({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`buildings/deletewithids`, {...payload}).then((resp) => {                
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    checkUnitRequestWidthIds({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`buildings/checkunitrequest`, {...payload}).then((resp) => {                
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    getBuildingAssignees({commit}, payload) {        
        return new Promise((resolve, reject) => {
                axios.get(buildFetchUrl(`buildings/${payload.building_id}/assignees`, payload))
                    .then(({data: r}) => {
                        if (!Array.isArray(r.data.data)) {
                            r.data.data = Object.values(r.data.data);                            
                        }                        
                        if(r.data.total){
                            EventBus.$emit('assignee-get-counted', r.data.total);
                        }                        
                        r.data.data = r.data.data.map((user) => {
                            if (user.type == 'provider') {
                                user.uType = 1;
                            } else if(user.type == 'user'){
                                user.uType = 3;
                            } else{
                                user.uType = 2;
                            }
                            return user;
                        });

                        resolve(r)
                    })
                    .catch(({response: {data: err}}) => reject(err));
            }
        );
    },
    getBuildingEmailReceptionists({commit}, {building_id}) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl(`buildings/${building_id}/email-receptionists`))
                .then(({data: r}) => (r && resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    saveBuildingEmailReceptionists({commit}, {building_id, ...restPayload}) {
        if(restPayload.global_email_receptionist) {
            return new Promise((resolve, reject) =>
                axios.post(buildFetchUrl(`buildings/${building_id}/email-receptionists`), { global_email_receptionist : restPayload.global_email_receptionist } )
                    .then(({data: r}) => (r && resolve(r)))
                    .catch(({response: {data: err}}) => reject(err)));
        } else {
            return new Promise((resolve, reject) =>
                axios.post(buildFetchUrl(`buildings/${building_id}/email-receptionists`), {...restPayload.categories, global_email_receptionist : restPayload.global_email_receptionist})
                    .then(({data: r}) => (r && resolve(r)))
                    .catch(({response: {data: err}}) => reject(err)));
        }

    },

}
