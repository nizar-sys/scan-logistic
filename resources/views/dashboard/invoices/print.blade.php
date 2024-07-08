<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .text-center {
            text-align: center;
        }
    </style>

</head>

<body>

    <div class="header">
        <h1 style="text-align: center; margin-bottom: 20px;">Invoice {{ $invoice->invoice_number }}</h1>

        <hr style="border: 1px solid #ccc; margin-bottom: 20px;">
    </div>


    <div class="content">
        <table width="100%">
            <tr>
                <td width="50%">
                    <table>
                        <tr>
                            <td>Tanggal - Jam</td>
                            <td>:</td>
                            <td>@date($invoice->date) WIB</td>
                        </tr>
                        <tr>
                            <td>Barcode</td>
                            <td>:</td>
                            <td>
                                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($invoice->invoice_number, 'C128') }}"
                                    alt="barcode" />
                            </td>
                        </tr>
                        <tr>
                            <td>Resi</td>
                            <td>:</td>
                            <td>{{ $invoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td>Marketplace</td>
                            <td>:</td>
                            <td>{{ ucfirst($invoice->marketplace) }}</td>
                        </tr>
                        <tr>
                            <td>Baju</td>
                            <td>:</td>
                            <td></td>
                        </tr>
                    </table>
                </td>
                <td width="50%">
                    <table>
                        <tr colspan="3">
                            <td>Nama Penjahit</td>
                            <td>:</td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table width="100%" style="margin-top: 20px;">
            <thead>
                @php $count = 0; @endphp
                @foreach ($invoice->details as $detail)
                    @if ($count % 3 == 0)
                        @if ($count > 0)
                            </tr>
                        @endif
                        <tr>
                    @endif
                    <td style="text-align: left;">
                        <img src="@imageToBase64('uploads/images/' . $detail->product->image)" width="200">
                        <br>
                        <ul style="list-style: none; padding: 0;">
                            <li>{{ $detail->product->name }}</li>
                            <li>{{ strtoupper($detail->size) }} <small>({{ $detail->availability }})</small></li>
                            <li>{{ $detail->quantity }}</li>
                        </ul>
                    </td>
                    @php $count++; @endphp
                @endforeach
                @if ($count > 0)
                    </tr>
                @endif
            </thead>
        </table>
    </div>
</body>

</html>
