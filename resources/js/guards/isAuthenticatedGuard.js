export default (to, from, next) => localStorage.token ? next() : next({name: 'login'})