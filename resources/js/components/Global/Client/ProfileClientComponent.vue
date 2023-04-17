<template>
  <div class="content-wrapper-customise">


    <div class="row">
      <div class="container-fluid mt--7">
        <div class="row">
          <div class="col-xl-4 order-xl-2 mb-5 mb-xl-0">
            <div class="card card-profile shadow">
              <div class="row justify-content-center">
                <div class="col-lg-3 order-lg-2">
                  <div class="card-profile-image">
                    <img src="/images/profile/profile.jpg" class="rounded-circle">
                  </div>
                </div>
              </div>
              <div class="card-body pt-0 pt-md-4">

                <div class="text-center">
                  <h3>
                    {{ Client.nom }} {{ Client.prenom }}
                  </h3>
                  <div class="h5 font-weight-300">
                    <i class="ni location_pin mr-2"></i>{{ Client.username }}
                  </div>
                  <div class="alert alert-danger" role="alert" v-if="nom_err_pass">
                    {{ nom_err_pass[0] }}
                  </div>
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group focused">
                        <label class="form-control-label" for="input-first-name">{{$t('message.Old_Password')}}</label>
                        <input type="password" class="form-control form-control-alternative"
                          v-model="formData.old_password">
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group focused">
                        <label class="form-control-label" for="input-last-name">{{$t('message.New_Password')}}</label>
                        <input type="password" class="form-control form-control-alternative"
                          v-model="formData.new_password">
                      </div>
                    </div>
                  </div>
                  <div class="row btn_modifier">

                    <button type="button" class="btn btn-primary float-end" @click.prevent='updatePassword'><i
                        class="fa fa-check" aria-hidden="true"></i>{{$t('message.Update')}}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-8 order-xl-1">
            <div class="card bg-secondary shadow">
              <div class="card-header bg-white border-0">
                <div class="row align-items-center">
                  <div class="col-6">
                    <h3 class="mb-0">{{$t('message.My_account')}}</h3>
                  </div>
                  <div class="col-6 activer-stock" v-if="Client.stock == 0">
                    <vs-button color="success" type="filled" @click.prevent="demandeActiverStock">
                      <i class="fa fa-check" aria-hidden="true"></i> {{$t('message.Activer_Stock')}}</vs-button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <form>
                  
                  <vs-alert title="Les informations sont essentielles" active="true" color="danger"
                    v-if="showMsgInformation">
                    {{$t('message.Please_complete_your_information_before_using_the_system')}}
                  </vs-alert><br>



                  
                  <div class="alert alert-danger" role="alert" v-if="nom_err">
                    {{ nom_err[0] }}
                  </div>

                  <div class="pl-lg-4">
                    <div class="row">
                      <h6 class="heading-small text-muted mb-4 d-flex">{{$t('message.Whatsapp_Notification')}}
                         <vs-switch style="margin-left: 10px;" color="success" v-model="Client.notification_statut" /></h6>
                      
                      </div>
                  </div>
                   
                 
                  <h6 class="heading-small text-muted mb-4">{{$t('message.User_information')}}</h6>
                  <div class="pl-lg-4">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label class="form-control-label" for="input-email">{{$t('message.Email')}}<span class="text-danger"> *</span></label>
                          <input type="email" class="form-control form-control-alternative disableChamps" v-model="Client.email">
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group focused">
                          <label class="form-control-label" for="input-first-name">{{$t('message.Phone_Number')}}<span class="text-danger"> *</span></label>
                          <input type="text" class="form-control form-control-alternative" v-model="Client.telephone">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group focused">
                          <label class="form-control-label" for="input-first-name">{{$t('message.Last_name')}}<span class="text-danger"> *</span></label>
                          <input type="text" class="form-control form-control-alternative disableChamps"
                            v-model="Client.nom">
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group focused">
                          <label class="form-control-label" for="input-last-name">{{$t('message.First_name')}}<span class="text-danger"> *</span></label>
                          <input type="text" class="form-control form-control-alternative disableChamps"
                            v-model="Client.prenom">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label class="form-control-label" for="input-email">{{$t('message.CIN')}}<span class="text-danger"> *</span></label>
                          <input type="text" class="form-control form-control-alternative disableChamps"
                            v-model="Client.cin">
                        </div>
                      </div>

                    </div>


                  </div>
                  <hr class="my-4">
                  <!-- Address -->
                  <h6 class="heading-small text-muted mb-4">{{$t('message.Bank_Information')}}</h6>
                  <div class="pl-lg-4">
                    <div class="row">
                      <div class="col-md-8">
                        <div class="form-group focused">
                          <label class="form-control-label" for="input-address">{{$t('message.RibBank')}}<span class="text-danger"> *</span></label>
                          <input id="input-address" class="form-control form-control-alternative" type="text"
                            v-model="Client.ribBank">
                        </div>
                      </div>
                      <div class="col-lg-4" v-if="Client.role == 'Client'">
                        <div class="form-group focused">
                          <label class="form-control-label" for="input-city">{{$t('message.ICE')}}</label>
                          <input type="text" id="input-city" class="form-control form-control-alternative">
                        </div>
                      </div>
                    </div>
                    <div class="row">

                      <div class="col-lg-4" v-if="Client.role == 'Client'">
                        <div class="form-group">
                          <label for="id_bank">{{$t('message.BankName')}}<span class="text-danger"> *</span></label>
                          <span class="text-danger"></span>
                          <select class="form-control" id="id_bank" v-model="Client.id_bank">
                            <option name="id_bank" :value="bank.id_bank" v-for="bank in banks">{{ bank.nomBank }}
                            </option>
                          </select>
                        </div>
                      </div>
                    </div>


                  </div>
                  <hr class="my-4">
                  <!-- Address -->
                  <h6 class="heading-small text-muted mb-4" v-if="Client.role == 'Client'">{{$t('message.Company_Information')}}<span class="text-danger"> *</span></h6>
                  <div class="pl-lg-4" v-if="Client.role == 'Client'">
                    <div class="row">
                      <div class="col-lg-4">
                        <div class="form-group focused">
                          <label class="form-control-label" for="input-city2">{{$t('message.Company')}}<span class="text-danger"> *</span></label>
                          <input type="text" id="input-city2" class="form-control form-control-alternative"
                            v-model="Client.company">
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group focused">
                          <label class="form-control-label" for="input-country">{{$t('message.WebSite')}}<span class="text-danger"> *</span></label>
                          <input type="text" id="input-country" class="form-control form-control-alternative"
                            v-model="Client.website">
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label class="form-control-label" for="input-country">{{$t('message.Address')}}<span class="text-danger"> *</span></label>
                          <input type="text" id="input-postal-code" class="form-control form-control-alternative"
                            v-model="Client.adresse">
                        </div>
                      </div>
                    </div>
                  </div>



                </form>

                <div class="row btn_modifier">

                  <button type="button" class="btn btn-primary float-end" @click.prevent='updateProfile'><i
                      class="fa fa-check" aria-hidden="true"></i> {{$t('message.Update')}}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'ProfileClientComponent',
  props: ['Client'],
  data() {
    return {
      token: localStorage.getItem('token'),
      nom_err: '',
      nom_err_pass: '',
      showMsgInformation: true,
      errors: {},
      formData: {
        old_password: '',
        new_password: '',

      },
      banks: '',
      switch_notification:true,
    }
  },
  methods: {
    async updateProfile() {
      this.nom_err = '';
      await axios.post('/api/modifierProfile', this.Client)
        .then(res => {
          if (res.data.message == 'Client update successfully') {
            this.$vs.notify({
              time: 5000,
              title: `Votre information Modifier`,
              color: 'success', position: 'top-right',

            })
          }
          setTimeout(() => {
            location.reload();
          }, 2000);
        })
        .catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors || {};
            this.nom_err = this.errors[Object.keys(this.errors)[0]];
          }
        });
    },
    async updatePassword() {
      this.nom_err_pass = '';
      await axios.post('/api/updatePassword', this.formData)
        .then(res => {
          if (res.data.message == 'password update successfully') {
            this.$vs.notify({
              time: 5000,
              title: `Votre mot de pass Modifier`,
              color: 'success', position: 'top-right',

            })
            this.formData = {
              old_password: '',
              new_password: '',

            }
          } else if (res.data.message == 'old password incorrect') {
            this.$vs.notify({
              time: 5000,
              title: `Votre mot de pass est incorrect`,
              color: 'danger', position: 'top-right',

            })
          }
          else if (res.data.message == 'old password and new password can\'t be same') {
            this.$vs.notify({
              time: 5000,
              title: `old password and new password can\'t be same`,
              color: 'danger', position: 'top-right',

            })
          }
        })
        .catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors || {};
            this.nom_err_pass = this.errors[Object.keys(this.errors)[0]];
          }
        });
    },
    async getBanks() {
      await axios.get('/api/getBank')
        .then(res => { this.banks = res.data.data; })
        .catch(error => console.log(res));
    },
    async demandeActiverStock() {
      this.$vs.loading({ color: "#22c16b" });
      await axios.get('/api/demandeActiverStock')
        .then(res => {
          if (res.data.message == 'Demande envoyer successfully') {
            this.$vs.notify({
              time: 5000,
              title: `Demande envoyer successfully`,
              color: 'success', position: 'top-right',

            })
          } else if (res.data.message == 'Demande déja envoyé') {
            this.$vs.notify({
              time: 5000,
              title: `Demande déja envoyé`,
              color: 'warning', position: 'top-right',

            })
          }
        })
        .catch(error => { console.log(error) }).finally(() => this.$vs.loading.close());
    }

  },
  async beforeMount() {
    axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
    this.getBanks();
    if (this.Client.nom != null && this.Client.prenom != null & this.Client.cin != null) {
      this.showMsgInformation = false;
      var list;
      list = document.querySelectorAll(".disableChamps");
      for (var i = 0; i < list.length; ++i) {
        list[i].style.pointerEvents = 'none';
      }
    }
  },
  mounted() {
   
  },

}




</script>
<style scoped>
.card.card-profile.shadow {
  height: 97.5%;
}
.activer-stock {
  justify-content: flex-end;
  align-items: center;
  display: flex;
}

.row.btn_modifier {
  width: 40%;
  float: right;
  margin: 0px 19px;
}

hr {
  margin-top: 2rem;
  margin-bottom: 2rem;
  border: 0;
  border-top: 1px solid rgba(0, 0, 0, .1);
}

.card-profile-image {
  min-height: 144px;
}

.card {
  position: relative;
  display: flex;
  flex-direction: column;
  min-width: 0;
  word-wrap: break-word;
  border: 1px solid rgba(0, 0, 0, .05);
  border-radius: .375rem;
  background-color: #fff;
  background-clip: border-box;
  overflow: hidden;
}

.card>hr {
  margin-right: 0;
  margin-left: 0;
}

.card-header:first-child {
  border-radius: calc(.375rem - 1px) calc(.375rem - 1px) 0 0;
}

.progress {
  font-size: .75rem;
  display: flex;
  overflow: hidden;
  height: 1rem;
  border-radius: .375rem;
  background-color: #e9ecef;
  box-shadow: inset 0 .1rem .1rem rgba(0, 0, 0, .1);
}

.media {
  display: flex;
  align-items: flex-start;
}

.media-body {
  flex: 1 1;
}

.bg-secondary {
  background-color: #f7fafc !important;
}


.avatar-sm {
  font-size: .875rem;
  width: 36px;
  height: 36px;
}

.btn {
  font-size: .875rem;
  position: relative;
  transition: all .15s ease;
  letter-spacing: .025em;
  text-transform: none;
  will-change: transform;
}


.card-profile-image img {
  position: absolute;
  left: 50%;
  max-width: 110px;
  transition: all .15s ease;
  transform: translate(-50%, -30%);
  border-radius: .375rem;
  top: 11%;
  max-height: 110px;
  width: 110px;
  height: 110px;
}

.card-profile-image img:hover {
  transform: translate(-50%, -33%);
}

.card-profile-stats {
  padding: 1rem 0;
}

.card-profile-stats>div {
  margin-right: 1rem;
  padding: .875rem;
  text-align: center;
}

.card-profile-stats>div:last-child {
  margin-right: 0;
}

.card-profile-stats>div .heading {
  font-size: 1.1rem;
  font-weight: bold;
  display: block;
}

.card-profile-stats>div .description {
  font-size: .875rem;
  color: #adb5bd;
}

.main-content {
  position: relative;
}



.form-control-alternative {
  transition: box-shadow .15s ease;
  border: 0;
  box-shadow: 0 1px 3px rgba(50, 50, 93, .15), 0 1px 0 rgba(0, 0, 0, .02);
}

.form-control-alternative:focus {
  box-shadow: 0 4px 6px rgba(50, 50, 93, .11), 0 1px 3px rgba(0, 0, 0, .08);
}

.input-group {
  transition: all .15s ease;
  border-radius: .375rem;
  box-shadow: none;
}
</style>