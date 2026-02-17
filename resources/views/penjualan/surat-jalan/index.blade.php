@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Surat Jalan Keluar</h4>

    <a href="{{ route('penjualan.sj.create') }}" class="btn btn-primary mb-3">
        + Buat Surat Jalan Keluar
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item->no }}</td>
                <td>{{ $item->tgl }}</td>
                <td>{{ $item->customer->nama ?? '-' }}</td>
                <td>{{ $item->type }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
