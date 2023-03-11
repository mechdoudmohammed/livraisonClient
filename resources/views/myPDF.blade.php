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
            border: 1px solid black;
            padding-right: 26px;
            margin-left: 50px;
        }
    </style>
</head>

<body class="A4">

    <div class="row test">

        @foreach($data as $key)
        <div class="col-6 sticker" style="margin:5px 1%;border:1.5px solid" v-for="(item, index) in getpackageClient" :key="index">
            <div class="row company" style="border: 1px solid;font-weight: 600;text-align: center;">
                <table style="margin-left:10px">
                    <tr>
                        <td style="width:80px ; padding:10px">
                            <img src="{{ public_path('/images/logo.png') }}" width="50" height="50" />
                        </td>
                        <td>
                            <span style="font-size: 12px;">ColiZone</span>
                        </td>
                    </tr>
                    <tr>

                </table>

            </div>
            <div class="row" style="border: 1px solid;display: flex;padding: 16px; text-align:center;justify-content: center;">
                <div class="col-8" style="width: 70% !important;float: right;display: grid;justify-content: center;align-items: center;align-content: center;text-align: center;">
                    <b style="margin-right: 55px;">{{ $key->id_commande }}</b>
                    {!! DNS1D::getBarcodeHTML($key->id_commande, 'C128',1,30) !!}
                </div>
                <div class="col-3" width='width: 30% !important;'>
                    {!! DNS2D::getBarcodeHTML($key->id_commande, 'QRCODE',4,4) !!}
                </div>

            </div>
            <div class="row client" style="border: 1px solid;padding:16px;height:130px">
                <span style="font-size: 15px;font-weight: 600;margin-left:10px">Destination:</span>
                <table style="margin-left:20px">
                    <tr>
                        <td style="width:300px">
                        <p style="font-size: 12px;margin-bottom: 2px;">
                                {{$key->ville}}
                            </p>
                        </td>
                        <td>
                        <p style="font-size: 12px;margin-bottom: 2px;">{{$key->nom_client_commande}}
                            </p>
                          
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="font-size: 12px;margin-bottom: 2px;">
                                {{$key->adresse_client_commande}}
                            </p>
                        </td>
                        <td>
                        <p style="font-size: 12px;margin-bottom: 2px;">{{$key->telephone_client_commande}}</p>
                       
                        </td>
                    </tr>
                </table>

            </div>
            <div class="row price" style="border: 1px solid;padding: 15px;display: flex;justify-content: center;align-items: center;height:200px">
                <div class="col-6" style='margin-bottom: 24px;'>
                    <table>
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
                <table style="margin-left:20px;width: 100%;">
                    <tr>
                        @if($key->type_autorisation =='allow')
                        <td><img src="{{ public_path('/images/colis-ouverture.png') }}" width="25" height="25" /></td>
                        @else
                        <td><img src="{{ public_path('/images/colis-close.png') }}" width="25" height="25" /></td>
                        @endif
                        <td style="text-align:center ;"><span style="font-size: 12px;font-weight: 600;text-align: center;">
                                {{$key->additional_commentaire}}</span></td>
                    </tr>
                    @php
                    $detailscommandes=DB::table('detailscommandes')->join('articles','articles.id','detailscommandes.id_article')
                    ->where('id_commande',$key->id_commande)->get();
                    @endphp

                    <tr>
                        <td colspan="4">

                            @for ($i = 0 ; $i <= count($detailscommandes)-1 ; $i++)
                            @if($i<4)
                            {{$detailscommandes[$i]->nom_article}} <b>{{$detailscommandes[$i]->quantite_article}}</b>
                             @if($i<count($detailscommandes)-1)
                             |
                             @endif
                            @endif
                       
                                @endfor
                             

                        </td>
                    </tr>


                </table>

            </div>
            <div class="row origine" style="border: 1px solid;padding:10px;height:150px">
                <span style="font-size: 15px;font-weight: 600;margin-left:10px">Origine:</span>
                <table style="margin-left:20px;width: 100%;">
                    <tr>
                        <td style="width:250px">
                            <p style="font-size: 12px;margin-bottom: 6px;">

                            @if(isset($key->siteweb_store))
                            {{$key->siteweb_store}}
                            @else
                            {{$key->website}}
                            @endif</p>
                        </td>
                        <td>
                            <p style="font-size: 12px;margin-bottom: 6px;">
                            @if($key->nom_store)
                            {{$key->nom_store}}
                            @else
                            {{$key->company}}
                            @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="font-size: 12px;margin-bottom: 6px;">
                            @if($key->telephone_store)
                            {{$key->telephone_store}}
                            @else
                            {{$key->telephone_client}}
                            @endif
                            </p>
                        </td>
                        <td>
                            <p style="font-size: 12px;margin-bottom: 6px;">
                            @if($key->adresse_store)
                            {{$key->adresse_store}}
                            @else
                            {{$key->adresse}}
                            @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center ;"><span style="font-size: 12px;font-weight: 600;text-align: center;">
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

            <div v-if='(((index + 1) % 4 == 0) && (getpackageClient.length > (index + 1)))' class="html2pdf__page-break">
            </div>
        </div>
        @endforeach

    </div>

</body>


</html>