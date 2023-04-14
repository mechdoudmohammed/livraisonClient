<template>
    <div class="content-wrapper-customise">
        <div class="page-header-dashboard">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> {{$t('message.Articles')}}
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
                                @click="" v-if="Client.role == 'Client'"> {{$t('message.Add_Article')}}</button>
                        </div>
                        <vs-table stripe :data="articles.data">
                            <template slot="thead">
                                <vs-th>
                                    {{$t('message.Id')}}
                                </vs-th>
                                <vs-th>
                                 {{$t('message.Article_Name')}}
                                </vs-th>
                                <vs-th>
                                  {{$t('message.Comment')}}
                                </vs-th>
                                <vs-th>
                                 {{$t('message.Price')}}
                                </vs-th>
                                <vs-th>
                                  {{$t('message.Stock')}}
                                </vs-th>
                                <vs-th>
                                  {{$t('message.Status')}}
                                </vs-th>
                                <vs-th v-if="Client.role == 'Client'">
                                  {{$t('message.Operation')}}
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
                                        {{ tr.prix_article }} {{$t('message.Dhs')}}
                                    </vs-td>

                                    <vs-td :data="tr.stock_article">
                                        {{ tr.stock_article }}
                                    </vs-td>
                                    <vs-td :data="tr.etat_article">
                                        <b class="badge badge badge-gradient-secondary"
                                            v-if="tr.etat_article == 'En stock'">{{ tr.etat_article }}</b>
                                        <b class="badge badge badge-gradient-info"
                                            v-if="tr.etat_article == 'En traitement'">{{$t('message.Processing')}}</b>
                                    </vs-td>
                                    <vs-td :data="tr.etat_article" v-if="Client.role == 'Client'">
                                
                                        <button type="button" class="btn btn-danger"
                                            @click.prevent="downloadSticker(tr.id)"><i
                                                class="fa fa-download"></i></button>
                                 <button type="button" class="btn btn-success" 
                                                v-if="tr.etat_article == 'En stock'"
                                    @click="downloadHistoriqueArticle(tr.id)"><i class="fa fa-file-excel"></i></button>
                                    <button type="button" class="btn btn-history" data-bs-toggle="modal"
                                            data-bs-target="#showArticleHistory" @click.prevent="getHistoriqueArticles(tr.id)">
                                            <i class="fa fa-history"></i>
                                        </button>

                                    </vs-td>
                                </vs-tr>
                            </template>
                        </vs-table>
                        <vs-pagination  v-if="locale=='ar'" @input="getArticles(0)" :max="9" :total="articles.last_page"
                            v-model="articles.current_page" prev-icon="arrow_forward"
                            next-icon="arrow_back"></vs-pagination>
                            <vs-pagination v-else @input="getArticles(0)" :max="9" :total="articles.last_page"
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
                        <h5 class="modal-title" id="ajouterArticle" v-if="!edit">{{$t('message.Add_Article')}}</h5>
                        <h5 class="modal-title" id="ajouterArticle" v-if="edit">{{$t('message.Edit_Article')}}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="alert alert-danger" role="alert" v-if="nom_err">
                                {{ nom_err[0] }}
                            </div>
                            <label for="nom_article">{{$t('message.Article_Name')}}<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" id="nom_article" v-model="formData.nom_article"
                                name="nom_article">
                        </div>
                        <div class="form-group">
                            <label for="nom_article">{{$t('message.Comment')}}</label>
                            <input type="text" class="form-control" id="nom_article" v-model="formData.commentaire"
                                name="commentaire">
                        </div>
                        <div class="form-group">
                            <label for="prix_article">{{$t('message.Price')}}<span class="text-danger"> *</span></label>
                            <input type="number" class="form-control" id="prix_article" v-model="formData.prix_article"
                                name="prix_article">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btn_cancel" class="btn btn-secondary"
                            data-bs-dismiss="modal"><i class="fa fa-times"></i></button>
                        <button type="button" class="btn btn-primary" v-if="!edit"
                            @click.prevent="addArticle(false)">{{$t('message.Create')}}</button>
                        <button type="button" class="btn btn-primary" v-if="edit"
                            @click.prevent="updateArticle(formData.selected_article)">{{$t('message.Edit')}}</button>
                      

                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="showArticleHistory" tabindex="-1" aria-labelledby="showArticleHistory" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="showArticleHistory">
                            {{ $t('message.Article_History') }}
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="page-content page-container" id="page-content">
                            <div class="padding">
                                <div class="row">
                                    <div class="col-lg-12">

                                        <div class="timeline">
                                            <div class="tl-item" v-for="HistoriqueArticle in HistoriqueArticles">
                                                <div class="tl-dot">
                                                    <a class="tl-author" href="#" data-abc="true"><span
                                                            class="w-32 avatar circle gd-info">
                                                            <b>{{ HistoriqueArticle.new_stock }}</b><br>

                                                        </span></a>
                                                </div>
                                                <div class="tl-content">
                                                    <div class="etat_commande">
                                                        <b
                                                            class="badge badge-gradient-info">{{ HistoriqueArticle.description }}</b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1">
                                                        {{ $t('message.By') }}
                                                        <b>{{ HistoriqueArticle.username }}</b><br>
                                                        {{ $t('message.At') }}
                                                        <b>{{ moment(HistoriqueArticle.updated_at).format('YYYY-MM-DD HH:mm:ss') }}
                                                        </b>
                                                    </div>
                                                </div>
                                               
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btn_cancel" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-times"></i>
                        </button>
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
            locale: localStorage.getItem("locale"),
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
                { text: '1-20 '+this.$t('message.Items'), value: '20' },
                { text: '1-50 '+this.$t('message.Items'), value: '50' },
                { text: '1-150 '+this.$t('message.Items'), value: '150' },

            ],
            formDataCherche3: {
                selected_option3: '20',

            },
            HistoriqueArticles: {},
        }
    },


    methods: {
        async getHistoriqueArticles(id) {
            this.$vs.loading({ color: '#22c16b' })
            await axios.get('/api/historiqueArticle/' + id)
                .then(res => { this.HistoriqueArticles = res.data.data })
                .catch(error => console.log(res))
                .finally(() => this.$vs.loading.close());
        },
        async downloadHistoriqueArticle(id){
            this.$vs.loading({ color: '#22c16b' })
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
                    .catch(error => console.log(res)).finally(() => this.$vs.loading.close());



        },
        async downloadSticker(id) {
            this.$vs.loading({ color: '#22c16b' })
            let formData = new FormData()
            formData.append('id', id)
            _.each(this.formData, (value, key) => { formData.append(key, value) })
                await axios.post('/api/downloadStickerArticle', formData, { responseType: 'blob' })
                    .then(res => {
                        window.open(URL.createObjectURL(res.data))
                        // const link = document.createElement('a');
                        // link.href = URL.createObjectURL(res.data);
                        // link.download = id+'.pdf';
                        // document.body.append(link);
                        // link.click();
                        // link.remove();

                    })
                    .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
        
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
            this.$vs.loading({ color: '#22c16b' })
            await axios.get('/api/Article/' + id)
                .then(res => { this.formData = res.data.data })
                .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
        },

        async addArticle() {
            this.$vs.loading({ color: '#22c16b' })
            await axios.post('/api/Article', this.formData).then((res) => {
                this.$vs.notify({
                                title:this.$t('message.Article_has_been_added'),
                                color: 'success',
                                position: "top-right",
                                time: 4000,
                            });
               
                this.initialiserFormData();
                document.getElementById("btn_cancel").click()
            }).catch(error => {
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                    this.nom_err = this.errors[Object.keys(this.errors)[0]];
                }
            }).finally(() => this.$vs.loading.close());
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
