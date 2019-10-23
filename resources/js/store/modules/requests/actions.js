import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';
import queryString from 'query-string'
import { EventBus } from '../../../event-bus.js';

export default {
    getRequests({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('requests', payload))
            .then(({data: r}) => {
                commit('SET_REQUESTS', r.data);                
                EventBus.$emit('request-get-counted', r.data.total);
                resolve(r)
             }).catch(({response: {data: err}}) => reject(err)));
    },
    createRequest({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.post('requests', payload).then((response) => {
                resolve(response.data);
            }).catch(({response: {data: err}}) => reject(err));
        });
    },
    updateRequest({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.put(`requests/${payload.id}`, payload).then((response) => {
                resolve(response.data);
            }).catch(({response: {data: err}}) => reject(err));
        });
    },
    getRequest({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.get(`requests/${payload.id}`).then((response) => {
                resolve(response.data);
            }).catch(({response: {data: err}}) => reject(err));
        });
    },
    downloadRequestPDF(_, {id}) {
        return new Promise((resolve, reject) => {
            axios.post(`requests/${id}/download-pdf`, {}, {
                responseType: 'arraybuffer',
                headers: {
                    'Accept': 'application/pdf'
                }
            })
                .then((resp) => resolve(resp))
                .catch(({response: {data: err}}) => reject(err));
        });
    },
    deleteRequest({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`requests/${payload.id}`).then((response) => {
                resolve(response.data)
            }).catch(({response: {data: err}}) => reject(err))
        })
    },
    deleteRequestWithIds({}, payload) {        
        return new Promise((resolve, reject) => {
            axios.post(`requests/deletewithids`, {ids: _.map(payload, 'id')}).then((resp) => {                
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    async addRequestComment({}, {id, ...payload}) {
        try {
            const {data} = await axios.post(`requests/${id}/comments`, payload);

            return Promise.resolve(data);
        } catch (err) {
            return Promise.reject(err.response.data);
        }
    },
    uploadRequestMedia({}, {id, ...payload}) {
        return new Promise((resolve, reject) => {
            axios.post(`requests/${id}/media`, payload).then((resp) => {
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err));
        });
    },
    deleteRequestMedia({}, {id, media_id}) {
        return new Promise((resolve, reject) => {
            axios.delete(`requests/${id}/media/${media_id}`).then((resp) => {
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err));
        });
    },
    sendServiceRequestMail({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`requests/${payload.request}/notify`, payload).then((resp) => {
                resolve(resp);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    assignManager({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`requests/${payload.request}/managers/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    assignProvider({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`requests/${payload.request}/providers/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    assignAdministrator({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`requests/${payload.request}/users/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    massEdit({}, payload) {
        return new Promise((resolve, reject) => {
            axios.put(`requests/massedit`, payload).then((resp) => {
                resolve(resp);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    unassignAssignee({}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`requests-assignees/${payload.toAssignId}`).then((resp) => {
                resolve(resp);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    getAssignees({commit}, payload) {
        return new Promise((resolve, reject) => {
                axios.get(buildFetchUrl(`requests/${payload.request_id}/assignees`, payload))
                    .then(({data: r}) => {
                        if (!Array.isArray(r.data.data)) {
                            r.data.data = Object.values(r.data.data);
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
    getRequestConversations({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('conversations', {
                ...payload,
                conversationable: 'request',
                get_all: true
            }))
                .then(({data: r}) => {
                    resolve(r);
                })
                .catch(({response: {data: err}}) => reject(err)));
    },
    async getRequestTemplates ({commit}, {id}) {
        const {data} = await axios.get(`requests/${id}/communicationTemplates`)

        commit('SAVE_REQUEST_TEMPLATES', {id, data: data.data})
    },
    getRequestTags({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.get(`requests/${payload.id}/tags`).then((response) => {
                resolve(response.data);
            }).catch(({response: {data: err}}) => reject(err));
        });
    },
    createRequestTags({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`requests/${payload.id}/tags`, payload).then((response) => {
                resolve(response.data);
            }).catch(({response: {data: err}}) => reject(err));
        });
    },
    deleteRequestTag({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`requests/${payload.id}/tags/${payload.tag_id}`).then((response) => {
                resolve(response.data);
            }).catch(({response: {data: err}}) => reject(err));
        })
    }
}
