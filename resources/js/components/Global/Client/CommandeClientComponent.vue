<template>
    <div class="content-wrapper-customise">
        <div class="page-header-dashboard">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span>{{ $t('message.Orders') }}
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

                                    <i class="fa fa-phone" aria-hidden="true"></i>
                                    {{ $t('message.Pickup') }}
                                </button>
                                <button style="margin: 0 13px" type="button" class="btn btn-primary float-end"
                                    @click.prevent="confirmeCommande" v-if="btn_confirme">

                                    <i class="fa fa-check" aria-hidden="true"></i>
                                    {{ $t('message.Confirme') }}
                                </button>
                            </div>
                            <div class="chercher">
                                <!-- Example single danger button -->

                                <vs-select class="selectExample" v-model="formDataCherche3.selected_option3"
                                    @change="classifierCommande(formDataCherche3.selected_option3)">
                                    <vs-select-item :key="index" :value="item.value" :text="changeSelectedItem(item)"
                                        v-for="(item, index) in options3.slice(0, 1)" v-if="totalOrders <= 20" />

                                    <vs-select-item :key="index2" :value="item.value" :text="changeSelectedItem(item)"
                                        v-for="(item, index2) in options3.slice(0, 2)" v-else-if="totalOrders <= 50" />

                                    <vs-select-item :key="index3" :value="item.value" :text="changeSelectedItem(item)"
                                        v-for="(item, index3) in options3.slice(0, 3)" v-else-if="totalOrders <= 150" />

                                    <vs-select-item :key="index4" :value="item.value" :text="changeSelectedItem(item)"
                                        v-for="(item, index4) in options3.slice(0, 4)" v-else-if="totalOrders <= 200" />
                                    <vs-select-item :key="index5" :value="item.value" :text="changeSelectedItem(item)"
                                        v-for="(item, index5) in options3.slice(0, 4)" v-else="" />
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
                                    <vs-input :placeholder="$t('message.Search')" v-model="formDataCherche.valeur_recherche"
                                        @keyup.enter="chercher(formDataCherche3.selected_option3)" />
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
                                <!-- <vs-th> {{ $t('message.Store') }} </vs-th> -->
                                <vs-th> {{ $t('message.Destination') }} </vs-th>
                                <vs-th> {{ $t('message.Phone_Number') }} </vs-th>
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
                                            <span class="noreponse" v-if="tr.etat_commande == 'RETURNEDLV'">{{
                                                $t('message.Return_sent_to_agency') }}</span>
                                            <span class="noreponse" v-if="tr.etat_commande == 'RETURNEDAG'">{{
                                                $t('message.Return_received_by_agency') }}</span>
                                            <span class="noreponse" v-if="tr.etat_commande == 'RETURNEDEV'">{{
                                                $t('message.Return_send') }}</span>
                                            <span class="noreponse" v-if="tr.etat_commande == 'RETURNEDRR'">{{
                                                $t('message.Return_received_by_Ramasseur') }}</span>
                                            <span class="noreponse" v-if="tr.etat_commande == 'RETURNED'">{{
                                                $t('message.Return_send_to_hometown') }}</span>

                                            <span class="noreponse" v-if="tr.etat_commande == 'CANCELEDLV'">{{
                                                $t('message.Return_sent_to_agency') }}</span>
                                            <span class="noreponse" v-if="tr.etat_commande == 'CANCELEDAG'">{{
                                                $t('message.Return_received_by_agency') }}</span>
                                            <span class="noreponse" v-if="tr.etat_commande == 'CANCELEDEV'">{{
                                                $t('message.Return_send') }}</span>
                                            <span class="noreponse" v-if="tr.etat_commande == 'CANCELEDRR'">{{
                                                $t('message.Return_received_by_Ramasseur') }}</span>
                                            <span class="noreponse" v-if="tr.etat_commande == 'CANCELED'">{{
                                                $t('message.Return_send_to_hometown') }}</span>




                                        </div>
                                        <span><time-ago :datetime="tr.updated_at" long :locale="locale"></time-ago>
                                        </span>
                                    </vs-td>
                                    <!-- <vs-td :data="tr.nom_store">
                                        <span v-if="tr.nom_store != null">
                                            {{ tr.nom_store }}
                                        </span>
                                        <span v-else-if="tr.nom_store == null">
                                            {{ tr.company }}
                                        </span>
                                    </vs-td> -->
                                    <vs-td :data="tr.nom_client_commande">
                                        <b>{{ tr.nom_client_commande }}</b><br>
                                        {{ tr.ville_client_commande }}<br>
                                        {{ tr.prix_commande }} {{ $t('message.Dhs') }}
                                    </vs-td>
                                    <vs-td :data="tr.telephone_client_commande">
                                        {{ tr.telephone_client_commande }}
                                        <span @click="whatsapp(tr.telephone_client_commande, tr.nom_client_commande)"
                                            class="whatsapp"><i class="fab fa-whatsapp" aria-hidden="true"></i></span>
                                    </vs-td>
                                    <vs-td :data="tr.etat_commande">
                                        <b class="badge badge badge-gradient-secondary"
                                            v-if="tr.etat_commande == 'CREATED'">{{ $t('message.CREATED') }}</b>
                                        <b class="badge badge badge-gradient-info" v-if="tr.etat_commande == 'CONFIRMED'">{{
                                            $t('message.CONFIRMED') }}</b>
                                        <b class="badge badge badge-gradient-secondary"
                                            v-if="tr.etat_commande == 'PICKUP'">{{ $t('message.PICKUP') }}</b>
                                        <b class="badge badge badge-gradient-secondary"
                                            v-if="tr.etat_commande == 'PROCESSING'">{{ $t('message.PROCESSING') }}</b>
                                        <b class="badge badge badge-gradient-info" v-if="tr.etat_commande == 'HOME'">{{
                                            $t('message.HOME') }}</b>
                                        <b class="badge badge badge-gradient-info"
                                            v-if="tr.etat_commande == 'CHANGERPRIX'">{{ $t('message.CHANGERPRIX') }}</b>
                                        <b class="badge badge badge-gradient-info" v-if="tr.etat_commande == 'INHOUSE'">{{
                                            $t('message.INHOUSE') }}</b>
                                        <b class="badge badge badge-gradient-primary"
                                            v-if="tr.etat_commande == 'ENROUTE'">{{ $t('message.ENROUTE') }}</b>
                                        <b class="badge badge badge-gradient-primary"
                                            v-if="tr.etat_commande == 'RAMASSER'">{{ $t('message.RAMASSER') }}</b>
                                        <b class="badge badge badge-gradient-info" v-if="tr.etat_commande == 'TRANSIT'">{{
                                            tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'NOREPONSE'">NO REP + SMS</b>
                                        <b class="badge badge badge-gradient-info" v-if="tr.etat_commande == 'REPORTED'">{{
                                            tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'RETURNEDLV'" data-toggle="tooltip"
                                            title="Retours envoye vers agence">{{ $t('message.RETURNEDLV') }}</b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'RETURNEDAG'" data-toggle="tooltip"
                                            title="Retours envoye vers agence">{{ $t('message.RETURNEDAG') }}</b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'RETURNEDEV'" data-toggle="tooltip"
                                            title="Retours envoye vers agence">{{ $t('message.RETURNEDEV') }}</b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'RETURNEDRR'" data-toggle="tooltip"
                                            title="Retours envoye vers agence">{{ $t('message.RETURNEDRR') }}</b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'RETURNED'">{{ $t('message.RETURNED') }}</b>

                                        <b class="badge badge badge-gradient-info" v-if="tr.etat_commande == 'ASSIGN'">{{
                                            $t('message.ASSIGN') }}</b>
                                        <b class="badge badge badge-gradient-success"
                                            v-if="tr.etat_commande == 'DELIVERED'">{{ $t('message.DELIVERED') }}</b>


                                        <b class="badge badge badge-gradient-danger"
                                            v-if="tr.etat_commande == 'CANCELED'">{{ $t('message.CANCELED') }}</b>
                                        <b class="badge badge badge-gradient-danger"
                                            v-if="tr.etat_commande == 'CANCELEDLV'">{{ $t('message.CANCELEDLV') }}</b>
                                        <b class="badge badge badge-gradient-danger"
                                            v-if="tr.etat_commande == 'CANCELEDEV'">{{ $t('message.CANCELEDEV') }}</b>
                                        <b class="badge badge badge-gradient-danger"
                                            v-if="tr.etat_commande == 'CANCELEDAG'">{{ $t('message.CANCELEDAG') }}</b>
                                        <b class="badge badge badge-gradient-danger"
                                            v-if="tr.etat_commande == 'CANCELEDRR'">{{ $t('message.CANCELEDRR') }}</b>



                                        <b class="badge badge badge-gradient-info" v-if="tr.etat_commande == 'DMSUIVIE'">{{
                                            $t('message.DMSUIVIE') }}</b>
                                        <b class="badge badge badge-gradient-danger"
                                            v-if="tr.etat_commande == 'ARCHIVED'">{{ tr.etat_commande }}</b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'ANNULER'">{{ $t('message.ANNULER') }}
                                        </b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'ANNULER_CL'">{{ $t('message.ANNULER') }}
                                        </b>
                                        <b class="badge badge badge-gradient-warning"
                                            v-if="tr.etat_commande == 'RELANCER'">{{ $t('message.RELANCER') }}</b>
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
                                            v-if="tr.etat_commande != 'DELIVERED' && tr.etat_commande != 'ANNULER_CL' && tr.etat_commande != 'ANNULER' && tr.etat_commande != 'CANCELED' && tr.etat_commande != 'CANCELEDEV' && tr.etat_commande != 'CANCELEDRR' && tr.etat_commande != 'CANCELEDLV' && tr.etat_commande != 'CANCELEDAG' && tr.etat_commande != 'RETURNEDLV'
                                                && tr.etat_commande != 'RETURNED' && tr.etat_commande != 'RETURNEDEV' && tr.etat_commande != 'RETURNEDRR' && tr.etat_commande != 'RETURNEDAG' && tr.etat_commande != 'RETURNED' && tr.etat_commande != 'CREATED' && tr.etat_commande != 'CONFIRMED'"
                                            @click.prevent="reclamationCommande(tr)">
                                            <i class="fa fa-info"></i>
                                        </button>
                                    </vs-td>
                                </vs-tr>
                            </template>
                        </vs-table>
                        <vs-pagination v-if="locale == 'ar'" @input="classifierCommande(formDataCherche3.selected_option3)"
                            :max="9" :total="commandes.last_page" v-model="commandes.current_page" prev-icon="arrow_forward"
                            next-icon="arrow_back"></vs-pagination>
                        <vs-pagination v-else @input="classifierCommande(formDataCherche3.selected_option3)" :max="9"
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
                        <div class="form-group" v-if="Client.id_intern_statut == 1">
                            <label for="id_commande_intern">{{ $t('message.id_intern') }}</label>

                            <input type="text" class="form-control" id="id_commande_intern	" v-model="formData.id_commande_intern"
                                name="id_commande_intern" />
                        </div>
                        <div class="form-group">
                            <label for="ville_client">{{ $t('message.City') }}<span class="text-danger"> *</span><span
                                    v-if="commentaire_ville"> {{ commentaire_ville }}</span></label>

                            <v-select placeholder="Veuillez-vous selectionner une ville"
                                v-model="formData.ville_client_commande" name="ville_client" :options="villes" label="ville"
                                index="id" @input="checkCommentCity(formData.ville_client_commande)" />
                        </div>
                        <div class="form-group" v-if="Object.keys(stores).length > 0">
                            <label for="ville_client">{{ $t('message.Store') }}</label>
                            <v-select placeholder="Veuillez-vous selectionner un Store" v-model="formData.store"
                                name="store" :options="stores" label="nom_store" index="id" />
                        </div>
                        <div class="form-group">
                            <label for="nom_client">{{ $t('message.Name') }}<span class="text-danger"> *</span></label>

                            <input type="text" class="form-control" id="nom_client" v-model="formData.nom_client_commande"
                                name="nom_client" />
                        </div>
                        <div class="form-group">
                            <label for="adresse_client">{{ $t('message.Address') }}<span class="text-danger">
                                    *</span></label>

                            <input type="text" class="form-control" id="adresse_client"
                                v-model="formData.adresse_client_commande" name="adresse_client" />
                        </div>
                        <div class="form-group">
                            <label for="telephone_client">{{ $t('message.Phone_Number') }}<span class="text-danger">
                                    *</span></label>

                            <input type="text" class="form-control" id="telephone_client"
                                v-model="formData.telephone_client_commande" name="telephone_client"
                                @change="checkBlackList" />
                            <p class="text-danger" id="text-blacklist"></p>
                        </div>
                        <div class="form-group">
                            <label for="prix_commande">{{ $t('message.Price') }}<span class="text-danger"> *</span></label>

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
                        <button type="button" id="btn_cancel" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-times"></i>
                        </button>
                        <button type="button" class="btn btn-primary" v-if="!edit" @click.prevent="addCommandeStock()">
                            {{ $t('message.Add_Order') }}
                        </button>
                        <button type="button" class="btn btn-primary" v-if="edit" @click.prevent="updateCommande()">
                            {{ $t('message.Edit_Order') }}
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
                                <vs-button color="danger" href="model/Model.xlsm" type="filled"><i class="fa fa-download"
                                        aria-hidden="true"></i>
                                    {{ $t('message.Model_Excel') }}</vs-button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btn_cancel" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-times"></i>
                        </button>
                        <button type="button" class="btn btn-primary" v-if="!edit" @click.prevent="addCommande('excel')">
                            {{ $t('message.Add_Order') }}
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

                                                            <img v-if="hCommande.etat_commande == 'CANCELEDLV' || hCommande.etat_commande == 'CANCELEDAG' || hCommande.etat_commande == 'CANCELEDRR'
                                                                || hCommande.etat_commande == 'CANCELED' || hCommande.etat_commande == 'CANCELEDEV'"
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
                                                            v-if="hCommande.etat_commande === 'CANCELEDAG'">{{
                                                                $t('message.CANCELED') }}</b>
                                                        <b class="badge badge-gradient-danger"
                                                            v-if="hCommande.etat_commande === 'CANCELEDEV'">{{
                                                                $t('message.CANCELED') }}</b>
                                                        <b class="badge badge-gradient-danger"
                                                            v-if="hCommande.etat_commande === 'CANCELEDRR'">{{
                                                                $t('message.CANCELED') }}</b>
                                                        <b class="badge badge-gradient-danger"
                                                            v-if="hCommande.etat_commande === 'CANCELEDLV'">{{
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


                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.commentaire_commande && hCommande.etat_commande == 'COMMENTAIRE'">
                                                        <span class="commentaireCommande">{{ hCommande.commentaire_commande
                                                        }}</span>
                                                        <br>
                                                        {{ $t('message.By') }}:
                                                        <b v-if="hCommande.clientUsername">{{ hCommande.clientUsername
                                                        }}</b>
                                                        <b v-else>{{ hCommande.username }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.commentaire_commande && hCommande.etat_commande != 'COMMENTAIRE'">

                                                        <span class="commentaireCommande"
                                                            v-if="hCommande.commentaire_commande == 'Request order tracking'">{{
                                                                $t('message.Request_order_tracking') }}</span>

                                                        <span class="commentaireCommande"
                                                            v-else-if="hCommande.commentaire_commande == 'Relaunch request'">{{
                                                                $t('message.Relaunch_request') }}</span>
                                                        <span class="commentaireCommande"
                                                            v-else-if="hCommande.commentaire_commande == 'Avec Sms'">{{
                                                                $t('message.With_Sms') }}</span>








                                                        <span class="commentaireCommande" v-else>{{
                                                            hCommande.commentaire_commande }}</span>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
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

                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'NOREPONSE'">
                                                        {{ $t('message.Package_without_response') }} {{ $t('message.By') }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>




                                                    <div class="tl-date text-muted mt-2"
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
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'RAMASSER'">
                                                        {{ $t('message.Package_PickUp') }}
                                                        {{ $t('message.By') }}
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>


                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'ASSIGN'">
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'DMSUIVIE'">
                                                        {{ $t('message.By') }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'INHOUSE'">
                                                        {{ $t('message.Package_is_ready_to_send') }}
                                                        {{ $t('message.By') }}
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-2"
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
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'CONFIRMED'">
                                                        {{ $t('message.Confirmed') }} {{ $t('message.By') }}:
                                                        <b>{{ hCommande.clientUsername }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'PROCESSING'">
                                                        {{ $t('message.Processe') }} {{ $t('message.By') }}:
                                                        <b>{{ hCommande.clientUsername }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'PICKUP'">

                                                        {{ $t('message.PICKUP') }} {{ $t('message.By') }}:
                                                        <b v-if="hCommande.clientUsername">{{ hCommande.clientUsername
                                                        }}</b>
                                                        <b v-else>{{ hCommande.username }}</b>
                                                        <br>
                                                        <span v-if="hCommande.id_package != null">{{
                                                            $t('message.Package') }}
                                                            :</span>
                                                        <b v-if="hCommande.id_package != null">{{
                                                            hCommande.id_package }}</b>
                                                        <br v-if="hCommande.id_package != null" />

                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'CREATED'">
                                                        {{ $t('message.Created') }} {{ $t('message.By') }}:
                                                        <b>
                                                            {{ hCommande.clientUsername }} </b><br />{{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'ANNULER'">{{ $t('message.By') }}:
                                                        <b v-if="hCommande.clientUsername">{{ hCommande.clientUsername
                                                        }}</b>
                                                        <b v-else>{{ hCommande.username }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'ANNULER_CL'">{{ $t('message.By')
                                                        }}:
                                                        <b v-if="hCommande.clientUsername">{{ hCommande.clientUsername
                                                        }}</b>
                                                        <b v-else>{{ hCommande.username }}</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'CHANGERPRIX'">
                                                        {{ $t('message.By') }}
                                                        <b v-if="hCommande.clientUsername">{{ hCommande.clientUsername
                                                        }}</b>
                                                        <b v-else>{{ hCommande.username }}</b><br>
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'RELANCER'">
                                                        {{ $t('message.By') }}
                                                        <b v-if="hCommande.clientUsername">{{ hCommande.clientUsername
                                                        }}</b>
                                                        <b v-else>{{ hCommande.username }}</b>
                                                        <br>
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'REPORTED'">
                                                        {{ $t('message.Reporter') }} {{ $t('message.By') }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        {{ $t('message.To') }}
                                                        <b>{{ hCommande.reported_date }}
                                                        </b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        {{ hCommande.updated_at }}
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'TRANSIT'">
                                                        {{ $t('message.Package_Ready_for_Delivery') }} {{ $t('message.By')
                                                        }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        <b>{{ hCommande.reported_date }}
                                                        </b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        {{ hCommande.updated_at }}
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'DELIVERED'">
                                                        {{ $t('message.Delivered') }}
                                                        {{ $t('message.By') }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'CANCELEDLV'">
                                                        <!-- <span class="commentaireCommande">{{
                                                            $t('message.Package_send_to_agency')
                                                        }}</span><br> -->

                                                        {{ $t('message.Order_canceled') }}
                                                        {{ $t('message.By') }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        <br>
                                                     
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'CANCELEDAG'">
                                                        {{ $t('message.Order_received') }}
                                                        {{ $t('message.By') }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        <br>
                                                        <span v-if="hCommande.id_bon_retour_client != null">{{
                                                            $t('message.Return_receipt') }}
                                                            :</span>
                                                        <b v-if="hCommande.id_bon_retour_client != null">{{
                                                            hCommande.id_bon_retour_client }}</b>
                                                        <br v-if="hCommande.id_bon_retour_client != null" />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'CANCELEDEV'">
                                                        {{ $t('message.Package_send_to_origin_city') }}
                                                        {{ $t('message.By') }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        <br>
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'CANCELEDRR'">
                                                        {{$t('message.Return_received_by_responsible') }}
                                                        {{ $t('message.By') }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        <br>
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'CANCELED'">
                                                        {{ $t('message.Order_canceled') }}
                                                        {{ $t('message.By') }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        <br>
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'RETURNEDLV'">
                                                        <span class="commentaireCommande">{{
                                                            $t('message.Package_send_to_agency')
                                                        }}</span><br>
                                                        {{ $t('message.By') }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        <br />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'RETURNEDAG'">
                                                        {{ $t('message.Package_in_agency')}}
                                                        {{ $t('message.By') }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        <br>
                                                        <span v-if="hCommande.id_bon_retour_client != null">{{
                                                            $t('message.Return_receipt') }}
                                                            :</span>
                                                        <b v-if="hCommande.id_bon_retour_client != null">{{
                                                            hCommande.id_bon_retour_client }}</b>
                                                        <br v-if="hCommande.id_bon_retour_client != null" />
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'RETURNEDEV'">
                                                        {{$t('message.Package_send_to_origin_city')}}
                                                        {{ $t('message.By') }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        <br>
                                                       
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'RETURNEDRR'">
                                                        {{$t('message.Return_received_by_responsible') }}
                                                        {{ $t('message.By') }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        <br>
                                                        {{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.etat_commande == 'RETURNED'">
                                                        {{ $t('message.By') }}
                                                        <b v-if="hCommande.username">{{ hCommande.username }}</b>
                                                        <b v-else>Service Client</b>
                                                        <br>
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
                                                            v-if="hCommande.statut_facture == 'NOTPAID' && hCommande.type_facture == 'client'">
                                                            {{ $t('message.Invoiced') }}
                                                        </b>
                                                        <b class="badge badge badge-gradient-success"
                                                            v-if="hCommande.statut_facture == 'PAID' && hCommande.type_facture == 'client'">{{
                                                                hCommande.statut_facture }}</b>
                                                    </div>

                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.statut_facture == 'NOTPAID'">
                                                        {{ $t('message.Invoiced') }} {{ $t('message.By') }}:
                                                        <b v-if="hCommande.username ==
                                                            null
                                                            ">Service Facturation</b>
                                                        <br>
                                                        <b v-if="hCommande.username != null">{{ hCommande.username }}</b>
                                                        <span v-if="hCommande.id_facture != null">
                                                            {{ $t('message.Invoice_id') }}:
                                                            <b>{{ hCommande.id_facture }}</b>
                                                        </span>
                                                        <br />{{ $t('message.At') }}
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-2"
                                                        v-if="hCommande.statut_facture == 'PAID'">{{ $t('message.Paid') }}
                                                        {{ $t('message.By') }}:
                                                        <b v-if="hCommande.username == null">Service Facturation</b>
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
                            <i class="fa fa-times"></i>
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
                            {{ $t('message.Order_id') }}: <b>{{ formDataUpdate.id_commande }}</b>
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="page-content page-container" id="page-content">
                            <div class="p-3">


                                <div class="form-group row">
                                    <div class="alert alert-danger" role="alert" v-if="nom_err">
                                        {{ nom_err[0] }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label for="nom_client">{{ $t('message.Name') }}</label>

                                        <input type="text" class="form-control" id="nom_client"
                                            v-model="formDataUpdate.nom_client_commande" name="nom_client" />
                                    </div>
                                    <!-- <div class="col-3">
                                        <button type="button" class="btn btn-info" @click.prevent="
                                            updateCommandeInfo('nom_client_commande', formData.nom_client_commande, formData.id_commande)
                                        "> {{ $t('message.Update') }}
                                        </button>

                                    </div> -->
                                </div>
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label for="telephone_client">{{ $t('message.Phone_Number') }}</label>

                                        <input type="text" class="form-control" id="telephone_client"
                                            v-model="formDataUpdate.telephone_client_commande" name="telephone_client" />
                                    </div>
                                    <!-- <div class="col-3">
                                        <button type="button" class="btn btn-info" @click.prevent="
                                            updateCommandeInfo('telephone_client_commande', formData.telephone_client_commande, formData.id_commande)
                                        "> {{ $t('message.Update') }}
                                        </button>

                                    </div> -->
                                </div>
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label for="adresse_client">{{ $t('message.Address') }}</label>

                                        <input type="text" class="form-control" id="adresse_client"
                                            v-model="formDataUpdate.adresse_client_commande" name="adresse_client" />
                                    </div>
                                    <!-- <div class="col-3">
                                        <button type="button" class="btn btn-info" @click.prevent="
                                            updateCommandeInfo('adresse_client_commande', formData.adresse_client_commande, formData.id_commande)
                                        "> {{ $t('message.Update') }}
                                        </button>

                                    </div> -->
                                </div>
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label for="prix_commande">{{ $t('message.Price') }}</label>

                                        <input type="text" class="form-control" id="prix_commande"
                                            v-model="formDataUpdate.prix_commande" name="prix_commande" />
                                    </div>
                                    <!-- <div class="col-3">
                                        <button type="button" class="btn btn-info" @click.prevent="
                                            updateCommandeInfo('prix_commande', formData.prix_commande, formData.id_commande)
                                        "> {{ $t('message.Update') }}
                                        </button>

                                    </div> -->
                                </div>
                                <div class="form-group row">

                                    <div class="col-6">
                                        <button type="button" class="btn btn-info" style="min-width: 200px;" @click.prevent="updateCommandeInfo()
                                            "> <i class="fas fa-check"></i> {{ $t('message.Update') }}
                                        </button>

                                    </div>
                                </div>

                                <div class="form-group row"
                                    v-if="formDataUpdate.etat_commande == 'PROCESSING' || formDataUpdate.etat_commande == 'CHANGERPRIX' || formDataUpdate.etat_commande == 'RELANCER' || formDataUpdate.etat_commande == 'REPORTED' || formDataUpdate.etat_commande == 'RAMASSER' || formDataUpdate.etat_commande == 'DMSUIVIE' || formDataUpdate.etat_commande == 'ENROUTE' || formDataUpdate.etat_commande == 'TRANSIT' || formDataUpdate.etat_commande == 'INHOUSE' || formDataUpdate.etat_commande == 'HOME' || formDataUpdate.etat_commande == 'ASSIGN'">
                                    <div class="col-6" style="display: flex;justify-content: center;">
                                        <button type="button" class="btn btn-danger" @click.prevent="editStatut('ANNULER')"
                                            style="min-width: 160px;">
                                            <i class="fa fa-times"></i> {{ $t('message.Annuler') }}
                                        </button>
                                    </div>
                                    <div class="col-6" style="display: flex;justify-content: center;"
                                        v-if="(formDataUpdate.etat_commande == 'TRANSIT' || formDataUpdate.etat_commande == 'NOREPONSE' || formDataUpdate.etat_commande == 'DMSUIVIE' || formDataUpdate.etat_commande == 'REPORTED'
                                            || formDataUpdate.etat_commande == 'HOME' || formDataUpdate.etat_commande == 'CHANGERPRIX') && relaunch">
                                        <button type="button" class="btn btn-info" @click.prevent="editStatut('RELANCER')"
                                            style="min-width: 160px;">
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
                            <i class="fa fa-times"></i>
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

@media only screen and (max-width: 768px) {
    div#btn-top-package {
        justify-content: flex-end;
    }

    .chercher {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .con-select {
        margin-left: 0px;
        margin-top: 5px;
        width: 6% !important;
        min-width: 139px;
        margin-right: 18px;
    }

    .btn-sync {
        border-radius: 31px;
        color: #198ae3;
        width: 41px;
        min-width: 39px;
        border: 1px solid #198ae3;
        margin-left: 0px;
        background: transparent;
        margin-top: 5px;
    }

    .search_bar {
        width: auto;
        display: flex;
        margin-top: 5px;
        margin-left: 0px;
    }

}

.form-group.row {
    align-items: flex-end;
    justify-content: center;

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
            locale: localStorage.getItem("locale"),
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
                id_commande: '',
                ville_client_commande: "",
                id_commande_intern:'',
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
            formDataUpdate: {
                id_commande: '',
                nom_client_commande: "",
                adresse_client_commande: "",
                telephone_client_commande: "",
                prix_commande: "",
            },
            nbrCommande: 0,
            totalOrders: 0,
            villes: [],
            historiqueCommande: {},
            options: [

                { text: this.FirstLowerRestUper(this.$t('message.Order_id')), value: "id_commande" },
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
                { text: this.FirstLowerRestUper(this.$t('message.ANNULER')), value: "ANNULER" },



            ],
            formDataCherche2: {
                selected_option2: "DEFAULT",
            },
            responsable: "",
            options3: [
                { text: '1-20 ', value: '20' },
                { text: '1-50 ', value: '50' },
                { text: '1-150 ', value: '150' },
                { text: '1-200 ', value: '200' },
            ],
            formDataCherche3: {
                selected_option3: "20",
            },
            btn_confirme: false,
            historiqueFacture: [],
            stores: [],
            relaunch: '',
            commentaire_ville: '',
        };
    },
    watch: {
        // whenever question changes, this function will run
        selected_type(newQuestion, oldQuestion) {

            this.getStores();
            this.nom_err = "";
            this.formData = {
                ville_client_commande: "",
                id_commande_intern:'',
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
        async checkCommentCity(ville) {

            await axios.get("/api/getVilleCommentaire/" + ville.id)
                .then((res) => {
                    this.commentaire_ville = res.data.data[0].commentaire;

                })
                .catch((error) => console.log(res))

        },
        changeSelectedItem(item) {
            if (parseInt(item.value) == '20' && parseInt(item.value) >= this.totalOrders) {
                return '1-' + this.totalOrders + ' ' + this.$t('message.of') + this.totalOrders;
            }
            if (parseInt(item.value) == '50' && parseInt(item.value) >= this.totalOrders) {
                return '1-' + this.totalOrders + ' ' + this.$t('message.of') + this.totalOrders;
            }
            if (parseInt(item.value) == '150' && parseInt(item.value) >= this.totalOrders) {
                return '1-' + this.totalOrders + ' ' + this.$t('message.of') + this.totalOrders;
            }
            if (parseInt(item.value) == '200' && parseInt(item.value) >= this.totalOrders) {
                return '1-' + this.totalOrders + ' ' + this.$t('message.of') + this.totalOrders;
            }
            if (parseInt(item.value) == 0) {
                return '0-0 ' + this.$t('message.of') + this.totalOrders;
            }
            return item.text + ' ' + this.$t('message.of') + this.totalOrders;
        },

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
                confirmButtonColor: '#08ab5f',
                cancelButtonColor: "#d33",
                confirmButtonText: this.$t('message.Yes_I_Confirme'),
                cancelButtonText: this.$t('message.Cancel')
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
                        "<table class='table table-borderless' id='info-table' style='text-align: left;'>" +
                        "<tr><td><b>" + this.$t('message.Original') + "</b></td></tr>";

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
                        "<tr><td style='min-width: 135px;'><b>" + this.$t('message.Destination') + "</b></td></tr>" +
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
                        "</td></tr><tr><td>" + this.$t('message.Price') + " :<b> </b></td><td> " +
                        this.commande.prix_commande + ' ' + this.$t('message.Dhs') +
                        " </td></tr>";



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
            if (this.locale == 'ar') {
                document.getElementById("info-table").setAttribute("dir", "rtl");
                document.getElementById("info-table").style.textAlign = 'right';
            }
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
                                this.totalOrders = res.data.data.total
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
                                this.totalOrders = res.data.data.total
                            })
                            .catch((error) => console.log(res))
                            .finally(() => this.$vs.loading.close());
                    }
                    this.nbrCommande = this.commandes.to;
                }, 200);
            }
            if (this.totalOrders < 20) {
                this.formDataCherche3.selected_option3 = 20
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
            this.$vs.loading({ color: "#22c22b" });
            await axios
                .get("/api/afficheArticleDisponible")
                .then((res) => {
                    this.articles = res.data.data;
                })
                .catch((error) => console.log(res)).finally(() => this.$vs.loading.close());
            for (let i = 0; i < Object.keys(this.articles).length; i++) {
                this.articles[i]["quantite"] = 1;
            }
        },
        async getCommande(id) {
            // this.formData.articles
            this.$vs.loading({ color: "#22c22b" });
            await axios
                .get("/api/Commande/" + id)
                .then((res) => {
                    this.formData = res.data.data;
                    this.formData.articles = res.data.data2;
                    this.ListArticles = res.data.data2;
                })
                .catch((error) => console.log(res)).finally(() => this.$vs.loading.close());
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
            this.$vs.loading({ color: "#22c22b" });
            await axios
                .get("/api/Villes")
                .then((res) => {
                    this.villes = res.data.data;
                })
                .catch((error) => console.log(res)).finally(() => this.$vs.loading.close());
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
                            this.$vs.notify({
                                text: this.message,
                                color: statut_icon,
                                position: "top-right",
                                time: 4000,
                            });
                        } else if (
                            this.message == "Les commandes created successfully"
                        ) {
                            this.message == this.$t('message.Orders_created_successfully');
                            var statut_icon = "success";
                            this.$vs.notify({
                                text: this.message,
                                color: statut_icon,
                                position: "top-right",
                                time: 4000,
                            });
                        }
                        else if (
                            this.message == "Insufficient balance"
                        ) {
                            var statut_icon = "danger";
                            Swal.fire({
                                position: 'center',
                                icon: 'error',
                                title: this.$t('message.Insufficient_balance'),
                                html: this.$t('message.Insufficient_balance_text'),
                                showConfirmButton: true,
                            })
                        }
                        document.getElementById("btn_cancel").click();
                        this.initialiserFormData();
                        await this.classifierCommande(this.formDataCherche3.selected_option3)

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
            this.$vs.loading({ color: "#22c22b" });
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
                        this.$vs.notify({
                            title: this.message,
                            color: statut_icon,
                            position: "top-right",
                            time: 8000,
                        });
                    } else if (
                        this.message == "Erreur" ||
                        this.message == "Erreur la quantité entrer plus que le stock" || this.message == "Vous avez saisi un grand nombre de commandes pendant une journée !!"
                    ) {
                        var statut_icon = "danger";
                        this.$vs.notify({
                            title: this.message,
                            color: statut_icon,
                            position: "top-right",
                            time: 8000,
                        });
                    }

                    else if (
                        this.message == "commande created successfully"
                    ) {
                        var statut_icon = "success";
                        this.$vs.notify({
                            title: this.message,
                            color: statut_icon,
                            position: "top-right",
                            time: 8000,
                        });
                    }
                    else if (
                        this.message == "Insufficient balance"
                    ) {
                        var statut_icon = "danger";
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: this.$t('message.Insufficient_balance'),
                            html: this.$t('message.Insufficient_balance_text'),
                            showConfirmButton: true,
                        })
                    }

                    this.initialiserFormData();
                    this.getArticles();

                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                        this.nom_err = this.errors[Object.keys(this.errors)[0]];
                    }
                }).finally(() => this.$vs.loading.close());
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
        async updateCommandeInfo() {
            let formData = new FormData();
            formData.append("id_commande", this.formDataUpdate.id_commande);
            formData.append("adresse_client_commande", this.formDataUpdate.adresse_client_commande);
            formData.append("prix_commande", this.formDataUpdate.prix_commande);
            formData.append("telephone_client_commande", this.formDataUpdate.telephone_client_commande);
            formData.append("nom_client_commande", this.formDataUpdate.nom_client_commande);
            this.$vs.loading({ color: "#22c22b" });
            await axios
                .post("/api/updateCommandeInfo", formData)
                .then((res) => {

                    if (res.data.message == 'commande update successfully') {
                        this.$vs.notify({
                            title: res.data.message,
                            color: 'success',
                            position: "top-right",
                            time: 4000,
                        });
                        $("#reclamationCommande").modal("hide");
                        this.classifierCommande(this.formDataCherche3.selected_option3);

                    } else if (res.data.message == 'Erreur') {
                        this.$vs.notify({
                            title: this.$t('message.Error'),
                            color: 'danger',
                            position: "top-right",
                            time: 8000,
                        });
                    }
                    else if (res.data.message == 'Le prix doit etre superieur ou egale 0') {
                        this.$vs.notify({
                            title: this.$t('message.Price_must_be_greater_than_or_equal_to_0'),
                            color: 'danger',
                            position: "top-right",
                            time: 8000,
                        });
                    }
                    else if (res.data.message == 'Le prix doit être inférieur au prix précédent') {
                        this.$vs.notify({
                            title: this.$t('message.Price_must_be_lower_than_the_previous_price'),
                            color: 'danger',
                            position: "top-right",
                            time: 8000,
                        });
                    }

                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                        this.nom_err = this.errors[Object.keys(this.errors)[0]];
                    }
                }).finally(() => this.$vs.loading.close());
        },
        async updateCommande() {

            this.formData.selected_type = this.selected_type;
            this.formData.articles = this.ListArticles;
            this.$vs.loading({ color: "#22c22b" });
            await axios
                .post("/api/updateCommande", this.formData)
                .then((res) => {
                    this.message = res.data.message;
                    this.classifierCommande(this.formDataCherche3.selected_option3)
                    $("#ajouterCommandeStock").modal('hide')
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
                }).finally(() => this.$vs.loading.close());
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
            $("#ajouterCommandeStock").modal("show");
            if (btn_val) {
                await this.getCommande(tr.id_commande);
                this.formData.selected_commande = tr.id_commande;

            }

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
                    id_commande_intern:'',
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
                (this.nomFile = ""),
                (this.stock_quantite = ""),
                (this.formData = {
                    ville_client_commande: "",
                    id_commande_intern:'',
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

                    { text: this.FirstLowerRestUper(this.$t('message.Order_id')), value: "id_commande" },
                    { text: this.FirstLowerRestUper(this.$t('message.Name')), value: "nom_client_commande" },
                    { text: this.FirstLowerRestUper(this.$t('message.Phone_Number')), value: "telephone_client_commande" },
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
                    { text: this.FirstLowerRestUper(this.$t('message.ANNULER')), value: "ANNULER" },
                ]),
                (this.formDataCherche2 = {
                    selected_option2: "DEFAULT",
                }),
                (this.responsable = ""),
                (this.options3 = [
                    { text: '1-20 ', value: '20' },
                    { text: '1-50 ', value: '50' },
                    { text: '1-150 ', value: '150' },
                    { text: '1-200 ', value: '200' },
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
            this.formDataUpdate.prix_commande = tr.prix_commande;
            this.formDataUpdate.adresse_client_commande = tr.adresse_client_commande;
            this.formDataUpdate.id_commande = tr.id_commande;
            this.formDataUpdate.telephone_client_commande = tr.telephone_client_commande;
            this.formDataUpdate.nom_client_commande = tr.nom_client_commande;
            this.formDataUpdate.etat_commande = tr.etat_commande;
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
                    confirmButtonColor: '#08ab5f',
                    cancelButtonColor: "#d33",
                    confirmButtonText: this.$t('message.Yes_I_Confirme'),
                    cancelButtonText: this.$t('message.Close')
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
                    confirmButtonColor: '#08ab5f',
                    cancelButtonColor: "#d33",
                    confirmButtonText: this.$t('message.Yes_I_Confirme'),
                    cancelButtonText: this.$t('message.Close')
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
                    confirmButtonColor: '#08ab5f',
                    cancelButtonColor: "#d33",
                    confirmButtonText: this.$t('message.Yes_I_Confirme'),
                    cancelButtonText: this.$t('message.Close')
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
            this.$vs.loading({ color: "#22c22b" });
            await axios
                .post("/api/changeStatutCommande", formData)
                .then((res) => {
                    this.message = res.data.message;
                })
                .catch((error) => console.log(res)).finally(() => this.$vs.loading.close());
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
            this.$vs.loading({ color: "#22c22b" });
            axios.get("/api/getFacture/" + tr.id_facture, { responseType: "blob" })
                .then((res) => {
                    window.open(URL.createObjectURL(res.data));
                })
                .catch((error) => console.log(res)).finally(() => this.$vs.loading.close());
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