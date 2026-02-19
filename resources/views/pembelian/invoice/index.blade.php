@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header bg-dark text-white">
            <div class="d-flex justify-content-between">
                <h4 class="mb-2">Daftar Invoice Pembelian</h4>
                <a href="{{ route('pembelian.invoice.create') }}" class="btn btn-primary mb-2">
                    + Buat Invoice Masuk
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th>No.</th>
                        <th>Tanggal Faktur</th>
                        <th>No. Faktur</th>
                        <th>No. PO</th>
                        <th>Supplier</th>
                        <th>Nominal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $index => $invoice)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $invoice->tgl->format('d-m-Y') }}</td>
                            <td>{{ $invoice->no }}</td>
                            {{-- <td>{{ $invoice->deliveryNote->orders->no ?? '-' }}</td> --}}
                            <td>{{ $invoice->no_so ?? '-' }}</td>
                            <td>{{ $invoice->supplier->nama_supplier ?? '-' }}</td>
                            <td>{{ number_format($invoice->grand_total, 2) }}</td>
                            <td>
                                <button class="btn btn-info btn-sm btn-detail" data-id="{{ $invoice->id }}">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-body-detail">
                Loading...
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-detail').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id;

                fetch('/invPurchase/' + id + '/detail')
                    .then(res => res.text())
                    .then(data => {
                        document.getElementById('modal-body-detail').innerHTML = data;
                        new bootstrap.Modal(document.getElementById('detailModal')).show();
                    })
                    .catch(err => console.error(err));
            });
        });
    });
</script>