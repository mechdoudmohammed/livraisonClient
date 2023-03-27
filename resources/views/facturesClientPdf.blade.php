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
            <tr class="heading">
                <td>Client</td>
                <td>Rib</td>
                <td>Bank</td>
                <td>Total</td>
                <td>Verser</td>
                <td>Payer</td>
            </tr>
           
            @foreach($data as $key)
            <tr class="item">
                <td>{{ $key->username }}</td>
                <td>{{ $key->ribBank }}</td>
                <td>{{ $key->nomBank }}</td>
                <td>{{ round($key->total,2) }} Dhs</td>
                <td>..............</td>
                <td>..............</td>
            </tr>
            @endforeach


        </table>

        <div class="footer">

        </div>
    </div>

</body>

</html>