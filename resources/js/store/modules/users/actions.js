import axios from '../../../axios';
import {buildFetchUrl} from "../../../helpers/url";
import router from 'routes'

export default {
    getUsers({commit}, payload) {
        if(router.currentRoute.name === 'adminUsers'){
            payload['roles'] = [];
            payload['roles'][0] = 'administrator';
        }
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('users', payload))
                .then(({data: r}) => (
                    r && commit('SET_USERS', r.data), resolve(r))
                )
                .catch(({response: {data: err}}) => reject(err)));
    },
    getUser(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.get(`users/${id}`)
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    createUser(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('users', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    updateUser(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.put(`users/${id}`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteUser(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.delete(`users/${id}`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteUserWithIds({}, payload) {        
        return new Promise((resolve, reject) => {
            axios.post(`users/deletewithids`, {ids: _.map(payload, 'id')}).then((resp) => {                
                resolve(resp.data);
            }).catch(({response: {data: err}}) => reject(err))
        });
    },
    me({dispatch, commit}) {
        return new Promise((resolve, reject) =>
            axios.get('/users/me').then(({data: r}) => {
                if (r.data) {
                    commit('SET_LOGGED_IN', true);
                    commit('SET_LOGGED_IN_USER', r.data);
                }

                resolve(r);
            }).catch(({response: {data: err, status}}) => {
                if (status === 401) {
                    dispatch('forceLogout');
                }

                reject(err);
            }));
    },

    login({dispatch}, {email, password}) {
        return new Promise((resolve, reject) =>
            axios.post('auth/login', {email, password})
                .then(({data: r}) => {
                    if (r.access_token) {
                        localStorage.setItem('token', r.access_token)
                    }

                    resolve(r);
                })
                .catch(({response: {data: err}}) => reject(err)));
    },
    autoLogin({dispatch}, {token}) {
        return new Promise((resolve, reject) =>
            axios.post('auth/autologin', {token})
                .then(({data: r}) => {
                    if (r.access_token) {
                        localStorage.setItem('token', r.access_token)
                    }

                    resolve(r);
                })
                .catch(({response: {data: err}}) => reject(err)));
    },/*
    logout({dispatch}) {
        return new Promise((resolve, reject) =>
            axios.get('auth/logout').then(({status}) => {
                if (status === 200) {
                    dispatch('forceLogout');
                }

                resolve();
            }).catch(({response: {data: err}}) => reject(err)));
    },*/
    logout({dispatch, commit}) {
        return new Promise((resolve, reject) =>
            axios.get('auth/logout').then(({status}) => {
                if (status === 200) {
                    localStorage.removeItem('token');
                    // dispatch('application/setLocale', '');
                    // commit('SET_LOGGED_IN', false);
                    // commit('SET_LOGGED_IN_USER', {});
                }
                resolve();
            }).catch((error) => {
                console.log(error);
            }));
    },
    forceLogout({commit}) {
        localStorage.removeItem('token');


        //TODO figure out how to logout without reloading the page
        window.location.reload();
        // dispatch('application/setLocale', '');
        commit('SET_LOGGED_IN', false);
        commit('SET_LOGGED_IN_USER', {});


    },
    sendForgotPassword(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('auth/forgot_password', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    resetPasswordRequest(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('auth/reset_password', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    updateUserSettings({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.put('users/me/settings', payload.settings)
                .then(({data: r}) => {
                    commit('SET_LOGGED_IN_USER', payload);

                    resolve(r);
                })
                .catch(({response: {data: err}}) => reject(err)));
    },
    changeUserPassword(_, {password_old, password, password_confirmation}) {
        return new Promise((resolve, reject) =>
            axios.put('users/me/changePassword', {
                password_old,
                password,
                password_confirmation
            }).then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    uploadAvatar({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.post('users/me/upload_image', payload)
                .then(({data: r}) => {
                    resolve(r);
                }).catch(({response: {data: err}}) => reject(err)));
    },
    changeDetails(_, payload) {
        return new Promise((resolve, reject) =>
            axios.put('users/me/', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    uploadLogo(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('users/logo_upload', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },

    getSettings({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get('settings')
                .then(r => {
                    commit('SET_REAL_ESTATE', r.data.data);
                    resolve(r.data)
                })
                .catch(({response: {data: err}}) => reject(err)));
    },
    updateSettings(_, payload) {
        return new Promise((resolve, reject) =>
            axios.put('settings', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    uploadUserAvatar({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.post(`users/${payload.id}/upload_image`, payload)
                .then(({data: r}) => {
                    resolve(r);
                }).catch(({response: {data: err}}) => reject(err)));
    },
    async addReview ({state, commit}, params) {
        const {data} = await this._vm.axios.post('addReview', params)
        const newData = state
        newData.loggedInUser.resident.review = params.review
        newData.loggedInUser.resident.rating = params.rating
        commit('SET_LOGGED_IN_USER', newData.loggedInUser);
        return data;
    }
}
