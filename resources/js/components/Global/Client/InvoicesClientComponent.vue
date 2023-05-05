<template>
    <div class="content-wrapper-customise">
        <div class="page-header-dashboard">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> {{ $t('message.Invoices') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">


                        <div id="btn-top-package">

                            <div class="search_bar">
                                <vs-select class="selectExample" v-model="formDataCherche.selected_option">
                                    <vs-select-item :key="index" :value="item.value" :text="item.text"
                                        v-for="item, index in options2" />
                                </vs-select>


                                <vs-input :placeholder="$t('message.Search')" v-model="formDataCherche.valeur_recherche" @keyup.enter="getFactures(formDataCherche3.selected_option3)"/>
                                <button class="btn-chercher" @click="getFactures(formDataCherche3.selected_option3)" ><i
                                        class="fa fa-search" aria-hidden="true"></i></button>
                            </div>
                            <vs-select class="selectExample" v-model="formDataCherche3.selected_option3"
                                @change="getFactures(formDataCherche3.selected_option3)">
                                <vs-select-item :key="index" :value="item.value" :text="item.text"
                                    v-for="item, index in options3" />
                            </vs-select>
                        </div>
                        <vs-table stripe :data="factures.data">
                            <template slot="thead">
                                <vs-th>
                                    {{ $t('message.Invoice_id') }}
                                </vs-th>
                                <vs-th>
                                    {{ $t('message.Invoice_type') }}
                                </vs-th>
                                <vs-th>
                                    {{ $t('message.Orders_Count') }}
                                </vs-th>
                                <vs-th>
                                    {{ $t('message.ToTal') }}
                                </vs-th>
                                <vs-th>
                                    {{ $t('message.Shipping_Fees') }}
                                </vs-th>
                                <vs-th>
                                    {{ $t('message.Total_Net') }}
                                </vs-th>
                                <vs-th>
                                    {{ $t('message.Status') }}
                                </vs-th>
                                <vs-th>
                                    {{ $t('message.Operation') }}
                                </vs-th>
                            </template>

                            <template slot-scope="{data}">
                                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                    <vs-td :data="tr.id_facture">
                                        {{ tr.id_facture }}<br>
                                        <span><time-ago :datetime="tr.updated_at" long :locale="locale"></time-ago> </span>
                                    </vs-td>

                                    <vs-td :data="tr.type_facture">
                                        <b v-if="tr.type_facture == 'client'" class="badge badge badge-gradient-info">F</b>
                                        <b v-if="tr.type_facture == 'clientManual'"
                                            class="badge badge badge-gradient-primary">M</b>
                                    </vs-td>
                                    <vs-td :data="tr.nombre_commande">
                                        {{ tr.nombre_commande }}
                                    </vs-td>
                                    <vs-td :data="tr.total_facture">
                                        {{ tr.total_facture }} {{ $t('message.Dhs') }}
                                    </vs-td>

                                    <vs-td :data="tr.frais_livraison_facture" v-if="tr.frais_livraison_facture">
                                        {{ tr.frais_livraison_facture }} {{ $t('message.Dhs') }}
                                    </vs-td>
                                    <vs-td :data="tr.frais_livraison_facture" v-else-if="!tr.frais_livraison_facture">
                                        0 {{ $t('message.Dhs') }}
                                    </vs-td>

                                    <vs-td :data="tr.frais_livraison_facture">
                                        {{ tr.total_facture - tr.frais_livraison_facture }} {{ $t('message.Dhs') }}
                                    </vs-td>


                                    <vs-td :data="tr.statut_facture">
                                        <b class="badge badge badge-gradient-danger"
                                            v-if="tr.statut_facture == 'NOTPAID'">{{ $t('message.NotPaid') }}</b>

                                        <b class="badge badge badge-gradient-success" v-if="tr.statut_facture == 'PAID'">{{
                                            $t('message.Paid') }}</b>
                                    </vs-td>
                                    <vs-td>
                                        <button type="button" class="btn btn-valide"
                                            @click.prevent="getFactureClient(tr.id_facture)"><i
                                                class="fa fa-print"></i></button>
                                        <button type="button" class="btn btn-danger"
                                            @click.prevent="downloadFactureClient(tr.id_facture)"><i
                                                class="fa fa-download"></i></button>
                                    </vs-td>

                                </vs-tr>
                            </template>
                        </vs-table>
                        <vs-pagination @input="getFactures(0)" :max="9" :total="factures.last_page"
                            v-model="factures.current_page" prev-icon="arrow_back"
                            next-icon="arrow_forward"></vs-pagination>
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
    name: 'InvoicesClientComponent',
    components: { TimeAgo, },
    data() {
        return {
            token: localStorage.getItem('token'),
            locale: localStorage.getItem("locale"),
            edit: false,
            errors: {},
            factures: {
                'data': '',
                'current_page': 1,
                'last_page': 1
            },
            nom_err: '',
            options3: [
                { text: '1-20 ' + this.$t('message.Items'), value: '20' },
                { text: '1-50 ' + this.$t('message.Items'), value: '50' },
                { text: '1-150 ' + this.$t('message.Items'), value: '150' },
                { text: '1-200 ' + this.$t('message.Items'), value: '200' },

            ],
            formDataCherche3: {
                selected_option3: '20',

            },
            options2: [
                { text: this.$t('message.Invoice_id'), value: 'id_facture' },
                { text: this.$t('message.Order_id'), value: 'id_commande' },

            ],

            formDataCherche: {
                selected_option: 'id_facture',
                valeur_recherche: '',
                selected_option2: '',
            },
        }
    },


    methods: {
        async getFactures(count_nbr) {
            this.formDataCherche.selected_option2 = this.formDataCherche3.selected_option3;
            this.$vs.loading({ color: '#22c16b' })
            setTimeout(async () => {
                if (count_nbr > 1) {
                    await axios.post('/api/FactureClient?page=' + this.factures.current_page + '&count_nbr=' + count_nbr, this.formDataCherche)
                        .then(res => { this.factures = res.data.data; })
                        .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
                } else {
                    await axios.post('/api/FactureClient?page=' + this.factures.current_page + '&count_nbr=20', this.formDataCherche)
                        .then(res => { this.factures = res.data.data; })
                        .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
                }
            }, 200);

        },
        getFactureClient(id) {
            this.$vs.loading({ color: '#22c16b' })
            axios.get('/api/getFacture/' + id, { responseType: 'blob' })
                .then(res => {
                    window.open(URL.createObjectURL(res.data))


                })
                .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
        },
        downloadFactureClient(id) {
this.$vs.loading({ color: '#22c16b' })
            axios.get('/api/getFacture/' + id, { responseType: 'blob' })
                .then(res => {
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(res.data);
                    link.download = id + '.pdf';
                    document.body.append(link);
                    link.click();
                    link.remove();
                })
                .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
        },
    },
    async beforeMount() {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`


    },
    mounted() {

    },

}




</script>
