
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
            const {data} = await this._vm.axios.get(id ? `pinboard/${id}` : 'pinboard', {params})
            if (state.data.length) {
                if (id) {
                    const newData = state
                    newData.data.unshift(data.data)
                    commit('set', newData)
                }
                else {
                    const {data: items, ...rest} = data.data
                    commit('set', {
                        data: [
                            ...state.data,
                            ...items
                        ],
                        ...rest
                    })
                }
            } else {
                commit('set', data.data)
            }
        },
        async getById ({state, commit}, {id, ...params} = {}) {
            const {data} = await this._vm.axios.get(`pinboard/${id}`, {params})
            if (state.data.length) {
                const newData = state
                newData.data.unshift(data.data)
                commit('set', newData)
            } else {
                const newData = state
                newData.data.push(data.data)
                commit('set', newData)
            }
        },
        async create ({state, commit}, params) {
            const {data} = await this._vm.axios.post('pinboard', params, {showMessage: true})
            const newData = state

            newData.data.unshift(data.data)

            commit('set', newData)
            return data;
        },
        async update ({commit}, {id, ...params}) {
            const {data} = await this._vm.axios.put(`pinboard/${id}`, params, {showMessage: true})

            commit('update', data.data)
            return data;
        },
        async delete ({commit}, {id}) {
            await this._vm.axios.delete(`pinboard/${id}`, {showMessage: true})

            commit('delete', id)
        },
        async publish ({commit}, {id, ...params}) {
            const {data} = await this._vm.axios.post(`pinboard/${id}/publish`, params)

            commit('update', data.data)
        },
        async like ({commit, getters}, {id}) {
            const {data} = await this._vm.axios.post(`pinboard/${id}/like`)
            // const newData = getters[TYPES.getters.getById](id)
            const newData = getters.getById(id)
            
            Object.assign(newData, {
                liked: true,
                likes: newData.likes.concat([data.data]),
                likes_count: newData.likes_count + 1
            })

            commit('update', newData)
        },
        async unlike ({commit, getters}, {id}) {
            const {data} = await this._vm.axios.post(`pinboard/${id}/unlike`)
            // const newData = getters[TYPES.getters.getById](id)
            const newData = getters.getById(id)

            Object.assign(newData, {
                liked: false,
                likes: newData.likes.filter(user => user.id !== data.data.id),
                likes_count: newData.likes_count - 1
            })

            commit('update', newData)
        },
        async assignBuilding ({commit}, {id, buildingId}) {
            const {data} = await this._vm.axios.post(`pinboard/${id}/buildings/${buildingId}`)

            commit('update', data.data)
        },
        async unassignBuilding ({commit}, {id, buildingId}) {
            const {data} = await this._vm.axios.delete(`pinboard/${id}/buildings/${buildingId}`)

            commit('update', data.data)
        },
        async assignQuarter ({commit}, {id, quarterId}) {
            const {data} = await this._vm.axios.post(`pinboard/${id}/quarters/${quarterId}`)

            commit('update', data.data)
        },
        async unassignQuarter ({commit}, {id, quarterId}) {
            const {data} = await this._vm.axios.delete(`pinboard/${id}/quarters/${quarterId}`)

            commit('update', data.data)
        },
        async assignProvider ({commit}, {id, providerId}) {
            const {data} = await this._vm.axios.post(`pinboard/${id}/providers/${providerId}`)

            commit('update', data.data)
        },
        async unassignProvider ({commit}, {id, providerId}) {
            const {data} = await this._vm.axios.delete(`pinboard/${id}/providers/${providerId}`)

            commit('update', data.data)
        },
        async getComments ({commit}, {parent_id, ...params}) {
            const {data} = await this._vm.axios.get('comments', {params})
        },
        async addMedia({ commit}, {id, media}) {
            commit('addmedia', {data_id : id, media})
        },
    },
    getters: {
        getById: ({data}) => id => data.find(item => item.id === id)
    },
    mutations: {
        reset: state => Object.assign(state, {
            data: [],
            current_page: 0,
        }),
        set: (state, payload) => Object.assign(state, payload),
        update: ({data}, payload) => { Object.assign(data.find(({id}) => id === payload.id), payload);
         },
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
