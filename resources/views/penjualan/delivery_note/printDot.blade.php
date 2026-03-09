<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: "Courier New", monospace;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 3px;
        }

        .header td {
            border: none;
        }

        .items th {
            border-bottom: 1px dashed black;
            text-align: left;
        }

        .items td {
            padding: 3px 2px;
        }

        .footer td {
            border: none;
        }

        @media print {
            body {
                margin: 10px;
            }
        }
    </style>

</head>

<body>

    <table class="header">

        <tr>
            <td width="60%">
                <b>{{ config('company.nama') }}</b><br>
                {{ config('company.alamat') }}<br>
                {{ config('company.kecamatan') }}<br>
                {{ config('company.provinsi') }}
            </td>
        </tr>

    </table>

    <br>

    <center>
        <b>SURAT JALAN</b>
    </center>

    <br>

    <table class="header">

        <tr>

            <td width="50%" style="vertical-align:top">

                No SJ : {{ $dn->no }}<br>
                Tanggal : {{ $dn->tgl->format('d-m-Y') }}<br>
                Customer : {{ $dn->order->customer->nama_customer }}<br>
                Alamat : {{ $dn->order->customer->alamat }}

            </td>

            <td width="50%" style="vertical-align:top">

                Alamat Kirim :<br>
                {{ $dn->alamat_kirim }}

            </td>

        </tr>

    </table>

    <br>

    @php
        $no = 1;
    @endphp

    <table class="items">

        <thead>

            <tr>
                <th width="5%">No</th>
                <th width="10%">Qty</th>
                <th>Nama Barang</th>
                <th width="35%">Keterangan</th>
            </tr>

        </thead>

        <tbody>

            @foreach($dn->details as $detail)

                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>{{ $detail->orderDetail->barang->nama_barang }}</td>
                    <td>{{ $detail->keterangan }}</td>
                </tr>

            @endforeach

        </tbody>

    </table>

    <br><br>

    <table class="footer">

        <tr>

            <td width="33%">
                Hormat Kami
                <br><br><br><br><br>
                {{ config('company.nama') }}
            </td>

            <td width="33%" style="text-align:center">

                Driver
                <br><br><br><br><br>

            </td>

            <td width="33%" style="text-align:center">

                Penerima
                <br><br><br><br><br>

            </td>

        </tr>

    </table>

</body>

</html>