import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';
import { EventBus } from '../../../event-bus.js';
export default {
    getUnits({commit}, payload) {
        return new Promise((resolve, reject) => 
            axios.get(buildFetchUrl('units', payload))
                 .then(({data: r}) => {
                    commit('SET_UNITS', r.data);                                        
                    EventBus.$emit('unit-get-counted', r.data.total);
                    resolve(r)
                 })
                 .catch(({response: {data: err}}) => reject(err)));
    },
    getUnit(_, {id}) {
        return new Promise((resolve, reject) => 
            axios.get(`units/${id}`)
                 .then(({data: r}) => resolve(r.data))
                 .catch(({response: {data: err}}) => reject(err)));
    },
    getResidentAssignees(_, payload) {
        return new Promise((resolve, reject) => 
            axios.get(buildFetchUrl(`units/${payload.unit_id}`))
                .then(({data: r}) => {
                    let residents = r.data.residents;
                    let res = {
                        data: {
                            data : []
                        }
                    }
                    

                    res.data.data = residents

                    res.data.data = res.data.data.map((user) => {
                        if (user.status == 1) {
                            user.statusString = 'Active';
                        } else {
                            user.statusString = 'Not Active';
                        }
                        user.name = user.first_name + " " + user.last_name;
                        return user;
                    });

                    resolve(res);
                })
                .catch(({response: {data: err}}) => reject(err)));
    },
    createUnit(_, payload) {
        return new Promise((resolve, reject) => 
            axios.post('units', payload)
                 .then(({data: r}) => resolve(r))
                 .catch(({response: {data: err}}) => reject(err)));
    },
    updateUnit(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) => 
            axios.put(`units/${id}`, restPayload)
                 .then(({data: r}) => resolve(r))
                 .catch(({response: {data: err}}) => reject(err)));
    },
    deleteUnit(_, {id}) {
        return new Promise((resolve, reject) => 
            axios.delete(`units/${id}`)
                 .then(({data: r}) => resolve(r))
                 .catch(({response: {data: err}}) => reject(err)));
    },
    deleteUnitWithIds({}, payload) {        
        return new Promise((resolve, reject) => {
            axios.post(`units/deletewithids`, {ids: _.map(payload, 'id')}).then((resp) => {                
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    uploadUnitFile(_, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`units/${payload.id}/media`, {...payload})
                .then((resp) => {
                    resolve({
                        success: true,
                        message: resp.data.message,
                        media: resp.data.data
                    });
                }).catch(({response: {data: err}}) => reject(err))
        })
    },
    deleteUnitFile(_, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`units/${payload.id}/media/${payload.media_id}`)
                .then((resp) => {
                    resolve(resp.data);
                }).catch(({response: {data: err}}) => reject(err))
        });
    },
}
