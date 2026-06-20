<!DOCTYPE html>
<html>

<head>
    <title>Article</title>
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
            font-size: 17px;
        }
    </style>
</head>

<body class="A4">

    <div class="row test">
        @foreach($data as $key)
        <div class="col-10 sticker" style="margin:5px 1%;border:2px solid" v-for="(item, index) in getpackageClient" :key="index">
            <div class="row company" style="border: 1px solid;font-weight: 600;text-align: center;">
                <table style="margin-left:10px;margin-bottom:15px;margin-top:15px">
                    <tr>
                        <td style="width:170px ; padding:5px;">
                            <img src="{{ public_path('/images/logoFiles2.png') }}" width="150" height="55" />
                        </td>
                        <td style="width:600px;" class='facture_info'>
                            <b>ColiZone</b><br />
                            <b>Mag 4 Hub Fés</b><br />
                            <b>08 08 67 01 71</b><br />
                        </td>
                    </tr>


                </table>

            </div>

            <div class="row" style="border: 1.5px solid;display: flex;padding: 16px;">
                <div class="col-9" style="width: 80% !important;float: right;display: grid;">
                    <b style="margin-left: 20px;font-size:19px">{{$key->id_article}} </b>

                      {!! DNS1D::getBarcodeHTML($key->id_article, 'C39',2,67) !!}

                </div>
                <div class="col-3" width='width: 25% !important;'>
                    {!! DNS2D::getBarcodeHTML($key->id_article, 'QRCODE',5,5) !!}
                </div>

            </div>

            <div class="row client" style="border: 1px solid;" style="margin-left:10px;margin-bottom:35px;margin-top:25px">
                <span style="font-size: 18px;font-weight: 600;margin-left:10px">Information Article:</span>
                <table style="margin-left:20px;width: 100%;margin-bottom:15px;margin-top:15px">
                    <tr>
                        <td>
                            <p style="font-size: 17px;margin-bottom: 8px;">Article Id: {{$key->id_article}}
                            </p>
                        </td>
                        <td>
                            <p style="font-size: 17px;margin-bottom: 8px;">Statut :
                                @if($key->etat_article=='En stock')
                                En stock
                                @elseif($key->etat_article=='En traitement')
                                Nouveaux
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="font-size: 17px;margin-bottom: 8px;">Nom: {{$key->nom_article}}
                            </p>
                        </td>
                        <td style="width:350px">
                            <p style="font-size: 17px;margin-bottom:8px;">Prix: {{$key->prix_article}} Dhs
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row origine" style="border: 1px solid;" style="margin-left:10px;margin-bottom:35px;margin-top:25px">
                <span style="font-size: 18px;font-weight: 600;margin-left:10px">Origine:</span>
                <table style="margin-left:20px;width: 100%;margin-bottom:15px;margin-top:15px">
                    <tr>
                        <td>
                            <p style="font-size: 17px;margin-bottom: 8px;">{{$key->nom}} {{$key->prenom}}</p>
                        </td>
                        <td>
                            <p style="font-size: 17px;margin-bottom: 8px;">{{$key->company}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="font-size: 17px;margin-bottom: 8px;">{{$key->telephone}}</p>
                        </td>
                        <td style="width:350px">
                            <p style="font-size: 17px;margin-bottom: 8px;">{{$key->website}}</p>
                        </td>
                    </tr>
                </table>



            </div>

        </div>
        @endforeach
    </div>


</body>


</html>
