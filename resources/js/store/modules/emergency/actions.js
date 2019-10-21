import axios from '@/axios';

export default {
    getEmergency({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get('emergency', payload)
                .then(({data: r}) => {
                    commit('SET_EMERGENCY', r.data);

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
    createEmergency(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('emergency', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    updateEmergency(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.put(`emergency/${id}`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteEmergency(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.delete(`emergency/${id}`)
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    
}
