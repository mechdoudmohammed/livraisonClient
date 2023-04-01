/* fin Client importaion */
import home from "./components/pages/User/home.vue";
import login from "./components/pages/User/login.vue";
import inscription from "./components/pages/User/inscription.vue";
import waitVerification from "./components/pages/User/waitVerification.vue";
import successVerification from "./components/pages/User/successVerification.vue";
import dashboardClient from "./components/Global/Client/dashboardClient.vue";

/* fin Client importaion */

import error404 from "./components/Global/Errors/Errors404Component.vue";
import store from "./store";

let routes = [
    //========Client routes===============//
    { path: "/404", component: error404 },
    { path: "*", redirect: "/404" },
    {
        name: "login",
        path: "/",
        component: login,
        beforeEnter: async (to, from, next) => {
            if (localStorage.getItem('token') != null) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`
                await axios.get('api/client')
                    .then(response => {
                        store.state.loginClient = true;
                        if (response.data.statut == 'Active' && response.data.email_verified_at != null) {
           next({path: '/dashboardClient',});
                          } else if ((response.data.statut == 'Inactive' || response.data.statut == 'Active') && response.data.email_verified_at == null) {
                       
                            next({path: '/waitVerification',})
                       
                          } 
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            next();
                           }
                    })

            } else {
                next();
            }

        },
    },
    {
        name: "waitVerification",
        path: "/waitVerification",
        component: waitVerification,
        beforeEnter: async (to, from, next) => {
            if (localStorage.getItem('token') != null) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`
                await axios.get('api/client')
                    .then(response => {
                        store.state.loginClient = true;
                        if (response.data.statut == 'Active' && response.data.email_verified_at != null) {
                            next({path: '/dashboardClient',})
                          } else if ((response.data.statut == 'Inactive' || response.data.statut == 'Active') && response.data.email_verified_at == null) {
                            next();
                          } 
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            next();
                           }
                    })

            } else {
                next({
                    path: '/login',
                    query: { redirect: to.fullPath }
                  })
            }

        },
    },
    {
        name: "loginClient",
        path: "/login",
        component: login,
        beforeEnter: async (to, from, next) => {
            if (localStorage.getItem('token') != null) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`
                await axios.get('api/client')
                    .then(response => {
                        store.state.loginClient = true;
                        if (response.data.statut == 'Active' && response.data.email_verified_at != null) {
           next({path: '/dashboardClient',});
                          } else if ((response.data.statut == 'Inactive' || response.data.statut == 'Active') && response.data.email_verified_at == null) {
                     
                            next({path: '/waitVerification',})
                       
                          } 
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            next();
                           }
                    })

            } else {
                next();
            }

        },
    },
    {
        name: "inscription",
        path: "/inscription",
        component: inscription,
        beforeEnter: async (to, from, next) => {
            if (localStorage.getItem('token') != null) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`
                await axios.get('api/client')
                    .then(response => {
                        store.state.loginClient = true;
                        if (response.data.statut == 'Active' && response.data.email_verified_at != null) {
                            next({path: '/dashboardClient',})
                          } else if ((response.data.statut == 'Inactive' || response.data.statut == 'Active') && response.data.email_verified_at == null) {
                            next({path: '/waitVerification',})
                          } 
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            next();
                           }
                    })
            } else {
                next()
            }

        },
    },
    {
        name: "dashboardClient",
        path: "/dashboardClient",
        component: dashboardClient,
        beforeEnter: async (to, from, next) => {
            if (localStorage.getItem('token') != null) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`
                
                await axios.get('api/client')
                    .then(response => {
                        store.state.loginClient = true;
                        if (response.data.statut == 'Active' && response.data.email_verified_at != null) {
                            next();
                          } else if ((response.data.statut == 'Inactive' || response.data.statut == 'Active') && response.data.email_verified_at == null) {
                            next({path: '/waitVerification',})
                       
                          } 
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            localStorage.removeItem('token');
                            next();
                           }
                    })

            } else {
              
            }

        },
    },
    {
        name: "article",
        path: "/article",
        component: dashboardClient,
        meta: { requiresAuthClient: true },
    },
    {
        name: "commande",
        path: "/commande",
        component: dashboardClient,
        meta: { requiresAuthClient: true },
    },
    {
        name: "package",
        path: "/package",
        component: dashboardClient,
        meta: { requiresAuthClient: true },
    },

    {
        name: "invoices",
        path: "/invoices",
        component: dashboardClient,
        meta: { requiresAuthClient: true },
    },
    {
        name: "profile",
        path: "/profile",
        component: dashboardClient,
        meta: { requiresAuthClient: true },
    },
    {
        name: "reclamation",
        path: "/reclamation",
        component: dashboardClient,
        meta: { requiresAuthClient: true },
    },
    {
        name: "suivie",
        path: "/suivie",
        component: dashboardClient,
        meta: { requiresAuthClient: true },
    },
    {
        name: "retour",
        path: "/retour",
        component: dashboardClient,
        meta: { requiresAuthClient: true },
    },
    {
        name: "employe",
        path: "/employe",
        component: dashboardClient,
        meta: { requiresAuthClient: true },
    },
    {
        name: "statistique",
        path: "/statistique",
        component: dashboardClient,
        meta: { requiresAuthClient: true },
    },
    {
        name: "store",
        path: "/store",
        component: dashboardClient,
        meta: { requiresAuthClient: true },
    },
    {
        name: "successVerification",
        path: "/email/verify/success",
        component: successVerification,
    },
    //========Fin Client routes===============//

]
/* fin admin importaion */
export default {
    hashbang: false,
    mode: 'history',
    historyApiFallback: true,

    routes
};
