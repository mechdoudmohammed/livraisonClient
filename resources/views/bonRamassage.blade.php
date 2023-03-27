<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Facture</title>
    <!-- Favicon -->
    <link rel="icon" href="./images/favicon.png" type="image/x-icon" />
    <!-- Invoice styling -->
    <style>
        body {
            /* font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; */
            text-align: center;
            color: #777;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
          
            margin: auto;
            padding: 30px;

            font-size: 16px;
            line-height: 24px;
            
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .invoice-box table tr.heading td {
            background: #00b4c2;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            color: white;
        }

        .invoice-box table tr.details td {
            text-align: end;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            font-weight: bold;
            text-align: end;

        }

        td.total-text {
            text-align: end;
        }

        .facture_info {
            text-align: end;
        }

        tr.heading {
            text-align: end;
        }

        tr.item {
            text-align: end;
        }

        .footer {
            height: 6px;
            background: #00b4c2;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }

        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table>
            <td>
                <table>
                    <tr>

                    <td class="title">
                              <img src="{{ public_path('/images/logoFiles.png') }}" alt="Company logo" style="width: 37%; max-width: 330px" />
                        </td>

                        <td class='facture_info'>
                            <b>ColiZone S.A.R.L</b><br />
                            <b>CITE DHAR MEHREZ FES</b><br />
                            <b>+212 767 09 13 77</b><br />
                            <b>Contact@ColiZone.ma</b><br />
                        </td>
                    </tr>
                </table>
            </td>
        </table>
        <table>
            <tr class="information">

                <td>
                    Nom & prenom: <b>{{$data->nom}} {{$data->prenom}}</b>
                </td>
                <td>
                    Client N°: <b>{{$data->id}}</b>
                </td>
            </tr>
            <tr>
                <td>
                    Adresse : <b>{{$data->adresse}}</b>
                </td>
                <td>
                    CNI: <b>{{$data->cin}}</b>
                </td>
            </tr>
    


        </table>
        <table>
            <tr class="heading">
                <td>ID Commande</td>
                <td>Nom</td>
                <td>Telephone</td>
                <td>Ville</td>
                <td>Prix Commande</td>
                

            </tr>
            @foreach($data2 as $key)
           
            <tr class="item">
                <td>{{ $key->id_commande }}</td>
                <td>{{ $key->nom_client_commande }}</td>
                <td>{{ $key->telephone_client_commande }}</td>
                <td>{{ $key->ville_client }}</td>
                <td>{{ $key->prix_commande }} Dhs</td>

            @endforeach


        </table>
        <div class="footer">

        </div>
        <table>
            <tr class="information">
                <td>Signature Client</td>
                <td >Signature Responsable</td>
                

            </tr>


        </table>
    </div>

</body>

</html>