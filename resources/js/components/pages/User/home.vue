<template>
    <section>

        <!-- Spinner End -->
        <!-- Navbar Start -->
        <nav
            class="navbar navbar-expand-lg bg-white navbar-light shadow border-top border-5 border-primary sticky-top p-0">
            <a href="/" class="navbar-brand bg-primary d-flex align-items-center px-4 px-lg-5">
                <h2 class="mb-2 text-white"><img src="images/ColiZone.png" alt="Erreur image" width="216" height="68"></h2>
            </a>
            <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse"
                data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto p-4 p-lg-0">
                    <li class="nav-item active">
                        <router-link to="/">
                            <a href='' class="nav-link">Home</a>
                        </router-link>
                    </li>
                    <li class="nav-item active">

                        <a href='#Tracking' class="nav-link">Tracking</a>

                    </li>
                    <li class="nav-item active">

                        <a href='#Pricing' class="nav-link">Prices</a>

                    </li>
                    <li class="nav-item">
                        <router-link to="/login">
                            <a href='' class="nav-link">Login</a></router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/inscription">
                            <a href='' class="nav-link">Sign Up</a>
                        </router-link>
                    </li>
                </div>
                <span class="m-0 pe-lg-5 d-none d-lg-block"><i class="fa fa-headphones text-primary me-3"></i>+212 767 09 13 77</span>
            </div>
        </nav>
        <!-- Navbar End -->
        <!-- Carousel Start -->
        <div class="container-fluid p-0" id="Tracking">
            <div class="owl-carousel header-carousel position-relative mb-5">
                <div class="owl-carousel-item position-relative">
                    <img class="img-fluid" :src="getImage('img/carousel-1.jpg')" alt="Erreur image" width="1423" height="531">
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center"
                        style="background: rgba(6, 3, 21, .5);">
                        <div class="container">
                            <div class="row justify-content-start">
                                <div class="col-10 col-lg-8">
                                    <h5 class="text-white text-uppercase mb-3 animated slideInDown">Transport &
                                        Logistics Solution</h5>
                                    <h4 class="display-3 text-white animated slideInDown mb-4">We will get it Delivered
                                    </h4>
                                    <p class="fs-5 fw-medium text-white mb-4 pb-2">Nous vous propose le service
                                        logistique le plus simple et la meilleure expérience client du marché.</p>

                                    <div class="searchArea">
                                        <button type="button" class="btn btn-primary" @click="getHistoriqueCommande()">
                                            <i class="fa fa-search" aria-hidden="true"></i>
                                            Find
                                        </button>
                                        <div class="inputDiv">

                                            <input type="text" id="inputField" v-model="id_commande"
                                                placeholder="(EX: CASKST59SAL162145)" />
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Carousel End -->


        <!-- Service Start -->
        <div class="container-xxl py-3">
            <div class="container py-3">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <span class="text-secondary text-uppercase">OUR SERVICES</span>
                    <h1 class="mb-5">Discover our services</h1>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="service-item p-4">
                            <div class="overflow-hidden mb-4">
                                <img class="img-fluid" :src="getImage('img/ramassage.png')" alt="Erreur image" width="360" height="216">
                            </div>
                            <b class="mb-3">Pickup</b>
                            <p>Stet stet justo dolor sed duo. Ut clita sea sit ipsum diam lorem diam.</p>
   
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="service-item p-4">
                            <div class="overflow-hidden mb-4"> 
                                <img class="img-fluid" :src="getImage('img/livraison.png')" alt="Erreur image" width="360" height="216">
                            </div>
                            <b class="mb-3">Delivery</b>
                            <p>Stet stet justo dolor sed duo. Ut clita sea sit ipsum diam lorem diam.</p>
                     
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.7s">
                        <div class="service-item p-4">
                            <div class="overflow-hidden mb-4">
                                <img class="img-fluid" :src="getImage('img/warehousing.png')" alt="Erreur image" width="360" height="216">
                            </div>
                            <b class="mb-3">Warehousing</b>
                            <p>Stet stet justo dolor sed duo. Ut clita sea sit ipsum diam lorem diam.</p>

                        </div>
                    </div>

                </div>
            </div>
        </div>
   

        <div class="container-xxl py-3" id="Pricing">
            <div class="container py-3">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <span class="text-secondary text-uppercase">Pricing Plan</span>
                    <h1 class="mb-5">Perfect Pricing Plan</h1>
                </div>
                <div class="row">
                    <div class="col-lg-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <vs-table stripe :data="villes.data" :sst="true" @search="getVilles" search>
                                    <template slot="thead">
                                        <vs-th>
                                            Ville
                                        </vs-th>
                                        <vs-th>
                                            Frais livraison
                                        </vs-th>
                                        <vs-th>
                                            Frais retour
                                        </vs-th>
                                    </template>

                                    <template slot-scope="{data}">
                                        <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                                            <vs-td :data="tr.nom_ville">
                                                {{ tr.nom_ville }}
                                            </vs-td>
                                            <vs-td :data="tr.prix_livraison">
                                                {{ tr.prix_livraison }} Dhs
                                            </vs-td>
                                            <vs-td :data="tr.prix_retour">
                                                {{ tr.prix_retour }} Dhs
                                            </vs-td>
                                        </vs-tr>
                                    </template>
                                </vs-table>
                                <vs-pagination @input="getVilles()" :max="9" :total="villes.last_page"
                                    v-model="villes.current_page" prev-icon="arrow_back"
                                    next-icon="arrow_forward"></vs-pagination>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <!-- Pricing End -->





        <!-- Footer Start -->
        <div class="container-fluid bg-dark text-light footer pt-5 wow fadeIn" data-wow-delay="0.1s"
            style="margin-top: 6rem;">
            <div class="container py-3">
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <h4 class="text-light mb-4">Address</h4>
                        <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Maroc</p>
                        <p class="mb-2"><i class="fa fa-phone me-3"></i>07 67 09 13 77</p>
                        <p class="mb-2"><i class="fa fa-envelope me-3"></i>contact@Colizone.ma</p>
                        <div class="d-flex pt-2">
                            <a class="btn btn-outline-light btn-social" href="/"><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-outline-light btn-social" href="/"><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-light btn-social" href="/"><i class="fab fa-youtube"></i></a>
                            <a class="btn btn-outline-light btn-social" href="/"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h4 class="text-light mb-4">Services</h4>
                        <a class="btn btn-link" href="/">Colizone</a>

                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h4 class="text-light mb-4">Quick Links</h4>
                        <a class="btn btn-link" href="/">Colizone</a>

                    </div>
  
                </div>
            </div>
            <div class="container">
                <div class="copyright">
                    <div class="row">
                        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">

                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                            Copyright © 2023 . Design and Developed by <a class="border-bottom"
                                href="https://www.ColiZone.com">ColiZone</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="showCommande" tabindex="-1" aria-labelledby="showCommande" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Order History
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="page-content page-container" id="page-content">
                            <div class="padding">
                                <div class="row">
                                    <div class="col-lg-12">

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
                                                            v-if="hCommande.etat_commande == 'DMSUIVIE'">{{
                                                                hCommande.etat_commande
                                                            }}</b>

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
                                                        v-if="hCommande.etat_commande == 'ENROUTE'">
                                                        Package sent to
                                                        <b>{{ hCommande.nom_ville }}</b>
                                                        By:
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
                                                        with
                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'PROCESSING'">
                                                        processe By:

                                                        <b>{{ hCommande.username }}</b>
                                                        <br />
                                                        At
                                                        <b>{{ hCommande.updated_at }}
                                                        </b>
                                                    </div>
                                                    <div class="tl-date text-muted mt-1"
                                                        v-if="hCommande.etat_commande == 'PICKUP'">
                                                        Request Pickup By:

                                                        <b>{{ hCommande.clientUsername }}</b>
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
    </section>


</template>
<style scoped>
.searchArea button,
.searchArea .inputDiv,
.searchArea input {
    height: 44px;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    margin: 0;
}

.searchArea button {
    float: right;
    margin-left: -1px;
    border: solid 1px gray;
}

.searchArea .inputDiv {
    overflow: hidden;
    border: solid 1px gray;
}

.searchArea input {
    border: 0;
    width: 100%;
    padding: 0 3px;
    height: 42px;
    border-radius: 6px;
}

.searchArea {
    width: 75%;
}

button.btn.btn-primary {
    background: #198ae3;
    color: white;
}
</style>
<script>

import { mapState, mapActions } from 'vuex';

export default {
    name: "home",
    data() {
        return {
            token: localStorage.getItem('token'),
            errors: {},
            villes: {
                'data': '',
                'current_page': 1,
                'last_page': 1
            },
            id_commande:'',
            historiqueCommande:[],

        }
    },
    computed: {
        ...mapState([
            'loginClient',

        ])
    },
    methods: {
        async getVilles(valeur_recherche) {
            this.$vs.loading({ color: '#22c16b' })
            setTimeout(async () => {
                await axios.post('/api/visiteur/villes?page=' + this.villes.current_page, { 'ville': valeur_recherche })
                    .then(res => { this.villes = res.data.data; })
                    .catch(error => console.log(res)).finally(() => this.$vs.loading.close());

            }, 200);

        },
        async getHistoriqueCommande() {
            this.$vs.loading({ color: "#22c16b" });
            this.historiqueCommande = [];

            await axios.get("/api/visiteur/historiqueCommande/" + this.id_commande)
                .then((res) => {
                    this.historiqueCommande = res.data.data;
                    $("#showCommande").modal('show')
                })

                .catch((error) => console.log(res))
                .finally(() => this.$vs.loading.close());
        },
        ...mapActions([
            'isClient'
        ]),
        getImage(url) {
            // let baseurl = 'baseurl';
            if (url.match(this.baseUrl)) {
                return url;
            } else {
                return this.baseUrl + url;
            }
        }

    },

    async mounted() {

    }

}
</script>
