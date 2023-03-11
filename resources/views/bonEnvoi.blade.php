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
            max-width: 800px;
            margin: auto;
            padding: 30px;

            font-size: 16px;
            line-height: 24px;
            /* font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				color: #555; */
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

        /* .invoice-box table tr td:nth-child(2) {
				text-align: right;
			} */

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        /* .invoice-box table tr.information table td {
				padding-bottom: 40px;
			} */

        .invoice-box table tr.heading td {
            background: #00b4c2;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            color: white;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            font-weight: bold;

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
                            <img src="{{ public_path('/images/logo.png') }}" alt="Company logo" style="width: 35%; max-width: 300px" />
                        </td>

                        <td class='facture_info'>
                            Information Entreprise<br />
                            Date: 12/1/2022<br />
                        </td>
                    </tr>
                </table>
            </td>
        </table>
        <h1>Bon d'envoi N°:{{$id_bon_envoi}}</h1><br>
        <table>
            <tr class="information">

                <td>

                    Nombre de Colis: <b>{{$data4->nbr_colis-$data3}}</b>

                </td>
                <td>
                    Responsable: <b>{{$data->responsable}}</b>
                </td>
                <td>
                    Zone: <b>{{$data->nom_zone}}</b>
                </td>
            </tr>
            <tr class="information">

                <td>
                    Nombre de Colis Annuler: <b>{{$data3}}</b>
                </td>



            </tr>


        </table>
        <table>
            <tr class="heading">
                <td>ID Commande</td>
                <td>Client</td>
                <td>Telephone</td>
                <td>Adresse</td>
                <td>COD</td>
                <td>Livreur</td>


            </tr>
            @foreach($data2 as $key)

            <tr class="item">
                <td>
                    @if($key->etat_commande=='ANNULER_CL')
                    **
                    @endif
                    {{ $key->id_commande }}

                </td>
                <td>{{ $key->nom_client_commande }}</td>
                <td>{{ $key->telephone_client_commande }}</td>
                <td>{{ $key->adresse_client_commande }}</td>
                
                <td>{{ $key->prix_commande }}</td>
                <td>        </td>
                @endforeach


        </table>
        <div class="footer">

        </div>

    </div>

</body>

</html>