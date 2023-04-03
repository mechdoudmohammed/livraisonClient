<template>
    <div class="content-wrapper-customise">
        <div class="page-header-dashboard">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span>   {{$t('message.Employee_list')}}
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="btn-top-package">
                            <button type="button" style="margin: 0 13px;" class="btn btn-primary float-end"
                              @click.prevent="checkEmploye(false)"><i
                                    class="fa fa-plus" aria-hidden="true"></i>   {{$t('message.Add_Staff')}}</button>
                        </div>
               
                        <vs-table stripe :data="employes.data">
                            <template slot="thead">
                                <vs-th>
                                    {{$t('message.Id')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Name')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Phone_Number')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Username')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Zone')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Stock')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Status')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Operation')}}
                                </vs-th>
                            </template>
                            <template slot-scope="{data}">
                                <vs-tr :data='tr' :key="indextr" v-for="(tr, indextr) in data">
                                    <vs-td :data="tr.id">
                                        {{ tr.id }}
                                    </vs-td>
                                    <vs-td :data="tr.nom">
                                        {{ tr.nom }} {{ tr.prenom }}
                                    </vs-td>
                                    <vs-td :data="tr.telephone">
                                        {{ tr.telephone }}
                                    </vs-td>
                                    <vs-td :data="tr.username">
                                        {{ tr.username }}
                                    </vs-td>
                                    <vs-td :data="tr.nom_zone">
                                        {{ tr.nom_zone }}
                                    </vs-td>
                                    
                                    <vs-td :data="tr.stock">
                                       <span class="badge badge badge-gradient-info" v-if="tr.stock==1">{{$t('message.Yes')}}</span>
                                        <span class="badge badge badge-gradient-info" v-if="tr.stock==0">{{$t('message.No')}}</span>
                                    </vs-td>
                                    <vs-td :data="tr.statut">
                                        <b class="badge badge badge-gradient-danger" v-if="tr.statut == 'Inactive'">{{
                                                tr.statut
                                        }}</b>
                                        <b class="badge badge badge-gradient-success" v-if="tr.statut == 'Active'">{{
                                                tr.statut
                                        }}</b>
                                    </vs-td>
                                    <vs-td>
                                        <button type="button" class="btn btn-valide" v-if="tr.statut == 'Inactive'"
                                            @click.prevent="BlockEmployeClient(tr.id)"><i class="fa fa-check"></i></button>
                                        <button type="button" class="btn btn-warning" @click.prevent="checkEmploye(true,tr.id)"><i
                                                class="fa fa-edit"></i></button>
                                        <button type="button" class="btn btn-danger" @click.prevent="BlockEmployeClient(tr.id)"
                                            v-if="tr.statut == 'Active'"><i class="fa fa-ban"></i></button>
                                    </vs-td>


                                </vs-tr>
                            </template>
                        </vs-table>
                        <vs-pagination @input="getEmployes(0)" :max="9" :total="employes.last_page"
                            v-model="employes.current_page" prev-icon="arrow_back"
                            next-icon="arrow_forward"></vs-pagination>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ajouterEmploye" tabindex="-1" aria-labelledby="ajouterEmploye" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ajouterEmploye" v-if="!edit">{{$t('message.Add_Staff')}}</h5>
                        <h5 class="modal-title" id="ajouterEmploye" v-if="edit">{{$t('message.Edit_Employee')}}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="alert alert-danger" role="alert" v-if="nom_err">
                                {{ nom_err[0] }}
                            </div>
                            <label for="id_ville">{{$t('message.City')}}<span class="text-danger"> *</span></label>
                            <span class="text-danger"></span>
                            <v-select :placeholder="$t('message.Please_Select_City')" v-model="ville"
                                name="id_ville" :options="villes" label="ville" index="id" />
                        </div>

                        <div class="form-group">
                            <label for="prenom">{{$t('message.Last_Name')}} <span class="text-danger"> *</span></label>
                            <span class="text-danger"></span>
                            <input type="text" class="form-control" id="nom" v-model="formData.nom" name="nom">
                        </div>
                        <div class="form-group">
                            <label for="prenom">{{$t('message.First_Name')}} <span class="text-danger"> *</span> </label>
                            <span class="text-danger"></span>
                            <input type="text" class="form-control" id="prenom" v-model="formData.prenom" name="prenom">
                        </div>
                        <div class="form-group">
                            <label for="telephone">{{$t('message.Phone_Number')}} <span class="text-danger"> *</span></label>
                            <span class="text-danger"></span>
                            <input type="text" class="form-control" id="telephone" v-model="formData.telephone"
                                name="telephone">
                        </div>
                        <div class="form-group">
                            <label for="email">{{$t('message.Email')}}<span class="text-danger"> *</span></label>
                            <span class="text-danger"></span>
                            <input type="text" class="form-control" id="email" v-model="formData.email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="cin">{{$t('message.CIN')}} <span class="text-danger"> *</span></label>
                            <span class="text-danger"></span>
                            <input type="text" class="form-control" id="cin" v-model="formData.cin" name="cin">
                        </div>
                        <div class="form-group">
                            <label for="ribBank">{{$t('message.RibBank')}}</label>
                            <span class="text-danger"></span>
                            <input type="text" class="form-control" id="ribBank" v-model="formData.ribBank"
                                name="ribBank">
                        </div>
                        <div class="form-group">
                            <label for="id_bank">{{$t('message.BankName')}}</label>
                            <span class="text-danger"></span>
                            <select class="form-control" id="id_bank" v-model="formData.id_bank">
                                <option name="id_bank" :value="bank.id_bank" v-for="bank in banks">{{ bank.nomBank }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="username">{{$t('message.Username')}}<span class="text-danger"> *</span></label>
                            <span class="text-danger"></span>
                            <input type="text" class="form-control" id="username" v-model="formData.username"
                                name="username">
                        </div>
                        <div class="form-group">
                            <label for="password">{{$t('message.Password')}} <span class="text-danger"> *</span></label>
                            <span class="text-danger"></span>
                            <input type="password" class="form-control" id="password" v-model="formData.password"
                                name="password">
                        </div>
                
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" v-if="!edit" @click.prevent="addEmploye()">{{$t('message.Add_Staff')}}</button>
                        <button type="button" class="btn btn-primary" v-if="edit"
                            @click.prevent="updateEmploye(formData.selected_client)">{{$t('message.Edit_Employee')}}</button>
                        <button type="button" id="btn_cancel" class="btn btn-secondary"
                            data-bs-dismiss="modal"><i class="fas fa-sign-out-alt"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import { TimeAgo } from 'vue2-timeago'
import 'vue2-timeago/dist/vue2-timeago.css'
export default {
    props: ['Client'],
    name: 'EmployeClientComponent',
    components: { TimeAgo, },
    data() {
        return {
            token: localStorage.getItem('token'),
            nom_err: '',
            errors: {},
            selected: [],
            villes: [],
          
            employes: {'data': '',
                'current_page': 1,
                'last_page': 1},
            message: {},
            edit:false,
            formData: {
                nom: '',
                prenom: '',
                telephone: '',
                email: '',
                cin: '',
                ribBank: '',
                bankName: '',
                statut: '',
                username: '',
                password: '',
                id_ville:'',
                selected_employe:'',
            },
            banks: '',
            ville:'',
        }
    },
    methods: {
        
        async getBanks() {
            await axios.get('/api/getBank')
                .then(res => { this.banks = res.data.data; })
                .catch(error => console.log(res));
        },
        async getEmployes(count_nbr) {
            this.$vs.loading({ color: '#22c16b' })
            setTimeout(async () => {
                if (count_nbr > 1) {
                    await axios.post('/api/getEmployeClient?page=' + this.employes.current_page + '&count_nbr=' + count_nbr)
                        .then(res => { this.employes = res.data.data; })
                        .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
                } else {
                    await axios.post('/api/getEmployeClient?page=' + this.employes.current_page + '&count_nbr=20')
                        .then(res => { this.employes = res.data.data; })
                        .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
                }
            }, 200);

        },
        async addEmploye() {
            console.log(this.formData)
            this.formData.id_ville = this.ville['id'];
            await axios.post('/api/EmployeClient', this.formData).then((res) => {
                if(res.data.message=='Employe Created successfully'){
                      this.$vs.notify({
                        title: this.$t('message.Add_Employee'),
                        color: 'success', position: 'top-right',
                        time: 4000
                    })
                    this.initialiserFormData();
                this.getEmployes();
                $("#ajouterEmploye").modal('hide')
                }
              
            }).catch(error => {
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                    this.nom_err = this.errors[Object.keys(this.errors)[0]];
                }
            })
        },
        async getVilles() {
            await axios.get('/api/Villes')
                .then(res => { this.villes = res.data.data; })
                .catch(error => console.log(res));
        },
        async getEmploye(id) {
            await axios.get('/api/EmployeClient/'+id)
                .then(res => { this.formData = res.data.data; })
                .catch(error => console.log(res));
                this.ville = {
                id: this.formData.ville,
                ville: this.formData.nom_ville,
            };
        },
        async checkEmploye(btn_val, id_employe) {
            this.initialiserFormData();
            await this.getVilles();
            this.edit = btn_val;
            if (btn_val) {
                this.getEmploye(id_employe);
                this.formData.selected_employe = id_employe;
              
            }
            $("#ajouterEmploye").modal('show')
        },
        async BlockEmployeClient(id) {
            await axios.get('/api/BlockEmployeClient/'+id)
                .then(res => {})
                .catch(error => console.log(res));
                this.getEmployes(0);
        },
        initialiserFormData() {
            this.formData= {
                nom: '',
                prenom: '',
                telephone: '',
                email: '',
                cin: '',
                ribBank: '',
                bankName: '',
                statut: '',
                username: '',
                password: '',
                id_ville:'',
                selected_employe:'',
            },
            this.nom_err= '',
            this.ville=''
        }
    },
    async beforeMount() {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
this.getBanks();

    },
    mounted() {

    },

}
</script>
