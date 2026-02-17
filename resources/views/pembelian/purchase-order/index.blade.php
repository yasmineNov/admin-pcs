@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h4>Purchase Orders</h4>
        <a href="{{ route('pembelian.purchase-order.create') }}" class="btn btn-success btn-sm">+ Buat PO</a>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped" id="po-table">
            <thead class="bg-secondary text-white">
                <tr>
                    <th>No</th>
                    <th>No. PO</th>
                    <th>Tanggal PO</th>
                    <th>Supplier</th>
                    <th>Nominal</th>
                    {{-- <th>Subtotal</th> --}}
                    {{-- <th>DPP</th> --}}
                    {{-- <th>Pajak</th> --}}
                    {{-- <th>Nominal</th> --}}
                    <th>Keterangan</th>
                    {{-- <th>Status</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $i => $po)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $po->no }}</td>
                    <td>{{ $po->tgl->format('d-m-Y') }}</td>
                    <td>{{ $po->supplier->nama_supplier ?? '-' }}</td>
                    {{-- <td>{{ number_format($po->dpp, 2) }}</td> --}}
                    {{-- <td>{{ number_format($po->subtotal ?? 0, 2) }}</td> --}}
                    <td>{{ number_format($po->dpp, 2) }}</td>
                    {{-- <td>{{ number_format($po->pajak, 2) }}</td> --}}
                    {{-- <td>{{ number_format($po->total, 2) }}</td> --}}
                    <td>{{ $po->keterangan ?? '-' }}</td>
                    {{-- <td>
                        @if($po->status == 'Belum Lunas')
                            <span class="badge bg-danger">{{ $po->status }}</span>
                        @elseif($po->status == 'Terbayar Sebagian')
                            <span class="badge bg-warning text-dark">{{ $po->status }}</span>
                        @elseif($po->status == 'Lunas')
                            <span class="badge bg-success">{{ $po->status }}</span>
                        @else
                            <span class="badge bg-secondary">{{ $po->status }}</span>
                        @endif
                    </td> --}}
                </tr>
                {{-- Hidden Detail Row --}}
            <tr id="detail-{{ $po->id }}" style="display:none; background:#f8f9fa;">
                <td colspan="6">
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Barang</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($po->details as $detail)
                                <tr>
                                    <td class="details-control" style="cursor:pointer;">+</td>
                                    <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                                    <td>{{ $detail->qty }}</td>
                                    <td>{{ number_format($detail->harga,0,',','.') }}</td>
                                    <td>{{ number_format($detail->subtotal,0,',','.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

    var table = $('#po-table').DataTable({
        order: [[3, 'desc']],
        columnDefs: [
            { orderable: false, targets: 0 }
        ]
    });

    $('#po-table tbody').on('click', 'td.details-control', function () {

        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var poId = tr.data('id');
        var button = $(this);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            button.text('+');
        } else {

            $.ajax({
                url: "/pembelian/purchase-order/" + poId + "/detail",
                type: "GET",
                success: function(response) {

                    var html = `
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    response.forEach(function(detail) {
                        html += `
                            <tr>
                                <td>${detail.barang ? detail.barang.nama_barang : '-'}</td>
                                <td>${detail.qty}</td>
                                <td>${Number(detail.harga).toLocaleString('id-ID')}</td>
                                <td>${Number(detail.subtotal).toLocaleString('id-ID')}</td>
                            </tr>
                        `;
                    });

                    html += `</tbody></table>`;

                    row.child(html).show();
                    tr.addClass('shown');
                    button.text('-');

                }
            });

        }

    });

});
</script>

@endsection

