@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Buat Invoice Masuk</h4>

    <form action="{{ route('penjualan.invoice.store') }}" method="POST">
        @csrf

        <input type="hidden" name="type" value="masuk">

        <div class="mb-3">
            <label>Pilih Surat Jalan</label>
            <select name="delivery_note_id" class="form-control">
                @foreach($sjMasuk as $sj)
                <option value="{{ $sj->id }}">{{ $sj->no }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Grand Total</label>
            <input type="number" name="grand_total" class="form-control">
        </div>

        <button class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
