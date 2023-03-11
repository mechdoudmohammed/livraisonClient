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
            border: 1px solid black;
            padding-right: 26px;
            margin-left: 50px;
        }
    </style>
</head>

<body class="A4">

    <div class="row test">
    @foreach($data as $key)
        <div class="col-6 sticker" style="margin:5px 1%" v-for="(item, index) in getpackageClient" :key="index">
            <div class="row company" style="border: 1px solid;font-weight: 600;text-align: center;">
                <table style="margin-left:10px">
                    <tr>
                        <td style="width:80px ; padding:10px">
                            <img src="{{ public_path('/images/logo.png') }}" width="50" height="50" />
                        </td>
                        <td>
                            <span style="font-size: 12px;">S.A.R.L Fes 19 returnjghjghjgfhhkjgjfkhjgfhlkgfjlkhjgf</span>
                        </td>
                    </tr>
                    <tr>

                </table>

            </div>

            <div class="row" style="border: 1px solid;display: flex;padding: 6px; text-align:center;justify-content: center;">
                <div class="col-8" style="width: 60% !important;float: right;display: grid;justify-content: center;align-items: center;align-content: center;text-align: center;">
                    <b style="margin-right: 55px;">FJSKF1594DDSC </b>
                    {!! DNS1D::getBarcodeHTML('FJSKF1594DDSC', 'C128',1,23) !!}
                </div>
                <div class="col-3" width='width: 25% !important;'>
                    {!! DNS2D::getBarcodeHTML('FJSKF1594DDSC', 'QRCODE',3,3) !!}
                </div>

            </div>
           
            <div class="row client" style="border: 1px solid;">
                <span style="font-size: 15px;font-weight: 600;margin-left:10px">Information Article:</span>
                <table style="margin-left:20px">
                    <tr>
                        <td style="width:250px">
                       
                            <p style="font-size: 12px;margin-bottom: 6px;">Nom: {{$key->nom_article}}
                           
                            </p>
                        </td>
                        <td style="width:250px">
                       
                       <p style="font-size: 12px;margin-bottom: 6px;">Prix: {{$key->prix_article}}
                      
                       </p>
                   </td>
                    </tr>
                </table>

            </div>
            <div class="row origine" style="border: 1px solid;">
                <span style="font-size: 15px;font-weight: 600;margin-left:10px">Origine:</span>
                <table style="margin-left:20px;width: 100%;">
                    <tr>
                        <td style="width:250px">
                            <p style="font-size: 12px;margin-bottom: 6px;">{{$key->website}}</p>
                        </td>
                        <td>
                            <p style="font-size: 12px;margin-bottom: 6px;">{{$key->company}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="font-size: 12px;margin-bottom: 6px;">{{$key->telephone}}</p>
                        </td>
                        <td>
                            <p style="font-size: 12px;margin-bottom: 6px;">{{$key->nom}} {{$key->prenom}}</p>
                        </td>
                    </tr>
                </table>



            </div>

        </div>
        @endforeach
    </div>


</body>


</html>