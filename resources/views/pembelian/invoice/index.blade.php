@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header bg-dark text-white">
        <h4>Daftar Invoice Pembelian</h4>
        <a href="{{ route('pembelian.invoice.create') }}" class="btn btn-primary mb-3">
        + Buat Invoice Masuk
    </a>
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
                    <td>{{ number_format($invoice->grand_total,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
