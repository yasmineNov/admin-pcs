@extends('layouts.admin')

@section('content')
<h3>Faktur</h3>
<a href="{{ route('faktur.create') }}" class="btn btn-primary mb-2">+ Faktur</a>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead class="thead-dark">
        <tr>
            <th>Tgl SJ</th>
            <th>No SJ</th>
            <th>Tgl Faktur</th>
            <th>No Faktur</th>
            <th>No PO</th>
            <th>Customer</th>
            <th class="text-end">Grand Total</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($faktur as $f)
<tr>
    <td>-</td>
    <td>-</td>
    <td>{{ \Carbon\Carbon::parse($f->tgl)->format('d-m-Y') }}</td>
    <td>{{ $f->no }}</td>
    <td>{{ $f->no_so ?? '-' }}</td>
    <td>{{ $f->customer->nama_customer ?? '-' }}</td>
    <td class="text-end">{{ number_format($f->grand_total,0,',','.') }}</td>
    <td>
        <a href="{{ route('faktur.edit', $f->id) }}" class="btn btn-sm btn-warning">Edit</a>
        <form action="{{ route('faktur.destroy', $f->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus faktur?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger">Hapus</button>
        </form>
    </td>
</tr>
@endforeach

    </tbody>
</table>
@endsection
