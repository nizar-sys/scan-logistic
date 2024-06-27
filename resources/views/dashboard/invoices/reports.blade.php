<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }
    </style>

</head>

<body>

    <div class="header">
        <h1 style="text-align: center; margin-bottom: 20px;">Laporan Invoice</h1>

        <p style="text-align: center; margin-bottom: 10px;">
            <strong>Periode:</strong>
            @if ($startDate)
                @date($startDate)
                @if ($endDate)
                    hingga @date($endDate)
                @else
                    hingga Sekarang
                @endif
            @elseif ($endDate)
                Sampai @date($endDate)
            @else
                Semua Data
            @endif
        </p>

        <hr style="border: 1px solid #ccc; margin-bottom: 20px;">
    </div>


    <table id="invoice-table" class="table table-flush table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Resi</th>
                <th>Tanggal</th>
                <th>Marketplace</th>
                <th>Nama/motif Baju</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoices as $invoice)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>@date($invoice->created_at) WIB</td>
                    <td>{{ ucfirst($invoice->marketplace) }}</td>
                    <td>
                        {{ $invoice->details->pluck('product.name')->implode(', ') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-center">Total Invoice</th>
                <th>{{ count($invoices) }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
