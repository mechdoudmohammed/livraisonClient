require("./bootstrap");
import Vue from "vue";
import vSelect from "vue-select";
Vue.component("v-select", vSelect);
import "vue-select/dist/vue-select.css";
import Vuesax from "vuesax";
import "vuesax/dist/vuesax.css"; //Vuesax styles
import VueRouter from "vue-router";
import routes from "./routes";
import store from "./store";
import moment from "moment";
import "vue2-timeago/dist/vue2-timeago.css";
import VueApexCharts from "vue-apexcharts";
import en from "./lang/en";
import fr from "./lang/fr";
import ar from "./lang/ar";
import VueI18n from "vue-i18n";
import 'sweetalert2/dist/sweetalert2.min.css';

Vue.use(VueI18n);
const messages = {
    en,
    fr,
    ar
};

axios.defaults.headers.common["Authorization"] = `Bearer ${localStorage.getItem("token")}`;
async function myAsyncFunction() {
    await axios.get('api/client').then((response) => {
    
        currentUser = response.data;
      
    }).catch((errors) => {
        if (errors.response.status === 401) {
            localStorage.removeItem('token');
        }
    });
  }
  




const i18n = new VueI18n({
    locale: localStorage.getItem("locale"), // set locale
    messages, // set locale messages
    silentTranslationWarn: process.env.NODE_ENV === "production",
});

Vue.use(VueRouter);
const router = new VueRouter(routes);

Vue.use(VueApexCharts);

Vue.component("apexchart", VueApexCharts);

Vue.prototype.moment = moment;

router.beforeEach(async (to, from, next) => {
    if (
        typeof to.meta.requiresAuthAdmin != "undefined" &&
        to.meta.requiresAuthAdmin == true
    ) {
        if (store.state.loginAdmin) {
            next();
        } else {
            axios.defaults.headers.common[
                "Authorization"
            ] = `Bearer ${localStorage.getItem("token")}`;
            await axios
                .get("api/user")
                .then((response) => {
                    store.state.loginloginAdmin = true;
                    if (
                        response.data.statut == "Active" &&
                        response.data.email_verified_at != null
                    ) {
                        next();
                    } else if (
                        (response.data.statut == "Inactive" ||
                            response.data.statut == "Active") &&
                        response.data.email_verified_at == null
                    ) {
                        next({ path: "/waitVerification" });
                    } else {
                        next({
                            path: "/login",
                            query: { redirect: to.fullPath },
                        });
                    }
                })
                .catch((error) => {
                    if (error.response.status === 401) {
                        next({
                            path: "/login",
                            query: { redirect: to.fullPath },
                        });
                    }
                });
        }
    } else if (
        typeof to.meta.requiresAuthClient != "undefined" &&
        to.meta.requiresAuthClient == true
    ) {
        if (store.state.loginClient) {
            next();
        } else {
            axios.defaults.headers.common[
                "Authorization"
            ] = `Bearer ${localStorage.getItem("token")}`;
            await axios
                .get("api/client")
                .then((response) => {
                    store.state.loginClient = true;
                    if (
                        response.data.statut == "Active" &&
                        response.data.email_verified_at != null
                    ) {
                        next();
                    } else if (
                        (response.data.statut == "Inactive" ||
                            response.data.statut == "Active") &&
                        response.data.email_verified_at == null
                    ) {
                        next({ path: "/waitVerification" });
                    } else {
                        next({
                            path: "/login",
                            query: { redirect: to.fullPath },
                        });
                    }
                })
                .catch((error) => {
                    if (error.response.status === 401) {
                        next({
                            path: "/login",
                            query: { redirect: to.fullPath },
                        });
                    }
                });
        }
    } else if (
        typeof to.meta.requiresAuthEmploye != "undefined" &&
        to.meta.requiresAuthEmploye == true
    ) {
        if (store.state.loginEmploye) {
            next();
        } else {
            axios.defaults.headers.common[
                "Authorization"
            ] = `Bearer ${localStorage.getItem("token")}`;
            await axios
                .get("api/employe")
                .then((response) => {
                    store.state.loginEmploye = true;
                    if (response.data.statut == "Active") {
                        next();
                    } else {
                        next({
                            path: "/login",
                            query: { redirect: to.fullPath },
                        });
                    }
                })
                .catch((error) => {
                    if (error.response.status === 401) {
                        next({
                            path: "/login",
                            query: { redirect: to.fullPath },
                        });
                    }
                });
        }
    } else {
        next(); // make sure to always call next()!
    }
});

Vue.use(Vuesax, {
    // options here
});

Vue.config.productionTip = false;

/* client importaion */
Vue.component(
    "dashboard-client-component",
    require("./components/Global/Client/dashboardClient.vue").default
);
Vue.component(
    "navbar-client-component",
    require("./components/Global/Client/NavbarClientComponent.vue").default
);
Vue.component(
    "sidebar-client-component",
    require("./components/Global/Client/SidebarClientComponent.vue").default
);
Vue.component(
    "main-client-component",
    require("./components/Global/Client/MainClientComponent.vue").default
);
Vue.component(
    "commande-client-component",
    require("./components/Global/Client/CommandeClientComponent.vue").default
);
Vue.component(
    "article-client-component",
    require("./components/Global/Client/ArticleClientComponent.vue").default
);
Vue.component(
    "package-client-component",
    require("./components/Global/Client/PackageClientComponent.vue").default
);

Vue.component(
    "invoices-client-component",
    require("./components/Global/Client/InvoicesClientComponent.vue").default
);
Vue.component(
    "profile-client-component",
    require("./components/Global/Client/ProfileClientComponent.vue").default
);
Vue.component(
    "reclamation-client-component",
    require("./components/Global/Client/ReclamationClientComponent.vue").default
);
Vue.component(
    "employe-client-component",
    require("./components/Global/Client/EmployeClientComponent.vue").default
);
Vue.component(
    "store-client-component",
    require("./components/Global/Client/StoreClientComponent.vue").default
);
Vue.component(
    "suivie-client-component",
    require("./components/Global/Client/SuivieClientComponent.vue").default
);
Vue.component(
    "retour-client-component",
    require("./components/Global/Client/RetourClientComponent.vue").default
);
Vue.component(
    "statistique-client-component",
    require("./components/Global/Client/StatistiqueClientComponent.vue").default
);

/* fin client importaion */

/* Visiteur importaion */
Vue.component("home", require("./components/pages/User/home.vue").default);
/* fin Visiteur importaion */

const app = new Vue({
    el: "#app",
    router,
    store,
    i18n,
});
