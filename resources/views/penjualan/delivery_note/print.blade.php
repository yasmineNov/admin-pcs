<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
        }

        .no-border td {
            border: none;
        }

        .text-right {
            text-align: right;
        }

        .text-middle {
            text-align: center;
        }
    </style>
</head>

<body>

    <table class="no-border">
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

    <h3 style="text-align:center;">Surat Jalan</h3>

    <table class="no-border">
        <tr>

            <td width="50%" style="vertical-align: top;">
                No SJ : {{ $dn->no }}<br>
                Tanggal: {{ $dn->tgl->translatedFormat('d F Y') }}<br>
                Customer: {{ $dn->order->customer->nama_customer }}<br>
                Alamat: {{ $dn->order->customer->alamat }}
            </td>

            <td width="50%" style="vertical-align: top;">
                Alamat Kirim : {{ $dn->alamat_kirim }}<br>
            </td>

        </tr>
    </table>

    <br>
    @php
        $no = 1;
    @endphp
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Jumlah</th>
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

    <br>

    <table class="no-border">
        <tr>

            <td width="30%">
                Hormat Kami
            </td>

            <td width="30%" style="text-align:center;">
                Driver
            </td>

            <td width="30%" style="text-align:center;">
                Penerima
            </td>

        </tr>
    </table>

</body>

</html>