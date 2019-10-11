import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    getResidents({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('residents', payload))
                .then(({data: r}) => {
                    commit('SET_RESIDENTS', r.data);

                    if (!payload.get_all) {
                        r.data.data = r.data.data.map((resident) => {
                            resident.name = `${resident.first_name} ${resident.last_name}`;
                            return resident;
                        });
                    }

                    resolve(r)
                })
                .catch(({response: {data: err}}) => reject(err)));
    },
    getResident(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.get(`residents/${id}`)
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    createResident(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('residents', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    updateResident(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.put(`residents/${id}`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteResident(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.delete(`residents/${id}`)
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteResidentWithIds({}, payload) {        
        return new Promise((resolve, reject) => {
            axios.post(`residents/deletewithids`, {ids: _.map(payload, 'id')}).then((resp) => {                
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },    
    myTenancy(_, payload) {
        return new Promise((resolve, reject) => {
            axios.get('residents/me')
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err))
        });
    },
    updateMyTenancy(_, payload) {
        return new Promise((resolve, reject) => {
            axios.put('residents/me', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err))
        });
    },
    uploadMediaFile(_, {id, ...payload}) {
        return new Promise((resolve, reject) => {
            axios.post(`residents/${id}/media`, payload)
                .then(({data}) => resolve(data))
                .catch(({response: {data: err}}) => reject(err));
        });
    },
    deleteMediaFile(_, {id, ...payload}) {
        return new Promise((resolve, reject) => {
            axios.delete(`residents/${id}/media/${payload.media_id}`)
                .then((resp) => {
                    resolve(resp.data);
                }).catch((error) => reject(error));
        });
    },
    changeResidentStatus(_, {id, status}) {
        return new Promise((resolve, reject) => {
            axios.put(`residents/${id}/status`, {status})
                .then((resp) => {
                    resolve(resp.data)
                }).catch((error) => reject(error));
        });
    },
    downloadResidentCredentials(_, {id}) {
        return new Promise((resolve, reject) => {
            axios.post(`residents/${id}/download-credentials`, {}, {
                responseType: 'arraybuffer',
                headers: {
                    'Accept': 'application/pdf'
                }
            })
                .then((resp) => resolve(resp))
                .catch(({response: {data: err}}) => reject(err));
        });
    },
    sendResidentCredentials(_, {id}) {
        return new Promise((resolve, reject) => {
            axios.post(`residents/${id}/send-credentials`)
                .then((resp) => resolve(resp))
                .catch(({response: {data: err}}) => reject(err));
        });
    },
    getCountries({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(`countries`)
                .then(({data: r}) => (r && commit('SET_COUNTRIES', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    }
}
