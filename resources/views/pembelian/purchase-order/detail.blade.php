<table class="table table-bordered">
    <thead>
        <tr>
            <th>Barang</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($po->details as $detail)
        <tr>
            <td>{{ $detail->barang->nama_barang }}</td>
            <td>{{ $detail->qty }}</td>
            <td>{{ number_format($detail->harga,0,',','.') }}</td>
            <td>{{ number_format($detail->subtotal,0,',','.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

