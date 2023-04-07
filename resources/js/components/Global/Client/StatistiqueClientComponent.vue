<template>
    <div class="content-wrapper-customise">
        <div class="page-header-dashboard">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span>{{ $t('message.Statistics') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

                            <div class="col-xxl-6 col-md-4" style="margin-top: 12px;">
                                <div class="date_time_picker">
                                    <date-picker v-model="formData" range :placeholder="showDateHolder"></date-picker>
                                </div>
                            </div>
                            <div class="col-xxl-6 col-md-4">
                                <div class="dowload_model">
                                    <vs-button color="danger" @click.prevent="getClientsData" type="filled"><i
                                            class="fa fa-download" aria-hidden="true"></i>
                                        {{ $t('message.Customer_Data') }}</vs-button>
                                </div>
                            </div>
                        </div>


                        <div class="row">

                            <div class="col-xxl-2 col-md-4">
                                <div class="card info-card revenue-card">

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $t('message.Orders_Count') }}<span></span></h5>

                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-cube"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ totalColis }} {{ $t('message.Colis') }}</h6>
                                                <!--  <span class="text-success small pt-1 fw-bold">8%</span> <span
                                                    class="text-muted small pt-2 ps-1">increase</span> -->

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <div class="col-xxl-2 col-md-4">
                                <div class="card info-card revenue-card">

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $t('message.Delivred') }} <span></span></h5>

                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-check"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ DELIVERED }} {{ $t('message.Colis') }}</h6>
                                                <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span
                                                    class="text-muted small pt-2 ps-1">increase</span> -->

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <div class="col-xxl-2 col-md-4">
                                <div class="card info-card customers-card">

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $t('message.Returned') }} <span></span></h5>

                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-redo"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ RETURNED }} {{ $t('message.Colis') }}</h6>
                                                <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span
                                                    class="text-muted small pt-2 ps-1">increase</span> -->

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="card info-card revenue-card">

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $t('message.Total_COD') }}<span></span></h5>

                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-currency-usd"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6 v-if="REVENU != null">{{ REVENU }} {{ $t('message.Dhs') }}</h6>
                                                <h6 v-if="REVENU == null">0 {{ $t('message.Dhs') }}</h6>

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="card info-card sales-card">

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $t('message.Shipping_Fees') }} <span></span></h5>

                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-van-utility"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6 v-if="total_livraison != null">{{ total_livraison }}
                                                    {{ $t('message.Dhs') }}</h6>
                                                <h6 v-if="total_livraison == null">0 {{ $t('message.Dhs') }}</h6>
                                                <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span
                                                    class="text-muted small pt-2 ps-1">increase</span> -->

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="card info-card revenue-card">

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $t('message.Revenu_NET') }}<span></span></h5>

                                        <div class="d-flex align-items-center">


                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-coin"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ REVENU - total_livraison }} {{ $t('message.Dhs') }} </h6>


                                            </div>
                                        </div>
                                    </div>



                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-xxl-2 col-md-4">
                                <div class="card info-card en-cours-card">

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $t('message.Package_In_Progress') }} <span></span></h5>

                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-package"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ colisEnCour }} {{ $t('message.Colis') }}</h6>
                                                <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span
                                                    class="text-muted small pt-2 ps-1">increase</span> -->

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <div class="col-xxl-2 col-md-4">
                                <div class="card info-card customers-card">

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $t('message.Package_To_Track') }}<span></span></h5>

                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-phone"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ colisSuivie }} {{ $t('message.Colis') }}</h6>
                                                <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span
                                                    class="text-muted small pt-2 ps-1">increase</span> -->

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <div class="col-xxl-2 col-md-4">
                                <div class="card info-card revenue-card">

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $t('message.Paid_Delivered_Orders') }} <span></span></h5>

                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-calendar-check"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ colisFacture }} {{ $t('message.Colis') }}</h6>
                                                <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span
                                                    class="text-muted small pt-2 ps-1">increase</span> -->

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="card info-card cancel-card">

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $t('message.UnPaid_Delivered_Orders') }}<span></span></h5>

                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-calendar-remove"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ colisNonFacture }} {{ $t('message.Colis') }}</h6>
                                                <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span
                                                    class="text-muted small pt-2 ps-1">increase</span> -->

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="card info-card sales-card">

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $t('message.Invoices_Count') }} <span></span></h5>

                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-file"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ nbrFacture }} {{ $t('message.Invoices') }}</h6>
                                                <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span
                                                    class="text-muted small pt-2 ps-1">increase</span> -->

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="card info-card revenue-card">

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $t('message.Delivery_Rate') }} <span></span></h5>

                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-coin"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ (tauxLivraison).toFixed(2) }} %</h6>


                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <div class="row statistiqueRow">
                            <div class="statistique">
                                <h3>{{ $t('message.Income_per_day') }}</h3>
                                <apexchart type="bar" :options="chartOptions" :series="chartOptions.series">
                                </apexchart>
                            </div>

                            <div class="statistique">
                                <h3>{{ $t('message.Top_10_Cities') }}</h3>
                                <apexchart type="bar" :options="chartOptions2" :series="chartOptions2.series"></apexchart>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</template>
<style scoped>
.stretch-card>.card {
    width: 100%;
    min-width: 100%;
    height: 100%;
}

.card-title {
    padding: 15px 0 15px 0;
    font-size: 12px;
    font-weight: 600;
    color: #012970;
    font-family: "Poppins", sans-serif;
}

.statistique {
    width: 50%;
}

.statistiqueRow {
    display: flex;
}

@media only screen and (max-width: 1400px) {
    .statistique {
        width: 100%;
    }

}
</style>
<script>
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import axios from 'axios'


export default {
    props: ['Client'],
    name: 'StatistiqueClientComponent',
    components: {
        DatePicker
    },
    data() {
        return {
            CANCELED: 0,
            DELIVERED: 0,
            RETURNED: 0,
            REVENU: 0,
            total_livraison: 0,
            totalColis: 0,
            colisEnCour: 0,
            colisSuivie: 0,
            nbrFacture: 0,
            colisNonFacture: 0,
            colisFacture: 0,
            tauxLivraison: 0,

            formData: [],
            showDateHolder: '',

            token: localStorage.getItem('token'),
            chartOptions: {
                theme: {
                    mode: 'light',
                    palette: 'palette2',

                },
                series: [{
                    name: '',

                    data: []
                }],
                chart: {
                    id: 'vuechart-example'
                },
                xaxis: {
                    categories: [],
                },
            },


            chartOptions2: {
                theme: {
                    monochrome: {
                        enabled: true,
                        color: '#255aee',
                        shadeTo: 'light',
                        shadeIntensity: 0.65
                    }
                },

                series: [{
                    data: []
                }],


                chart: {
                    type: 'bar',
                    stacked: true,
                    height: 350
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                    }
                },
                dataLabels: {
                    enabled: false
                },

                xaxis: {
                    categories: [],
                }
            }

        }
    },
    watch: {
        formData(neew, olld) {
            this.initialiserFormData()
            this.getStatistique();
            this.getDeliveredCommande()
            this.getStatistiqueVille();
        }
    },

    methods: {
        async getClientsData() {

            await axios.get('/api/getClientsData', { responseType: 'blob' })
                .then(res => {
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(res.data);
                    link.download = 'CustomerData.xlsx';
                    document.body.append(link);
                    link.click();
                    link.remove();
                })
                .catch(error => console.log(res));


        },
        async getStatistique() {
            await axios.post('/api/commandeStatistiquesRevenue', this.formData)
                .then((res) => {
                    this.chartOptions = {
                        xaxis: {
                            categories: res.data.data2
                        },
                    },
                        this.chartOptions.series = [{
                            name: this.$t('message.Dhs'),
                            data: res.data.data
                        }]
                })
        },
        async getStatistiqueVille() {
            await axios.post('/api/statistiquesVille', this.formData)
                .then((res) => {
                    this.chartOptions2 = {
                        xaxis: {
                            categories: res.data.data2
                        },
                    },
                        this.chartOptions2.series = [{
                            name: this.$t('message.Colis'),
                            data: res.data.data
                        }]
                })
        },
        async getDeliveredCommande() {
            this.$vs.loading({ color: "#22c16b" });
            await axios.post('/api/getDeliveredCommande', this.formData)
                .then(res => {
                    this.DeliveredCommande = res.data.data; this.REVENU = res.data.data2['somme']; this.total_livraison = res.data.fraisLivraison;
                    this.totalColis = res.data.totalColis['nbrColis']; this.colisEnCour = res.data.colisEnCours['nbrColis']; this.colisNonFacture = res.data.colisNonFacture['nbrColis']
                        ; this.colisFacture = res.data.colisFacture['nbrColis']; this.nbrFacture = res.data.nbrFacture['nbrFacture']; this.tauxLivraison = res.data.tauxLivraison;
                    this.showDateHolder = res.data.dateRange;
                    this.DELIVERED = res.data.deliverdCommande.nbrColis
                    this.CANCELED = res.data.canceledCommande.nbrColis
                    this.RETURNED = res.data.returnedCommande.nbrColis
                    this.colisSuivie = res.data.suivieCommande.nbrColis

                })
                .catch(error => console.log(res)).finally(() => this.$vs.loading.close());



        },
        initialiserFormData() {
            this.CANCELED = 0,
                this.DELIVERED = 0,
                this.RETURNED = 0,
                this.REVENU = 0,
                this.total_livraison = 0,
                this.totalColis = 0

            this.colisEnCour = 0,
                this.colisSuivie = 0,
                this.nbrFacture = 0,
                this.colisNonFacture = 0,
                this.colisFacture = 0
        },
    },
    async beforeMount() {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        this.getStatistique();
        this.getDeliveredCommande();
        this.getStatistiqueVille();

    },
    mounted() {

        $(".form-check label,.form-radio label").append('<i class="input-helper"></i>');


    },

}




</script>
