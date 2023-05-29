<template>
    <div class="waitVerification">
        <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">

    <div class="navbar-menu-wrapper d-flex align-items-stretch">

      <ul class="navbar-nav navbar-nav-right">
        <li class="nav-item nav-profile dropdown">
          <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="nav-profile-img">
              <img src="images/profile/profile.jpg" alt="image">
              <span class="availability-status online"></span>
            </div>

          </a>
          <div id='profileDropdownplus' class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
           
            <a class="dropdown-item" @click="logout">
              <i class="mdi mdi-logout me-2 text-primary" ></i> Signout </a>
          </div>
        </li>

      </ul>

    </div>
  </nav>
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <!-- LOGO -->
            <tr>
                <td bgcolor="#4791ff" align="center">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                        <tr>
                            <td align="center" valign="top" style="padding: 40px 10px 40px 10px;"> </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td bgcolor="#4791ff" align="center" style="padding: 0px 10px 0px 10px;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                        <tr>
                            <td bgcolor="#ffffff" align="center" valign="top"
                                style="padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
                                <h1 style="font-size: 48px; font-weight: 400; margin: 2;">Welcome!</h1> <img
                                    src=" https://img.icons8.com/clouds/100/000000/handshake.png" width="125"
                                    height="120" style="display: block; border: 0px;" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                        <tr>
                            <td bgcolor="#ffffff" align="left"
                                style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                <b style="margin: 0;">Veuillez-vous vérifier votre email pour valider votre compte.</b>
                                <p style="margin: 0;">Si vous n’avez pas reçu votre e-mail de vérification, veuillez vérifier votre spam.</p>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="#ffffff" align="left">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td bgcolor="#ffffff" align="center" style="padding: 20px 30px 60px 30px;">
                                            <table border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td align="center" style="border-radius: 3px;" bgcolor="#4791ff">
                                                        <a @click.prevent="resend"
                                                            style="font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #4791ff; display: inline-block;">Revoyer
                                                            le code</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr> <!-- COPY -->

                    </table>
                </td>
            </tr>
            <tr>
                <td bgcolor="#f4f4f4" align="center" style="padding: 30px 10px 0px 10px;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                        <tr>
                            <td bgcolor="#FFF" align="center"
                                style="padding: 30px 30px 30px 30px; border-radius: 4px 4px 4px 4px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                <h2 style="font-size: 20px; font-weight: 400; color: #111111; margin: 0;">Need more
                                    help?</h2>
                                <p style="margin: 0;"><a href="https://api.whatsapp.com/send?phone=+212609920389&text=Salam,J'ai un problem avec l'inscription" target="_blank" style="color: #4791ff;">We&rsquo;re
                                        here to help you out</a></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
    </div>
</template>
<style scoped>
.waitVerification {
    height: 100vh !important;
    background: #f4f4f4;
}
.navbar .navbar-menu-wrapper {
    transition: width 0.25s ease;
    -webkit-transition: width 0.25s ease;
    -moz-transition: width 0.25s ease;
    -ms-transition: width 0.25s ease;
    color: #9c9fa6;
    width: 100%;
    height: 70px;
}
</style>
<script>
import axios from 'axios'
import store from '../../../store';
export default {
    name: "Inscription",
    data() {
        return {
            currentUser: false,
            errors: {},
            nom_err: '',
            token: localStorage.getItem('token') || "",
        }
    },
    methods: {
        async resend() {
            this.nom_err = '',
                this.errors = {};
            await axios.post('api/email/verify/resend', this.currentUser).then((response) => {
                this.$vs.notify({
                    time: 4000,
                    title: `email envoyer`,
                    text: 'email envoyer',
                    color: 'success', position: 'top-right',

                });
            }).catch((error) => {
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                    this.nom_err = this.errors[Object.keys(this.errors)[0]];
                }
            })
        },
        async logout(){
          const config={
            headers:{
              'Authorization':`Bearer ${this.token}`
            },
          };

            await axios.post('api/logoutClient',config).then((response) => {
                localStorage.removeItem('token');
                this.token='';
                store.state.loginClient=false;
                this.$router.push('/');
            }).catch((errors) => {
                console.log(errors)
            })
        }

    },
    async mounted() {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        if (this.token != "") {
            await axios.get('api/client').then((response) => {
                this.currentUser = response.data;

            }).catch((errors) => {
                console.log(errors)
            })
        };
    }

}
</script>