@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h4>Sales Orders</h4>
            <a href="{{ route('penjualan.sales-order.create') }}" class="btn btn-success btn-sm">+ Buat SO</a>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped" id="so-table">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th>No</th>
                        <th>No. SO</th>
                        <th>Tanggal SO</th>
                        <th>Customer</th>
                        <th>Nominal</th>
                        {{-- <th>Subtotal</th> --}}
                        {{-- <th>DPP</th> --}}
                        {{-- <th>Pajak</th> --}}
                        {{-- <th>Nominal</th> --}}
                        <th>Keterangan</th>
                        {{-- <th>Status</th> --}}
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $i => $so)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $so->no }}</td>
                            <td>{{ $so->tgl->format('d-m-Y') }}</td>
                            <td>{{ $so->customer?->nama_customer ?? '-' }}</td>

                            {{-- <td>{{ number_format($so->dpp, 2) }}</td> --}}
                            {{-- <td>{{ number_format($so->subtotal ?? 0, 2) }}</td> --}}
                            <td>{{ number_format($so->dpp, 2) }}</td>
                            {{-- <td>{{ number_format($so->pajak, 2) }}</td> --}}
                            {{-- <td>{{ number_format($so->total, 2) }}</td> --}}
                            <td>{{ $so->keterangan ?? '-' }}</td>
                            {{-- <td>
                                @if($so->status == 'Belum Lunas')
                                <span class="badge bg-danger">{{ $so->status }}</span>
                                @elseif($so->status == 'Terbayar Sebagian')
                                <span class="badge bg-warning text-dark">{{ $so->status }}</span>
                                @elseif($so->status == 'Lunas')
                                <span class="badge bg-success">{{ $so->status }}</span>
                                @else
                                <span class="badge bg-secondary">{{ $so->status }}</span>
                                @endif
                            </td> --}}
                            <td>
                                <button class="btn btn-sm btn-primary btn-detail" data-id="{{ $so->id }}">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        {{-- Hidden Detail Row --}}
                        <tr id="detail-{{ $so->id }}" style="display:none; background:#f8f9fa;">
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
                                        @foreach($so->details as $detail)
                                            <tr>
                                                <td class="details-control" style="cursor:pointer;">+</td>
                                                <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                                                <td>{{ $detail->qty }}</td>
                                                <td>{{ number_format($detail->harga, 0, ',', '.') }}</td>
                                                <td>{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
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
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail SO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                Loading...
            </div>
        </div>
    </div>
</div>
@section('scripts')
    <script>
        $(document).ready(function () {

            var table = $('#so-table').DataTable({
                order: [[3, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 0 }
                ]
            });

            $('#so-table tbody').on('click', 'td.details-control', function () {

                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var soId = tr.data('id');
                var button = $(this);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    button.text('+');
                } else {

                    $.ajax({
                        url: "/penjualan/sales-order/" + soId + "/detail",
                        type: "GET",
                        success: function (response) {

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

                            response.forEach(function (detail) {
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

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-detail').forEach(btn => {
                btn.addEventListener('click', function () {
                    let id = this.dataset.id;

                    fetch('/so/' + id)
                        .then(res => res.text())
                        .then(data => {
                            document.getElementById('detailContent').innerHTML = data;
                            new bootstrap.Modal(document.getElementById('detailModal')).show();
                        });
                });
            });
        });


    </script>

@endsection