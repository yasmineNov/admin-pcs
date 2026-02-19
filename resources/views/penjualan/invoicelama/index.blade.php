@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Invoice Masuk</h4>

    <a href="{{ route('penjualan.invoice.create') }}" class="btn btn-primary mb-3">
        + Buat Invoice Masuk
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Delivery Note</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $inv)
            <tr>
                <td>{{ $inv->no }}</td>
                <td>{{ $inv->deliveryNote->no }}</td>
                <td>{{ $inv->grand_total }}</td>
                <td>{{ $inv->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
