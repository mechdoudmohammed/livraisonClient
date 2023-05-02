import Vuex from 'vuex';
import Vue from 'vue';
import axios from 'axios'
Vue.use(Vuex);
const store = new Vuex.Store({
  state: {
    loginAdmin: false,
    loginClient: false,
    loginEmploye: false,
    token: localStorage.getItem('token'),
    currentUser: {}
  },

  mutations: {
    isClient: (state, loginClient) => {
      state.loginClient = loginClient;

    },
    isAdmin: (state, loginAdmin) => {
      state.loginAdmin = loginAdmin;

    },
    isEmploye: (state, loginEmploye) => {
      state.loginEmploye = loginEmploye;

    },
  },
  actions: {
    async isClient({ commit }) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`
      if (localStorage.getItem('token') != "") {
        await axios.get('api/client')
          .then(response => {
            commit('isClient', true);
          })
          .catch(error => {
         
          })
      } else {
        commit('isClient', false);
      }
    },
    async isAdmin({ commit }) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`
      if (localStorage.getItem('token')) {
        await axios.get('api/user')
          .then(response => {
            if (response.data.nom) {
              commit('isAdmin', true);
            }
          })
          .catch(error => {
          
          })
      }
      else {
        commit('isAdmin', false);
      }
    },
    async isEmploye({ commit }) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`
      if (localStorage.getItem('token')) {
        await axios.get('api/employe')
          .then(response => {
            if (response.data) {
              commit('isEmploye', true);
            }
          })
          .catch(error => {

          })
      }
      else {
        commit('isEmploye', false);
      }
    },



  }
});


export default store;



