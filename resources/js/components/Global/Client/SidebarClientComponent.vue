<template>
  <div class="page-body-wrapper" id="sidebarDiv">
    <!-- partial:partials/_sidebar.html -->
    <nav class="sidebar sidebar-offcanvas" id="sidebar">
      <ul class="nav">
        <li class="nav-item nav-profile">
          <router-link to="/profile">
            <a class="nav-link">
              <div class="nav-profile-image">
                <img src="images/profile/profile.jpg" alt="profile">
                <span class="login-status online"></span>
                <!--change to offline or busy as needed-->
              </div>

              <div class="nav-profile-text d-flex flex-column">
                <span class="font-weight-bold mb-2">{{ Client.nom }} {{ Client.prenom }}</span>
                <span class="text-secondary text-small">{{ Client.company }}</span>
              </div>
              <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
          </router-link>
        </li>
        <li class="nav-item" v-if="Client.role == 'Client'">
          <router-link to="/dashboardClient">
            <a class="nav-link" @click="hideMenu">
              <i class="mdi mdi-home menu-icon"></i>
              <span class="menu-title ">{{ $t('message.Dashboard') }}</span>

            </a>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/article" v-if="Client.stock == 1 && Client.role == 'Client' && Client && infoComplete">
            <a class="nav-link" @click="hideMenu">
              <i class="mdi mdi-dropbox menu-icon"></i>
              <span class="menu-title">{{ $t('message.Article') }}</span>

            </a>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/commande">
            <a class="nav-link" @click="hideMenu">
              <i class="mdi mdi-package-variant-closed menu-icon"></i>
              <span class="menu-title">{{ $t('message.Orders') }}</span>

            </a>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/suivie">
            <a class="nav-link" @click="hideMenu">
              <i class="mdi mdi-forum menu-icon"></i>
              <span class="menu-title">{{ $t('message.Track_This_Order') }}</span><span v-if="nbrColisDMsuivie>0" style="padding-right: 7px;padding-left: 7px;border-radius: 9px;background: red;color: white;">{{ nbrColisDMsuivie }}</span>

            </a>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/retour">
            <a class="nav-link" @click="hideMenu">
              <i class="mdi mdi-backup-restore menu-icon"></i>
              <span class="menu-title">{{ $t('message.Return_receipt') }}</span>

            </a>
          </router-link>
        </li>

        <li class="nav-item">
          <router-link to="/package">
            <a class="nav-link" @click="hideMenu">
              <i class="mdi mdi-printer-3d menu-icon"></i>
              <span class="menu-title">{{ $t('message.Package') }}</span>

            </a>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/invoices" v-if="Client.role == 'Client' && Client">
            <a class="nav-link" @click="hideMenu">
              <i class="mdi mdi-currency-usd menu-icon"></i>
              <span class="menu-title">{{ $t('message.Invoices') }}</span>

            </a>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/store" v-if="Client.role == 'Client' && Client">
            <a class="nav-link" @click="hideMenu">
              <i class="mdi mdi-store menu-icon"></i>
              <span class="menu-title">{{ $t('message.MultiStore') }}</span>

            </a>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/employe" v-if="Client.role == 'Client' && Client">
            <a class="nav-link" @click="hideMenu">
              <i class="mdi mdi-account menu-icon"></i>
              <span class="menu-title">{{ $t('message.Employees') }}</span>

            </a>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/statistique" v-if="Client.role == 'Client' && Client">
            <a class="nav-link" @click="hideMenu">
              <i class="mdi mdi-chart-line menu-icon"></i>
              <span class="menu-title">{{ $t('message.Statistics') }}</span>

            </a>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/reclamation" v-if="Client.role == 'Client' && Client">
            <a class="nav-link" @click="hideMenu">
              <i class="mdi mdi-information-outline menu-icon"></i>
              <span class="menu-title">{{ $t('message.Claim') }}</span>

            </a>
          </router-link>
        </li>



      </ul>
    </nav>
    <!-- partial -->

    <div class="main-panel" v-if="$route.name === 'dashboardClient' && Client.role == 'Client' && infoComplete">
      <main-client-component :Client="Client" v-if="Client">
      </main-client-component>
    </div>

    <div class="main-panel" v-if="$route.name === 'dashboardClient' && Client.role == 'EmployeClient'">
      <commande-client-component :Client="Client" v-if="Client">
      </commande-client-component>
    </div>

    <div class="main-panel" v-else-if="$route.name === 'commande' && (infoComplete || Client.role == 'EmployeClient')">
      <commande-client-component :Client="Client" v-if="Client">
      </commande-client-component>
    </div>

    <div class="main-panel" v-else-if="$route.name === 'package' && (infoComplete || Client.role == 'EmployeClient')">
      <package-client-component :Client="Client" v-if="Client">
      </package-client-component>
    </div>
    <div class="main-panel" v-else-if="$route.name === 'article' && infoComplete">
      <article-client-component :Client="Client" v-if="Client && Client.stock == 1">
      </article-client-component>
    </div>
    <div class="main-panel" v-else-if="$route.name === 'invoices' && Client.role == 'Client' && infoComplete">
      <invoices-client-component :Client="Client" v-if="Client">
      </invoices-client-component>
    </div>
    <div class="main-panel" v-else-if="$route.name === 'profile' && (infoComplete || Client.role == 'EmployeClient')">
      <profile-client-component :Client="Client" v-if="Client">
      </profile-client-component>
    </div>
    <div class="main-panel" v-else-if="$route.name === 'reclamation' && Client.role == 'Client' && infoComplete">
      <reclamation-client-component :Client="Client" v-if="Client">
      </reclamation-client-component>
    </div>
    <div class="main-panel" v-else-if="$route.name === 'store' && Client.role == 'Client' && infoComplete">
      <store-client-component :Client="Client" v-if="Client">
      </store-client-component>
    </div>
    <div class="main-panel" v-else-if="$route.name === 'employe' && Client.role == 'Client' && infoComplete">
      <employe-client-component :Client="Client" v-if="Client">
      </employe-client-component>
    </div>
    <div class="main-panel" v-else-if="$route.name === 'suivie' && (infoComplete || Client.role == 'EmployeClient')">
      <suivie-client-component :Client="Client" v-if="Client">
      </suivie-client-component>
    </div>
    <div class="main-panel" v-else-if="$route.name === 'retour' && (infoComplete || Client.role == 'EmployeClient')">
      <retour-client-component :Client="Client" v-if="Client">
      </retour-client-component>
    </div>
    <div class="main-panel" v-else-if="$route.name === 'statistique' && infoComplete">
      <statistique-client-component :Client="Client" v-if="Client && infoComplete">
      </statistique-client-component>
    </div>
    <div class="main-panel" v-else-if="!infoComplete && Client.role == 'Client'">
      <profile-client-component :Client="Client" v-if="Client">
      </profile-client-component>
    </div>



    <!-- main-panel ends -->
  </div>
</template>
  
<script>
// import NavbarAdminComponent from '../Admin/NavbarAdminComponent.vue'

import { onBeforeMount } from 'vue';

export default {
  props: ['Client'],
  name: "SidebarClientComponent",
  data() {
    return {
      currentUser: {},
      token: localStorage.getItem('token'),
      infoComplete: false,
      nbrColisDMsuivie:'',
    }
  },
  methods: {

    hideMenu() {
      var active = document.querySelector(".sidebar-offcanvas");
      active.classList.remove("active");
      document.getElementById("sidebar").style.removeProperty('left');
            document.getElementById("sidebar").style.removeProperty('right');
    },
  },
  mounted(){

  },
  async beforeMount() {
    axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
    if (this.Client.nom != null && this.Client.prenom != null && this.Client.cin != null && this.Client.ribBank != null && this.Client.company != null && this.Client.website != null && this.Client.adresse != null) {
      this.infoComplete = true;
    }
    await axios.get('/api/getCountDMsuivie')
                .then(res => { this.nbrColisDMsuivie = res.data.data['nbrColis']; })
                .catch(error => console.log(res));
 

  },


}
</script>
  