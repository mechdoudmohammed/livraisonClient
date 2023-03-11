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
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
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
                            <img src="{{ public_path('/images/logo.png') }}" alt="Company logo" style="width: 35%; max-width: 300px" />
                        </td>

                        <td class='facture_info'>

                            Date Debut: <b>{{$date_debut}}</b><br/>
                            Date Fin: <b>{{$date_fin}}</b><br/>
                        </td>
                    </tr>
                </table>
            </td>
        </table>
        <table>
            <tr class="heading">
                <td>Statut</td>
                <td>Quantité</td>
                <td>Revenue vendeur</td>
                <td>Revenue brut</td>
                <td>Frais de livreur</td>
                <td>Revenue net</td>
            </tr>
            <tr class="details">
                <td>Delivered</td>
                <td>{{ $data->nbr_commande }}</td>
                <td>{{ $data->revenu_vendeurs }} Dhs</td>
                <td>{{ round($data->cod_livraison, 2)}} Dhs</td>
                <td>{{ round($data->revenu_responsable, 2)}} Dhs</td>
                <td>{{ round($data->revenu_net, 2)}} Dhs</td>
            </tr>
            <tr class="details">
                <td>Canceled</td>
                <td>{{ $data2->nbr_commande }}</td>
                <td>{{ $data2->revenu_vendeurs }} Dhs</td>
                <td>{{ round($data2->cod_livraison, 2)}} Dhs</td>
                <td>{{ round($data2->revenu_responsable, 2)}} Dhs</td>
                <td>{{ round($data2->revenu_net, 2)}} Dhs</td>
            </tr>
            <tr class="details">
                <td>Retourned</td>
                <td>{{ $data3->nbr_commande }}</td>
                <td>{{ $data3->revenu_vendeurs }} Dhs</td>
                <td>{{ round($data3->cod_livraison, 2)}} Dhs</td>
                <td>{{ round($data3->revenu_responsable, 2)}} Dhs</td>
                <td>{{ round($data3->revenu_net, 2)}} Dhs</td>
            </tr>



            

            <tr class="total" style="border-top: 1px solid #777;">
                <td colspan="3" class='total-text'>Total Revenue vendeur :</td>
                <td colspan="3">{{ $data->revenu_vendeurs }} Dhs</td>
            </tr>

            <tr class="total">
                <td colspan="3" class='total-text'>Total brut:</td>
                <td colspan="3">{{ round($data->cod_livraison+$data2->cod_livraison+$data3->cod_livraison, 2) }} Dhs</td>
            </tr>
            <tr class="total">
                <td colspan="3" class='total-text'>Frais de livraison:</td>
                <td colspan="3">{{ round($data->revenu_responsable+$data2->revenu_responsable+$data3->revenu_responsable, 2)}} Dhs</td>
            </tr>
            <tr class="total">
                <td colspan="3" class='total-text'>Total Net:</td>
                <td colspan="3">{{ round($data->revenu_net+$data2->revenu_net+$data3->revenu_net, 2)}} Dhs</td>
            </tr>
        </table>
    
        <div class="footer">

        </div>
    </div>

</body>

</html>