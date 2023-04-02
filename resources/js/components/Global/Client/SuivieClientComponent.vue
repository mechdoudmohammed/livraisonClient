<template>
    <div class="content-wrapper-customise">
        <div class="page-header-dashboard">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span>{{ $t('message.Track_This_Order') }} <b class="badge badge badge-gradient-secondary">{{ nbrCommande
                }}</b>
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
                                    @change="classifierCommande(formDataCherche3.selected_option3)">
                                    <vs-select-item :key="index" :value="item.value" :text="item.text"
                                        v-for="(item, index) in options3" />
                                </vs-select>


                                <vs-select class="selectExample ml-3" v-model="formDataCherche.selected_option">
                                    <vs-select-item :key="index" :value="item.value" :text="item.text"
                                        v-for="(item, index) in options" />
                                </vs-select>
                                <button class="btn-sync" @click="refreshData">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <div class="search_bar">
                                    <vs-input :placeholder="$t('message.Search')"
                                        v-model="formDataCherche.valeur_recherche" />
                                    <button class="btn-chercher" @click="chercher(formDataCherche3.selected_option3)">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <vs-table stripe multiple v-model="selected" @dblSelection="copyCommande" :data="commandes.data">
                            <template slot="thead">
                                <vs-th v-if="Client.stock == 1"> {{ $t('message.Type') }}</vs-th>
                                <vs-th> {{ $t('message.Id') }} </vs-th>
                                <vs-th> {{ $t('message.Store') }} </vs-th>
                                <vs-th> {{ $t('message.Name') }} </vs-th>
                                <vs-th> {{ $t('message.Phone_Number') }} </vs-th>
                                <vs-th> {{ $t('message.City') }} </vs-th>
                                <vs-th> {{ $t('message.Price') }} </vs-th>
                                <vs-th> {{ $t('message.Status') }} </vs-th>
                                <vs-th> {{ $t('message.Invoice') }} </vs-th>
                                <vs-th> {{ $t('message.Operation') }} </vs-th>
                            </template>
                            <template slot-scope="{ data }">
                                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                    <vs-td :data="tr.id_article" v-if="Client.stock == 1">
                                        <b v-if="tr.type_commande == 'ramassage'"
                                            class="badge badge badge-gradient-info">R</b>
                                        <b v-if="tr.type_commande == 'stock'"
                                            class="badge badge badge-gradient-primary">S</b>
                                    </vs-td>

                                    <vs-td :data="tr.id_commande">
                                        {{ tr.id_commande }}
                                        <div class="additionalCommentaire">
                                            <span class="noreponse" v-if="tr.etat_commande == 'RETURNEDLV'">Return sent to
                                                agency</span>

                                            <span class="noreponse" v-if="tr.etat_commande == 'RETURNED'">Return send to
                                                hometown</span>

                                        </div>
                                        <span><time-ago :datetime="tr.updated_at" long :locale="locale"></time-ago>
                                        </span>
                                    </vs-td>
                                    <vs-td :data="tr.nom_store">
                                        <span v-if="tr.nom_store != null">
                                            {{ tr.nom_store }}
                                        </span>
                                        <span v-else-if="tr.nom_store == null">
                                            {{ tr.company }}
                                        </span>
                                    </vs-td>
                                    <vs-td :data="tr.nom_client_commande">
                                        {{ tr.nom_client_commande }}
                                    </vs-td>
                                    <vs-td :data="tr.telephone_client_commande">
                                        {{ tr.telephone_client_commande }}
                                        <span @click="whatsapp(tr.telephone_client_commande, tr.nom_client_commande)"
                                            class="whatsapp"><i class="fab fa-whatsapp" aria-hidden="true"></i></span>
                                    </vs-td>
                                    <vs-td :data="tr.ville_client_commande">
                                        {{ tr.ville_client_commande }}
                                    </vs-td>
                                    <vs-td :data="tr.prix_commande">
                                        {{ tr.prix_commande }}
                                    </vs-td>
                                    <vs-td :data="tr.etat_commande">
                                        <b class="badge badge badge-gradient-secondary"
                                            v-if="tr.etat_commande == 'CREATED'">{{ tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-info" v-if="tr.etat_commande == 'CONFIRMED'">{{
                                            tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-secondary"
                                            v-if="tr.etat_commande == 'PICKUP'">{{ tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-secondary"
                                            v-if="tr.etat_commande == 'PROCESSING'">{{ tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-info"
                                            v-if="tr.etat_commande == 'HOME'">INHOUSE</b>
                                        <b class="badge badge badge-gradient-info"
                                            v-if="tr.etat_commande == 'CHANGERPRIX'">CHANGEPRICE</b>
                                        <b class="badge badge badge-gradient-info" v-if="tr.etat_commande == 'INHOUSE'">{{
                                            tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-primary"
                                            v-if="tr.etat_commande == 'ENROUTE'">{{ tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-primary"
                                            v-if="tr.etat_commande == 'RAMASSER'">PICKUP</b>
                                        <b class="badge badge badge-gradient-info" v-if="tr.etat_commande == 'TRANSIT'">{{
                                            tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'NOREPONSE'">NO REP + SMS</b>
                                        <b class="badge badge badge-gradient-info" v-if="tr.etat_commande == 'REPORTED'">{{
                                            tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'RETURNEDLV'" data-toggle="tooltip"
                                            title="Retours envoye vers agence">RETURNED</b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'RETURNEDAG'" data-toggle="tooltip"
                                            title="Retours envoye vers agence">RETURNED</b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'RETURNEDEV'" data-toggle="tooltip"
                                            title="Retours envoye vers agence">RETURNED</b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'RETURNEDRR'" data-toggle="tooltip"
                                            title="Retours envoye vers agence">RETURNED</b>
                                        <b class="badge badge badge-gradient-info" v-if="tr.etat_commande == 'ASSIGN'">{{
                                            tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-success"
                                            v-if="tr.etat_commande == 'DELIVERED'">{{ tr.etat_commande }}</b>

                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'RETURNED'">{{ tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-danger"
                                            v-if="tr.etat_commande == 'CANCELED'">{{ tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-info" v-if="tr.etat_commande == 'DMSUIVIE'">{{
                                            tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-danger"
                                            v-if="tr.etat_commande == 'ARCHIVED'">{{ tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'ANNULER'">Annuler
                                        </b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'ANNULER_CL'">Annuler
                                        </b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'RELANCER'">RELAUNCH</b>
                                    </vs-td>
                                    <vs-td v-if="Client.role == 'Client'">
                                        <button class="badge badge badge-gradient-success"
                                            v-if="tr.statut_facture == 'PAID'" @click="getFacture(tr)">
                                            {{ $t('message.Paid') }}
                                        </button>
                                        <button class="badge badge badge-gradient-info"
                                            v-else-if="tr.statut_facture == 'NOTPAID'" @click="getFacture(tr)">
                                            {{ $t('message.Invoiced') }}
                                        </button>
                                        <b class="badge badge badge-gradient-danger"
                                            v-else-if="tr.statut_facture == null">{{ $t('message.NotPaid') }}</b>
                                    </vs-td>
                                    <vs-td v-if="Client.role == 'EmployeClient'">
                                        <button class="badge badge badge-gradient-success"
                                            v-if="tr.statut_facture == 'PAID'">
                                            {{ $t('message.Paid') }}
                                        </button>
                                        <button class="badge badge badge-gradient-info"
                                            v-else-if="tr.statut_facture == 'NOTPAID' && tr.type_facture == 'client'">
                                            {{ $t('message.Invoiced') }}
                                        </button>
                                        <b class="badge badge badge-gradient-danger"
                                            v-else-if="tr.statut_facture == null">{{ $t('message.NotPaid') }}</b>
                                    </vs-td>
                                    <vs-td :data="tr.etat_commande" style="min-width: 156px;">
                                        <button type="button" class="btn btn-valide"
                                            @click.prevent="showDetails(tr.id_commande)">
                                            <i class="fa fa-eye"></i>
                                        </button>

                                        <button type="button" class="btn btn-history" data-bs-toggle="modal"
                                            data-bs-target="#showCommande" v-if="tr.etat_commande != 'CREATED'"
                                            @click.prevent="getHistoriqueCommande(tr.id_commande)">
                                            <i class="fa fa-history"></i>
                                        </button>

                                        <button type="button" class="btn btn-warning" v-if="tr.etat_commande == 'CREATED'"
                                            @click.prevent="checkCommande(true, tr)">
                                            <i class="fa fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger"
                                            @click.prevent="deleteCommande(tr.id_commande)"
                                            v-if="tr.etat_commande == 'CREATED'">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger"
                                            v-if="tr.etat_commande != 'DELIVERED' && tr.etat_commande != 'ANNULER_CL' && tr.etat_commande != 'ANNULER' && tr.etat_commande != 'CANCELED' && tr.etat_commande != 'RETURNEDLV'
                                                && tr.etat_commande != 'RETURNED' && tr.etat_commande != 'RETURNEDEV' && tr.etat_commande != 'RETURNEDRR' && tr.etat_commande != 'RETURNEDAG' && tr.etat_commande != 'RETURNED' && tr.etat_commande != 'CREATED' && tr.etat_commande != 'CONFIRMED'"
                                            @click.prevent="reclamationCommande(tr)">
                                            <i class="fa fa-info"></i>
                                        </button>
                                    </vs-td>
                                </vs-tr>
                            </template>
                        </vs-table>
                        <vs-pagination v-if="locale=='ar'" @input="classifierCommande(formDataCherche3.selected_option3)" :max="9"
                            :total="commandes.last_page" v-model="commandes.current_page" prev-icon="arrow_forward"
                            next-icon="arrow_back"></vs-pagination>

                            <vs-pagination v-else @input="classifierCommande(formDataCherche3.selected_option3)" :max="9"
                            :total="commandes.last_page" v-model="commandes.current_page" prev-icon="arrow_back"
                            next-icon="arrow_forward"></vs-pagination>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="showCommande" tabindex="-1" aria-labelledby="showCommande" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="showCommande">
                            Order History
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="page-content page-container" id="page-content">
                            <div class="padding">
                                <div class="row">
                                    <div class="col-lg-12">

                                        <table v-if="livreur">
                                            <tr>
                                                <td>
                                                    <h6>
                                                        <i class="fas fa-user-circle responsable"></i>
                                                    </h6>
                                                </td>
                                                <td>
                                                    <h6>
                                                        {{ livreur["nom"] }}
                                                        {{ livreur["prenom"] }}
                                                    </h6>
                                                </td>
                                            </tr>
                                            <tr @click="whatsapp(livreur['telephone'], livreur['prenom'])"
                                                style="cursor: pointer;">
                                                <td>
                                                    <h6>
                                                        <i class="fab fa-whatsapp responsable_telephone"></i>
                                                    </h6>
                                                </td>
                                                <td>
                                                    <h6>
                                                        {{ livreur["telephone"] }}
                                                    </h6>
                                                </td>
                                            </tr>
                                        </table>
                                        <div class="timeline">
                                            <div class="tl-item" v-for="hCommande in historiqueCommande">
                                                <div class="tl-dot">
                                                    <a class="tl-author" href="#" data-abc="true"><span
                                                            class="w-32 avatar circle gd-info">

                                                            <img v-if="hCommande.etat_commande == 'ASSIGN'"
                                                                src="https://img.icons8.com/windows/22/198ae3/guest-male.png"
                                                                alt="." />
                                                            <img v-if="hCommande.typeCall == 'CallType.missed'"
                                                                src="https://img.icons8.com/windows/22/198ae3/missed-call.png"
                                                                alt="." />
                                                            <img v-if="hCommande.typeCall == 'CallType.outgoing'"
                                                                src="https://img.icons8.com/windows/22/198ae3/outgoing-call.png"
                                                                alt="." />
                                                            <img v-if="hCommande.typeCall == 'CallType.incoming'"
                                                                src="https://img.icons8.com/windows/22/198ae3/incoming-call.png"
                                                                alt="." />

                                                            <img v-if="hCommande.etat_commande == 'CREATED'"
                                                                src="https://img.icons8.com/windows/22/198ae3/plus.png"
                                                                alt="." />
                                                            <img v-if="hCommande.etat_commande == 'CONFIRMED'"
                                                                src="https://img.icons8.com/windows/22/198ae3/checkmark.png"
                                                                alt="." />
                                                            <img v-if="hCommande.etat_commande == 'PICKUP'"
                                                                src="https://img.icons8.com/windows/22/198ae3/successful-delivery.png"
                                                                alt="." />
                                                            <img v-if="hCommande.etat_commande == 'PROCESSING'"
                                                                src="https://img.icons8.com/windows/22/198ae3/holding-box.png"
                                                                alt="." />
                                                            <img v-if="hCommande.etat_commande == 'RAMASSER'"
                                                                src="https://img.icons8.com/windows/22/198ae3/shipped.png"
                                                                alt="." />
                                                            <img v-if="hCommande.etat_commande == 'ENROUTE'"
                                                                src="https://img.icons8.com/windows/22/198ae3/highway.png"
                                                                alt="." />
                                                            <img v-if="hCommande.etat_commande == 'DMSUIVIE'"
                                                                src="https://img.icons8.com/windows/22/198ae3/order-shipped.png"
                                                                alt="." />
                                                            <img v-if="hCommande.etat_commande == 'TRANSIT'"
                                                                src="https://img.icons8.com/windows/22/198ae3/motorcycle-delivery-multiple-boxes.png"
                                                                alt="." />
                                                            <img v-if="hCommande.etat_commande == 'REPORTED'"
                                                                src="https://img.icons8.com/windows/22/198ae3/time-span.png"
                                                                alt="." />
                                                            <img v-if="hCommande.etat_commande == 'HOME'"
                                                                src="https://img.icons8.com/windows/22/198ae3/home.png"
                                                                alt="." />
                                                            <img v-if="hCommande.etat_commande == 'INHOUSE'"
                                                                src="https://img.icons8.com/windows/22/198ae3/home.png"
                                                                alt="." />


                                                            <img v-if="hCommande.etat_commande == 'NOREPONSE'"
                                                                src="https://img.icons8.com/windows/22/198ae3/end-call.png"
                                                                alt="." />

                                                            <img v-if="hCommande.etat_commande == 'RELANCER'"
                                                                src="https://img.icons8.com/windows/22/198ae3/recurring-appointment.png"
                                                                alt="." />
                                                            <img v-if="hCommande.etat_commande == 'CHANGERPRIX'"
                                                                src="https://img.icons8.com/windows/22/198ae3/low-price.png"
                                                                alt="." />

                                                            <img v-if="hCommande.etat_commande == 'ANNULER'"
                                                                src="https://img.icons8.com/windows/22/198ae3/time-span.png"
                                                                alt="." />
                                                            <img v-if="hCommande.etat_commande == 'DELIVERED'"
                                                                src="https://img.icons8.com/windows/22/198ae3/double-tick.png"
                                                                alt="." />

                                                            <img v-if="hCommande.etat_commande == 'CANCELED'"
                                                                src="https://img.icons8.com/windows/22/198ae3/close-window.png"
                                                                alt="." />

                                                            <img v-if="hCommande.etat_commande == 'COMMENTAIRE'"
                                                                src="https://img.icons8.com/windows/22/198ae3/messaging-.png"
                                                                alt="." />


                                                            <img v-if="hCommande.etat_commande == 'RETURNEDLV' || hCommande.etat_commande == 'RETURNEDAG' || hCommande.etat_commande == 'RETURNEDRR'
                                                                || hCommande.etat_commande == 'RETURNED' || hCommande.etat_commande == 'RETURNEDEV'

                                                            "
                                                                src="https://img.icons8.com/windows/22/198ae3/circled-left-2.png"
                                                                alt="." />





                                                        </span></a>
                                                </div>
                                                <div class="tl-content">
                                                    <div class="etat_commande">
                                                        <b class="badge badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande == 'CREATED'">{{
                                                                hCommande.etat_commande
                                                            }}</b>
                                                        <b class="badge badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande == 'CONFIRMED'">{{
                                                                hCommande.etat_commande
                                                            }}</b>
                                                        <b class="badge badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande == 'DMSUIVIE'">{{
                                                                hCommande.etat_commande
                                                            }}</b>
                                                        <b class="badge badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande == 'PROCESSING'">{{
                                                                hCommande.etat_commande
                                                            }}</b>
                                                        <b class="badge badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande == 'PICKUP'">Pending Pickup</b>
                                                        <b class="badge badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande == 'HOME'">INHOUSE</b>
                                                        <b class="badge badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande == 'INHOUSE'">{{
                                                                hCommande.etat_commande
                                                            }}</b>

                                                        <b class="badge badge badge-gradient-primary"
                                                            v-if="hCommande.etat_commande == 'ENROUTE'">{{
                                                                hCommande.etat_commande
                                                            }}</b>
                                                        <b class="badge badge badge-gradient-primary"
                                                            v-if="hCommande.etat_commande == 'RAMASSER'">PICKUP</b>

                                                        <b class="badge badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande == 'TRANSIT'">{{
                                                                hCommande.etat_commande
                                                            }}</b>
                                                        <b class="badge badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande == 'REPORTED'">{{
                                                                hCommande.etat_commande
                                                            }}</b>
                                                        <b class="badge badge badge-gradient-success"
                                                            v-if="hCommande.etat_commande == 'DELIVERED'">{{
                                                                hCommande.etat_commande
                                                            }}</b>
                                                        <b class="badge badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande == 'ASSIGN'">{{
                                                                hCommande.etat_commande
                                                            }}</b>
                                                        <b class="badge badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande == 'RETURNED'">{{
                                                                hCommande.etat_commande
                                                            }}</b>
                                                        <b class="badge badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande == 'RETURNEDLV'">RETURNED</b>
                                                        <b class="badge badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande == 'RETURNEDAG'">RETURNED</b>
                                                        <b class="badge badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande == 'RETURNEDEV'">RETURNED</b>
                                                        <b class="badge badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande == 'RETURNEDRR'">RETURNED</b>
                                                        <b class="badge badge badge-gradient-danger"
                                                            v-if="hCommande.etat_commande == 'CANCELED'">{{
                                                                hCommande.etat_commande
                                                            }}</b>
                                                        <b class="badge badge badge-gradient-danger"
                                                            v-if="hCommande.etat_commande == 'ARCHIVED'">{{
                                                                hCommande.etat_commande
                                                            }}</b>

                                                        <b class="badge badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande == 'NOREPONSE'">Sans
                                                            Reponse</b>
                                                        <b class="badge badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande == 'ANNULER'">Annuler
                                                        </b>
                                                        <b class="badge badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande == 'ANNULER_CL'">Annuler
                                                        </b>
                                                        <b class="badge badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande == 'RELANCER'">Relaunch</b>
                                                        <b class="badge badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande == 'CHANGERPRIX'">Price change
                                                        </b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'NOREPONSE'">
                                                        Colis sans reponse By:
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'CANCELED'">
                                                        Commande annuler By:
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.commentaire_commande && hCommande.etat_commande == 'COMMENTAIRE'">
                                                        <span class="commentaireCommande">{{
                                                            hCommande.commentaire_commande
                                                        }}</span>
                                                        <br>
                                                        By:
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.commentaire_commande && hCommande.etat_commande != 'COMMENTAIRE'">
                                                        <span class="commentaireCommande">{{
                                                            hCommande.commentaire_commande
                                                        }}</span>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'callLog'">

                                                        <span class="commentaireCommande"
                                                            v-if="hCommande.typeCall == 'CallType.outgoing' && hCommande.durationCall != '0'"
                                                            style="background: #198754;">
                                                            Outgoing Call
                                                        </span>
                                                        <span class="commentaireCommande"
                                                            v-else-if="hCommande.typeCall == 'CallType.outgoing' && hCommande.durationCall == '0'"
                                                            style="background: #dc3545;">
                                                            Missed Call
                                                        </span>

                                                        <span class="commentaireCommande"
                                                            v-else-if="hCommande.typeCall == 'CallType.missed'"
                                                            style="background: #dc3545;">
                                                            Missed Call
                                                        </span>

                                                        <span class="commentaireCommande"
                                                            v-else-if="hCommande.typeCall == 'CallType.incoming'">
                                                            Incoming Call
                                                        </span>
                                                        <br>

                                                        <b
                                                            v-if="hCommande.typeCall == 'CallType.outgoing' && hCommande.durationCall != '0'">{{
                                                                hCommande.username }} Call {{ hCommande.nom_client_commande }}
                                                            ({{ moment.utc(hCommande.durationCall * 1000).format('mm:ss')
                                                            }})</b>


                                                        <b
                                                            v-if="hCommande.typeCall == 'CallType.missed' || (hCommande.typeCall == 'CallType.outgoing' && hCommande.durationCall == '0')">
                                                            {{ hCommande.nom_client_commande }} Missed call from {{
                                                                hCommande.username }}
                                                        </b>
                                                        <b v-if="hCommande.typeCall == 'CallType.incoming'">
                                                            {{ hCommande.nom_client_commande }} Call {{ hCommande.username
                                                            }}
                                                            ({{ moment.utc(hCommande.durationCall * 1000).format('mm:ss')
                                                            }})</b>




                                                        <br>
                                                        At
                                                        <b>{{ moment(hCommande.dateCall, 'x').format("MM/DD/YYYY hh:mm:ss")
                                                        }}
                                                        </b>




                                                    </div>






                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'ENROUTE'">
                                                        Package sent to
                                                        <b>{{ hCommande.nom_ville }}</b>
                                                        By
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'RAMASSER'">
                                                        Package PickUp
                                                        By
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>


                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'ASSIGN'">
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'DMSUIVIE'">
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'INHOUSE'">
                                                        Package is ready to send
                                                        By
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'HOME'">
                                                        package is in Hub
                                                        <b>{{ hCommande.nom_ville }}</b>
                                                        <!-- with
                                                        <b>{{ hCommande.username }}</b> -->
                                                        <br />
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'PROCESSING'">
                                                        processe By:

                                                        <b>{{ hCommande.clientUsername }}</b>
                                                        <br />
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'PICKUP'">
                                                        Request Pickup By:

                                                        <b v-if="hCommande.clientUsername">{{ hCommande.clientUsername
                                                        }}</b>
                                                        <b v-else>{{ hCommande.username }}</b>
                                                        <br />
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'CONFIRMED'">
                                                        Confirmer By:
                                                        <b>{{ hCommande.clientUsername }}</b>
                                                        <br />
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'CREATED'">
                                                        Create By:
                                                        <b>
                                                            {{ hCommande.clientUsername }} </b><br />At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'ANNULER'">
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'ANNULER_CL'">
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'CHANGERPRIX'">
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'RELANCER'">
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'REPORTED'">
                                                        Reporter By
                                                        <b>{{ hCommande.username }}</b>
                                                        To
                                                        <b>{{ hCommande.reported_date }}
                                                        </b>
                                                        <br />
                                                        At
                                                        {{ hCommande.updated_at }}
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'TRANSIT'">
                                                        Colis Prêt To Delivered By
                                                        <b>{{ hCommande.username }}</b>

                                                        <b>{{ hCommande.reported_date }}
                                                        </b>
                                                        <br />
                                                        At
                                                        {{ hCommande.updated_at }}
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'DELIVERED'">
                                                        Delivered By
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'RETURNEDLV'">
                                                        By
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'RETURNEDAG'">
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'RETURNEDEV'">
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'RETURNEDRR'">
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'RETURNED'">
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tl-item" v-for="hCommande in historiqueFacture">
                                                <div class="tl-dot">
                                                    <a class="tl-author" href="#" data-abc="true"><span
                                                            class="w-32 avatar circle gd-info">
                                                            <img v-if="hCommande.statut_facture == 'NOTPAID'"
                                                                src="https://img.icons8.com/windows/22/198ae3/estimate.png"
                                                                alt="." />
                                                            <img v-if="hCommande.statut_facture == 'PAID'"
                                                                src="https://img.icons8.com/windows/22/198ae3/money-bag.png"
                                                                alt="." />
                                                        </span>
                                                    </a>
                                                </div>
                                                <div class="tl-content">
                                                    <div class="etat_commande">
                                                        <b class="badge badge badge-gradient-danger" v-if="
                                                            hCommande.statut_facture == 'NOTPAID'"> Invoiced
                                                        </b>
                                                        <b class="badge badge badge-gradient-success" v-if="
                                                            hCommande.statut_facture == 'PAID'">{{
        hCommande.statut_facture }}</b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-1" v-if="
                                                        hCommande.statut_facture == 'NOTPAID'">
                                                        Invoiced By:
                                                        <b v-if="
                                                            hCommande.username ==
                                                            null
                                                        ">Admin</b>
                                                        <b v-else-if="
                                                            hCommande.username !=
                                                            null">{{ hCommande.username }}</b>
                                                        <br />At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.statut_facture == 'PAID'">Payé By:
                                                        <b v-if="hCommande.username == null">Admin</b>
                                                        <b v-else-if="hCommande.username != null">{{
                                                            hCommande.username
                                                        }}</b>
                                                        <br />
                                                        At
                                                        <b>{{
                                                            hCommande.updated_at
                                                        }}
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
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="reclamationCommande" tabindex="-1" data-bs-focus="false"
            aria-labelledby="reclamationCommande" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reclamationCommande">
                            Change Status
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="page-content page-container" id="page-content">
                            <div class="p-3">
                                <div class="form-group row">
                                    <div class="col-8">
                                        <b><i class="fas fa-box-open"></i>
                                            {{ formData.id_commande }}</b>
                                    </div>
                                    <div class="col-3">
                                        <b><i class="fas fa-dollar-sign"></i>
                                            {{ formData.prix_commande }} {{ $t('message.Dhs') }}</b>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <b><i class="fas fa-user-alt"></i>
                                        {{ formData.nom_client_commande }}</b>
                                </div>
                                <div class="form-group row">
                                    <div class="alert alert-danger" role="alert" v-if="nom_err">
                                        {{ nom_err[0] }}
                                    </div>
                                </div>
                                <div class="form-group row"
                                    v-if="formData.etat_commande == 'RELANCER' || formData.etat_commande == 'REPORTED' || formData.etat_commande == 'RAMASSER' || formData.etat_commande == 'PICKUP' || formData.etat_commande == 'DMSUIVIE' || formData.etat_commande == 'ENROUTE' || formData.etat_commande == 'TRANSIT' || formData.etat_commande == 'INHOUSE' || formData.etat_commande == 'HOME' || formData.etat_commande == 'ASSIGN'">
                                    <div class="col-5">
                                        <button type="button" class="btn btn-danger" @click.prevent="
                                            editStatut('ANNULER')
                                        ">
                                            <i class="fas fa-check"></i> Annuler
                                        </button>
                                    </div>
                                    <div class="col-7">
                                        <button type="button" class="btn btn-info" @click.prevent="
                                            editStatut('CHANGERPRIX')
                                        ">
                                            <i class="fas fa-truck"></i> {{ $t('message.Change_Price') }}
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group row" v-if="relaunch">
                                    <div class="col-6" v-if="formData.etat_commande == 'TRANSIT' || formData.etat_commande == 'NOREPONSE' || formData.etat_commande == 'DMSUIVIE' || formData.etat_commande == 'REPORTED'
                                        || formData.etat_commande == 'HOME'
                                    ">
                                        <button type="button" class="btn btn-warning" @click.prevent="
                                            editStatut('RELANCER')
                                        ">
                                            <i class="fas fa-clock"></i>
                                            {{ $t('message.Relaunch') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btn_cancel" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
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
    name: "SuivieClientComponent",
    components: { TimeAgo },
    data() {
        return {
            token: localStorage.getItem("token"),
            locale: localStorage.getItem("locale"),
            selected: [],
            edit: false,
            articles: {},
            ListArticles: {},
            errors: {},
            nbrCommande: '',
            commandes: {
                data: "",
                current_page: 1,
                last_page: 1,
            },
            selected_type: false,
            nomFile: "",
            nom_err: "",
            formData: {
                ville_client_commande: "",
                nom_client_commande: "",
                adresse_client_commande: "",
                telephone_client_commande: "",
                prix_commande: "",
                selected_commande: "",
                fichierCommande: "",
                quantite_article: 0,
                additional_commentaire: "",
                type_autorisation: "",
                prix_livraison_final: "",
                store: "",
            },
            villes: [],
            historiqueCommande: {},
            livreur: '',
            options: [
            { text:this.FirstLowerRestUper(this.$t('message.Order_id')), value: "id_commande" },
                    { text: this.FirstLowerRestUper(this.$t('message.Name')), value: "nom_client_commande" },
                    { text: this.FirstLowerRestUper(this.$t('message.Phone_Number')), value: "telephone_client_commande" },
                    { text: this.FirstLowerRestUper(this.$t('message.Price')), value: "prix_commande" },
                    { text: this.FirstLowerRestUper(this.$t('message.City')), value: "nom_ville" },
            ],
            formDataCherche: {
                selected_option: "id_commande",
                valeur_recherche: "",
            },
            responsable: "",
            options3: [
            { text: '1-20 '+this.$t('message.Items'), value: '20' },
                { text: '1-50 '+this.$t('message.Items'), value: '50' },
                { text: '1-150 '+this.$t('message.Items'), value: '150' },
                { text: '1-200 '+this.$t('message.Items'), value: '200' },
            ],
            formDataCherche3: {
                selected_option3: "20",
            },
            btn_confirme: false,
            historiqueFacture: [],
            stores: [],
            relaunch: '',
        };
    },
    watch: {
        // whenever question changes, this function will run
        selected_type(newQuestion, oldQuestion) {
            this.nom_err = "";

            this.formData = {
                ville_client_commande: "",
                nom_client_commande: "",
                adresse_client_commande: "",
                telephone_client_commande: "",
                prix_commande: "",
                selected_commande: "",
                fichierCommande: "",
                quantite_article: 0,
                additional_commentaire: "",
                type_autorisation: "",
            };
        },

        selected(neww, oldd) {
            if (Object.keys(neww).length > 0) {
                this.btn_confirme = true;
            } else {
                this.btn_confirme = false;
            }
        },
    },
    methods: {
        FirstLowerRestUper(world) {
            return world[0].toUpperCase() + world.slice(1).toLowerCase();
        },
        async refreshData() {
            await this.initialiserFormData();
            this.classifierCommande(this.formDataCherche3.selected_option3);
        },

        async showDetails(id) {
            this.$vs.loading({ color: "#22c22b" });
            await axios
                .get("/api/Commande/" + id)
                .then((res) => {
                    this.commande = res.data.data;
                    var text =
                        "<table class='table table-borderless' style='text-align: left;'>" +
                        "<tr><td colspan='2'><b>Original</b></td></tr>";

                    if (res.data.data.nom_store != null) {
                        text +=
                            "</td></tr><tr><td>Store :<b> </b></td><td>" +
                            this.commande.nom_store +
                            "</td></tr>";
                    } else {
                        text +=
                            "</td></tr><tr><td>Store :<b> </b></td><td>" +
                            this.commande.company +
                            "</td></tr>";
                    }

                    if (res.data.data.nom != null && res.data.data.prenom != null) {
                        text +=
                            "</td></tr><tr><td>Responsable :<b> </b></td><td>" +
                            this.commande.nom + ' ' + this.commande.prenom +
                            "</td></tr>";
                        text +=
                            "</td></tr><tr><td>Phone :<b> </b></td><td>" +
                            this.commande.telephone_responsable +
                            "</td></tr>";
                    }

                    text +=
                        "<tr><td colspan='2'><b>Destination</b></td></tr>" +
                        "</td></tr><tr><td>ORDER N°</td><td>" +
                        this.commande.id_commande +
                        "</td></tr>";
                    text +=
                        "</td></tr><tr><td>Client :<b> </b></td><td>" +
                        this.commande.nom_client_commande
                    "</td></tr>";

                    text +=
                        "</td></tr><tr><td>City :<b> </b></td><td>" +
                        this.commande.nom_ville +
                        "</td></tr>";
                    text +=
                        "</td></tr><tr><td>Adresse :<b> </b></td><td>" +
                        this.commande.adresse_client_commande +
                        "</td></tr>";
                    text +=
                        "</td></tr><tr><td>Phone :<b> </b></td><td>" +
                        this.commande.telephone_client_commande +
                        "</td></tr>";
                    text +=
                        "</td></tr><tr><td>Price :<b> </b></td><td>" +
                        this.commande.prix_commande +
                        $t('message.Dhs') + "</td></tr>";



                    if (res.data.data.additional_commentaire != null) {
                        text +=
                            "<tr><td>Commentaire :</td><td>" +
                            this.commande.additional_commentaire;
                    }
                    if (res.data.data.nom_article != null) {
                        text +=
                            "</td></tr><tr><td><b><i class='fa fa-shopping-bag'></i> Article :</b></td><td><b><i class='fa fa-sort'></i> Quantite :</b></td></tr>";
                        for (
                            let i = 0;
                            i < Object.keys(res.data.data2).length;
                            i++
                        ) {
                            text +=

                                "<tr><td>" +
                                res.data.data2[i].nom_article +
                                "</td><td>" +
                                res.data.data2[i].quantite +
                                "</td></tr>";
                        }
                    }
                    Swal.fire({
                        title: "<h5><b>Package informations</b></h5>",
                        html: text,
                        showCancelButton: false,
                    });
                })
                .catch((error) => console.log(res))
                .finally(() => this.$vs.loading.close());
        },
        async chercher(count_nbr) {
            this.$vs.loading({ color: "#22c16b" });
            setTimeout(async () => {
                if (count_nbr > 1) {
                    await axios
                        .post(
                            "/api/rechercheCommandeSuivie?page=" +
                            this.commandes.current_page +
                            "&count_nbr=" +
                            count_nbr,
                            this.formDataCherche
                        )
                        .then((res) => {
                            this.commandes = res.data.data;
                        })
                        .catch((error) => console.log(res))
                        .finally(() => this.$vs.loading.close());
                } else {
                    await axios
                        .post(
                            "/api/rechercheCommandeSuivie?page=" +
                            this.commandes.current_page +
                            "&count_nbr=20",
                            this.formDataCherche
                        )
                        .then((res) => {
                            this.commandes = res.data.data;
                        })
                        .catch((error) => console.log(res))
                        .finally(() => this.$vs.loading.close());
                }
            }, 200);
        },
        async classifierCommande(count_nbr) {
            this.$vs.loading({ color: "#22c16b" });
            setTimeout(async () => {
                if (count_nbr > 1) {
                    await axios
                        .post(
                            "/api/suivieCommande?page=" +
                            this.commandes.current_page +
                            "&count_nbr=" +
                            count_nbr,
                            this.formDataCherche2
                        )
                        .then((res) => {
                            this.commandes = res.data.data;
                        })
                        .catch((error) => console.log(res))
                        .finally(() => this.$vs.loading.close());
                } else {
                    await axios
                        .post(
                            "/api/suivieCommande?page=" +
                            this.commandes.current_page +
                            "&count_nbr=20",
                            this.formDataCherche2
                        )
                        .then((res) => {
                            this.commandes = res.data.data;
                        })
                        .catch((error) => console.log(res))
                        .finally(() => this.$vs.loading.close());
                }
                this.nbrCommande = this.commandes.to;
            }, 200);
        },
        copyCommande(tr) {
            navigator.clipboard.writeText(
                tr.id_commande +
                " " +
                tr.nom_client_commande +
                " " +
                tr.telephone_client_commande
            );

            this.$vs.notify({
                title: `Commande Copie`,
                text: `ID: ${tr.id_commande}`,
                color: "success",
                position: "top-right",
            });
        },
        whatsapp(telephone, nom) {
            const number = telephone;

            const text = "السلام عليكم ورحمة الله " + nom;

            let url =
                "https://api.whatsapp.com/send/?phone=212" +
                telephone +
                "&text=" +
                text +
                "&type=phone_number";
            window.open(url);
        },
        async getCommande(id) {
            // this.formData.articles
            await axios
                .get("/api/Commande/" + id)
                .then((res) => {
                    this.formData = res.data.data;
                    this.formData.articles = res.data.data2;
                    this.ListArticles = res.data.data2;
                })
                .catch((error) => console.log(res));
            if (this.formData.type_autorisation == "allow") {
                this.formData.type_autorisation = false;
            } else if (this.formData.type_autorisation == "deny") {
                this.formData.type_autorisation = true;
            }

            this.formData.ville_client_commande = {
                id: this.formData.ville_client_commande,
                ville: this.formData.nom_ville,
            };
            // this.formData.id_article = { 'id': this.formData.id_article, 'nom_article': this.formData.nom_article }
        },
        async getVilles() {
            await axios
                .get("/api/Villes")
                .then((res) => {
                    this.villes = res.data.data;
                })
                .catch((error) => console.log(res));
        },

        async getHistoriqueCommande(id) {
            this.$vs.loading({ color: "#22c16b" });
            this.historiqueCommande = [];
            this.historiqueFacture = [];
            this.responsable = "";

            await axios
                .get("/api/historiqueCommande/" + id)
                .then((res) => {
                    this.historiqueCommande = res.data.data;
                    this.historiqueFacture = res.data.data2;
                    this.responsable = res.data.responsable;
                    this.livreur = res.data.livreur;
                })
                .catch((error) => console.log(res))
                .finally(() => this.$vs.loading.close());
            $('#showCommande').modal('show');
        },

        initialiserFormData() {
            (this.ListArticles = []),
                (this.nom_err = ""),
                (this.edit = false),
                (this.errors = {}),
                (this.commandes = {
                    data: "",
                    current_page: 1,
                    last_page: 1,
                }),
                (this.articles = []),
                (this.selected_type = false),
                (this.nomFile = ""),
                (this.stock_quantite = ""),
                (this.formData = {
                    ville_client_commande: "",
                    nom_client_commande: "",
                    adresse_client_commande: "",
                    telephone_client_commande: "",
                    prix_commande: "",
                    selected_commande: "",
                    fichierCommande: "",
                    quantite_article: "",
                    additional_commentaire: "",
                    type_autorisation: "",
                    prix_livraison_final: "",
                    store: "",
                }),
                (this.historiqueCommande = {}),
                (this.options = [
                { text:this.FirstLowerRestUper(this.$t('message.Order_id')), value: "id_commande" },
                    { text: this.FirstLowerRestUper(this.$t('message.Name')), value: "nom_client_commande" },
                    { text: this.FirstLowerRestUper(this.$t('message.Phone_Number')), value: "telephone_client_commande" },
                    { text: this.FirstLowerRestUper(this.$t('message.Price')), value: "prix_commande" },
                    { text: this.FirstLowerRestUper(this.$t('message.City')), value: "nom_ville" },
                ]),
                (this.formDataCherche = {
                    selected_option: "id_commande",
                    valeur_recherche: "",

                }),

                (this.responsable = ""),
                (this.options3 = [
                    { text: "1-20 items", value: "20" },
                    { text: "1-50 items", value: "50" },
                    { text: "1-150 items", value: "150" },
                    { text: "1-200 items", value: "200" },
                ]),
                (this.formDataCherche3 = {
                    selected_option3: "20",
                }),
                (this.btn_confirme = false);
        },
        sweetAlert(typeMessage, Message) {
            Swal.fire({
                position: "center",
                icon: typeMessage,
                title: Message,
                showConfirmButton: false,
                timer: 6000,
            });
        },

        async reclamationCommande(tr) {
            this.formData = tr;
            this.nom_err = '';
            await axios
                .get("/api/verificationRelaunch/" + tr.id_commande)
                .then((res) => {
                    if (res.data.message == 'Order not relaunched') {
                        this.relaunch = true;
                    } else if (res.data.message == 'Order already relaunch') {
                        this.relaunch = false;
                    }
                })
                .catch((error) => console.log(res));
            $("#reclamationCommande").modal("show");
        },
        editStatut(button) {
            if (button == "ANNULER") {
                this.formData.statut = button;
                Swal.fire({
                    title: this.$t('message.Are_you_sure'),
                    text: "Vous voulez annuler la commande!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: this.$t('message.Yes_I_Confirme'),
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.editStatutAxios(this.formData);
                        $("#reclamationCommande").modal("hide");
                    }
                });
            } else if (button == "RELANCER") {
                this.formData.statut = button;

                Swal.fire({
                    title: this.$t('message.Are_you_sure'),
                    text: "The command will Relaunch!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: this.$t('message.Yes_I_Confirme'),
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.editStatutAxios(this.formData);
                        $("#reclamationCommande").modal("hide");
                    }
                });
            } else if (button == "CHANGERPRIX") {
                this.formData.statut = button;
                Swal.fire({
                    title: this.$t('message.Are_you_sure'),
                    text: this.$t('message.You_want_to_change_the_price'),
                    input: "number",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: this.$t('message.Yes_I_Confirme'),
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.formData.statut = button;
                        this.formData.commentaire_commande = result.value;
                        this.editStatutAxios(this.formData);
                        this.initialiserFormData();
                        $("#reclamationCommande").modal("hide");
                    }
                });
            }
        },
        async editStatutAxios(formData) {
            await axios
                .post("/api/changeStatutCommande", formData)
                .then((res) => {
                    this.message = res.data.message;
                })
                .catch((error) => console.log(res));
            if (this.message == "Erreur") {
                var statut_icon = "error";
            } else if (this.message == "Successfully") {
                var statut_icon = "success";
            } else {
                var statut_icon = "warning";
            }
            Swal.fire({
                position: "center",
                icon: statut_icon,
                title: this.message,
                showConfirmButton: false,
                timer: 4000,
            });
            this.classifierCommande(0);

            this.initialiserFormData();
        },
    },
    async beforeMount() {
        axios.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;
        this.$root.$refs.SuivieClientComponent = this;
    },
    async mounted() { },

    async created() {
        $(window).on("load", function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

    },
};
</script>