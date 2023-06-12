<template>
  <div class="page-body-wrapper" id="sidebarDiv">
    <!-- partial:partials/_sidebar.html -->
    <nav class="sidebar sidebar-offcanvas" id="sidebar">
      <ul class="nav">
        <li class="nav-item nav-profile">
          <router-link to="/profile">
            <a class="nav-link" @click="hideMenu">
              <div class="nav-profile-image">
                <img src="images/profile/profile.jpg" alt="profile">
                <span class="login-status online"></span>
                <!--change to offline or busy as needed-->
              </div>

              <div class="nav-profile-text d-flex flex-column">
                <span class="font-weight-bold mb-2">{{ Client.nom }} {{ Client.prenom }}</span>
                <span class="text-secondary text-small">{{ Client.company }}</span>
              </div>

              <div class="main-wrapper" v-if="pack">
                <div class="badge green" v-if="pack.pack_name == 'diamond'">
                  <div class="circle"> <i class="fa fa-gem"></i></div>
                  <div class="ribbon">Diamonde</div>
                </div>
                <div class="badge gold" v-else-if="pack.pack_name == 'gold'">
                  <div class="circle"> <i class="fa fa-rocket"></i></div>
                  <div class="ribbon">Gold</div>
                </div>
                <div class="badge silver" v-else-if="pack.pack_name == 'silver'">
                  <div class="circle"> <i class="fa fa-trophy"></i></div>
                  <div class="ribbon">Silver</div>
                </div>
                <div class="badge green" v-else>
                      <div class="circle"> <i class="fa fa-gem"></i></div>
                      <div class="ribbon">Personnalisé</div>
              </div>
             
               
              </div>
              <i class="mdi mdi-bookmark-check text-success nav-profile-badge" v-else></i>
 
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
              <span class="menu-title">{{ $t('message.Track_This_Order') }}</span><span v-if="nbrColisDMsuivie > 0"
                style="padding-right: 7px;padding-left: 7px;border-radius: 9px;background: red;color: white;">{{
                  nbrColisDMsuivie }}</span>

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
      nbrColisDMsuivie: '',
      pack: { pack_name: '' },
    }
  },
  methods: {

    hideMenu() {
      var active = document.querySelector(".sidebar-offcanvas");
      active.classList.remove("active");
      document.getElementById("sidebar").style.removeProperty('left');
      document.getElementById("sidebar").style.removeProperty('right');
    },
    async getMyPack() {
      await axios.get('/api/getMyPack')
        .then(res => { this.pack = res.data.data; })
        .catch(error => console.log(res));

    },
  },
  mounted() {

  },
  async beforeMount() {

    axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
    if (this.Client.nom != null && this.Client.prenom != null && this.Client.cin != null && this.Client.ribBank != null && this.Client.company != null && this.Client.adresse != null) {
      this.infoComplete = true;
      this.getMyPack();
    }
    await axios.get('/api/getCountDMsuivie')
      .then(res => { this.nbrColisDMsuivie = res.data.data['nbrColis']; })
      .catch(error => console.log(res));


  },


}
</script>
<style scoped>
.main-wrapper {
  margin: -41px 0px 0px -17px;
  text-align: end;
  scale: 0.2;
  height: 20px;
}

li.nav-item.nav-profile {
  max-height: 115px;
}

.badge {
  position: relative;
  margin: 1.5em 3em;
  width: 4em;
  height: 6.2em;
  border-radius: 10px;
  display: inline-block;
  top: 0;
  transition: all 0.2s ease;
}

.badge:before,
.badge:after {
  position: absolute;
  width: inherit;
  height: inherit;
  border-radius: inherit;
  background: inherit;
  content: "";
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  margin: auto;
}

.badge:before {
  transform: rotate(60deg);
}

.badge:after {
  transform: rotate(-60deg);
}

.badge:hover {
  top: -4px;
}

.badge .circle {
  width: 60px;
  height: 60px;
  position: absolute;
  background: #fff;
  z-index: 10;
  border-radius: 50%;
  top: 0;
  left: -2px;
  right: 0;
  bottom: 0;
  margin: auto;
}

.badge .circle i.fa {
  font-size: 2em;
  margin-top: 8px;
}

.badge .font {
  display: inline-block;
  margin-top: 1em;
}

.badge .ribbon {
  position: absolute;
  border-radius: 4px;
  padding: 5px 5px 4px;
  width: 100px;
  z-index: 11;
  color: #fff;
  bottom: 12px;
  left: 59%;
  margin-left: -55px;
  height: 25px;
  font-size: 14px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.27);
  text-shadow: 0 2px 2px rgba(0, 0, 0, 0.1);
  text-transform: uppercase;
  background: linear-gradient(to bottom right, #555 0%, #333 100%);
  cursor: default;
}

.yellow {
  background: linear-gradient(to bottom right, #ffeb3b 0%, #fbc02d 100%);
  color: #ffb300;
}

.orange {
  background: linear-gradient(to bottom right, #ffc107 0%, #f57c00 100%);
  color: #f68401;
}

.pink {
  background: linear-gradient(to bottom right, #F48FB1 0%, #d81b60 100%);
  color: #dc306f;
}

.red {
  background: linear-gradient(to bottom right, #f4511e 0%, #b71c1c 100%);
  color: #c62828;
}

.purple {
  background: linear-gradient(to bottom right, #ab47bc 0%, #4527a0 100%);
  color: #7127a8;
}

.teal {
  background: linear-gradient(to bottom right, #4DB6AC 0%, #00796B 100%);
  color: #34a297;
}

.blue {
  background: linear-gradient(to bottom right, #4FC3F7 0%, #2196F3 100%);
  color: #259af3;
}

.blue-dark {
  background: linear-gradient(to bottom right, #1976D2 0%, #283593 100%);
  color: #1c68c5;
}

.green {
  background: linear-gradient(to bottom right, #cddc39 0%, #8bc34a 100%);
  color: #7cb342;
}

.green-dark {
  background: linear-gradient(to bottom right, #4CAF50 0%, #1B5E20 100%);
  color: #00944a;
}

.silver {
  background: linear-gradient(to bottom right, #E0E0E0 0%, #BDBDBD 100%);
  color: #9e9e9e;
}

.gold {
  background: linear-gradient(to bottom right, #e6ce6a 0%, #b7892b 100%);
  color: #b7892b;
}
</style>