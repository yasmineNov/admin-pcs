<table border="1">
    <div><h2>LAPORAN PENJUALAN</h2></div>
    <thead style="background-color:#343a40;color:white;font-weight:bold;">
        <tr>
            <th>No</th>
            <th>No Invoice</th>
            <th>Tanggal</th>
            <th>Customer</th>
            <th>DPP</th>
            <th>PPN</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $i => $inv)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $inv->no }}</td>
            <td>{{ $inv->tgl->format('d-m-Y') }}</td>
            <td>{{ $inv->customer->nama_customer ?? '-' }}</td>
            <td>{{ number_format($inv->dpp,0,',','.') }}</td>
            <td>{{ number_format($inv->ppn,0,',','.') }}</td>
            <td>{{ number_format($inv->grand_total,0,',','.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
