<template>
    <div class="content-wrapper-customise">
        <div class="page-header-dashboard">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span>{{ $t('message.Orders') }} <b class="badge badge badge-gradient-secondary">{{ nbrCommande }}</b>
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row head-table">
                            <div id="btn-top-package">
                                <button type="button" class="btn btn-success float-end" data-bs-toggle="modal"
                                    data-bs-target="#ajouterCommande" data-bs-whatever="@mdo"
                                    @click="checkCommandeStock(false)">
                                    <i class="fa fa-file-excel"></i> {{ $t('message.MultiOrders') }}
                                </button>

                                <button type="button" style="margin: 0 13px" class="btn btn-primary float-end"
                                    data-bs-toggle="modal" data-bs-target="#ajouterCommandeStock" data-bs-whatever="@mdo"
                                    @click="checkCommandeStock(false)">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                    {{ $t('message.New_Order') }}
                                </button>

                                <button style="margin: 0 13px" type="button" class="btn btn-secondary float-end"
                                    @click.prevent="pickupCommande()">
                                    {{ $t('message.Pickup') }}
                                    <i class="fa fa-phone" aria-hidden="true"></i>
                                </button>
                                <button style="margin: 0 13px" type="button" class="btn btn-primary float-end"
                                    @click.prevent="confirmeCommande" v-if="btn_confirme">
                                    {{ $t('message.Confirme') }}
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="chercher">
                                <!-- Example single danger button -->
                                <vs-select class="selectExample" v-model="formDataCherche3.selected_option3"
                                    @change="classifierCommande(formDataCherche3.selected_option3)">
                                    <vs-select-item :key="index" :value="item.value" :text="item.text"
                                        v-for="(item, index) in options3" />
                                </vs-select>


                                <vs-select class="selectExample" v-model="formDataCherche2.selected_option2"
                                    @change="classifierCommande(formDataCherche3.selected_option3)"
                                    style="width: 348px !important;">

                                    <vs-select-item :key="index" :value="item.value" :text="item.text"
                                        v-for="(item, index) in options2" />
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
                                            <span class="noreponse" v-if="tr.etat_commande == 'RETURNEDLV'">Return sent
                                                to agency</span>
                                            <span class="noreponse" v-if="tr.etat_commande == 'RETURNEDAG'">Return
                                                received by agency</span>
                                            <span class="noreponse" v-if="tr.etat_commande == 'RETURNEDEV'">Return
                                                send</span>
                                            <span class="noreponse" v-if="tr.etat_commande == 'RETURNEDRR'">Return
                                                received by Ramasseur</span>
                                            <span class="noreponse" v-if="tr.etat_commande == 'RETURNED'">Return send to
                                                hometown</span>
                                        </div>
                                        <span><time-ago :datetime="tr.updated_at" long></time-ago>
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
                                    <vs-td :data="tr.ville_client_commande">
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
                        <vs-pagination @input="classifierCommande(formDataCherche3.selected_option3)" :max="9"
                            :total="commandes.last_page" v-model="commandes.current_page" prev-icon="arrow_back"
                            next-icon="arrow_forward"></vs-pagination>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ajouterCommandeStock" tabindex="-1" aria-labelledby="ajouterCommandeStock"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ajouterCommandeStock" v-if="!edit">
                            {{ $t('message.Add_Order') }}
                        </h5>

                        <h5 class="modal-title" id="ajouterCommandeStock" v-if="edit">
                            {{ $t('message.Edit_Order') }}
                        </h5>
                        <div class="stockChange" id="stockButton" v-if="Client.stock == 1">
                            <label class="switch">
                                <input type="checkbox" v-model="selected_type" />
                                <span class="slider round"></span>
                            </label>
                            <span style="font-size: 18px">{{ $t('message.Stock') }}</span>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="alert alert-danger" role="alert" v-if="nom_err">
                                {{ nom_err[0] }}
                            </div>

                            <div class="row" v-if="selected_type">
                                <div class="row">
                                    <div class="col-7">
                                        <label for="ville_client"><b class="badge badge badge-gradient-info">{{
                                            $t('message.Article') }}</b></label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-12">
                                            <v-select placeholder="Please select an item" multiple v-model="ListArticles"
                                                name="article" :options="articles" label="nom_article" index="id" @input="">
                                            </v-select>
                                        </div>
                                        <div class="row" id="qnt" v-for="x in ListArticles">
                                            <div class="col-6 quantite_colm">
                                                <label for="quantite_article"><b class="badge badge badge-gradient-info">{{
                                                    $t('message.Quantity_available') }}
                                                        :<span v-if="x.qnt">{{ x.qnt }}</span><span
                                                            v-else>0</span></b></label>
                                            </div>
                                            <div class="col-6">
                                                <input type="number" min="1" :max="x.stock_article" v-model="x.quantite" />

                                                <!-- <vs-input-number name="quantite_article" :bind="x.quantite" color="success"
                                                            min="1" :max="x.stock_article" v-model.number="x.quantite" /> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ville_client">{{ $t('message.City') }}</label>

                            <v-select placeholder="Veuillez-vous selectionner une ville"
                                v-model="formData.ville_client_commande" name="ville_client" :options="villes" label="ville"
                                index="id" />
                        </div>
                        <div class="form-group" v-if="Object.keys(stores).length > 0">
                            <label for="ville_client">{{ $t('message.Store') }}</label>
                            <v-select placeholder="Veuillez-vous selectionner un Store" v-model="formData.store"
                                name="store" :options="stores" label="nom_store" index="id" />
                        </div>
                        <div class="form-group">
                            <label for="nom_client">{{ $t('message.Name') }}</label>

                            <input type="text" class="form-control" id="nom_client" v-model="formData.nom_client_commande"
                                name="nom_client" />
                        </div>
                        <div class="form-group">
                            <label for="adresse_client">{{ $t('message.Address') }}</label>

                            <input type="text" class="form-control" id="adresse_client"
                                v-model="formData.adresse_client_commande" name="adresse_client" />
                        </div>
                        <div class="form-group">
                            <label for="telephone_client">{{ $t('message.Phone_Number') }}</label>

                            <input type="text" class="form-control" id="telephone_client"
                                v-model="formData.telephone_client_commande" name="telephone_client"
                                @change="checkBlackList" />
                            <p class="text-danger" id="text-blacklist"></p>
                        </div>
                        <div class="form-group">
                            <label for="prix_commande">{{ $t('message.Price') }}</label>

                            <input type="text" class="form-control" id="prix_commande" v-model="formData.prix_commande"
                                name="prix_commande" />
                        </div>
                        <div class="form-group">
                            <label for="additional_commentaire">{{ $t('message.Comment') }}</label>

                            <input type="text" class="form-control" id="additional_commentaire"
                                v-model="formData.additional_commentaire" name="additional_commentaire" />
                        </div>
                        <div class="form-group">
                            <!-- <input class="form-check-input" type="checkbox" id="type_autorisation"
                                            v-model="formData.type_autorisation" name="type_autorisation"> -->
                            <vs-checkbox color="danger" v-model="formData.type_autorisation" id="type_autorisation"
                                name="type_autorisation">{{ $t('message.Not_allowed_to_open_the_package') }}</vs-checkbox>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" v-if="!edit" @click.prevent="addCommandeStock()">
                            {{ $t('message.Add_Order') }}
                        </button>
                        <button type="button" class="btn btn-primary" v-if="edit" @click.prevent="updateCommande()">
                            {{ $t('message.Edit_Order') }}
                        </button>
                        <button type="button" id="btn_cancel" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ajouterCommande" tabindex="-1" aria-labelledby="ajouterCommande" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ajouterCommande" v-if="!edit">
                            {{ $t('message.Add_Order') }}
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" role="alert" v-if="nom_err">
                            {{ nom_err[0] }}
                        </div>
                        <div class="form-group" v-if="Object.keys(stores).length > 0">
                            <label for="ville_client">{{ $t('message.Store') }}</label>
                            <v-select placeholder="Veuillez-vous selectionner un Store" v-model="formData.store"
                                name="store" :options="stores" label="nom_store" index="id" />
                        </div>
                        <div class="form-group">


                            <label>{{ $t('message.Order_file') }} (Excel)</label>
                            <input type="file" class="file-upload-default" id="fichierCommande" name="fichierCommande"
                                @change="onFichierExcelChange" />
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" disabled :placeholder="nomFile" />
                                <span class="input-group-append"> </span>
                            </div>
                            <!-- <vs-upload @on-success="successUpload" :headers="{'Authorization' : `Bearer ${token}`}" limit="1" action="api/Commande" fileName="fichierCommande" :data="formData"/> -->

                            <label class="file-upload-browse btn btn-gradient-primary" for="fichierCommande"><i
                                    class="fa fa-upload" aria-hidden="true"></i>
                                {{ $t('message.Upload') }}</label>

                            <div class="dowload_model">
                                <vs-button color="danger" href="model/Model.xlsx" type="filled"><i class="fa fa-download"
                                        aria-hidden="true"></i>
                                    Model Excel</vs-button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" v-if="!edit" @click.prevent="addCommande('excel')">
                            {{ $t('message.Add_Order') }}
                        </button>
                        <button type="button" id="btn_cancel" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="showCommande" tabindex="-1" aria-labelledby="showCommande" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="showCommande">
                            {{ $t('message.Order_History') }}
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
                                                        <b class="badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande === 'CREATED'">{{
                                                                $t('message.CREATED') }}</b>
                                                        <b class="badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande === 'CONFIRMED'">{{
                                                                $t('message.CONFIRMED') }}</b>
                                                        <b class="badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande === 'DMSUIVIE'">{{
                                                                $t('message.DMSUIVIE') }}</b>
                                                        <b class="badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande === 'PROCESSING'">{{
                                                                $t('message.PROCESSING') }}</b>
                                                        <b class="badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande === 'PICKUP'">{{
                                                                $t('message.PICKUP') }}</b>
                                                        <b class="badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande === 'INHOUSE'">{{
                                                                $t('message.INHOUSE') }}</b>
                                                        <b class="badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande === 'HOME'">{{ $t('message.HOME')
                                                            }}</b>
                                                        <b class="badge badge-gradient-primary"
                                                            v-if="hCommande.etat_commande === 'ENROUTE'">{{
                                                                $t('message.ENROUTE') }}</b>
                                                        <b class="badge badge-gradient-primary"
                                                            v-if="hCommande.etat_commande === 'RAMASSER'">{{
                                                                $t('message.RAMASSER') }}</b>
                                                        <b class="badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande === 'TRANSIT'">{{
                                                                $t('message.TRANSIT') }}</b>
                                                        <b class="badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande === 'REPORTED'">{{
                                                                $t('message.REPORTED') }}</b>
                                                        <b class="badge badge-gradient-success"
                                                            v-if="hCommande.etat_commande === 'DELIVERED'">{{
                                                                $t('message.DELIVERED') }}</b>
                                                        <b class="badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande === 'ASSIGN'">{{
                                                                $t('message.ASSIGN') }}</b>
                                                        <b class="badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande === 'RETURNED'">{{
                                                                $t('message.RETURNED') }}</b>
                                                        <b class="badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande === 'RETURNEDLV'">{{
                                                                $t('message.RETURNEDLV') }}</b>
                                                        <b class="badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande === 'RETURNEDAG'">{{
                                                                $t('message.RETURNEDAG') }}</b>
                                                        <b class="badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande === 'RETURNEDEV'">{{
                                                                $t('message.RETURNEDEV') }}</b>
                                                        <b class="badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande === 'RETURNEDRR'">{{
                                                                $t('message.RETURNEDRR') }}</b>
                                                        <b class="badge badge-gradient-danger"
                                                            v-if="hCommande.etat_commande === 'CANCELED'">{{
                                                                $t('message.CANCELED') }}</b>
                                                        <b class="badge badge-gradient-danger"
                                                            v-if="hCommande.etat_commande === 'ARCHIVED'">{{
                                                                $t('message.ARCHIVED') }}</b>
                                                        <b class="badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande === 'NOREPONSE'">{{
                                                                $t('message.NOREPONSE') }}</b>
                                                        <b class="badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande === 'ANNULER'">{{
                                                                $t('message.ANNULER') }}</b>
                                                        <b class="badge badge-gradient-warning"
                                                            v-if="hCommande.etat_commande === 'ANNULER_CL'">{{
                                                                $t('message.ANNULER_CL') }}</b>

                                                        <b class="badge badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande == 'RELANCER'">{{
                                                                $t('message.RELANCE') }}</b>
                                                        <b class="badge badge badge-gradient-info"
                                                            v-if="hCommande.etat_commande == 'CHANGERPRIX'">{{
                                                                $t('message.CHANGERPRIX') }}
                                                        </b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'NOREPONSE'">
                                                        {{ $t('message.Package_without_response') }} {{ $t('message.By') }}
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'CANCELED'">
                                                        {{ $t('message.Order_canceled') }} {{ $t('message.By') }}:
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.commentaire_commande && hCommande.etat_commande == 'COMMENTAIRE'">
                                                        <span class="commentaireCommande">{{
                                                            hCommande.commentaire_commande
                                                        }}</span>
                                                        <br>
                                                        {{ $t('message.By') }}:
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
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
                                                            {{ $t('message.Outgoing_Call') }}
                                                        </span>
                                                        <span class="commentaireCommande"
                                                            v-else-if="hCommande.typeCall == 'CallType.outgoing' && hCommande.durationCall == '0'"
                                                            style="background: #dc3545;">
                                                            {{ $t('message.Missed_Call') }}
                                                        </span>

                                                        <span class="commentaireCommande"
                                                            v-else-if="hCommande.typeCall == 'CallType.missed'"
                                                            style="background: #dc3545;">
                                                            {{ $t('message.Missed_Call') }}
                                                        </span>

                                                        <span class="commentaireCommande"
                                                            v-else-if="hCommande.typeCall == 'CallType.incoming'">
                                                            {{ $t('message.Incoming_Call') }}
                                                        </span>
                                                        <br>

                                                        <b
                                                            v-if="hCommande.typeCall == 'CallType.outgoing' && hCommande.durationCall != '0'">{{
                                                                hCommande.username }} {{ $t('message.Call') }} {{
        hCommande.nom_client_commande }}
                                                            ({{ moment.utc(hCommande.durationCall * 1000).format('mm:ss')
                                                            }})</b>


                                                        <b
                                                            v-if="hCommande.typeCall == 'CallType.missed' || (hCommande.typeCall == 'CallType.outgoing' && hCommande.durationCall == '0')">
                                                            {{ hCommande.nom_client_commande }}
                                                            {{ $t('message.Missed_call_from') }} {{
                                                                hCommande.username }}
                                                        </b>
                                                        <b v-if="hCommande.typeCall == 'CallType.incoming'">
                                                            {{ hCommande.nom_client_commande }} {{ $t('message.Call') }} {{
                                                                hCommande.username
                                                            }}
                                                            ({{ moment.utc(hCommande.durationCall * 1000).format('mm:ss')
                                                            }})</b>




                                                        <br>
                                                        {{ $t('message.At') }}
                                                        <b>{{ moment(hCommande.dateCall, 'x').format("MM/DD/YYYY hh:mm:ss")
                                                        }}
                                                        </b>




                                                    </div>

                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'ENROUTE'">
                                                        {{ $t('message.Package_sent_to') }}
                                                        <b>{{ hCommande.nom_ville }}</b>
                                                        {{ $t('message.By') }}
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'RAMASSER'">
                                                        {{ $t('message.Package_PickUp') }}
                                                        {{ $t('message.By') }}
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>


                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'ASSIGN'">
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'DMSUIVIE'">
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'INHOUSE'">
                                                        {{ $t('message.Package_is_ready_to_send') }}
                                                        {{ $t('message.By') }}
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'HOME'">
                                                        {{ $t('message.Package_is_in_Hub') }}
                                                        <b>{{ hCommande.nom_ville }}</b>
                                                        <!-- with
                                                        <b>{{ hCommande.username }}</b> -->
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'CONFIRMED'">
                                                        {{ $t('message.Confirmer') }} {{ $t('message.By') }}:
                                                        <b>{{ hCommande.clientUsername }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'PROCESSING'">
                                                        {{ $t('message.Processe') }} {{ $t('message.By') }}:
                                                        <b>{{ hCommande.clientUsername }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'PICKUP'">
                                                        {{ $t('message.PICKUP') }} {{ $t('message.By') }}:
                                                        <b v-if="hCommande.clientUsername">{{ hCommande.clientUsername
                                                        }}</b>
                                                        <b v-else>{{ hCommande.username }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'CREATED'">
                                                        {{ $t('message.Create') }} {{ $t('message.By') }}:
                                                        <b>
                                                            {{ hCommande.clientUsername }} </b><br />{{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'ANNULER'">
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'ANNULER_CL'">
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'CHANGERPRIX'">
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'RELANCER'">
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'REPORTED'">
                                                        {{ $t('message.Reporter') }} {{ $t('message.By') }}
                                                        <b>{{ hCommande.username }}</b>
                                                        {{ $t('message.To') }}
                                                        <b>{{ hCommande.reported_date }}
                                                        </b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        {{ hCommande.updated_at }}
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'TRANSIT'">
                                                        {{ $t('message.Package_Ready_for_Delivery') }} {{ $t('message.By')
                                                        }}
                                                        <b>{{ hCommande.username }}</b>

                                                        <b>{{ hCommande.reported_date }}
                                                        </b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        {{ hCommande.updated_at }}
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'DELIVERED'">
                                                        {{ $t('message.Delivered') }}
                                                        {{ $t('message.By') }}
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'RETURNEDLV'">
                                                        {{ $t('message.By') }}
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'RETURNEDAG'">
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'RETURNEDEV'">
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'RETURNEDRR'">
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'RETURNED'">
                                                        {{ $t('message.At') }}
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
                                                        <b class="badge badge badge-gradient-danger"
                                                            v-if="
                                                                hCommande.statut_facture == 'NOTPAID' && hCommande.type_facture == 'client'">
                                                            {{ $t('message.Invoiced') }}
                                                        </b>
                                                        <b class="badge badge badge-gradient-success"
                                                            v-if="
                                                                hCommande.statut_facture == 'PAID' && hCommande.type_facture == 'client'">{{
        hCommande.statut_facture }}</b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-1" v-if="
                                                        hCommande.statut_facture == 'NOTPAID'">
                                                        {{ $t('message.Invoiced') }} {{ $t('message.By') }}:
                                                        <b v-if="
                                                            hCommande.username ==
                                                            null
                                                        ">Admin</b>
                                                        <b v-else-if="
                                                            hCommande.username !=
                                                            null">{{ hCommande.username }}</b>
                                                        <br />{{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.statut_facture == 'PAID'">{{ $t('message.Paid') }}
                                                        {{ $t('message.By') }}:
                                                        <b v-if="hCommande.username == null">Admin</b>
                                                        <b v-else-if="hCommande.username != null">{{
                                                            hCommande.username
                                                        }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
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
                            Quit
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
                            Quit
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
import moment from 'moment'
import "vue2-timeago/dist/vue2-timeago.css";
export default {
    props: ["Client"],
    name: "CommandeClientComponent",
    components: { TimeAgo },
    data() {
        return {
            livreur: '',
            token: localStorage.getItem("token"),
            selected: [],
            edit: false,
            articles: {},
            ListArticles: {},
            errors: {},
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
            nbrCommande: 0,
            villes: [],
            historiqueCommande: {},
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
                selected_option2: "",
            },
            options2: [
                { text: this.FirstLowerRestUper(this.$t('message.Default')), value: "DEFAULT" },
                { text: this.FirstLowerRestUper(this.$t('message.CREATED')), value: "CREATED" },
                { text: this.FirstLowerRestUper(this.$t('message.CONFIRMED')), value: "CONFIRMED" },
                { text: this.FirstLowerRestUper(this.$t('message.PICKUP')), value: "PICKUP" },
                { text: this.FirstLowerRestUper(this.$t('message.PROCESSING')), value: "PROCESSING" },
                { text: this.FirstLowerRestUper(this.$t('message.INHOUSE')), value: "INHOUSE" },
                { text: this.FirstLowerRestUper(this.$t('message.ENROUTE')), value: "ENROUTE" },
                { text: this.FirstLowerRestUper(this.$t('message.TRANSIT')), value: "TRANSIT" },
                { text: this.FirstLowerRestUper(this.$t('message.REPORTED')), value: "REPORTED" },
                { text: this.FirstLowerRestUper(this.$t('message.DELIVERED')), value: "DELIVERED" },
                { text: this.FirstLowerRestUper(this.$t('message.RETURNED')), value: "RETURNED" },
                { text: this.FirstLowerRestUper(this.$t('message.CANCELED')), value: "CANCELED" },
                { text: this.FirstLowerRestUper(this.$t('message.NOREPONSE')), value: "NOREPONSE" },
            ],
            formDataCherche2: {
                selected_option2: "DEFAULT",
            },
            responsable: "",
            options3: [
                { text: "1-20 items", value: "20" },
                { text: "1-50 items", value: "50" },
                { text: "1-150 items", value: "150" },
                { text: "1-200 items", value: "200" },
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

            this.getStores();
            this.nom_err = "";
            this.formData = {
                ville_client_commande: "",
                nom_client_commande: "",
                adresse_client_commande: "",
                telephone_client_commande: "",
                prix_commande: "",
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
            this.initialiserFormData();
            this.classifierCommande(0);
        },
        async deleteCommande(id) {
            Swal.fire({
                title: this.$t('message.Are_you_sure'),
                text: this.$t('message.You_want_delete_Order'),
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "I Confirme",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    this.$vs.loading({ color: "#22c22b" });
                    await axios
                        .delete("/api/Commande/" + id)
                        .then((res) => {
                            this.$vs.notify({
                                time: 5000,
                                title: this.$t('message.Order') + id,
                                text: this.$t('message.The_order_has_been_successfully_deleted'),
                                color: "danger",
                                position: "top-right",
                            });
                            this.classifierCommande(this.formDataCherche3.selected_option3)
                        })
                        .catch((error) => console.log(res)).finally(() => this.$vs.loading.close());
                }
            });


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
                            "</td></tr><tr><td>" + this.$t('message.Store') + " :<b> </b></td><td>" +
                            this.commande.nom_store +
                            "</td></tr>";
                    } else {
                        text +=
                            "</td></tr><tr><td>" + this.$t('message.Store') + " :<b> </b></td><td>" +
                            this.commande.company +
                            "</td></tr>";
                    }

                    if (res.data.data.nom != null && res.data.data.prenom != null) {
                        text +=
                            "</td></tr><tr><td>" + this.$t('message.Responsible') + " :<b> </b></td><td>" +
                            this.commande.nom + ' ' + this.commande.prenom +
                            "</td></tr>";
                        text +=
                            "</td></tr><tr><td>" + this.$t('message.Phone_Number') + " :<b> </b></td><td>" +
                            this.commande.telephone_responsable +
                            "</td></tr>";
                    }

                    text +=
                        "<tr><td colspan='2'><b>" + this.$t('message.Destination') + "</b></td></tr>" +
                        "</td></tr><tr><td>" + this.$t('message.Order_id') + " :</td><td>" +
                        this.commande.id_commande +
                        "</td></tr>";
                    text +=
                        "</td></tr><tr><td>" + this.$t('message.Client') + " :<b> </b></td><td>" +
                        this.commande.nom_client_commande
                    "</td></tr>";

                    text +=
                        "</td></tr><tr><td>" + this.$t('message.City') + " :<b> </b></td><td>" +
                        this.commande.nom_ville +
                        "</td></tr>";
                    text +=
                        "</td></tr><tr><td>" + this.$t('message.Address') + " :<b> </b></td><td>" +
                        this.commande.adresse_client_commande +
                        "</td></tr>";
                    text +=
                        "</td></tr><tr><td>" + this.$t('message.Phone_Number') + " :<b> </b></td><td>" +
                        this.commande.telephone_client_commande +
                        "</td></tr>";
                    text +=
                        "</td></tr><tr><td>" + this.$t('message.Price') + " :<b> </b></td><td>" +
                        this.commande.prix_commande +
                        " Dhs </td></tr>";



                    if (res.data.data.additional_commentaire != null) {
                        text +=
                            "<tr><td>" + this.$t('message.Comment') + " :</td><td>" +
                            this.commande.additional_commentaire;
                    }
                    if (res.data.data.nom_article != null) {
                        text +=
                            "</td></tr><tr><td><b>Articles :</b></td></tr>";
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
                        title: "<h5><b>" + this.$t('message.Order_Informations') + "</b></h5>",
                        html: text,
                        showCancelButton: false,
                    });
                })
                .catch((error) => console.log(res))
                .finally(() => this.$vs.loading.close());
        },
        async chercher(count_nbr) {
            if (this.formDataCherche2.selected_option2 != "") {
                this.formDataCherche.selected_option2 =
                    this.formDataCherche2.selected_option2;
            }
            this.$vs.loading({ color: "#22c22b" });
            setTimeout(async () => {
                if (count_nbr > 1) {
                    await axios
                        .post(
                            "/api/rechercheCommande?page=" +
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
                            "/api/rechercheCommande?page=" +
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
            if (this.formDataCherche.valeur_recherche != '') {
                this.chercher(this.formDataCherche3.selected_option3)
            } else {
                this.$vs.loading({ color: "#22c22b" });
                setTimeout(async () => {
                    if (count_nbr > 1) {
                        await axios
                            .post(
                                "/api/classifierCommande?page=" +
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
                                "/api/classifierCommande?page=" +
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
            }





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
                title: this.$t('message.Copy_Order'),
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
        onFichierExcelChange(e) {

            this.formData.fichierCommande = e.target.files[0];
            this.nomFile = this.formData.fichierCommande["name"];
            document.getElementById("fichierCommande").value = "";
        },
        async getArticles() {
            await axios
                .get("/api/afficheArticleDisponible")
                .then((res) => {
                    this.articles = res.data.data;
                })
                .catch((error) => console.log(res));
            for (let i = 0; i < Object.keys(this.articles).length; i++) {
                this.articles[i]["quantite"] = 1;
            }
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
        async addCommande(excel) {
            this.$vs.loading({ color: "#22c22b" });
            this.nom_err = "";
            this.errors = {};
            if (excel == "excel") {
                let formData = new FormData();
                formData.append(
                    "fichierCommande",
                    this.formData.fichierCommande
                );
                if (this.formData.store['id']) {
                    formData.append(
                        "store",
                        this.formData.store['id']
                    );
                }
                formData.append("typeCommande", "excel");
                await axios
                    .post("/api/Commande", formData, { headers: { "Content-Type": "multipart/form-data; charset=utf-8;boundary=" + Math.random().toString().substr(2), } })
                    .then(async (res) => {
                        this.message = res.data.message;
                        if (
                            this.message == "Erreur" ||
                            this.message == "Le fichier depasse 100 row" || this.message == "Vous avez saisi un grand nombre de commandes pendant une journée !!"
                        ) {
                            var statut_icon = "danger";
                        } else if (
                            this.message == "Les commandes created successfully"
                        ) {
                            this.message == this.$t('message.Orders_created_successfully');
                            var statut_icon = "success";
                        }
                        document.getElementById("btn_cancel").click();
                        this.initialiserFormData();
                        await this.classifierCommande(this.formDataCherche3.selected_option3)
                        this.$vs.notify({
                            text: this.message,
                            color: statut_icon,
                            position: "top-right",
                            time: 4000,
                        });
                    })
                    .catch((error) => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                            this.nom_err =
                                this.errors[Object.keys(this.errors)[0]];
                        }
                    }).finally(() => this.$vs.loading.close());
            }
        },
        async getHistoriqueCommande(id) {
            this.$vs.loading({ color: "#22c22b" });
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
        },
        async addCommandeStock() {

            this.formData.articles = this.ListArticles;
            await axios
                .post("/api/Commande", this.formData)
                .then((res) => {
                    this.message = res.data.message;
                    this.classifierCommande(this.formDataCherche3.selected_option3)
                    if (
                        this.message ==
                        "Erreur la quantité doit etre superieur de 0"
                    ) {
                        var statut_icon = "warning";
                    } else if (
                        this.message == "Erreur" ||
                        this.message == "Erreur la quantité entrer plus que le stock" || this.message == "Vous avez saisi un grand nombre de commandes pendant une journée !!"
                    ) {
                        var statut_icon = "danger";
                    } else if (
                        this.message == "commande created successfully"
                    ) {
                        var statut_icon = "success";
                    }
                    this.$vs.notify({
                        title: this.message,
                        color: statut_icon,
                        position: "top-right",
                        time: 4000,
                    });
                    this.initialiserFormData();
                    this.getArticles();
                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                        this.nom_err = this.errors[Object.keys(this.errors)[0]];
                    }
                });
        },
        async pickupCommande() {
            this.$vs.loading({ color: "#22c22b" });
            if (this.selected) {
                await axios
                    .post("/api/pickupCommande", this.selected)
                    .then((res) => {
                        if (res.data.message == "Erreur") {
                            this.$vs.notify({
                                title: this.$t('message.No_order_confirmed'),
                                color: 'warning',
                                position: "top-right",
                                time: 4000,
                            });

                        } else {
                            this.$vs.notify({
                                title: this.$t('message.Commande_Confirmed'),
                                text: this.$t('message.Orders_is_pickup'),
                                color: "success",
                                position: "top-right",
                                time: 4000,
                            });
                        }
                        this.classifierCommande(this.formDataCherche3.selected_option3)
                    })
                    .catch((error) => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    })
                    .finally(() => this.$vs.loading.close());
            } else {
                await axios
                    .get("/api/pickupCommande")
                    .then((res) => {
                        if (res.data.message == "Erreur") {
                            this.$vs.notify({
                                title: this.$t('message.No_order_confirmed'),
                                color: "warning",
                                position: "top-right",
                                time: 4000,
                            });


                        } else {
                            this.$vs.notify({
                                title: this.$t('message.Commande_Confirmed'),
                                text: this.$t('message.Orders_is_pickup'),
                                color: "success",
                                position: "top-right",
                                time: 4000,
                            });
                            this.classifierCommande(this.formDataCherche3.selected_option3)
                        }
                    })
                    .catch((error) => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    })
                    .finally(() => this.$vs.loading.close());
            }
            this.selected = [];
        },
        async confirmeCommande(id) {
            this.$vs.loading({ color: "#22c22b" });
            if (this.selected) {
                await axios
                    .post("/api/confirmeCommande", this.selected)
                    .then((res) => {
                        this.message = res.data.message;
                    })
                    .catch((error) => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                            this.nom_err =
                                this.errors[Object.keys(this.errors)[0]];
                        }
                    })
                    .finally(() => this.$vs.loading.close());
            }
            if (
                this.message == "No order confirmed"
            ) {
                var statut_icon = "danger";
                this.$vs.notify({
                    title: this.$t('message.No_order_confirmed'),
                    color: statut_icon,
                    position: "top-right",
                    time: 4000,
                });

            } else if (
                this.message == "Commande Stock Confirmed successfully"
            ) {
                this.$vs.notify({
                    title: this.$t('message.Commande_Confirmed'),
                    text: this.message,
                    color: "success",
                    position: "top-right",
                    time: 4000,
                });
            }
            else if (

                this.message == "Commande Confirmed successfully"
            ) {
                this.$vs.notify({
                    title: this.$t('message.Commande_Confirmed'),
                    text: this.message,
                    color: "success",
                    position: "top-right",
                    time: 4000,
                });
            } else {
                var statut_icon = "danger";
                this.$vs.notify({
                    title: this.message,
                    color: statut_icon,
                    position: "top-right",
                    time: 4000,
                });
            }

            this.classifierCommande(this.formDataCherche3.selected_option3)
            this.selected = [];
        },
        async updateCommande() {
            document.getElementById('stockButton').style.display = 'none';
            this.formData.selected_type = this.selected_type;
            this.formData.articles = this.ListArticles;
            await axios
                .post("/api/updateCommande", this.formData)
                .then((res) => {
                    this.message = res.data.message;
                    this.classifierCommande(this.formDataCherche3.selected_option3)
                    document.getElementById("btn_cancel").click();
                    if (
                        this.message ==
                        "Erreur la quantité doit etre superieur de 0"
                    ) {
                        var statut_icon = "warning";
                        this.message = this.$t('message.Quantity_must_be_greater_than_0')
                    } else if (
                        this.message == "Erreur" ||
                        this.message ==
                        "Erreur la quantité entrer plus que le stock"
                    ) {
                        var statut_icon = "danger";
                        this.message = this.$t('message.Quantity_enter_more_than_stock');
                    } else if (this.message == "commande update successfully") {
                        var statut_icon = "success";
                        this.message = this.$t('message.Commande_update_successfully');
                    }
                    this.$vs.notify({
                        title: this.message,
                        color: statut_icon,
                        position: "top-right",
                        time: 4000,
                    });
                    this.formData.fichierCommande = "";
                    this.nomFile = "";
                    this.initialiserFormData();
                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                        this.nom_err = this.errors[Object.keys(this.errors)[0]];
                    }
                });
        },
        async checkCommande(btn_val, tr) {
            await this.getArticles();
            if (tr.type_commande == "stock") {
                this.selected_type = true;
            } else if (tr.type_commande == "ramassage") {
                this.selected_type = false;
            }
            (this.nom_err = ""), (this.errors = {}), this.getVilles();
            this.getStores();
            this.edit = btn_val;
            if (btn_val) {
                await this.getCommande(tr.id_commande);
                this.formData.selected_commande = tr.id_commande;
            }
            $("#ajouterCommandeStock").modal("show");
        },
        checkCommandeStock(btn_val) {
            this.edit = btn_val;
            (this.articles = []),
                (this.stock = {
                    qnt_article_stock: "",
                    prix_article: "",
                }),
                (this.villes = []),
                (this.nom_err = ""),
                (this.errors = {}),
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
                    store: "",
                }),
                (this.ListArticles = []),
                this.getArticles();
            this.getVilles();
            this.getStores();
            $("input[type='search']").wrap("<form>");
            $("input[type='search']")
                .closest("form")
                .attr("autocomplete", "off");
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
                    { text:this.FirstLowerRestUper(this.$t('message.Order_Id')), value: "id_commande" },
                    { text: this.FirstLowerRestUper(this.$t('message.Name')), value: "nom_client_commande" },
                    { text: this.FirstLowerRestUper(this.$t('message.Phone_number')), value: "telephone_client_commande" },
                    { text: this.FirstLowerRestUper(this.$t('message.Price')), value: "prix_commande" },
                    { text: this.FirstLowerRestUper(this.$t('message.City')), value: "nom_ville" },
                ]),
                (this.formDataCherche = {
                    selected_option: "id_commande",
                    valeur_recherche: "",
                    selected_option2: "",
                }),
                (this.options2 = [
                    { text: this.FirstLowerRestUper(this.$t('message.Default')), value: "DEFAULT" },
                    { text: this.FirstLowerRestUper(this.$t('message.CREATED')), value: "CREATED" },
                    { text: this.FirstLowerRestUper(this.$t('message.CONFIRMED')), value: "CONFIRMED" },
                    { text: this.FirstLowerRestUper(this.$t('message.PICKUP')), value: "PICKUP" },
                    { text: this.FirstLowerRestUper(this.$t('message.PROCESSING')), value: "PROCESSING" },
                    { text: this.FirstLowerRestUper(this.$t('message.INHOUSE')), value: "INHOUSE" },
                    { text: this.FirstLowerRestUper(this.$t('message.ENROUTE')), value: "ENROUTE" },
                    { text: this.FirstLowerRestUper(this.$t('message.TRANSIT')), value: "TRANSIT" },
                    { text: this.FirstLowerRestUper(this.$t('message.REPORTED')), value: "REPORTED" },
                    { text: this.FirstLowerRestUper(this.$t('message.DELIVERED')), value: "DELIVERED" },
                    { text: this.FirstLowerRestUper(this.$t('message.RETURNED')), value: "RETURNED" },
                    { text: this.FirstLowerRestUper(this.$t('message.CANCELED')), value: "CANCELED" },
                    { text: this.FirstLowerRestUper(this.$t('message.NOREPONSE')), value: "NOREPONSE" },
                ]),
                (this.formDataCherche2 = {
                    selected_option2: "DEFAULT",
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

        async getNotification() {
            var commande;
            await axios
                .get("/api/getNotification")
                .then((res) => {
                    commande = res.data.data;
                })
                .catch((error) => console.log(res));
            for (var i = 0; i < commande.length; i++) {
                if (commande[i]["etat_commande"] == "DELIVERED") {
                    this.$vs.notify({
                        time: 10000,
                        title: `La Commande ${commande[i]["id_commande"]}`,
                        text: commande[i]["etat_commande"],
                        color: "success",
                        position: "top-right",
                    });
                } else {
                    this.$vs.notify({
                        time: 10000,
                        title: `La Commande ${commande[i]["id_commande"]}`,
                        text: "Pas de reponse",
                        color: "warning",
                        position: "top-right",
                    });
                }
            }
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
                    text: this.$t('message.Do_you_want_to_cancel_the_order'),
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
                    text: this.$t('message.The_command_will_Relaunch'),
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
                var statut_icon = "danger";
            } else if (this.message == "Successfully") {
                var statut_icon = "success";
            } else {
                var statut_icon = "warning";
            }
            this.$vs.notify({
                time: 5000,
                title: this.message,
                color: statut_icon,
                position: "top-right",
            });
            this.classifierCommande(this.formDataCherche3.selected_option3)

            this.initialiserFormData();
        },
        async getStores() {
            this.$vs.loading({ color: "#22c22b" });
            setTimeout(async () => {
                await axios
                    .get("/api/Store")
                    .then((res) => {
                        this.stores = res.data.data;
                        res.data.data.forEach(element => {
                            if (element['favorite_store'] == 1) {
                                this.formData.store = element
                            }
                        });
                    })
                    .catch((error) => console.log(res))
                    .finally(() => this.$vs.loading.close());
            }, 200);
        },
        getFacture(tr) {
            axios.get("/api/getFacture/" + tr.id_facture, { responseType: "blob" })
                .then((res) => {
                    window.open(URL.createObjectURL(res.data));
                })
                .catch((error) => console.log(res));
        },
        checkBlackList() {
            if ($('#telephone_client').val().length == 10) {
                axios.get("/api/checkBlackList/" + $('#telephone_client').val())
                    .then((res) => {
                        if (res.data.message == 'Le numéro entrer est en liste noir') {
                            document.getElementById('text-blacklist').innerHTML = res.data.message;
                        } else {
                            document.getElementById('text-blacklist').innerHTML = '';
                        }
                    })
                    .catch((error) => console.log(res))

            } else if ($('#telephone_client').val().length > 10 || $('#telephone_client').val().length < 10) {
                document.getElementById('text-blacklist').innerHTML = $t('message.The_phone_number_must_be_10_digits')
            } else {
                document.getElementById('text-blacklist').innerHTML = ''
            }
        }
    },
    async beforeMount() {
        axios.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;
    },
    async mounted() {


    },

    async created() {
        $(window).on("load", function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
        // setInterval(() => {
        //     this.getNotification();
        // }, 180000);
    },
};


</script>