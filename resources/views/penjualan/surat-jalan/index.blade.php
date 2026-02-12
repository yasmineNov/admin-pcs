@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between">
        <h2> Surat Jalan Penjualan</h2>
        <a href="{{ route('surat-jalan.create') }}" class="btn btn-primary mb-2">+ Tambah Surat Jalan</a>
        </div>
    </div>
    
            <table class="table">
    <thead class="thead-dark">
                <tr>
                    <th>Tanggal</th>
                    <th>No. Surat Jalan</th>
                    <th>No. PO</th>
                    <th>Nama Customer</th>
                    {{-- <th>Tanggal Terima</th> --}}
                    {{-- <th width="120">Aksi</th> --}}
                </tr>
            </thead>
        @foreach($data as $sj)
<tr class="sj-row" data-id="{{ $sj->id }}" style="cursor:pointer">
    <td>{{ $sj->tgl }}</td>
    <td>{{ $sj->no }}</td>
    <td></td>
    <td>{{ $sj->order->customer->nama_customer ?? '-' }}</td>
</tr>
<tr class="detail-row" id="detail-{{ $sj->id }}" style="display:none">
    <td colspan="4"></td>
</tr>
@endforeach

</table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.sj-row').forEach(row => {
        row.addEventListener('click', function () {

            let id = this.dataset.id;
            let detailRow = document.getElementById('detail-' + id);

            // toggle close
            if (detailRow.style.display === 'table-row') {
                detailRow.style.display = 'none';
                return;
            }

            // close all detail rows
            document.querySelectorAll('.detail-row').forEach(r => r.style.display = 'none');

            fetch(`/penjualan/surat-jalan/${id}/detail`)
                .then(res => res.json())
                .then(data => {

                    let html = `
                        <div class="p-3 bg-light">
                            <strong>Customer:</strong> ${data.customer.nama_customer}<br>
                            <strong>No SJ:</strong> ${data.no_sj}

                            <table class="table table-sm mt-3">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th width="100">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    data.details.forEach(d => {
                        html += `
                            <tr>
                                <td>${d.barang.nama_barang}</td>
                                <td>${d.qty}</td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    detailRow.children[0].innerHTML = html;
                    detailRow.style.display = 'table-row';
                });
        });
    });

});
</script>

@endsection