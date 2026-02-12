@extends('layouts.admin')

@section('content')
<h3> Data Penjualan </h3>

<div class="container-fluid">


{{-- <h4 class="mb-3">Data Penjualan</h4> --}}

<div class="card mb-4">
<div class="card-body">
<table class="table table-bordered table-striped">
<thead class="thead-dark">
<tr>
<th>No</th>
<th>Tgl Faktur</th>
<th>No Faktur</th>
<th>No PO</th>
<th>No SJ</th>
<th>Customer</th>
<th>Total</th>
<th>Terbayar</th>
<th>Sisa</th>
</tr>
</thead>
<tbody>
@foreach($data as $i => $row)
<tr>
<td>{{ $i+1 }}</td>
<td>{{ $row->tgl_faktur }}</td>
<td>{{ $row->no_faktur }}</td>
<td>{{ $row->no_po }}</td>
<td>{{ optional($row->suratJalan)->no_sj }}</td>
<td>{{ $row->customer->nama_customer }}</td>
<td>{{ number_format($row->total) }}</td>
<td>{{ number_format($row->terbayar) }}</td>
<td>{{ number_format($row->sisa) }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>

<div class="card">
<div class="card-header">Pelunasan Faktur Penjualan</div>
<div class="card-body">
<form method="POST" action="{{ route('penjualan.pelunasan') }}">
@csrf


<div class="row">
<div class="col-md-3">
<label>Tgl Pelunasan</label>
<input type="date" name="tgl_pelunasan" class="form-control" required>
</div>


<div class="col-md-3">
<label>Faktur</label>
<select name="faktur_id" class="form-control" required>
<option value="">- pilih -</option>
@foreach($data as $f)
@if($f->sisa > 0)
<option value="{{ $f->id }}">{{ $f->no_faktur }} - {{ $f->customer->nama_customer }}</option>
@endif
@endforeach
</select>
</div>


<div class="col-md-2">
<label>Bayar</label>
<input type="number" name="bayar" class="form-control" required>
</div>


<div class="col-md-2">
<label>Metode</label>
<select name="metode" class="form-control">
<option value="cash">Cash</option>
<option value="transfer">Transfer</option>
</select>
</div>


<div class="col-md-2">
<label>Bank</label>
<input type="text" name="bank" class="form-control">
</div>
</div>


<button class="btn btn-primary mt-3">Simpan Pelunasan</button>
</form>
</div>
</div>


</div>

@endsection