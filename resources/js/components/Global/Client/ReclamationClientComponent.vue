<template>
    <div class="content-wrapper-customise">
        <section class="chatBox" id="chatBox">
            <section class="msger" id="chatBox2">
                <header class="msger-header">
                    <div class="msger-header-title">
                        <i class="fas fa-comment-alt"></i> Reclamation N°: <b>{{ this.formData2.id_reclamation }}</b>
                    </div>
                    <div class="msger-header-options" style="cursor: pointer;" @click.prevent="closeChatBox">
                        <span><i class="fa fa-window-close" style=" color: #dc3545; font-size: 23px; "></i></span>
                    </div>
                </header>

                <main class="msger-chat" id="contentChatBox">
                    <div v-for="message in messages">
                        <div class="msg right-msg mb-2" v-if="message.id_client">
                            <div class="msg-img"
                                style="background-image: url(https://cdn-icons-png.flaticon.com/128/3135/3135715.png)">
                            </div>

                            <div class="msg-bubble">
                                <div class="msg-info">
                                    <div class="msg-info-name">{{ Client.nom }} {{ Client.prenom }}</div>
                                    <div class="msg-info-time"> <span><time-ago :datetime="message.created_at"
                                                style="color: white;" long></time-ago> </span></div>
                                </div>

                                <div class="msg-text">
                                    {{ message.message }}
                                </div>
                            </div>
                        </div>

                        <div class="msg left-msg mb-2" v-if="message.id_employe">
                            <div class="msg-img"
                                style="background-image: url(https://cdn-icons-png.flaticon.com/128/4140/4140048.png)">
                            </div>

                            <div class="msg-bubble">
                                <div class="msg-info">
                                    <div class="msg-info-name">{{ message.nom }} {{ message.prenom }}</div>
                                    <div class="msg-info-time"><span><time-ago :datetime="message.created_at"
                                                long></time-ago> </span></div>
                                </div>

                                <div class="msg-text">
                                    {{ message.message }}
                                </div>
                            </div>
                        </div>

                        <div class="msg left-msg mb-2" v-if="message.id_admin">
                            <div class="msg-img"
                                style="background-image: url(https://cdn-icons-png.flaticon.com/128/4140/4140048.png)">
                            </div>

                            <div class="msg-bubble">
                                <div class="msg-info">
                                    <div class="msg-info-name">Admin</div>
                                    <div class="msg-info-time"><span><time-ago :datetime="message.created_at"
                                                long></time-ago> </span></div>
                                </div>

                                <div class="msg-text">
                                    {{ message.message }}
                                </div>
                            </div>
                        </div>
                    </div>


                </main>


                <div class="msger-inputarea">
                    <input type="text" class="msger-input" placeholder="Enter your message..."
                        v-model="formData2.message">
                    <button @click.prevent="sendMessage" class="msger-send-btn">Send</button>
                </div>
            </section>
        </section>


        <div class="page-header-dashboard">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> Liste des reclamations
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <div id="btn-top-package">

                            <button type="button" style="margin: 0 13px;" class="btn btn-primary float-end"
                                data-bs-toggle="modal" data-bs-target="#ajouterReclamation" data-bs-whatever="@mdo"><i
                                    class="fa fa-plus" aria-hidden="true"></i> New Reclamation</button>

                        </div>
                        <div class="chercher">
                            <div class="search_bar">

                                <vs-select class="selectExample" v-model="formDataCherche3.selected_option3"
                                    @change="getReclamations(formDataCherche3.selected_option3)">

                                    <vs-select-item :key="index" :value="item.value" :text="item.text"
                                        v-for="item, index in options3" />

                                </vs-select>

                                <vs-select class="selectExample" v-model="formDataCherche.selected_option">



                                    <vs-select-item :key="index" :value="item.value" :text="item.text"
                                        v-for="item, index in options2" />

                                </vs-select>

                                <vs-input placeholder="Search" v-model="formDataCherche.valeur_recherche" />

                                <button class="btn-chercher"
                                    @click="getReclamations(formDataCherche3.selected_option3)"><i class="fa fa-search"
                                        aria-hidden="true"></i></button>



                            </div>

                        </div>






                        <vs-table stripe :data="reclamations.data">
                            <template slot="thead">
                                <vs-th>

                                    Id Reclamation

                                </vs-th>
                                <vs-th>

                                    Type

                                </vs-th>
                                <vs-th>

                                    Object

                                </vs-th>

                                <vs-th>

                                    Statut

                                </vs-th>
                                <vs-th>
                                    Operation
                                </vs-th>
                            </template>

                            <template slot-scope="{data}">
                                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                                    <vs-td :data="tr.id_reclamation">

                                        {{ tr.id_reclamation }}<br>

                                        <span><time-ago :datetime="tr.updated_at" long></time-ago> </span>

                                    </vs-td>

                                    <vs-td :data="tr.type_facture">
                                        <b v-if="tr.id_commande != null"
                                            class="badge badge badge-gradient-info">Order</b>
                                        <b v-else-if="tr.id_facture != null"
                                            class="badge badge badge-gradient-primary">Invoice</b>
                                        <b v-else="tr.id_facture != null"
                                            class="badge badge badge-gradient-primary">Other</b>
                                    </vs-td>
                                    <vs-td :data="tr.object_reclamation">
                                        <span class="reclamation-msg">{{ tr.object_reclamation }}</span>
                                    </vs-td>

                                    <vs-td :data="tr.statut_reclamation">
                                        <b class="badge badge badge-gradient-danger"
                                            v-if="tr.statut_reclamation == 'En Traitement'">{{
                                                tr.statut_reclamation
                                            }}</b>
                                        <b class="badge badge badge-gradient-danger"
                                            v-else-if="tr.statut_reclamation == 'Rejected'">{{
                                                tr.statut_reclamation
                                            }}</b>
                                        <b class="badge badge badge-gradient-success"
                                            v-else-if="tr.statut_reclamation == 'Done'">{{ tr.statut_reclamation }}</b>
                                        <b class="badge badge badge-gradient-primary" v-else>{{
                                            tr.statut_reclamation
                                        }}</b>
                                    </vs-td>
                                    <vs-td>
                                        <button type="button" class="btn btn-valide"
                                            @click.prevent="showDetails(tr.id_reclamation)"><i
                                                class="fa fa-eye"></i></button>
                                        <button type="button" class="btn btn-success"
                                            @click.prevent="showChat(tr.id_reclamation, tr.statut_reclamation)"><i
                                                class="fa fa-comments"></i></button>
                                    </vs-td>


                                </vs-tr>
                            </template>
                        </vs-table>
                        <vs-pagination @input="getReclamations(0)" :max="9" :total="reclamations.last_page"
                            v-model="reclamations.current_page" prev-icon="arrow_back"
                            next-icon="arrow_forward"></vs-pagination>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ajouterReclamation" tabindex="-1" aria-labelledby="ajouterReclamation"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ajouterReclamation">Add a new Claim
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="alert alert-danger" role="alert" v-if="nom_err">
                                {{ nom_err[0] }}
                            </div>
                            <label for="Type_reclamation">Type</label>

                            <select class="form-control" id="Type_reclamation" v-model="formData.Type_reclamation">
                                <option name="Type_reclamation">Order</option>
                                <option name="Type_reclamation">Invoice</option>
                                <option name="Type_reclamation">Other</option>
                            </select>
                        </div>
                        <div class="form-group"
                            v-if='formData.Type_reclamation == "Order" || formData.Type_reclamation == "Invoice"'>
                            <label for="id">{{ formData.Type_reclamation }} N°</label>

                            <input type="text" class="form-control" id="id" v-model='formData.id' name="id">
                        </div>
                        <div class="form-group">
                            <label for="object_commande">Object</label>

                            <input type="text" class="form-control" id="object_commande"
                                v-model='formData.object_reclamation' name="object_commande">
                        </div>
                        <div class="form-group">
                            <label for="message_reclamation">Message</label>

                            <!-- <input type="text" class="form-control" id="message_reclamation"
                                v-model='formData.message_reclamation' name="message_reclamation"> -->

                            <textarea id="message_reclamation" v-model='formData.message_reclamation'
                                name="message_reclamation" class="form-control" rows="3">

                            </textarea>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click.prevent="addReclamation">Validate
                            Claim</button>

                        <button type="button" id="btn_cancel" class="btn btn-secondary"
                            data-bs-dismiss="modal">Annuler</button>

                    </div>
                </div>
            </div>
        </div>







    </div>
</template>
<style scoped>
.reclamation-msg {
    max-width: 30ch;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    display: inline-block;
}

section.chatBox {
    display: none;
    position: fixed;
    z-index: 9999;
    align-items: center;
    height: 97%;

}

.msger {
    display: none;
    flex-flow: column wrap;
    justify-content: space-between;
    width: 100%;
    max-width: 667px;
    margin: 25px 10px;
    height: calc(100% - 250px);
    border: #ddd;
    border-radius: 5px;
    background: #fff;
    box-shadow: 0 15px 15px -5px rgba(0, 0, 0, 0.2);
    position: fixed;
    z-index: 99999;
}


.msger-header {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-bottom: var(--border);
    background: #eee;
    color: #666;
}

.msger-chat {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
}

.msger-chat::-webkit-scrollbar {
    width: 6px;
}

.msger-chat::-webkit-scrollbar-track {
    background: #ddd;
}

.msger-chat::-webkit-scrollbar-thumb {
    background: #bdbdbd;
}

.msg {
    display: flex;
    align-items: flex-end;
    margin-bottom: 10px;
}

.msg:last-of-type {
    margin: 0;
}

.msg-img {
    width: 50px;
    height: 50px;
    margin-right: 10px;
    background: #ddd;
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
    border-radius: 50%;
}

.msg-bubble {
    max-width: 450px;
    padding: 15px;
    border-radius: 15px;
    background: #ececec;
}

.msg-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.msg-info-name {
    margin-right: 10px;
    font-weight: bold;
}

.msg-info-time {
    font-size: 0.85em;
}

.left-msg .msg-bubble {
    border-bottom-left-radius: 0;
}

.right-msg {
    flex-direction: row-reverse;
}

.right-msg .msg-bubble {
    background: #579ffb;
    color: #fff;
    border-bottom-right-radius: 0;
}

.right-msg .msg-img {
    margin: 0 0 0 10px;
}

.msger-inputarea {
    display: flex;
    padding: 10px;
    border-top: 2px solid #ddd;
    background: #eee;
}

.msger-inputarea * {
    padding: 10px;
    border: none;
    border-radius: 3px;
    font-size: 1em;
}

.msger-input {
    flex: 1;
    background: #ddd;
}

.msger-send-btn {
    margin-left: 10px;
    background: rgb(0, 196, 65);
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.23s;
}

.msger-send-btn:hover {
    background: rgb(0, 180, 50);
}

.msger-chat {
    background-color: #fcfcfe;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='260' height='260' viewBox='0 0 260 260'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23dddddd' fill-opacity='0.4'%3E%3Cpath d='M24.37 16c.2.65.39 1.32.54 2H21.17l1.17 2.34.45.9-.24.11V28a5 5 0 0 1-2.23 8.94l-.02.06a8 8 0 0 1-7.75 6h-20a8 8 0 0 1-7.74-6l-.02-.06A5 5 0 0 1-17.45 28v-6.76l-.79-1.58-.44-.9.9-.44.63-.32H-20a23.01 23.01 0 0 1 44.37-2zm-36.82 2a1 1 0 0 0-.44.1l-3.1 1.56.89 1.79 1.31-.66a3 3 0 0 1 2.69 0l2.2 1.1a1 1 0 0 0 .9 0l2.21-1.1a3 3 0 0 1 2.69 0l2.2 1.1a1 1 0 0 0 .9 0l2.21-1.1a3 3 0 0 1 2.69 0l2.2 1.1a1 1 0 0 0 .86.02l2.88-1.27a3 3 0 0 1 2.43 0l2.88 1.27a1 1 0 0 0 .85-.02l3.1-1.55-.89-1.79-1.42.71a3 3 0 0 1-2.56.06l-2.77-1.23a1 1 0 0 0-.4-.09h-.01a1 1 0 0 0-.4.09l-2.78 1.23a3 3 0 0 1-2.56-.06l-2.3-1.15a1 1 0 0 0-.45-.11h-.01a1 1 0 0 0-.44.1L.9 19.22a3 3 0 0 1-2.69 0l-2.2-1.1a1 1 0 0 0-.45-.11h-.01a1 1 0 0 0-.44.1l-2.21 1.11a3 3 0 0 1-2.69 0l-2.2-1.1a1 1 0 0 0-.45-.11h-.01zm0-2h-4.9a21.01 21.01 0 0 1 39.61 0h-2.09l-.06-.13-.26.13h-32.31zm30.35 7.68l1.36-.68h1.3v2h-36v-1.15l.34-.17 1.36-.68h2.59l1.36.68a3 3 0 0 0 2.69 0l1.36-.68h2.59l1.36.68a3 3 0 0 0 2.69 0L2.26 23h2.59l1.36.68a3 3 0 0 0 2.56.06l1.67-.74h3.23l1.67.74a3 3 0 0 0 2.56-.06zM-13.82 27l16.37 4.91L18.93 27h-32.75zm-.63 2h.34l16.66 5 16.67-5h.33a3 3 0 1 1 0 6h-34a3 3 0 1 1 0-6zm1.35 8a6 6 0 0 0 5.65 4h20a6 6 0 0 0 5.66-4H-13.1z'/%3E%3Cpath id='path6_fill-copy' d='M284.37 16c.2.65.39 1.32.54 2H281.17l1.17 2.34.45.9-.24.11V28a5 5 0 0 1-2.23 8.94l-.02.06a8 8 0 0 1-7.75 6h-20a8 8 0 0 1-7.74-6l-.02-.06a5 5 0 0 1-2.24-8.94v-6.76l-.79-1.58-.44-.9.9-.44.63-.32H240a23.01 23.01 0 0 1 44.37-2zm-36.82 2a1 1 0 0 0-.44.1l-3.1 1.56.89 1.79 1.31-.66a3 3 0 0 1 2.69 0l2.2 1.1a1 1 0 0 0 .9 0l2.21-1.1a3 3 0 0 1 2.69 0l2.2 1.1a1 1 0 0 0 .9 0l2.21-1.1a3 3 0 0 1 2.69 0l2.2 1.1a1 1 0 0 0 .86.02l2.88-1.27a3 3 0 0 1 2.43 0l2.88 1.27a1 1 0 0 0 .85-.02l3.1-1.55-.89-1.79-1.42.71a3 3 0 0 1-2.56.06l-2.77-1.23a1 1 0 0 0-.4-.09h-.01a1 1 0 0 0-.4.09l-2.78 1.23a3 3 0 0 1-2.56-.06l-2.3-1.15a1 1 0 0 0-.45-.11h-.01a1 1 0 0 0-.44.1l-2.21 1.11a3 3 0 0 1-2.69 0l-2.2-1.1a1 1 0 0 0-.45-.11h-.01a1 1 0 0 0-.44.1l-2.21 1.11a3 3 0 0 1-2.69 0l-2.2-1.1a1 1 0 0 0-.45-.11h-.01zm0-2h-4.9a21.01 21.01 0 0 1 39.61 0h-2.09l-.06-.13-.26.13h-32.31zm30.35 7.68l1.36-.68h1.3v2h-36v-1.15l.34-.17 1.36-.68h2.59l1.36.68a3 3 0 0 0 2.69 0l1.36-.68h2.59l1.36.68a3 3 0 0 0 2.69 0l1.36-.68h2.59l1.36.68a3 3 0 0 0 2.56.06l1.67-.74h3.23l1.67.74a3 3 0 0 0 2.56-.06zM246.18 27l16.37 4.91L278.93 27h-32.75zm-.63 2h.34l16.66 5 16.67-5h.33a3 3 0 1 1 0 6h-34a3 3 0 1 1 0-6zm1.35 8a6 6 0 0 0 5.65 4h20a6 6 0 0 0 5.66-4H246.9z'/%3E%3Cpath d='M159.5 21.02A9 9 0 0 0 151 15h-42a9 9 0 0 0-8.5 6.02 6 6 0 0 0 .02 11.96A8.99 8.99 0 0 0 109 45h42a9 9 0 0 0 8.48-12.02 6 6 0 0 0 .02-11.96zM151 17h-42a7 7 0 0 0-6.33 4h54.66a7 7 0 0 0-6.33-4zm-9.34 26a8.98 8.98 0 0 0 3.34-7h-2a7 7 0 0 1-7 7h-4.34a8.98 8.98 0 0 0 3.34-7h-2a7 7 0 0 1-7 7h-4.34a8.98 8.98 0 0 0 3.34-7h-2a7 7 0 0 1-7 7h-7a7 7 0 1 1 0-14h42a7 7 0 1 1 0 14h-9.34zM109 27a9 9 0 0 0-7.48 4H101a4 4 0 1 1 0-8h58a4 4 0 0 1 0 8h-.52a9 9 0 0 0-7.48-4h-42z'/%3E%3Cpath d='M39 115a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm6-8a6 6 0 1 1-12 0 6 6 0 0 1 12 0zm-3-29v-2h8v-6H40a4 4 0 0 0-4 4v10H22l-1.33 4-.67 2h2.19L26 130h26l3.81-40H58l-.67-2L56 84H42v-6zm-4-4v10h2V74h8v-2h-8a2 2 0 0 0-2 2zm2 12h14.56l.67 2H22.77l.67-2H40zm13.8 4H24.2l3.62 38h22.36l3.62-38z'/%3E%3Cpath d='M129 92h-6v4h-6v4h-6v14h-3l.24 2 3.76 32h36l3.76-32 .24-2h-3v-14h-6v-4h-6v-4h-8zm18 22v-12h-4v4h3v8h1zm-3 0v-6h-4v6h4zm-6 6v-16h-4v19.17c1.6-.7 2.97-1.8 4-3.17zm-6 3.8V100h-4v23.8a10.04 10.04 0 0 0 4 0zm-6-.63V104h-4v16a10.04 10.04 0 0 0 4 3.17zm-6-9.17v-6h-4v6h4zm-6 0v-8h3v-4h-4v12h1zm27-12v-4h-4v4h3v4h1v-4zm-6 0v-8h-4v4h3v4h1zm-6-4v-4h-4v8h1v-4h3zm-6 4v-4h-4v8h1v-4h3zm7 24a12 12 0 0 0 11.83-10h7.92l-3.53 30h-32.44l-3.53-30h7.92A12 12 0 0 0 130 126z'/%3E%3Cpath d='M212 86v2h-4v-2h4zm4 0h-2v2h2v-2zm-20 0v.1a5 5 0 0 0-.56 9.65l.06.25 1.12 4.48a2 2 0 0 0 1.94 1.52h.01l7.02 24.55a2 2 0 0 0 1.92 1.45h4.98a2 2 0 0 0 1.92-1.45l7.02-24.55a2 2 0 0 0 1.95-1.52L224.5 96l.06-.25a5 5 0 0 0-.56-9.65V86a14 14 0 0 0-28 0zm4 0h6v2h-9a3 3 0 1 0 0 6H223a3 3 0 1 0 0-6H220v-2h2a12 12 0 1 0-24 0h2zm-1.44 14l-1-4h24.88l-1 4h-22.88zm8.95 26l-6.86-24h18.7l-6.86 24h-4.98zM150 242a22 22 0 1 0 0-44 22 22 0 0 0 0 44zm24-22a24 24 0 1 1-48 0 24 24 0 0 1 48 0zm-28.38 17.73l2.04-.87a6 6 0 0 1 4.68 0l2.04.87a2 2 0 0 0 2.5-.82l1.14-1.9a6 6 0 0 1 3.79-2.75l2.15-.5a2 2 0 0 0 1.54-2.12l-.19-2.2a6 6 0 0 1 1.45-4.46l1.45-1.67a2 2 0 0 0 0-2.62l-1.45-1.67a6 6 0 0 1-1.45-4.46l.2-2.2a2 2 0 0 0-1.55-2.13l-2.15-.5a6 6 0 0 1-3.8-2.75l-1.13-1.9a2 2 0 0 0-2.5-.8l-2.04.86a6 6 0 0 1-4.68 0l-2.04-.87a2 2 0 0 0-2.5.82l-1.14 1.9a6 6 0 0 1-3.79 2.75l-2.15.5a2 2 0 0 0-1.54 2.12l.19 2.2a6 6 0 0 1-1.45 4.46l-1.45 1.67a2 2 0 0 0 0 2.62l1.45 1.67a6 6 0 0 1 1.45 4.46l-.2 2.2a2 2 0 0 0 1.55 2.13l2.15.5a6 6 0 0 1 3.8 2.75l1.13 1.9a2 2 0 0 0 2.5.8zm2.82.97a4 4 0 0 1 3.12 0l2.04.87a4 4 0 0 0 4.99-1.62l1.14-1.9a4 4 0 0 1 2.53-1.84l2.15-.5a4 4 0 0 0 3.09-4.24l-.2-2.2a4 4 0 0 1 .97-2.98l1.45-1.67a4 4 0 0 0 0-5.24l-1.45-1.67a4 4 0 0 1-.97-2.97l.2-2.2a4 4 0 0 0-3.09-4.25l-2.15-.5a4 4 0 0 1-2.53-1.84l-1.14-1.9a4 4 0 0 0-5-1.62l-2.03.87a4 4 0 0 1-3.12 0l-2.04-.87a4 4 0 0 0-4.99 1.62l-1.14 1.9a4 4 0 0 1-2.53 1.84l-2.15.5a4 4 0 0 0-3.09 4.24l.2 2.2a4 4 0 0 1-.97 2.98l-1.45 1.67a4 4 0 0 0 0 5.24l1.45 1.67a4 4 0 0 1 .97 2.97l-.2 2.2a4 4 0 0 0 3.09 4.25l2.15.5a4 4 0 0 1 2.53 1.84l1.14 1.9a4 4 0 0 0 5 1.62l2.03-.87zM152 207a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm6 2a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm-11 1a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm-6 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm3-5a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm-8 8a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm3 6a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm0 6a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm4 7a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm5-2a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm5 4a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm4-6a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm6-4a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm-4-3a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm4-3a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm-5-4a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm-24 6a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm16 5a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm7-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0zm86-29a1 1 0 0 0 0 2h2a1 1 0 0 0 0-2h-2zm19 9a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2h-2a1 1 0 0 1-1-1zm-14 5a1 1 0 0 0 0 2h2a1 1 0 0 0 0-2h-2zm-25 1a1 1 0 0 0 0 2h2a1 1 0 0 0 0-2h-2zm5 4a1 1 0 0 0 0 2h2a1 1 0 0 0 0-2h-2zm9 0a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2h-2a1 1 0 0 1-1-1zm15 1a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2h-2a1 1 0 0 1-1-1zm12-2a1 1 0 0 0 0 2h2a1 1 0 0 0 0-2h-2zm-11-14a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2h-2a1 1 0 0 1-1-1zm-19 0a1 1 0 0 0 0 2h2a1 1 0 0 0 0-2h-2zm6 5a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2h-2a1 1 0 0 1-1-1zm-25 15c0-.47.01-.94.03-1.4a5 5 0 0 1-1.7-8 3.99 3.99 0 0 1 1.88-5.18 5 5 0 0 1 3.4-6.22 3 3 0 0 1 1.46-1.05 5 5 0 0 1 7.76-3.27A30.86 30.86 0 0 1 246 184c6.79 0 13.06 2.18 18.17 5.88a5 5 0 0 1 7.76 3.27 3 3 0 0 1 1.47 1.05 5 5 0 0 1 3.4 6.22 4 4 0 0 1 1.87 5.18 4.98 4.98 0 0 1-1.7 8c.02.46.03.93.03 1.4v1h-62v-1zm.83-7.17a30.9 30.9 0 0 0-.62 3.57 3 3 0 0 1-.61-4.2c.37.28.78.49 1.23.63zm1.49-4.61c-.36.87-.68 1.76-.96 2.68a2 2 0 0 1-.21-3.71c.33.4.73.75 1.17 1.03zm2.32-4.54c-.54.86-1.03 1.76-1.49 2.68a3 3 0 0 1-.07-4.67 3 3 0 0 0 1.56 1.99zm1.14-1.7c.35-.5.72-.98 1.1-1.46a1 1 0 1 0-1.1 1.45zm5.34-5.77c-1.03.86-2 1.79-2.9 2.77a3 3 0 0 0-1.11-.77 3 3 0 0 1 4-2zm42.66 2.77c-.9-.98-1.87-1.9-2.9-2.77a3 3 0 0 1 4.01 2 3 3 0 0 0-1.1.77zm1.34 1.54c.38.48.75.96 1.1 1.45a1 1 0 1 0-1.1-1.45zm3.73 5.84c-.46-.92-.95-1.82-1.5-2.68a3 3 0 0 0 1.57-1.99 3 3 0 0 1-.07 4.67zm1.8 4.53c-.29-.9-.6-1.8-.97-2.67.44-.28.84-.63 1.17-1.03a2 2 0 0 1-.2 3.7zm1.14 5.51c-.14-1.21-.35-2.4-.62-3.57.45-.14.86-.35 1.23-.63a2.99 2.99 0 0 1-.6 4.2zM275 214a29 29 0 0 0-57.97 0h57.96zM72.33 198.12c-.21-.32-.34-.7-.34-1.12v-12h-2v12a4.01 4.01 0 0 0 7.09 2.54c.57-.69.91-1.57.91-2.54v-12h-2v12a1.99 1.99 0 0 1-2 2 2 2 0 0 1-1.66-.88zM75 176c.38 0 .74-.04 1.1-.12a4 4 0 0 0 6.19 2.4A13.94 13.94 0 0 1 84 185v24a6 6 0 0 1-6 6h-3v9a5 5 0 1 1-10 0v-9h-3a6 6 0 0 1-6-6v-24a14 14 0 0 1 14-14 5 5 0 0 0 5 5zm-17 15v12a1.99 1.99 0 0 0 1.22 1.84 2 2 0 0 0 2.44-.72c.21-.32.34-.7.34-1.12v-12h2v12a3.98 3.98 0 0 1-5.35 3.77 3.98 3.98 0 0 1-.65-.3V209a4 4 0 0 0 4 4h16a4 4 0 0 0 4-4v-24c.01-1.53-.23-2.88-.72-4.17-.43.1-.87.16-1.28.17a6 6 0 0 1-5.2-3 7 7 0 0 1-6.47-4.88A12 12 0 0 0 58 185v6zm9 24v9a3 3 0 1 0 6 0v-9h-6z'/%3E%3Cpath d='M-17 191a1 1 0 0 0 0 2h2a1 1 0 0 0 0-2h-2zm19 9a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2H3a1 1 0 0 1-1-1zm-14 5a1 1 0 0 0 0 2h2a1 1 0 0 0 0-2h-2zm-25 1a1 1 0 0 0 0 2h2a1 1 0 0 0 0-2h-2zm5 4a1 1 0 0 0 0 2h2a1 1 0 0 0 0-2h-2zm9 0a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2h-2a1 1 0 0 1-1-1zm15 1a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2h-2a1 1 0 0 1-1-1zm12-2a1 1 0 0 0 0 2h2a1 1 0 0 0 0-2H4zm-11-14a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2h-2a1 1 0 0 1-1-1zm-19 0a1 1 0 0 0 0 2h2a1 1 0 0 0 0-2h-2zm6 5a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2h-2a1 1 0 0 1-1-1zm-25 15c0-.47.01-.94.03-1.4a5 5 0 0 1-1.7-8 3.99 3.99 0 0 1 1.88-5.18 5 5 0 0 1 3.4-6.22 3 3 0 0 1 1.46-1.05 5 5 0 0 1 7.76-3.27A30.86 30.86 0 0 1-14 184c6.79 0 13.06 2.18 18.17 5.88a5 5 0 0 1 7.76 3.27 3 3 0 0 1 1.47 1.05 5 5 0 0 1 3.4 6.22 4 4 0 0 1 1.87 5.18 4.98 4.98 0 0 1-1.7 8c.02.46.03.93.03 1.4v1h-62v-1zm.83-7.17a30.9 30.9 0 0 0-.62 3.57 3 3 0 0 1-.61-4.2c.37.28.78.49 1.23.63zm1.49-4.61c-.36.87-.68 1.76-.96 2.68a2 2 0 0 1-.21-3.71c.33.4.73.75 1.17 1.03zm2.32-4.54c-.54.86-1.03 1.76-1.49 2.68a3 3 0 0 1-.07-4.67 3 3 0 0 0 1.56 1.99zm1.14-1.7c.35-.5.72-.98 1.1-1.46a1 1 0 1 0-1.1 1.45zm5.34-5.77c-1.03.86-2 1.79-2.9 2.77a3 3 0 0 0-1.11-.77 3 3 0 0 1 4-2zm42.66 2.77c-.9-.98-1.87-1.9-2.9-2.77a3 3 0 0 1 4.01 2 3 3 0 0 0-1.1.77zm1.34 1.54c.38.48.75.96 1.1 1.45a1 1 0 1 0-1.1-1.45zm3.73 5.84c-.46-.92-.95-1.82-1.5-2.68a3 3 0 0 0 1.57-1.99 3 3 0 0 1-.07 4.67zm1.8 4.53c-.29-.9-.6-1.8-.97-2.67.44-.28.84-.63 1.17-1.03a2 2 0 0 1-.2 3.7zm1.14 5.51c-.14-1.21-.35-2.4-.62-3.57.45-.14.86-.35 1.23-.63a2.99 2.99 0 0 1-.6 4.2zM15 214a29 29 0 0 0-57.97 0h57.96z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
</style>
<script>
import axios from 'axios'
import { TimeAgo } from 'vue2-timeago'
import 'vue2-timeago/dist/vue2-timeago.css'
export default {
    props: ['Client'],
    name: 'ReclamationClientComponent',
    components: { TimeAgo, },
    data() {
        return {
            token: localStorage.getItem('token'),
            edit: false,
            errors: {},
            reclamations: {
                'data': '',
                'current_page': 1,
                'last_page': 1
            },
            formData: {
                object_reclamation: '',
                message_reclamation: '',
                id: '',
                Type_reclamation: 'Order',
            },
            formData2: {
                message: '',
                id_reclamation: '',
            },

            nom_err: '',
            options3: [
                { text: '1-20 items', value: '20' },
                { text: '1-50 items', value: '50' },
                { text: '1-150 items', value: '150' },

            ],
            formDataCherche3: {
                selected_option3: '20',

            },
            options2: [
                { text: 'Default', value: 'default' },
                { text: 'Invoice N°', value: 'id_facture' },
                { text: 'Order N°', value: 'id_commande' },

            ],

            formDataCherche: {
                selected_option: 'default',
                valeur_recherche: '',
                selected_option2: '',
            },
            messages: {},
            realTimeOption: '',
        }
    },


    methods: {
        async getReclamations(count_nbr) {
            this.formDataCherche.selected_option2 = this.formDataCherche3.selected_option3;
            this.$vs.loading({ color: '#22c16b' })
            setTimeout(async () => {
                if (count_nbr > 1) {
                    await axios.post('/api/getReclamation?page=' + this.reclamations.current_page + '&count_nbr=' + count_nbr, this.formDataCherche)
                        .then(res => { this.reclamations = res.data.data; })
                        .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
                } else {
                    await axios.post('/api/getReclamation?page=' + this.reclamations.current_page + '&count_nbr=20', this.formDataCherche)
                        .then(res => { this.reclamations = res.data.data; })
                        .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
                }
            }, 200);

        },
        async addReclamation() {
            this.$vs.loading({ color: '#22c16b' })
            await axios.post('/api/Reclamation', this.formData).then((res) => {
                this.getReclamations();
                if (res.data.message == 'Claim created successfully') {
                    this.$vs.notify({
                        time: 5000,
                        title: `Claim created successfully`,
                        color: 'success',
                        position: 'top-right',

                    });
                    $("#ajouterReclamation").modal('hide')
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
            }).finally(() => this.$vs.loading.close());
        },
        async showDetails(id) {
             this.$vs.loading({ color: '#22c16b' })
            await axios.get('/api/Reclamation/' + id)

                .then(res => {
                    console.log(res.data.data['id_commande'])
                    var text = "<table class='table table-borderless' style='text-align: left;'>" +
                        "<tr><td><b>Object:</b></td></tr><tr><td>" + res.data.data['object_reclamation'];
                    if (res.data.data['id_commande'] != null) {
                        text += "<tr><td><b>Id Commande</b></td></tr><tr><td>" + res.data.data['id_commande']
                    }


                    Swal.fire({
                        title: 'Reclamation N°: ' + res.data.data['id_reclamation'],
                        html: text,
                        showCancelButton: false,
                    })


                })
                .catch(error => console.log(res)).finally(() => this.$vs.loading.close());;
        },

        async showChat(id, statut_reclamation) {
             this.$vs.loading({ color: '#22c16b' })
            if (this.realTimeOption != '') {
                window.clearInterval(this.realTimeOption)
            }
            this.formData2.id_reclamation = id;
            await axios.get('/api/getMessages/' + id)

                .then(res => {
                    this.messages = res.data.data
                })
                .catch(error => console.log(res)).finally(() => this.$vs.loading.close());;

            if (statut_reclamation != 'Done' && statut_reclamation != 'Close') {
                this.realTimeOption = setInterval(async () => {
                    await axios.get('/api/getMessages/' + id)
                        .then(res => {
                            this.messages = res.data.data
                        })
                        .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
                }, 15000);
            }


            document.getElementById('chatBox').style.display = 'flex'
            document.getElementById('chatBox2').style.display = 'flex'
            document.getElementById("contentChatBox").scrollTop = document.getElementById("contentChatBox").scrollHeight;
        },
        async sendMessage() {
             this.$vs.loading({ color: '#22c16b' })
            await axios.post('/api/sendMessage', this.formData2)
                .then(async (res) => {
                    if (res.data.message == 'Your claim is Close') {
                        this.$vs.notify({
                            time: 5000,
                            title: res.data.message,
                            color: "danger",
                            position: "top-right",
                        });
                    } else if (res.data.message == 'Your claim is Done') {
                        this.$vs.notify({
                            time: 5000,
                            title: res.data.message,
                            color: "success",
                            position: "top-right",
                        });
                    }

                    await axios.get('/api/getMessages/' + this.formData2.id_reclamation)
                        .then(res => {
                            this.messages = res.data.data;


                        })
                        .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
                })
                .catch(error => console.log(res)).finally(() => this.$vs.loading.close());



            document.getElementById("contentChatBox").scrollTop = document.getElementById("contentChatBox").scrollHeight;
            this.formData2.message = '';

        },
        closeChatBox() {
            document.getElementById('chatBox').style.display = 'none';
            if (this.realTimeOption != '') {
                window.clearInterval(this.realTimeOption)
            }
        },

        initialiserFormData() {
            this.errors = {},
                this.reclamations = {
                    'data': '',
                    'current_page': 1,
                    'last_page': 1
                },
                this.formData = {
                    object_reclamation: '',
                    message_reclamation: '',
                    id: '',
                    Type_reclamation: 'Invoice',
                },
                this.nom_err = '',
                this.options3 = [
                    { text: '1-20 items', value: '20' },
                    { text: '1-50 items', value: '50' },
                    { text: '1-150 items', value: '150' },

                ],
                this.formDataCherche3 = {
                    selected_option3: '20',

                },
                this.options2 = [
                    { text: 'Invoice N°', value: 'id_facture' },

                ],

                this.formDataCherche = {
                    selected_option: 'id_facture',
                    valeur_recherche: '',
                    selected_option2: '',
                }
        }
    },

    async beforeMount() {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`


    },
    mounted() {

    },

}
</script>
