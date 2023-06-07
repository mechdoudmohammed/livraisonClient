<!DOCTYPE html>
<html>

<head>
    <title>Package</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="{{ public_path('css\style.css') }}">
    <link rel="stylesheet" href="{{ public_path('css\bootstrap.min.css') }}" media="all" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    <style>
        section.sheet.padding-10mm {
            background: white;
        }

        .test {
            margin-left: 10px;

        }

        .page-break {
            page-break-after: always;
        }

        .col-6 {
            flex: 0 0 auto;
            width: 47%;
            display: inline-block;
        }

        .statut {
            border: 1.5px solid black;
            padding-right: 26px;
            margin-left: 50px;
        }

        .facture_info {
            text-align: start;
            font-size: 12px;
        }
    </style>
</head>

<body class="A4">

    <div class="row test">

        @foreach($data as $key)
        <div class="col-6 sticker" style="margin:3px 1%;border:1.5px solid" v-for="(item, index) in getpackageClient" :key="index">
            <div class="row company" style="border: 1.5px solid;font-weight: 600;text-align: center;">
                <table style="margin-left:6px;margin-bottom:15px;margin-top:15px">
                    <tr>
                        <td style="width:150px ; padding:5px;">
                            <img src="{{ public_path('/images/logoFiles.png') }}" width="120" height="45" />
                        </td>
                        <td style="width:320px;" class='facture_info'>
                            <b>ColiZone S.A.R.L</b><br />
                            <b>Mag 4 Résidence Assafa Av. Al Joulane Fés</b><br />
                            <b>08 08 67 01 71</b><br />

                        </td>
                    </tr>


                </table>

            </div>
            <div class="row" style="border: 1.5px solid;display: flex;padding: 16px; text-align:center;justify-content: center;">
                <div class="col-9" style="width: 80% !important;float: right;display: grid;justify-content: center;align-items: center;align-content: center;text-align: center;">
                    <b style="margin-right: 15px">{{ $key->id_commande }}</b>
                    {!! DNS1D::getBarcodeHTML($key->id_commande, 'C39',1,47) !!}
                </div>
                <div class="col-3" >
                    {!! DNS2D::getBarcodeHTML($key->id_commande, 'QRCODE',4,4) !!}
                </div>

            </div>
            <div class="row client" style="border: 1.5px solid;padding:16px;height:117px">
                <span style="font-size: 16px;font-weight: 600;margin-left:6px">Destination:</span>
                <table style="margin-left:10px;margin-right:10px;">
                    <tr>
                        <td style="width:250px">
                            <b style="font-size: 14px;margin-bottom: 2px;">
                                {{$key->ville}}
    </b><b style='font-size:11px'>(Hub-{{$key->nom_zone}})</b>
                        </td>
                        <td>
                            <b style="font-size: 14px;margin-bottom: 2px;">{{$key->nom_client_commande}}
    </b>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b style="font-size: 14px;margin-bottom: 2px;">
                                {{$key->adresse_client_commande}}
    </b>
                        </td>
                        <td>
                            <b style="font-size: 14px;margin-bottom: 2px;">{{$key->telephone_client_commande}}</b>

                        </td>
                    </tr>
                </table>

            </div>
            <div class="row price" style="border: 1.5px solid;padding: 15px;display: flex;justify-content: center;align-items: center;height:220px">
                <div class="col-6" style='margin-bottom: 24px;'>
                    <table>
                         <tr>
                    @if($key->type_autorisation =='allow')
                        <td style="font-size: 15px;text-align: center;"><img src="{{ public_path('/images/colis-ouverture.png') }}" width="25" height="25" /></td>
                        @else
                        <td style="font-size: 15px;text-align: center;"><img src="{{ public_path('/images/colis-close.png') }}" width="25" height="25" /></td>
                        @endif
  </tr>
                        <tr>
                            <td style="font-size: 13px;">Paiment à la livraison</td>
                        </tr>
                        <tr>
                            <td style="font-size: 15px;font-weight: 600;text-align: center;"> {{$key->prix_commande}} Dhs</td>
                        </tr>
                        <tr>
                            <td style="font-size: 15px;text-align: center;">{{ date('d-m-Y', strtotime($key->created_at)) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-6">
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
                <table style="width: 100%;">
                    <tr>
                       
                        <td style="text-align:center ;font-size: 14px;font-weight: 600;">
                            {{$key->additional_commentaire}}
                        </td>
                    </tr>
                    @php
                    $detailscommandes=DB::table('detailscommandes')->join('articles','articles.id_article','detailscommandes.id_article')
                    ->where('id_commande',$key->id_commande)->get();
                    @endphp

                    <tr>
                        <td colspan="4">

                            @for ($i = 0 ; $i <= count($detailscommandes)-1 ; $i++) @if($i<4) {{$detailscommandes[$i]->nom_article}} <b>{{$detailscommandes[$i]->quantite_article}}</b>
                                @if($i<count($detailscommandes)-1) | @endif @endif @endfor </td>
                    </tr>


                </table>

            </div>
            <div class="row origine" style="border: 1.5px solid;padding:10px;height:130px">
                <span style="font-size: 16px;font-weight: 600;margin-left:6px">Origine:</span>
                <table style="margin-left:10px;margin-right:10px;width: 100%;">
                    <tr>
                        <td style="width:250px">
                            <b style="font-size: 14px;margin-bottom: 6px;">

                                @if(isset($key->siteweb_store))
                                {{$key->siteweb_store}}
                                @else
                                {{$key->website}}
                                @endif
    </b>
                        </td>
                        <td>
                            <b style="font-size: 14px;margin-bottom: 6px;">
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
                            <b style="font-size: 14px;margin-bottom: 6px;">
                                @if($key->telephone_store)
                                {{$key->telephone_store}}
                                @else
                                {{$key->telephone_client}}
                                @endif
    </b>
                        </td>
                        <!-- <td>
                            <b style="font-size: 14px;margin-bottom: 6px;">
                          @if($key->ville_client)
                                {{$key->ville_client}}
                                @else
                                {{$key->store_ville}}
                                @endif
    </b>
                        </td> -->
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center ;"><span style="font-size: 14px;font-weight: 600;text-align: center;margin-right:15px">
                                @if($key->nom_store)
                                {{$key->nom_store}}
                                @else
                                {{$key->company}}
                                @endif
                                vous remercie pour votre commande</span></td>
                    </tr>
                </table>



            </div>

            <div v-if='(((index + 1) % 4 == 0) && (getpackageClient.length > (index + 1)))' class="html2pdf__page-break">
            </div>
        </div>
        @endforeach

    </div>

</body>


</html>