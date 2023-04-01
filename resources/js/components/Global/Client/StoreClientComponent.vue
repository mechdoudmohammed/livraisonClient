<template>
    <div class="content-wrapper-customise">

        <div class="page-header-dashboard">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> {{$t('message.Stores')}}
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                      
                        <div id="btn-top-package">
                            <button type="button" style="margin: 0 13px;" class="btn btn-primary float-end"
                                @click="checkStore(false)"><i class="fa fa-plus" aria-hidden="true"></i> {{$t('message.Add_Store')}}</button>
                        </div>
                        <vs-table stripe :data="stores.data">

                            <template slot="thead">
                                <vs-th>
                                    {{$t('message.Favorite')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Store_Id')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Name')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.City')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Address')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.WebSite')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Phone_Number')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Status')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Operation')}}
                                </vs-th>
                            </template>
                            <template slot-scope="{data}">
                                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                    <vs-td :data="tr.id">
                                        <i class="fas fa-star" v-if="tr.favorite_store == 0"
                                            @click.prevent=changeFavorite(tr.id)></i>
                                        <i class="fas fa-star favorite" v-else-if="tr.favorite_store == 1"></i>
                                    </vs-td>
                                    <vs-td :data="tr.id">
                                        {{ tr.id }}<br>
                                        <span><time-ago :datetime="tr.updated_at" long></time-ago> </span>
                                    </vs-td>
                                    <vs-td :data="tr.nom_store">
                                        {{ tr.nom_store }}
                                    </vs-td>
                                    <vs-td :data="tr.ville">
                                        {{ tr.ville }}
                                    </vs-td>
                                    <vs-td :data="tr.adresse_store">
                                        {{ tr.adresse_store }}

                                    </vs-td>
                                    <vs-td :data="tr.siteweb_store">
                                        {{ tr.siteweb_store }}

                                    </vs-td>
                                    <vs-td :data="tr.telephone_store">
                                        {{ tr.telephone_store }}
                                    </vs-td>
                                    <vs-td :data="tr.statut">
                                        <b class="badge badge badge-gradient-danger"
                                            v-if="tr.statut_store == 'Inactive'">{{ tr.statut_store }}</b>
                                        <b class="badge badge badge-gradient-success"
                                            v-if="tr.statut_store == 'Active'">{{ tr.statut_store }}</b>
                                    </vs-td>
                                    <vs-td>
                                        <button type="button" class="btn btn-valide"
                                            v-if="tr.statut_store == 'Inactive'" @click.prevent="BlockStore(tr.id)"><i
                                                class="fa fa-check"></i></button>
                                        <button type="button" class="btn btn-warning"
                                            @click.prevent="checkStore(true, tr.id)"><i class="fa fa-edit"></i></button>
                                        <button type="button" class="btn btn-danger" @click.prevent="BlockStore(tr.id)"
                                            v-if="tr.statut_store == 'Active'"><i class="fa fa-ban"></i></button>
                                    </vs-td>
                                </vs-tr>
                            </template>
                        </vs-table>
                        <vs-pagination @input="getStores(0)" :max="9" :total="stores.last_page"
                            v-model="stores.current_page" prev-icon="arrow_back"
                            next-icon="arrow_forward"></vs-pagination>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ajouterStore" tabindex="-1" aria-labelledby="ajouterStore" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ajouterStore" v-if="!edit">{{$t('message.Add_Store')}}
                        </h5>
                        <h5 class="modal-title" id="ajouterStore" v-if="edit">{{$t('message.Edit_Store')}}</h5>

                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="alert alert-danger" role="alert" v-if="nom_err">
                                {{ nom_err[0] }}
                            </div>
                            <label for="ville_client">{{$t('message.City')}}</label>

                            <v-select :placeholder="$t('message.Please_Select_City')" v-model="formData.ville"
                                name="ville" :options="villes" label="ville" index="id" />
                        </div>


                        <div class="form-group">

                            <label for="nom_store">{{$t('message.Store_Name')}}</label>
                            <input type="text" class="form-control" id="nom_store" v-model='formData.nom_store'
                                name="nom_store">
                        </div>
                        <div class="form-group">
                            <label for="siteweb_commande">{{$t('message.WebSite')}}</label>

                            <input type="text" class="form-control" id="siteweb_commande"
                                v-model='formData.siteweb_store' name="siteweb_commande">
                        </div>
                        <div class="form-group">
                            <label for="telephone_store">{{$t('message.Phone_Number')}}</label>

                            <input type="text" class="form-control" id="telephone_store"
                                v-model='formData.telephone_store' name="telephone_store">
                        </div>
                        <div class="form-group">
                            <label for="adresse_store">{{$t('message.Address')}}</label>

                            <input type="text" class="form-control" id="adresse_store" v-model='formData.adresse_store'
                                name="adresse_store">
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" v-if="!edit" @click.prevent="addStore()">Create</button>
                        <button type="button" class="btn btn-primary" v-if="edit"
                            @click.prevent="updateStore()">{{$t('message.Edit')}}</button>

                        <button type="button" id="btn_cancel" class="btn btn-secondary"
                            data-bs-dismiss="modal">Annuler</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.fa-star {
    font-size: 16px;
    color: #ffffff;
    background: #198ae3;
    padding: 5px;
    border-radius: 21px;
}

.fa-star:hover {
    font-size: 19px;
    color: #ffc107;
    padding: 6px;
    border-radius: 11px;
    background: transparent;
}

.favorite {
    font-size: 19px;
    color: #ffc107;
    padding: 6px;
    border-radius: 11px;
    background: transparent;
}
</style>
<script>
import axios from 'axios'
import { TimeAgo } from 'vue2-timeago'
import 'vue2-timeago/dist/vue2-timeago.css'
export default {
    props: ['Client'],
    name: 'StoreClientComponent',
    components: { TimeAgo, },
    data() {
        return {
            token: localStorage.getItem('token'),
            edit: false,
            errors: {},
            villes: [],
            stores: {
                'data': '',
                'current_page': 1,
                'last_page': 1
            },
            formData: {
                nom_store: '',
                siteweb_store: '',
                telephone_store: '',
                adresse_store: '',
                ville: '',
            },
            nom_err: '',

        }
    },


    methods: {
        async changeFavorite(id) {
            await axios.get('/api/changeFavorite/'+id)
                .then(res => {  })
                .catch(error => console.log(res));
                this.getStores(0)
        },
        async getVilles() {
            await axios.get('/api/Villes')
                .then(res => { this.villes = res.data.data; })
                .catch(error => console.log(res));
        },
        async getStores(count_nbr) {
            this.$vs.loading({ color: '#22c16b' })
            setTimeout(async () => {
                if (count_nbr > 1) {
                    await axios.post('/api/getStore?page=' + this.stores.current_page + '&count_nbr=' + count_nbr, this.formDataCherche)
                        .then(res => { this.stores = res.data.data; })
                        .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
                } else {
                    await axios.post('/api/getStore?page=' + this.stores.current_page + '&count_nbr=20', this.formDataCherche)
                        .then(res => { this.stores = res.data.data; })
                        .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
                }
            }, 200);

        },
        async getStore(id) {
            this.$vs.loading({ color: '#22c16b' })

            await axios.get('/api/Store/' + id)
                .then(res => { this.formData = res.data.data; })
                .catch(error => console.log(res)).finally(() => this.$vs.loading.close());

            this.formData.ville = { 'id': this.formData.id_ville, 'ville': this.formData.ville }


        },
        async BlockStore(id) {
            await axios.get('/api/BlockStore/' + id)
                .then(res => { })
                .catch(error => console.log(res));
            this.getStores(0);
        },
        async addStore() {
            await axios.post('/api/Store', this.formData).then((res) => {
                this.getStores();
                if (res.data.message == 'Store created successfully') {
                    this.$vs.notify({
                        time: 5000,
                        title: `Store created successfully`,
                        color: 'success',
                        position: 'top-right',
                    });
                    $("#ajouterStore").modal('hide')
                    this.initialiserFormData();
                } else {
                    this.$vs.notify({
                        time: 5000,
                        title: res.data.message,
                        color: 'danger',
                        position: 'top-right',
                    });
                }


            }).catch(error => {
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                    this.nom_err = this.errors[Object.keys(this.errors)[0]];
                }
            })
        },
        async showDetails(id) {
            await axios.get('/api/Store/' + id)

                .then(res => {
                    var text = "<table class='table table-borderless' style='text-align: left;'>" +
                        "<tr><td><b>Object:</b></td></tr><tr><td>" + res.data.data['object_store'];
                    text += "</td></tr><tr><td><b>Msg:</b></td></tr><tr><td>" + res.data.data['message_store'] + "</td></tr>"
                    if (res.data.data['id_commande'] != null) {
                        text += "<tr><td><b>Id Commande</b></td></tr><tr><td>" + res.data.data['id_commande']
                    }


                    Swal.fire({
                        title: 'Store N°: ' + res.data.data['id_store'],
                        html: text,
                        showCancelButton: false,
                    })


                })
                .catch(error => console.log(res));
        },
        async updateStore() {

            await axios.post('/api/updateStore', this.formData).then((res) => {
                if (res.data.message == 'Store modifier successfully') {
                    this.$vs.notify({
                        title: `Store Updated`,
                        text: res.data.message,
                        color: 'success', position: 'top-right',
                        time: 4000
                    })
                }
                this.initialiserFormData();
                $("#ajouterStore").modal('hide')
                this.getStores();
            }).catch(error => {
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                    this.nom_err = this.errors[Object.keys(this.errors)[0]];
                }
            })
        },
        initialiserFormData() {
            this.formData = {
                nom_store: '',
                siteweb_store: '',
                telephone_store: '',
                adresse_store: '',
                ville: '',
            },
                this.nom_err = ''
        },
        checkStore(btn_val, id_store) {
            this.initialiserFormData();
            this.edit = btn_val;
            if (btn_val) {
                this.getStore(id_store);
            }

            $("#ajouterStore").modal('show')

        }
    },
    async beforeMount() {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        this.getVilles()

    },
    mounted() {

    },

}
</script>
