<template>

  <div class="content-wrapper">
    <div class="page-header-dashboard">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
          <i class="mdi mdi-home"></i>
        </span> {{$t('message.Dashboard')}}
      </h3>


      <div v-for="annonce in annonces" class="mt-2">
        <vs-alert :active.sync="active1" :color="annonce.type_annonce.toLowerCase()" closable close-icon="close">
          {{ annonce.text_annonce }}
        </vs-alert>
      </div>


      <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
          <li class="breadcrumb-item active" aria-current="page">
            <!-- <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i> -->
          </li>
        </ul>
      </nav>
    </div>
    <div class="row">
      <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-secondary card-img-holder text-white">
          <div class="card-body">
            <img :src="getImage('images/dashboard/circle.svg')" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3">{{$t('message.Delivered_this_month')}} <i
                class="mdi mdi-chart-line mdi-24px float-right"></i>
            </h4>
            <h2 class="mb-5">{{ DELIVERED }} {{$t('message.Order')}}</h2>
            <!-- <h6 class="card-text">Increased by 60%</h6> -->
          </div>
        </div>
      </div>
      <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-secondary card-img-holder text-white">
          <div class="card-body">
            <img :src="getImage('images/dashboard/circle.svg')" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3">{{$t('message.Delivery_Rate')}} <i class="mdi mdi-diamond mdi-24px float-right"></i>
            </h4>
            <h2 class="mb-5">{{ tauxLivraison }} %</h2>
            <!-- <h6 class="card-text">Increased by 5%</h6> -->
          </div>
        </div>
      </div>
      <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-success card-img-holder text-white">
          <div class="card-body">
            <img :src="getImage('images/dashboard/circle.svg')" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3">{{$t('message.Returned_this_month')}}<i class="mdi mdi-diamond mdi-24px float-right"></i>
            </h4>
            <h2 class="mb-5">{{ RETURNED }} {{$t('message.Order')}}</h2>
            <!-- <h6 class="card-text">Increased by 5%</h6> -->
          </div>
        </div>
      </div>
    
      <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-info card-img-holder text-white">
          <div class="card-body">
            <img :src="getImage('images/dashboard/circle.svg')" class="card-img-absolute" alt="circle-image" />
            <h4 class="font-weight-normal mb-3">{{$t('message.COD_this_month')}}<i
                class="mdi mdi-bookmark-outline mdi-24px float-right"></i>
            </h4>
            <h2 class="mb-5">{{ REVENU }} {{$t('message.Dhs')}}</h2>
            <!-- <h6 class="card-text">Decreased by 10%</h6> -->
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 grid-margin">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title"> {{$t('message.Recent_Payments')}}</h4>
            <div class="table-responsive">
              <vs-table stripe :data="factures.data">
                <template slot="thead">
                  <vs-th>
                    {{$t('message.Invoice_id')}}
                  </vs-th>
                  <vs-th>
                    {{$t('message.Invoice_type')}}
                  </vs-th>
                  <vs-th>
                    {{$t('message.Orders_Count')}}
                  </vs-th>
                  <vs-th>
                    {{$t('message.Tolal')}}
                  </vs-th>
                  <vs-th>
                    {{$t('message.Shipping_Fees')}}
                  </vs-th>
                  <vs-th>
                    {{$t('message.Total_Net')}}
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
                    <vs-td :data="tr.id_facture">
                      {{ tr.id_facture }}<br>
                      <span><time-ago :datetime="tr.updated_at" long :locale="locale"></time-ago> </span>
                    </vs-td>

                    <vs-td :data="tr.type_facture">
                      <b v-if="tr.type_facture == 'client'" class="badge badge badge-gradient-info">F</b>
                      <b v-if="tr.type_facture == 'clientManual'" class="badge badge badge-gradient-primary">M</b>
                    </vs-td>
                    <vs-td :data="tr.nombre_commande">
                      {{ tr.nombre_commande }}
                    </vs-td>
                    <vs-td :data="tr.total_facture">
                      {{ tr.total_facture }} {{$t('message.Dhs')}}
                    </vs-td>

                    <vs-td :data="tr.frais_livraison_facture" v-if="tr.frais_livraison_facture">
                      {{ tr.frais_livraison_facture }} {{$t('message.Dhs')}}
                    </vs-td>
                    <vs-td :data="tr.frais_livraison_facture" v-else-if="!tr.frais_livraison_facture">
                      0 {{$t('message.Dhs')}}
                    </vs-td>

                    <vs-td :data="tr.frais_livraison_facture">
                      {{ tr.total_facture - tr.frais_livraison_facture }} {{$t('message.Dhs')}}
                    </vs-td>


                    <vs-td :data="tr.statut_facture">
                      <b class="badge badge badge-gradient-danger" v-if="tr.statut_facture == 'NOTPAID'">{{
                        tr.statut_facture
                      }}</b>

                      <b class="badge badge badge-gradient-success" v-if="tr.statut_facture == 'PAID'">{{
                        tr.statut_facture
                      }}</b>
                    </vs-td>
                    <vs-td>
                      <button type="button" class="btn btn-valide"
                        @click.prevent="downloadFactureClient(tr.id_facture)"><i class="fa fa-print"></i></button>
                      <button type="button" class="btn btn-danger" @click.prevent=""><i
                          class="fa fa-download"></i></button>
                    </vs-td>

                  </vs-tr>
                </template>
              </vs-table>
            </div>
          </div>
        </div>
      </div>
    </div>
   

  </div>
</template>
<script>
import { TimeAgo } from 'vue2-timeago'
import 'vue2-timeago/dist/vue2-timeago.css'
export default {
  name: 'MainComponent',
  components: { TimeAgo, },
  data() {
    return {
      formData: {},
      test: [],
      annonces: [],
      reservations: [],
      clients: [],
      totale: 0,
      nbr_client: 0,
      DeliveredCommande: {
        DeliveredCommande: '',
        etat_commande: '',
      },
      tauxLivraison:0,
      CANCELED: 0,
      DELIVERED: 0,
      RETURNED: 0,
      REVENU: 0,
      active1: true,
      chartOptions: {
        chart: {
          id: 'vuechart-example'
        },
        xaxis: {
          categories: [],
        },
      },
      series: [{
        name: '',

        data: []
      }],
      factures: {
        'data': '',
        'current_page': 1,
        'last_page': 1
      },
      locale: localStorage.getItem('locale'),

    }
  },
  methods: {
    getImage(url) {
      // let baseurl = 'baseurl';
      if (url.match(this.baseUrl)) {
        return url;
      } else {
        return this.baseUrl + url;
      }
    },
    async getDeliveredCommande() {
      await axios.post('/api/getDeliveredCommande')
        .then(res => { this.DeliveredCommande = res.data.data; this.REVENU = res.data.data2['somme'],this.tauxLivraison=res.data.tauxLivraison })
        .catch(error => console.log(res));
      for (var key in this.DeliveredCommande) {
        if (this.DeliveredCommande[key].etat_commande == 'CANCELED') {
          this.CANCELED = this.DeliveredCommande[key].nbrCommande
        } else if (this.DeliveredCommande[key].etat_commande == 'DELIVERED') {
          this.DELIVERED = this.DeliveredCommande[key].nbrCommande
        } else if (this.DeliveredCommande[key].etat_commande == 'RETURNEDAG' || this.DeliveredCommande[key].etat_commande == 'RETURNEDLV' || this.DeliveredCommande[key].etat_commande == 'RETURNEDEV' || this.DeliveredCommande[key].etat_commande == 'RETURNED' || this.DeliveredCommande[key].etat_commande == 'RETURNEDRR') {
          this.RETURNED += this.DeliveredCommande[key].nbrCommande
        }

      }

    },
    async getFactures() {
      this.$vs.loading({ color: '#22c16b' })
      setTimeout(async () => {
        await axios.post('/api/FactureClient?page=' + this.factures.current_page + '&count_nbr=5', this.formDataCherche)
          .then(res => { this.factures = res.data.data; })
          .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
      }, 200);

    },
    downloadFactureClient(id) {

      axios.get('/api/getFacture/' + id, { responseType: 'blob' })
        .then(res => {
          window.open(URL.createObjectURL(res.data))
        })
        .catch(error => console.log(res));
    },

  },


  async beforeCreate() {
    await axios.get('/api/commandeStatistiquesRevenue')
      .then((res) => {
        this.chartOptions = {
          xaxis: {
            categories: res.data.data2
          },
        },
          this.series = [{
            name: $t('message.Dhs'),
            data: res.data.data
          }]
      })
      .catch(error => console.log(res));

    await axios.get('/api/getAnnonces')
      .then((res) => {
        this.annonces = res.data.data;
      })
      .catch(error => console.log(res));



  },
  async mounted() {
    this.getDeliveredCommande();
    this.getFactures();
  }

}

</script>
