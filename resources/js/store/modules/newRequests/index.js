// TODO - to be completed

export default {
    namespaced: true,
    state: {
        data: []
    },
    actions: {
        reset ({commit}) {
            commit('reset')
        },
        async get ({state, commit}, {id, ...params} = {}) {
            const {data} = await this._vm.axios.get(id ? `requests/${id}` : 'requests', {params, showMessage: false})

            if (state.data.length) {
                const {data: items, ...rest} = data.data

                commit('set', {
                    data: [
                        ...state.data,
                        ...items
                    ],
                    ...rest
                })
            } else {
                commit('set', data.data)
            }
        },
        async create ({state, commit}, params) {
            const {data} = await this._vm.axios.post('requests', params, {showMessage: true})
            const newData = state

            newData.data.unshift(data.data)

            commit('set', newData)
            return data;
        },
        async update ({commit}, {id, ...params}) {
            const {data} = await this._vm.axios.put(`requests/${id}`, params, {showMessage: true})

            commit('update', data.data)
        },
        async delete ({commit}, {id}) {
            await this._vm.axios.delete(`requests/${id}`, {showMessage: true})

            commit('delete', id)
        },
        async uploadMedia({state, commit}, {id, ...payload}) {
            const {data} = await this._vm.axios.post(`requests/${id}/media`, payload);
            if(data.success == true) {
                commit('addmedia', {data_id : id, media : data.data})
            }
            return data;
        },
        async addMedia({ commit}, {id, media}) {
            commit('addmedia', {data_id : id, media})
        },
    },
    getters: {
        getById: ({data}) => id => data.find(request => request.id === id)
    },
    mutations: {
        reset: state => Object.assign(state, {
            data: [],
            current_page : 0
        }),
        set: (state, payload) => Object.assign(state, payload),
        update: ({data}, payload) => Object.assign(data.find(({id}) => id === payload.id), payload),
        delete: ({data}, id) => {
            let i = data.length

            while (i--) {
                if (id === data[i].id) {
                    data.splice(i, 1)

                    break
                }
            }
        },
        addmedia: ({data}, {data_id, media}) => {
            let item = data.find(({id}) => id === data_id)
            if(!item.media)
                item.media = [];
            item.media.push(media);
        }
    }
}