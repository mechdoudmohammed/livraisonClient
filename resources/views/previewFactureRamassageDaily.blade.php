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
                            <img src="{{ public_path('/images/logo.png') }}" alt="Company logo" style="width: 20%; max-width: 300px" />
                        </td>
                   
                        <td class='facture_info'>
                            Invoice N°: <b>{{$data4->id_facture}}</b><br />
                            Date: <b>{{$data4->date_facture}}</b><br />
                        </td>
                    </tr>
                </table>
            </td>
        </table>
        <table>
            <tr class="information">

                <td>
                    Nom & prenom: <b>{{$data3->nom}} {{$data3->prenom}}</b>
                </td>
                <td>
                    RIB: <b>{{$data3->ribBank}}</b>
                </td>
            </tr>
            <tr>
                <td>
                    Adresse : <b>{{$data3->adresse}}</b>
                </td>
                <td>
                    CNI: <b>{{$data3->cin}}</b>
                </td>
            </tr>
            <tr>
                <td>
                    Responsable N°: <b>{{$data3->id_employe}}</b>
                </td>
            </tr>


        </table>

        <table>
            <tr class="heading">
                <td>ID Commande</td>
                <td>Ville</td>
                <td>livré par</td>
                <td>Prix uniterre</td>
                <td>Statut</td>
            </tr>
           
            @foreach($data2 as $key)
            <tr class="item">
                <td>{{ $key['id_commande'] }}</td>
                <td>{{ $key['nom_ville'] }}</td>
                <td>{{ $key['username'] }}</td>
                <td>{{ round($key['prix_commande'],2) }} Dhs</td>

                <td>{{ $key['etat_commande']}}</td>
            </tr>
            @endforeach


        </table>
        <table>

<tr class="total" style="border-top: 1px solid #777;">
    <td colspan="3" class='total-text'>Total:</td>
    <td colspan="2">{{round($data4->total_facture,2)}} Dhs</td>
</tr>

</table>
        <div class="footer">

        </div>
    </div>

</body>

</html>