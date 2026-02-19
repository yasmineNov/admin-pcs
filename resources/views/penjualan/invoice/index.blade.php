@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header bg-dark text-white">
        <h4>Daftar Invoice Penjualan</h4>
        <a href="{{ route('penjualan.invoice.create') }}" class="btn btn-primary mb-3">
        + Buat Invoice Keluar
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
                    <th>Customer</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $index => $invoice)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $invoice->tgl->format('d-m-Y') }}</td>
                    <td>{{ $invoice->no }}</td>
                    <td>{{ $invoice->deliveryNote->order->no ?? '-' }}</td>
                    <td>{{ $invoice->deliveryNote->order->customer->nama_customer ?? '-' }}</td>
                    <td>{{ number_format($invoice->grand_total,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
