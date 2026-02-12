@extends('layouts.admin')

@section('content')
<h3>Buat Faktur</h3>
<a href="{{ route('faktur.index') }}" class="btn btn-secondary mb-2">Kembali</a>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $err)
        <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('faktur.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Tanggal Faktur</label>
        <input type="date" name="tgl_faktur" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>No Faktur</label>
        <input type="text" name="no_faktur" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>No PO</label>
        <input type="text" name="no_po" class="form-control">
    </div>
    <div class="mb-3">
        <label>Customer</label>
        <select name="customer_id" class="form-control" required>
            <option value="">-- Pilih Customer --</option>
            @foreach ($customers as $c)
            <option value="{{ $c->id }}">{{ $c->nama_customer }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Surat Jalan (Bisa pilih lebih dari 1)</label>
        <select name="surat_jalan_id[]" class="form-control" multiple required>
            @foreach ($suratJalans as $sj)
            <option value="{{ $sj->id }}">
                {{ $sj->no_sj }} - {{ $sj->customer->nama_customer }} ({{ \Carbon\Carbon::parse($sj->tgl_sj)->format('d-m-Y') }})
            </option>
            @endforeach
        </select>
        <small class="text-muted">Tekan Ctrl (Windows) / Cmd (Mac) untuk pilih lebih dari 1 SJ</small>
    </div>

    <button class="btn btn-primary">Simpan Faktur</button>
</form>
@endsection
