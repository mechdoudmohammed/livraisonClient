<!DOCTYPE html>
<html>

<head>
    <title>Package</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="{{ public_path('css\style.css') }}">
    <link rel="stylesheet" href="{{ public_path('css\bootstrap.min.css') }}" media="all" />


    <style>
        .page-break {
            page-break-after: always;
        }

        .statut {
            border: 1px solid black;
            padding-right: 26px;
            margin-left: 50px;
        }

        .facture_info {
            text-align: start;
            font-size: 12px;
        }
    </style>
</head>

<body>
@php

@endphp

    @foreach($data as $key)
    <section>


        <div style="border:2px solid ;width:100%;height:134mm;max-height:134mm" v-for="(item, index) in getpackageClient" :key="index">
            <div class="row company" style="border-top: 1px solid;font-weight: 600;text-align: center;">
                <table style="margin-left:10px;margin-bottom:15px;margin-top:15px">
                    <tr>
                        <td style="width:170px ; padding:5px;">
                            <img src="{{ public_path('/images/logoFiles2.png') }}" width="130" height="45" />
                        </td>
                        <td style="width:300px;" class='facture_info'>
                            <b>ColiZone</b><br />
                            <b>Mag 4 Hub Fés</b><br />
                            <b>08 08 67 01 71</b><br />
                        </td>
                    </tr>


                </table>

            </div>
            <div class="row" style="border-top: 1px solid;display: flex;padding: 8px; text-align:center;justify-content: center;">
                <div class="col-9" style="width: 70% !important;float: right;display: grid;justify-content: center;align-items: center;align-content: center;text-align: center;">
                    <b style="margin-top:20px;margin-right: 50px">{{ $key->id_commande }}</b>
                    {!! DNS1D::getBarcodeHTML($key->id_commande, 'C39',1,47) !!}
                </div>
                <div class="col-3" style="margin-left:15px !important;">
                    {!! DNS2D::getBarcodeHTML($key->id_commande, 'QRCODE',4,4) !!}
                </div>

            </div>
            <div class="row client" style="border-top: 1px solid;padding:4px;">
                <span style="font-size: 15px;font-weight: 600;margin-left:20px">Destination:</span>
                <table style="margin-left:20px">
                    <tr>
                        <td style="width:330px">
                            <p style="font-size: 14px;margin-bottom: 1px;">
                               <b> {{$key->ville}}</b><b style='font-size:11px'>(Hub-{{$key->nom_zone}})</b>
                            </p>
                        </td>
                        <td>
                            <p style="font-size: 14px;margin-bottom:1px;"><b>{{$key->nom_client_commande}}</b>
                            </p>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="font-size: 14px;margin-bottom: 1px;">
                               <b> {{$key->adresse_client_commande}}</b>
                            </p>
                        </td>
                        <td>
                            <p style="font-size: 14px;margin-bottom: 1px;"><b>{{$key->telephone_client_commande}}<b></p>

                        </td>
                    </tr>
                </table>

            </div>
            <div class="row price" style="border-top: 1px solid;padding-top: 5px;display: flex;justify-content: center;align-items: center;">
                <div class="col-5" style="margin-left:10px;justify-content: center;align-items: center;align-content: center;text-align: center; position:absolute">
                    <table>
                        <tr>
                            @if($key->type_autorisation =='allow')
                            <td style="font-size: 15px;text-align: center;"><img src="{{ public_path('/images/colis-ouverture.png') }}" width="30" height="30" /></td>
                            @else
                            <td style="font-size: 15px;text-align: center;"><img src="{{ public_path('/images/colis-close.png') }}" width="30" height="30" /></td>
                            @endif
                        </tr>
                        <tr>

                            <td style="font-size: 13px;margin-top:13px">Paiment à la livraison</td>
                        </tr>
                        <tr>
                            <td style="font-size: 17px;font-weight: 700;text-align: center;"> {{$key->prix_commande}} Dhs</td>
                        </tr>
                        <tr>
                            <td style="font-size: 15px;text-align: center;">{{ date('d-m-Y', strtotime($key->created_at)) }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-6" style="float: right;display: grid;justify-content: center;align-items: center;align-content: center;text-align: center;">
                    <table style="border-spacing:3px; border-collapse: separate;">
                        <tr>
                            <td>PR</td>
                            <td class="statut"></td>
                            <td class="statut"></td>
                            <td class="statut"></td>
                            <td class="statut"></td>
                        </tr>
                        <tr>
                            <td>IJ</td>
                            <td class="statut"></td>
                            <td class="statut"></td>
                            <td class="statut"></td>
                            <td class="statut"></td>
                        </tr>
                        <tr>
                            <td>RP</td>
                            <td class="statut"></td>
                            <td class="statut"></td>
                            <td class="statut"></td>
                            <td class="statut"></td>
                        </tr>

                        <tr>
                            <td>AN</td>
                            <td class="statut"></td>
                            <td class="statut"></td>
                            <td class="statut"></td>
                            <td class="statut"></td>
                        </tr>


                    </table>

                </div>
                <table style="margin-left:20px;width: 100%;">
                    <tr>

                        <td style="text-align:center ;font-size: 11px;font-weight: 600">
                            {{$key->additional_commentaire}}
                        </td>
                    </tr>
                    @php
                    $detailscommandes=DB::table('detailscommandes')->join('articles','articles.id_article','detailscommandes.id_article')
                    ->where('id_commande',$key->id_commande)->get();
                    @endphp

                    <tr>
                        <td colspan="4" style="font-size: 13px;">

                            @for ($i = 0 ; $i <= count($detailscommandes)-1 ; $i++) @if($i<4) {{$detailscommandes[$i]->nom_article}} <b>{{$detailscommandes[$i]->quantite_article}}</b>
                                @if($i<count($detailscommandes)-1) | @endif @endif @endfor </td>
                    </tr>


                </table>

            </div>
            <div class="row origine" style="border-top: 1px solid;padding:2px">
                <span style="font-size: 15px;font-weight: 600;margin-left:20px">Origine:</span>
                <table style="margin-left:20px;width: 100%;">
                    <tr>
                        <td style="width:250px">
                            <b style="font-size: 14px;margin-bottom: 1px;">

                                @if(isset($key->siteweb_store))
                                {{$key->siteweb_store}}
                                @else
                                {{$key->website}}
                                @endif
    </b>
                        </td>
                        <td>
                            <b style="font-size: 14px;margin-bottom: 1px;">
                                @if($key->nom_store)
                                {{$key->nom_store}}
                                @else
                                {{$key->company}}
                                @endif
    </b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b style="font-size: 14px;margin-bottom: 1px;">
                            @if($key->tele_store)
                                {{$key->tele_store}}
                                @elseif($key->telephone_store)
                                {{$key->telephone_store}}
                                @else
                                {{$key->telephone_client}}
                                @endif
    </b>
                        </td>
                        <!-- <td>
                            <b style="font-size: 14px;margin-bottom: 1px;">
                                @if($key->ville_client)
                                {{$key->ville_client}}
                                @else
                                {{$key->store_ville}}
                                @endif
    </b>
                        </td> -->
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center ;"><span style="font-size: 14px;font-weight: 600;text-align: center;margin-right:20px">
                                @if($key->nom_store)
                                {{$key->nom_store}}
                                @else
                                {{$key->company}}
                                @endif
                                vous
                                remercie pour votre commande</span></td>
                    </tr>
                </table>



            </div>


        </div>

    </section>
    <div class="page-break">
    </div>
    @endforeach




</body>


</html>
