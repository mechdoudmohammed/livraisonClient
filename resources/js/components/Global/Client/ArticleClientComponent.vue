<template>
    <div class="content-wrapper-customise">
        <div class="page-header-dashboard">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> List of Items
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="btn-top-package">
                            <vs-select class="selectExample" v-model="formDataCherche3.selected_option3"
                                @change="getArticles(formDataCherche3.selected_option3)">
                                <vs-select-item :key="index" :value="item.value" :text="item.text"
                                    v-for="item, index in options3" />
                            </vs-select>
                            <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal"
                                data-bs-target="#ajouterArticle" data-bs-whatever="@mdo"
                                @click="" v-if="Client.role == 'Client'">New Articles</button>
                        </div>
                        <vs-table stripe :data="articles.data">
                            <template slot="thead">
                                <vs-th>
                                    Id
                                </vs-th>
                                <vs-th>
                                    Name Article
                                </vs-th>
                                <vs-th>
                                    Commentaire
                                </vs-th>
                                <vs-th>
                                    Price
                                </vs-th>
                                <vs-th>
                                    Stock
                                </vs-th>
                                <vs-th>
                                    Statut
                                </vs-th>
                                <vs-th v-if="Client.role == 'Client'">
                                    Operation
                                </vs-th>
                            </template>
                            <template slot-scope="{data}">
                                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                    <vs-td :data="tr.id">
                                        {{ tr.id }}
                                    </vs-td>
                                    <vs-td :data="tr.nom_article">
                                        {{ tr.nom_article }}
                                    </vs-td>
                                    <vs-td :data="tr.commentaire">
                                        {{ tr.commentaire }}
                                    </vs-td>
                                    <vs-td :data="tr.prix_article">
                                        {{ tr.prix_article }} Dhs
                                    </vs-td>

                                    <vs-td :data="tr.stock_article">
                                        {{ tr.stock_article }}
                                    </vs-td>
                                    <vs-td :data="tr.etat_article">
                                        <b class="badge badge badge-gradient-secondary"
                                            v-if="tr.etat_article == 'En stock'">{{ tr.etat_article }}</b>
                                        <b class="badge badge badge-gradient-info"
                                            v-if="tr.etat_article == 'En traitement'">{{ tr.etat_article }}</b>
                                    </vs-td>
                                    <vs-td :data="tr.etat_article" v-if="Client.role == 'Client'">
                                        <button type="button" class="btn btn-valide"
                                            v-if="tr.etat_article == 'En stock'"><i class="fa fa-plus"></i></button>
                                        <button type="button" class="btn btn-danger"
                                            v-if="tr.etat_article == 'En traitement'"
                                            @click.prevent="downloadSticker(tr.id)"><i
                                                class="fa fa-download"></i></button>
                                 <button type="button" class="btn btn-success" 
                                                v-if="tr.etat_article == 'En stock'"
                                    @click="downloadHistoriqueArticle(tr.id)"><i class="fa fa-file-excel"></i></button>
                                    </vs-td>
                                </vs-tr>
                            </template>
                        </vs-table>
                        <vs-pagination @input="getArticles(0)" :max="9" :total="articles.last_page"
                            v-model="articles.current_page" prev-icon="arrow_back"
                            next-icon="arrow_forward"></vs-pagination>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ajouterArticle" tabindex="-1" aria-labelledby="ajouterArticle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ajouterArticle" v-if="!edit">Add a new item</h5>
                        <h5 class="modal-title" id="ajouterArticle" v-if="edit">Edit Article</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="alert alert-danger" role="alert" v-if="nom_err">
                                {{ nom_err[0] }}
                            </div>
                            <label for="nom_article">Item Name</label>
                            <input type="text" class="form-control" id="nom_article" v-model="formData.nom_article"
                                name="nom_article">
                        </div>
                        <div class="form-group">
                            <label for="nom_article">Commentaire</label>
                            <input type="text" class="form-control" id="nom_article" v-model="formData.commentaire"
                                name="commentaire">
                        </div>
                        <div class="form-group">
                            <label for="prix_article">Price</label>
                            <input type="number" class="form-control" id="prix_article" v-model="formData.prix_article"
                                name="prix_article">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" v-if="!edit"
                            @click.prevent="addArticle(false)">Add Item</button>
                        <button type="button" class="btn btn-primary" v-if="edit"
                            @click.prevent="updateArticle(formData.selected_article)">Edit Article</button>
                        <button type="button" id="btn_cancel" class="btn btn-secondary"
                            data-bs-dismiss="modal">Annuler</button>

                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>

import axios from 'axios'

export default {
    props: ['Client'],
    name: 'ArticleClientComponent',
    data() {
        return {
            token: localStorage.getItem('token'),
            edit: false,
            errors: {},
            articles: {
                data: "",
                current_page: 1,
                last_page: 1,
            },
            nom_err: '',
            formData: {
                nom_article: '',
                prix_article: '',
                commentaire: '',
            },
            options3: [
                { text: '1-20 items', value: '20' },
                { text: '1-50 items', value: '50' },
                { text: '1-150 items', value: '150' },

            ],
            formDataCherche3: {
                selected_option3: '20',

            },
        }
    },


    methods: {
        async downloadHistoriqueArticle(id){
            let formData = new FormData()
            formData.append('id', id)
            _.each(this.formData, (value, key) => { formData.append(key, value) })
                await axios.post('/api/downloadHistoriqueArticle', formData, { responseType: 'blob' })
                    .then(res => {
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(res.data);
                        link.download = id+'.xlsx';
                        document.body.append(link);
                        link.click();
                        link.remove();

                    })
                    .catch(error => console.log(res));



        },
        async downloadSticker(id) {
            let formData = new FormData()
            formData.append('id', id)
            _.each(this.formData, (value, key) => { formData.append(key, value) })
                await axios.post('/api/downloadStickerArticle', formData, { responseType: 'blob' })
                    .then(res => {
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(res.data);
                        link.download = id+'.pdf';
                        document.body.append(link);
                        link.click();
                        link.remove();

                    })
                    .catch(error => console.log(res));
        
        },
        async getArticles(count_nbr) {
  
            this.$vs.loading({ color: '#22c16b' })
            setTimeout(async () => {
                if (count_nbr > 1) {
                    await axios.get("/api/Article?page=" + this.articles.current_page + "&count_nbr=" + count_nbr)
                        .then(res => { this.articles = res.data.data; })
                        .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
                } else {
                    await axios.get("/api/Article?page=" + this.articles.current_page + "&count_nbr=20")
                        .then(res => { this.articles = res.data.data; })
                        .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
                }
            }, 200);

        },
        async getArticle(id) {
            await axios.get('/api/Article/' + id)
                .then(res => { this.formData = res.data.data })
                .catch(error => console.log(res));
        },

        async addArticle() {
            await axios.post('/api/Article', this.formData).then((res) => {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'La article a été enregistrée',
                    showConfirmButton: false,
                    timer: 4000
                })
                this.initialiserFormData();
                document.getElementById("btn_cancel").click()
            }).catch(error => {
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                    this.nom_err = this.errors[Object.keys(this.errors)[0]];
                }
            })
            await this.getArticles(0);
    


        },
        initialiserFormData() {
            this.nom_err = '',
                this.edit = false,
                this.errors = {},
                this.formData = {
                    nom_article: '',
                    prix_article: '',
                }
        }
    },
    async beforeMount() {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`


    },
    mounted() {

        $(".form-check label,.form-radio label").append('<i class="input-helper"></i>');


    },

}




</script>
