<template>
    <div class="content-wrapper-customise">
        <div class="page-header-dashboard">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span>
                Return receipt
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row head-table">

                            <div class="chercher">
                                <!-- Example single danger button -->
                                <vs-select class="selectExample" v-model="formDataCherche3.selected_option3"
                                    @change="classifierBonRetour(formDataCherche3.selected_option3)">
                                    <vs-select-item :key="index" :value="item.value" :text="item.text"
                                        v-for="(item, index) in options3" />
                                </vs-select>


                                <vs-select class="selectExample ml-3" v-model="formDataCherche.selected_option">
                                    <vs-select-item :key="index" :value="item.value" :text="item.text"
                                        v-for="(item, index) in options" />
                                </vs-select>

                                <div class="search_bar">
                                    <vs-input placeholder="Search" v-model="formDataCherche.valeur_recherche" />
                                    <button class="btn-chercher" @click="classifierBonRetour(formDataCherche3.selected_option3)">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <vs-table stripe v-model="selected" :data="bonRetour.data">
                            <template slot="thead">
                                <vs-th> Id </vs-th>
                                <vs-th> NbrColis </vs-th>
                                <vs-th> Statut </vs-th>
                                <vs-th> Operation </vs-th>
                            </template>
                            <template slot-scope="{ data }">
                                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                    <vs-td :data="tr.id_bon_retour_client">
                                        {{ tr.id_bon_retour_client }}<br>
                                        <span><time-ago :datetime="tr.updated_at" long></time-ago>
                                        </span>
                                    </vs-td>
                                    <vs-td :data="tr.nbrColis_bonRetourClient">
                                        {{ tr.nbrColis_bonRetourClient }}
                                    </vs-td>
                                    <vs-td :data="tr.statut_bonRetourClient">
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.statut_bonRetourClient == 'Envoyer'">Envoyer</b>
                                        <b class="badge badge badge-gradient-success"
                                            v-if="tr.statut_bonRetourClient == 'recu'">recu</b>
                                    </vs-td>
                                    <vs-td :data="tr.statut_bonRetourClient">
                                        <button type="button" class="btn btn-valide"
                                            @click.prevent="getBonRetour(tr.id_bon_retour_client)"><i
                                                class="fa fa-print"></i></button>
                                        <button type="button" class="btn btn-danger"
                                            @click.prevent="downloadBonRetour(tr.id_bon_retour_client)"><i
                                                class="fa fa-download"></i></button>

                                    </vs-td>
                                </vs-tr>
                            </template>
                        </vs-table>
                        <vs-pagination @input="classifierBonRetour(formDataCherche3.selected_option3)" :max="9"
                            :total="bonRetour.last_page" v-model="bonRetour.current_page" prev-icon="arrow_back"
                            next-icon="arrow_forward"></vs-pagination>
                    </div>
                </div>
            </div>
        </div>


    </div>
</template>
<style scoped>
input[type="number"] {
    border-radius: 21px;
    text-align: center;
    border: 1px solid #198ae3;
    padding: 0px 7px;
    width: 40%;
    margin-left: 23px;
}
</style>
<script>
import axios from "axios";
import { TimeAgo } from "vue2-timeago";
import "vue2-timeago/dist/vue2-timeago.css";
export default {
    props: ["Client"],
    name: "RetourClientComponent",
    components: { TimeAgo },
    data() {
        return {
            token: localStorage.getItem("token"),
            selected: [],
            errors: {},

            bonRetour: {
                data: "",
                current_page: 1,
                last_page: 1,
            },

            nom_err: "",

            options: [
                { text: "Num Bon", value: "id_bon_retour_client" },
                { text: "Num Order", value: "id_commande" },

            ],
            formDataCherche: {
                selected_option: "id_bon_retour_client",
                valeur_recherche: "",
            },
            options3: [
                { text: "1-20 items", value: "20" },
                { text: "1-50 items", value: "50" },
                { text: "1-150 items", value: "150" },
                { text: "1-200 items", value: "200" },
            ],
            formDataCherche3: {
                selected_option3: "20",
            },

        };
    },

    methods: {
        getBonRetour(id) {
            this.$vs.loading({ color: "#22c16b" });
            axios.get('/api/getBonRetour/' + id, { responseType: 'blob' })
                .then(res => {
                    window.open(URL.createObjectURL(res.data))
                })
                .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
        },
        downloadBonRetour(id) {
            this.$vs.loading({ color: "#22c16b" });
            axios.get('/api/getBonRetour/' + id, { responseType: 'blob' })
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

        async classifierBonRetour(count_nbr) {
            this.$vs.loading({ color: "#22c16b" });
            setTimeout(async () => {
                if (count_nbr > 1) {
                    await axios
                        .post(
                            "/api/bonRetour?page=" +
                            this.bonRetour.current_page +
                            "&count_nbr=" +
                            count_nbr,
                            this.formDataCherche
                        )
                        .then((res) => {
                            this.bonRetour = res.data.data;
                        })
                        .catch((error) => console.log(res))
                        .finally(() => this.$vs.loading.close());
                } else {
                    await axios
                        .post(
                            "/api/bonRetour?page=" +
                            this.bonRetour.current_page +
                            "&count_nbr=20",
                            this.formDataCherche
                        )
                        .then((res) => {
                            this.bonRetour = res.data.data;
                        })
                        .catch((error) => console.log(res))
                        .finally(() => this.$vs.loading.close());
                }

            }, 200);
        },



    },
    async beforeMount() {
        axios.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;

    },
    async mounted() { },

    async created() {
        $(window).on("load", function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

    },
};

</script>