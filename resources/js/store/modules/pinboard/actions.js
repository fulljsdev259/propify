import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    getPinboards({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('pinboard', payload))
                .then(({data: r}) => (r && commit('SET_PINBOARD', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    getPinboard(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.get(`pinboard/${id}`)
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    createPinboard(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('pinboard', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    updatePinboard(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.put(`pinboard/${id}`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deletePinboard(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.delete(`pinboard/${id}`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deletePinboardWithIds({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`pinboard/deletewithids`, {ids: _.map(payload, 'id')}).then((resp) => {
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    incrementViews(_, payload) {
        return new Promise((resolve, reject) =>
            axios.put(`pinboard/${payload.id}/views`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    likePinboard(_, id) {
        return new Promise((resolve, reject) =>
            axios.post(`pinboard/${id}/like`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    unlikePinboard(_, id) {
        return new Promise((resolve, reject) =>
            axios.post(`pinboard/${id}/unlike`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    changePinboardPublish(_, {id, ...body}) {
        return new Promise((resolve, reject) =>
            axios.post(`pinboard/${id}/publish`, body)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    uploadPinboardMedia(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.post(`pinboard/${id}/media`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deletePinboardMedia({}, {id, media_id}) {
        return new Promise((resolve, reject) => {
            axios.delete(`pinboard/${id}/media/${media_id}`).then((resp) => {
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err));
        });
    },
    getArticlePinboards({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('pinboard', {...payload, type: 1}))
                .then(({data: r}) => (r && commit('SET_PINBOARD', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    getPinboardTruncated({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('pinboard', payload))
                .then(({data: r}) => {
                    r.data.data = r.data.data.map((pinboard) => {
                        pinboard.preview = `${pinboard.content.substring(0, 50)}...`;
                        return pinboard;
                    });
                    resolve(r)
                })
                .catch(({response: {data: err}}) => reject(err)));
    },
    assignPinboardQuarter({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`pinboard/${payload.id}/quarters/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    assignPinboardBuilding({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`pinboard/${payload.id}/buildings/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    unassignPinboardBuilding({}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`pinboard/${payload.id}/buildings/${payload.toAssignId}`).then((response) => {
                resolve(response.data)
            }).catch(({response: {data: err}}) => reject(err))
        })
    },
    unassignPinboardQuarter({}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`pinboard/${payload.id}/quarters/${payload.toAssignId}`).then((response) => {
                resolve(response.data)
            }).catch(({response: {data: err}}) => reject(err))
        })
    },
    getPinboardAssignments({}, payload) {
        return new Promise((resolve, reject) => {
            axios.get(buildFetchUrl(`pinboard/${payload.pinboard_id}/locations`, payload)).then((response) => {
                response.data.data.data = response.data.data.data.map((building) => {
                    if (building.type === 'building') {
                        building.aType = 1;
                        building.assignmentType = 'building';
                    } else {
                        building.aType = 2;
                        building.assignmentType = 'quarter';
                    }
                    return building;
                });
                resolve(response.data);
            }).catch(({response: {data: err}}) => reject(err));
        });
    },
    assignPinboardProvider({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`pinboard/${payload.id}/providers/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    unassignPinboardProvider({}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`pinboard/${payload.id}/providers/${payload.toAssignId}`).then((response) => {
                resolve(response.data)
            }).catch(({response: {data: err}}) => reject(err))
        })
    },
}
