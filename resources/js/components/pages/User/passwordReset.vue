
<template>
  <div class="main-container">
    <section class="ftco-section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 text-center mb-5">
            <a class="heading-section" href="/"><img src="/images/whiteLogo.png" alt="Company logo"
                style="width: 60%; max-width: 300px" /></a>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-md-12 col-lg-10">
            <div class="wrap d-md-flex">
              <div class="text-wrap p-4 p-lg-5 text-center d-flex align-items-center order-md-last">

              </div>
              <div class="login-wrap p-4 p-lg-5">


                <form action="#" class="signin-form">
                  <img src="images/shild.png" class="shildImg" />
                  <div class="form-group mb-3">
                    <label class="label" for="password">Password</label>
                    <input type="password" class="form-control" placeholder="Password" v-model="password">
                  </div>
                  <div class="form-group mb-3">
                    <label class="label" for="password">Confirmed Password</label>
                    <input type="password" class="form-control" placeholder="Password" v-model="passwordConfirmation">
                  </div>

                  <div class="form-group">
                    <button type="button" class="form-control btn btn-primary submit px-3"
                      @click.prevent="resetPassword()">Changer password</button>
                  </div>

                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>
  
<script>
export default {
  data() {
    return {
      email: '',
      password: '',
      token: '',
      passwordConfirmation: '',
      status: '',
      errorMessages: []
    };
  },
  methods: {
    resetPassword() {
      // Make an API request to your Laravel backend to reset password
      this.$vs.loading({ color: "#22c22b" })
      axios.post('/api/resetPassword', {
        email: this.$route.query.email,
        password: this.password,
        password_confirmation: this.passwordConfirmation,
        token: this.$route.query.token
      })
        .then(response => {
          this.$vs.notify({
            time: 5000,
            title: response.data.status,
            color: "success",
            position: "top-right",
          });
          setTimeout(() => {
            this.$router.push('/login');
          }, 2000);

        })
        .catch(error => {
          if (error.response.status === 422) {
            this.$vs.notify({
              time: 8000,
              title: error.response.data.errors[Object.keys(error.response.data.errors)[0]][0],
              color: "danger",
              position: "top-right",
            });
          }
          if (error.response.status === 500) {
            if (error.response.data.message == 'This password reset token is invalid.') {
              this.$vs.notify({
                time: 8000,
                title: 'Invalid link. Please attempt to reset your password.',
                color: "danger",
                position: "top-right",
              });
            }

          }


        }).finally(() => this.$vs.loading.close());
    }
  },
  created() {

  }
};
</script>
<style scoped>
.shildImg {
  min-width: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  max-width: 120px;
}

.main-container {
  height: auto;

  min-height: 100vh;
  background-image: url("../../../../../public/images/background.jpg") !important;
}

.alert-danger {
  color: #ffffff;
  background-color: #dc3545;
  border-color: #dc3545;
  width: 65%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto;
}

a {
  -webkit-transition: .3s all ease;
  -o-transition: .3s all ease;
  transition: .3s all ease;
  color: #198ae3;
}

a:hover,
a:focus {
  text-decoration: none !important;
  outline: none !important;
  -webkit-box-shadow: none;
  box-shadow: none;
}

h1,
h2,
h3,
h4,
h5,
.h1,
.h2,
.h3,
.h4,
.h5 {
  line-height: 1.5;
  font-weight: 400;
  font-family: "Lato", Arial, sans-serif;
  color: #000;
}

.bg-primary {
  background: #198ae3 !important;
}

.ftco-section {
  padding: 7em 0;
}

.ftco-no-pt {
  padding-top: 0;
}

.ftco-no-pb {
  padding-bottom: 0;
}

.heading-section {
  font-size: 28px;
  color: #000;
}

.img {
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center center;
}

.wrap {
  width: 100%;
  border-radius: 5px;
  -webkit-box-shadow: 0px 10px 34px -15px rgba(0, 0, 0, 0.24);
  -moz-box-shadow: 0px 10px 34px -15px rgba(0, 0, 0, 0.24);
  box-shadow: 0px 10px 34px -15px rgba(0, 0, 0, 0.24);
}

.text-wrap,
.login-wrap {
  width: 50%;
}

@media (max-width: 991.98px) {
  .login-wrap {
    width: 100%;
  }

  .text-wrap {
    display: none !important;
  }
}

.text-wrap {

  background-repeat: no-repeat;
  background-position: center;
  background-image: url("../../../../../public/images/reset.jpg");
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#4557c7', endColorstr='#198ae3', GradientType=1);
  color: #fff;
  background-size: 100%;
}

.text-wrap .text h2 {
  font-weight: 900;
  color: #fff;
}

.login-wrap {
  position: relative;
  background: #fff;
}

.login-wrap h3 {
  font-weight: 300;
}

.form-group {
  position: relative;
}

.form-group .label {
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #000;
  font-weight: 700;
}

.form-group a {
  color: gray;
}

.form-control {
  height: 48px;
  background: rgba(0, 0, 0, 0.05);
  color: #000;
  font-size: 16px;
  border-radius: 50px;
  -webkit-box-shadow: none;
  box-shadow: none;
  border: 1px solid transparent;
  padding-left: 20px;
  padding-right: 20px;
  -webkit-transition: all 0.2s ease-in-out;
  -o-transition: all 0.2s ease-in-out;
  transition: all 0.2s ease-in-out;
}

@media (prefers-reduced-motion: reduce) {
  .form-control {
    -webkit-transition: none;
    -o-transition: none;
    transition: none;
  }
}

.form-control::-webkit-input-placeholder {
  /* Chrome/Opera/Safari */
  color: rgba(0, 0, 0, 0.2) !important;
}

.form-control::-moz-placeholder {
  /* Firefox 19+ */
  color: rgba(0, 0, 0, 0.2) !important;
}

.form-control:-ms-input-placeholder {
  /* IE 10+ */
  color: rgba(0, 0, 0, 0.2) !important;
}

.form-control:-moz-placeholder {
  /* Firefox 18- */
  color: rgba(0, 0, 0, 0.2) !important;
}

.form-control:focus,
.form-control:active {
  outline: none !important;
  -webkit-box-shadow: none;
  box-shadow: none;
  background: rgba(0, 0, 0, 0.07);
  border-color: transparent;
}

.social-media {
  position: relative;
  width: 100%;
}

.social-media .social-icon {
  display: block;
  width: 40px;
  height: 40px;
  background: transparent;
  border: 1px solid rgba(0, 0, 0, 0.05);
  font-size: 16px;
  margin-right: 5px;
  border-radius: 50%;
}

.social-media .social-icon span {
  color: #999999;
}

.social-media .social-icon:hover,
.social-media .social-icon:focus {
  background: #198ae3;
}

.social-media .social-icon:hover span,
.social-media .social-icon:focus span {
  color: #fff;
}

.checkbox-wrap {
  display: block;
  position: relative;
  padding-left: 30px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 500;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.checkbox-wrap input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "\f0c8";
  font-family: "FontAwesome";
  position: absolute;
  color: rgba(0, 0, 0, 0.1);
  font-size: 20px;
  margin-top: -4px;
  -webkit-transition: 0.3s;
  -o-transition: 0.3s;
  transition: 0.3s;
}

@media (prefers-reduced-motion: reduce) {
  .checkmark:after {
    -webkit-transition: none;
    -o-transition: none;
    transition: none;
  }
}

/* Show the checkmark when checked */
.checkbox-wrap input:checked~.checkmark:after {
  display: block;
  content: "\f14a";
  font-family: "FontAwesome";
  color: rgba(0, 0, 0, 0.2);
}

/* Style the checkmark/indicator */
.checkbox-primary {
  color: #198ae3;
}

.checkbox-primary input:checked~.checkmark:after {
  color: #198ae3;
}

.btn {
  cursor: pointer;
  -webkit-box-shadow: none !important;
  box-shadow: none !important;
  font-size: 15px;
  padding: 10px 20px;
  border-radius: 50px;
}

.btn:hover,
.btn:active,
.btn:focus {
  outline: none;
}

.btn.btn-primary {
  background: #198ae3;
  border: 1px solid #198ae3;
  color: #fff;
  background: #4557c7;
  background: -moz-linear-gradient(-45deg, #4557c7 0%, #198ae3 100%);
  background: -webkit-gradient(left top, right bottom, color-stop(0%, #4557c7), color-stop(100%, #198ae3));
  background: -webkit-linear-gradient(-45deg, #4557c7 0%, #198ae3 100%);
  background: -o-linear-gradient(-45deg, #4557c7 0%, #198ae3 100%);
  background: -ms-linear-gradient(-45deg, #4557c7 0%, #198ae3 100%);
  background: -webkit-linear-gradient(315deg, #4557c7 0%, #198ae3 100%);
  background: -o-linear-gradient(315deg, #4557c7 0%, #198ae3 100%);
  background: linear-gradient(135deg, #4557c7 0%, #198ae3 100%);
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#4557c7', endColorstr='#198ae3', GradientType=1);
}

.btn.btn-primary:hover {
  border: 1px solid #198ae3;
  background: #198ae3;
  color: #fff;
}

.btn.btn-primary.btn-outline-primary {
  border: 1px solid #198ae3;
  background: transparent;
  color: #198ae3;
}

.btn.btn-primary.btn-outline-primary:hover {
  border: 1px solid transparent;
  background: #198ae3;
  color: #fff;
}

.btn.btn-white {
  background: #fff;
  border: 1px solid #fff;
  color: #fff;
}

.btn.btn-white:hover {
  border: 1px solid #fff;
  background: transparent;
  color: #fff;
}

.btn.btn-white.btn-outline-white {
  border: 1px solid #fff;
  background: transparent;
  color: #fff;
}

.btn.btn-white.btn-outline-white:hover {
  border: 1px solid transparent;
  background: #fff;
  color: #000;
}
</style>