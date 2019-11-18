import { EventBus } from '../../../event-bus.js';
export default {
    namespaced: true,
    state: {
        pinboard: {},
        request: {},
        listing: {},
        internalNotices: {}
    },
    actions: {
        async get ({commit, getters}, {parent_id, ...params}) {
            if (getters.get(params.id, params.commentable)) {
                const currentComments = getters.get(params.id, params.commentable)
                const newComments = currentComments.data.filter(comment => comment.isNew)

                params.exclude = currentComments.data.slice(0, newComments.length).reduce((array, {id}) => {
                    array.push(id)

                    return array
                }, [])
            }

            let newParams = params, url = params.commentable !== 'internalNotices' ? 'comments' : 'internalNotices'

            if (parent_id && params.id) {
                const {id, ...restParams} = params
                const comment = getters.get(id, restParams.commentable).data.find(({id}) => id === parent_id)

                if (comment) {
                    newParams = restParams
                    
                    const newChildren = comment.children.data.filter(({isNew}) => isNew)

                    newParams.exclude = comment.children.data.slice(0, newChildren.length).reduce((array, {id}) => {
                        array.push(id)

                        return array
                    }, [])
                }

                url += '/' + parent_id
            }

            const {data} = await this._vm.axios.get(url, {params: newParams})
            

            commit('set', {
                parent_id,
                id: params.id,
                data: data.data,
                commentable: params.commentable
            })
        },
        async create ({commit, rootGetters}, {id, ...params}) {

            let url = params.commentable == 'internalNotices' ? `` : `/${id}/comments`;
            const {data} = await this._vm.axios.post({
                pinboard: 'pinboard',
                listing: 'listings',
                request: 'requests',
                conversation: 'conversations',
                internalNotices: 'internalNotices'
            }[params.commentable] + url, params)

            commit('create', {
                id,
                data: data.data,
                parent_id: params.parent_id,
                commentable: params.commentable
            })

            switch (params.commentable) {
                case 'pinboard':
                    let pinboard = rootGetters['newPinboard/getById'](id)

                    if (pinboard) {
                        Object.assign(pinboard, {comments_count: pinboard.comments_count + 1})

                        commit('newPinboard/update', pinboard, {root: true})
                    }

                    break
                case 'listing':
                    let listing = rootGetters['newPinboard/getById'](id)

                    if (listing) {
                        Object.assign(listing, {comments_count: listing.comments_count + 1})
                        
                        commit('newListings/update', listing, {root: true})
                    }

                    break
                case 'request':
                    let request = rootGetters['newRequests/getById'](id)

                    if (request) {
                        Object.assign(request, {comments_count: request.comments_count + 1})
                        
                        commit('newRequests/update', request, {root: true})
                    }

                    break
            }
        },
        async createOnly ({}, {id, ...params}) {
            const {data} = await this._vm.axios.post({
                pinboards: 'pinboard',
                listing: 'listings',
                request: 'requests',
                conversation: 'conversations'
            }[params.commentable] + `/${id}/comments`, params)

        },
        async update ({commit}, {id, parent_id, child_id, ...params}) {        
            const {data} = await this._vm.axios.put( `${ params.commentable !== 'internalNotices' ? 'comments': 'internalNotices' }/${child_id ? child_id : parent_id}`, params.commentable !== 'internalNotices' ? child_id ? {parent_id, ...params} : params : { request_id: id, user_id: this.getters.loggedInUser.id, comment: params.comment} )

            commit('update', {id, parent_id, child_id, commentable: params.commentable, data: data.data})
        },
        async delete ({commit, dispatch}, {id, parent_id, child_id, ...params}) {
            const {data} = await this._vm.axios.delete(`${ params.commentable !== 'internalNotices' ? 'comments': 'internalNotices' }/${child_id ? child_id : parent_id}`, child_id ? {parent_id, ...params} : params)

            commit('delete', {id, parent_id, child_id, commentable: params.commentable, data: data.data})
        },
        clear ({commit}, commentable) {
            commit('clear', commentable)
        },
        reset ({commit}) {
            commit('reset')
        },
    },
    getters: {
        get: state => (id, commentable) => state[commentable][id] ? state[commentable][id] : { data : [], total : 0}
    },
    mutations: {
        set (state, {id, data, parent_id, commentable}) {
            data.data = data.data.map(comment => {
                comment.children = {data: [], total: 0}
    
                return comment
            }).reverse()
    
            let props = Object.keys(data).filter(prop => prop !== 'data')
    
            if (state[commentable][id] && !parent_id) {
                for (let prop of props) {
                    if (state[commentable][id][prop] !== data[prop]) {
                        state[commentable][id][prop] = data[prop]
                    }
                }
    
                state[commentable][id].data = data.data.concat(state[commentable][id].data)
            } else if (id && parent_id) {
                let comment = state[commentable][id].data.find(({id}) => id == parent_id)
    
                if (comment) {
                    if (!comment.children.data.length) {
                        comment.children = data
                    } else {
                        for (let prop of props) {
                            if (comment.children[prop] !== data[prop]) {
                                comment.children[prop] = data[prop]
                            }
                        }
    
                        comment.children.data = data.data.concat(comment.children.data)
                    }
                }
            } else {
                state[commentable][id] = {...state[commentable][id], ...data}
            }
        },
        create (state, {id, data, parent_id, commentable}) {
            data.isNew = true
            if(commentable === 'internalNotices'){
                EventBus.$emit('notice-comment-added');
            } else if(commentable === 'pinboard'){
                EventBus.$emit('pinboard-comment-added');
            } else if(commentable === 'request'){
                EventBus.$emit('request-comment-added');
            }
            if (id && parent_id) {
                let comment = state[commentable][id].data.find(({id}) => id === parent_id)
    
                if (comment) {
                    comment.children_count++
                    comment.children.total++
                    comment.children.data.push(data)
                }
            } else {
                data.children = {data: [], total: 0}
    
                state[commentable][id].total++
                state[commentable][id].data.push(data)
            }
        },
        update (state, {id, data, parent_id, child_id, commentable}) {
            const comment = state[commentable][id].data.find(c => c.id === parent_id)
    
            if (comment) {
                if (child_id) {
                    const childComment = comment.children.data.find(({id}) => id === child_id)
    
                    if (childComment) {
                        Object.assign(childComment, data)
                    }
                } else {
                    Object.assign(comment, data)
                }
            }
        },
        delete (state, {id, data, child_id, parent_id, commentable}) {            
            if(commentable === 'internalNotices'){
                EventBus.$emit('notice-comment-deleted');
            } else if(commentable === 'pinboard'){
                EventBus.$emit('pinboard-comment-deleted');
            } else if(commentable === 'request'){
                EventBus.$emit('request-comment-deleted');
            }
            const idx = state[commentable][id].data.findIndex(c => c.id === parent_id)
    
            if (idx > -1) {
                if (child_id) {
                    const childIdx = state[commentable][id].data[idx].children.data.findIndex(cc => cc.id === child_id)
                    
                    if (childIdx > -1) {
                        state[commentable][id].data[idx].children.data.splice(childIdx, 1)
                        state[commentable][id].data[idx].children_count--
                    }
                } else if (data.comment) {
                    state[commentable][id].data.splice(idx, 1)
                } else {
                    state[commentable][id].data[idx].comment = data.comment
                }
            }
        },
        clear (state, {commentable}) {
            Object.assign(state[commentable], {})
        },
        reset: state => Object.assign(state, {
            pinboard: {},
            request: {},
            listing: {},
            internalNotices: {}
        })
    }
}