<template>
  <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">

      <a class="navbar-brand brand-logo" href="/"><img src="/images/logo2.png" alt="logo" /></a>

    </div>
    <div class="navbar-menu-wrapper d-flex align-items-stretch">
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
        <span class="mdi mdi-menu"></span>
      </button>
      <!-- <div class="search-field d-none d-md-block">
        <form class="d-flex align-items-center h-100" action="#">
          <div class="input-group">
            <div class="input-group-prepend bg-transparent">
              <i class="input-group-text border-0 mdi mdi-magnify"></i>
            </div>
            <input type="text" class="form-control bg-transparent border-0" placeholder="Chercher ">
          </div>
        </form>
      </div> -->
      <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
        data-toggle="offcanvas">
        <span class="mdi mdi-menu"></span>
      </button>
      <ul class="navbar-nav navbar-nav-right">

        <li class="nav-item d-none d-lg-block full-screen-link">
          <a class="nav-link">
            <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#" data-bs-toggle="dropdown"
            aria-expanded="false">
            <i class="mdi mdi-email-outline"></i>
            <span class="count-symbol bg-warning"></span>
          </a>
          <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="messageDropdown">
            <h6 class="p-3 mb-0">Messages</h6>
            <div class="dropdown-divider"></div>
            <h6 class="p-3 mb-0 text-center">Afficher tous les messages</h6>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
            data-bs-toggle="dropdown">
            <i class="mdi mdi-bell-outline"></i>
            <span class="count-symbol bg-danger" v-if="Object.keys(notifications).length > 0"></span>
          </a>


          <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
            aria-labelledby="notificationDropdown">
            <h6 class="p-3 mb-0">Notifications</h6>
            <div class="dropdown-divider"></div>


            <a class="dropdown-item preview-item" v-if="notification.titre == 'Demande Suivie'"
              @click.prevent="getCommandeSuivie(notification.id_commande, notification.id)"
              v-for="notification in notifications">
              <div class="preview-thumbnail">
                <div class="preview-icon bg-warning">
                  <i class="mdi mdi-calendar"></i>
                </div>
              </div>

              <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                <h6 class="preview-subject font-weight-normal mb-1">{{ notification.titre }}</h6>
                <p class="text-gray ellipsis mb-0">{{ notification.description }}</p>
                <span><time-ago :datetime="notification.updated_at" long></time-ago></span>
              </div>
            </a>

            <div class="dropdown-divider"></div>
            <h6 class="p-3 mb-0 text-center">Afficher toutes les notifications</h6>
          </div>

        </li>
        <li class="nav-item nav-logout d-none d-lg-block">
          <a class="nav-link">
            <i class="mdi mdi-power" @click="logout"></i>
          </a>
        </li>

        <li class="nav-item nav-settings d-none d-lg-block">
          <a class="nav-link" href="#">
            <i class="mdi mdi-format-line-spacing"></i>
          </a>
        </li>

        <li class="nav-item nav-profile dropdown">
          <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-bs-toggle="dropdown"
            aria-expanded="false">
            <div class="nav-profile-img">
              <img src="images/profile/profile.jpg" alt="image">
              <span class="availability-status online"></span>
            </div>
            <div class="nav-profile-text">
              <p class="mb-1 text-black">{{ currentUser.nom }} {{ currentUser.prenom }}</p>
            </div>
          </a>
          <div id='profileDropdownplus' class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
            <router-link to="/profile">
              <a class="dropdown-item">
                <i class="mdi mdi-cached me-2 text-success"></i> Profile </a>
            </router-link>
            <div class="dropdown-divider"></div>

            <a class="dropdown-item" @click="logout">
              <i class="mdi mdi-logout me-2 text-primary"></i> Signout </a>
          </div>
        </li>
      </ul>

    </div>
  </nav>
</template>

<script>
import store from '../../../store';
import { TimeAgo } from "vue2-timeago";
import "vue2-timeago/dist/vue2-timeago.css";
export default {
  name: "NavbarClientComponent",
  components: { TimeAgo },
  data() {
    return {
      currentUser: {},
      token: localStorage.getItem('token'),
      notifications: [],
    }
  },
  methods: {
    async getNotification() {
      await axios.get('api/Notification').then((response) => {
        this.notifications = response.data
      }).catch((errors) => {
        console.log(errors)
      })
    },
    async getCommandeSuivie(id, id_notification) {
      if (this.$route.name != 'suivie') {
        await this.$router.push('suivie');

      }
      this.$root.$refs.SuivieClientComponent.getHistoriqueCommande(id);

      axios.delete('api/Notification/' + id_notification).then((response) => {
      }).catch((errors) => {
        console.log(errors)
      })
      this.getNotification();
    },

    async logout() {
      const config = {
        headers: {
          'Authorization': `Bearer ${this.token}`
        },
      };

      await axios.post('api/logoutClient', config).then((response) => {

        localStorage.removeItem('token');
        this.token = '';
        store.state.loginClient = false;
        this.$router.push('/login');
      }).catch((errors) => {
        console.log(errors)
      })
    }

  },
  beforeMount() {
    this.getNotification();

  },
  async mounted() {
    $(function () {
      $('[data-toggle="offcanvas"]').on("click", function () {
        $('.sidebar-offcanvas').toggleClass('active')
      });
    });

    $(function () {
      var body = $('body');
      $('[data-toggle="minimize"]').on("click", function () {
        if ((body.hasClass('sidebar-toggle-display')) || (body.hasClass('sidebar-absolute'))) {
          body.toggleClass('sidebar-hidden');
        } else {
          body.toggleClass('sidebar-icon-only');
        }
      });

    });
    $("#fullscreen-button").on("click", function toggleFullScreen() {
      if ((document.fullScreenElement !== undefined && document.fullScreenElement === null) || (document.msFullscreenElement !== undefined && document.msFullscreenElement === null) || (document.mozFullScreen !== undefined && !document.mozFullScreen) || (document.webkitIsFullScreen !== undefined && !document.webkitIsFullScreen)) {
        if (document.documentElement.requestFullScreen) {
          document.documentElement.requestFullScreen();
        } else if (document.documentElement.mozRequestFullScreen) {
          document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullScreen) {
          document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
        } else if (document.documentElement.msRequestFullscreen) {
          document.documentElement.msRequestFullscreen();
        }
      } else {
        if (document.cancelFullScreen) {
          document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
          document.webkitCancelFullScreen();
        } else if (document.msExitFullscreen) {
          document.msExitFullscreen();
        }
      }
    })
  }




}
</script>
