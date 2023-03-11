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
                            Invoice N°:{{ $data3->id_facture }}<br />
                            Date: {{ $data3->date_facture }}<br />
                        </td>
                    </tr>
                </table>
            </td>
        </table>
        <table>
            <tr class="information">

                <td>
                    Nom & prenom: <b>{{ $data3->nom }} {{ $data3->prenom }}</b>
                </td>
                <td>
                    RIB: <b>{{ $data3->ribBank }}</b>
                </td>
            </tr>
            <tr>
                <td>
                    Adresse : <b>{{ $data3->adresse }}</b>
                </td>
                <td>
                    CNI: <b>{{ $data3->cin }}</b>
                </td>
            </tr>
            <tr>
                <td>
                    Responsable N°: <b>{{ $data3->id_employe }}</b>
                </td>
            </tr>


        </table>
        <table>
            <tr class="heading">
                <td colspan="2">Statut</td>
                <td>Quantité</td>
                <td>AVG</td>
                <td colspan="2">Total</td>
            </tr>


            <tr class="details">
                <td colspan="2">Delivered</td>
                <td>{{ $data['delivered'] }}</td>
                @if($data['delivered']!=0)
                <td>{{ round($data['total_delivered']/$data['delivered'], 2)}} Dhs</td>
                @else
                <td>0 Dhs </td>
                @endif

                <td colspan="2">{{ $data['total_delivered'] }} Dhs</td>
            </tr>
            <tr class="details">
                <td colspan="2">Returned</td>
                <td>{{ $data['returned']}}</td>
                @if($data['returned']!=0)
                <td> {{ round($data['total_returned']/$data['returned'], 2)}} Dhs</td>
                @else
                <td>0 Dhs </td>
                @endif
                <td colspan="2">{{ $data['total_returned'] }} Dhs</td>
            </tr>
            <tr class="details">
                <td colspan="2">Cancel</td>
                <td>{{ $data['canceled'] }}</td>
                @if($data['canceled']!=0)
                <td>{{ round($data['total_canceled']/$data['canceled'], 2)}} Dhs</td>
                @else
                <td> 0 Dhs </td>
                @endif
                <td colspan="2">{{$data['total_canceled']}} Dhs</td>
            </tr>



            <tr class="total" style="border-top: 1px solid #777;">
                <td colspan="3" class='total-text'>Total:</td>
                <td colspan="2">{{$data['total_cod']}} Dhs</td>
            </tr>
            <tr class="total">
                <td colspan="3" class='total-text'>Frais de livraison:</td>
                <td colspan="2">{{$data['total_livraison']}} Dhs</td>
            </tr>
            <tr class="total">
                <td colspan="3" class='total-text'>Total Net:</td>
                <td colspan="2">{{$data['total_cod']-$data['total_livraison']}} Dhs</td>
            </tr>
        </table>
        <table>
            <tr class="heading">
                <td>ID Commande</td>
                <td>Ville</td>
                <td>Prix Commande</td>
                <td>Frais Livraison(R)</td>
                <td>Statut</td>
            </tr>
            @foreach($data2 as $key)
            <tr class="item">
                <td>{{ $key->id_commande }}</td>
                <td>{{ $key->nom_ville }}</td>
                <td>{{ $key->prix_commande }} Dhs</td>


                @if($key->etat_commande=='DELIVERED')
                <td>{{ $key->prix_livraison_ramasseur_ville }} Dhs</td>
                @elseif($key->etat_commande=='RETURNEDAG' || $key->etat_commande=='RETURNEDEV' || $key->etat_commande=='RETURNED' || $key->etat_commande=='RETURNEDRR')
                <td>{{ $key->prix_retour_ramasseur_ville }} Dhs</td>
                @elseif($key->etat_commande=='CANCELED')
                <td>{{ $key->prix_refus_ramasseur_ville }} Dhs</td>
                @endif


                @if($key->etat_commande=='RETURNEDAG' || $key->etat_commande=='RETURNED' || $key->etat_commande=='RETURNEDEV' || $key->etat_commande=='RETURNEDRR')
                <td>RETURNED</td>
                @else
                <td>{{ $key->etat_commande}}</td>
                @endif





            </tr>
            @endforeach


        </table>
        <div class="footer">

        </div>
    </div>

</body>

</html>