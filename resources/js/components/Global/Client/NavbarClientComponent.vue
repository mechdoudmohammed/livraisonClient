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
        @click.prevent="changeToRight" data-toggle="offcanvas">
        <span class="mdi mdi-menu"></span>
      </button>
      <ul class="navbar-nav navbar-nav-right">

        <li class="nav-item d-none d-lg-block full-screen-link">
          <a class="nav-link">
            <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
          </a>
        </li>
        <!-- <li class="nav-item dropdown">
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
        </li> -->
        <li class="nav-item dropdown">
          <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
            data-bs-toggle="dropdown">
            <i class="mdi mdi-bell-outline"></i>
            <span class="count-symbol bg-danger" v-if="Object.keys(notifications).length > 0"></span>
          </a>


          <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
            aria-labelledby="notificationDropdown">
            <h6 class="p-3 mb-0" v-if="locale == 'ar'" style="float: right;">{{ $t('message.Notifications') }}</h6>
            <h6 class="p-3 mb-0" v-else>{{ $t('message.Notifications') }}</h6>


            <a class="dropdown-item preview-item"
              @click.prevent="getCommandeSuivie(notification.id_commande, notification.id)"
              v-for="notification in notifications">
              <div class="preview-thumbnail">
                <div class="preview-icon bg-warning">
                  <i class="mdi mdi-calendar"></i>
                </div>
              </div>

              <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                <h6 class="preview-subject font-weight-normal mb-1"> {{ notification.titre }} </h6>

                <p class="text-gray ellipsis mb-0"> {{ notification.description }} </p>
                <span><time-ago :datetime="notification.updated_at" long :locale="locale"></time-ago></span>
              </div>
            </a>

            <div class="dropdown-divider"></div>
            <!-- <h6 class="p-3 mb-0 text-center">Afficher toutes les notifications</h6> -->
          </div>

        </li>
        <li class="nav-item nav-logout d-none d-lg-block">
          <a class="nav-link">
            <i class="mdi mdi-power" @click="logout"></i>
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
                <i class="mdi mdi-cached me-2 text-success"></i> {{$t('message.Profile')}} </a>
            </router-link>
            <div class="dropdown-divider"></div>

            <a class="dropdown-item" @click="logout">
              <i class="mdi mdi-logout me-2 text-primary"></i> {{$t('message.Signout')}} </a>
          </div>
        </li>
        <li class="nav-item dropdown" ref="myElement">
          <div class="lang-menu">
            <div class="selected-lang" v-if="locale == 'fr'" @click="displayDropdownLang">
              <img src="https://img.icons8.com/color/32/france.png" style="margin-right: 4px; width: 23px;">French
            </div>
            <div class="selected-lang" v-if="locale == 'en'" @click="displayDropdownLang">
              <img src="https://img.icons8.com/color/32/great-britain.png" style="margin-right: 4px;width: 23px;">English
            </div>
            <div class="selected-lang" v-if="locale == 'ar'" @click="displayDropdownLang">
              <img src="https://img.icons8.com/color/32/morocco.png" style="margin-right: 4px;width: 23px;">العربية
            </div>
            <ul id="dropdownLang">
              <li>
                <a @click.prevent="changeLanguage('en')" class="en">English</a>
              </li>
              <li>
                <a @click.prevent="changeLanguage('fr')" class="fr">French</a>
              </li>
              <li>
                <a @click.prevent="changeLanguage('ar')" class="ar">العربية</a>
              </li>
            </ul>

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
      locale: localStorage.getItem('locale'),
      notifications: [],
    }
  },
  methods: {
    displayDropdownLang() {
      document.addEventListener('click', this.handleClickOutside);
      document.getElementById('dropdownLang').style.display = 'block'
    },
    async changeLanguage(lang) {
      await axios.get('api/changeLanguage/' + lang).then((res) => {
        if (res.data.message == 'Language changed successfully') {
          this.$vs.notify({
            title: this.$t('message.Successfully'),
            color: "success",
            position: "top-right",
          });
          localStorage.setItem('locale', lang);
      location.reload();
        } else {
          this.$vs.notify({
            title: this.$t('message.error'),
            color: "danger",
            position: "top-right",
          });
        }

      }).catch((errors) => {
        console.log(errors)
      })

 
    },
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
    changeToRight() {
      if (localStorage.getItem('locale') == 'ar') {

        if (window.innerWidth <= 991) {
          if (document.getElementById("sidebar").style.right == "0px") {
            document.getElementById("sidebar").style.removeProperty('left');
            document.getElementById("sidebar").style.removeProperty('right');
          } else {
            document.getElementById("sidebar").style.left = "inherit";
            document.getElementById("sidebar").style.right = "0";
          }


        }

      }
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
    },
    async handleClickOutside(event) {
    
      if (!this.$refs.myElement.contains(event.target)) {
        document.getElementById('dropdownLang').style.display = 'none';
      }
    }






  },
  beforeMount() {
    this.getNotification();

  },
  beforeDestroy() {
    document.removeEventListener('click', this.handleClickOutside);
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
<style scoped>
.lang-menu {
  font-weight: bold;
  display: flex;
}

.lang-menu .selected-lang {
  display: flex;
  cursor: pointer;
  align-items: center;
}

.lang-menu .selected-lang:before {
  display: inline-block;
  width: 32px;
  height: 32px;
  background-size: contain;
  background-repeat: no-repeat;
}

.lang-menu ul {
  margin-top: 23px;
  padding: 3px;
  display: none;
  background-color: #fff;
  border: 1px solid #f8f8f8;
  width: 139px;
  border-radius: 5px;
  box-shadow: 0px 1px 10px rgba(0, 0, 0, 0.2);
  position: absolute;
}


.lang-menu ul li {
  list-style: none;

  display: flex;
  justify-content: space-between;
}

.lang-menu ul li a {
  text-decoration: none;
  width: 125px;
  padding: 5px 10px;
  display: block;
}

.lang-menu ul li:hover {
  background-color: #f2f2f2;
}

.lang-menu ul li a:before {
  content: '';
  display: inline-block;
  width: 25px;
  height: 25px;
  vertical-align: middle;
  margin-right: 10px;
  background-size: contain;
  background-repeat: no-repeat;

}



.en:before {
  background-image: url(https://img.icons8.com/color/32/great-britain.png);
}

.fr:before {
  background-image: url(https://img.icons8.com/color/32/france.png);
}

.ar:before {
  background-image: url(https://img.icons8.com/color/32/morocco.png);
}
</style>
