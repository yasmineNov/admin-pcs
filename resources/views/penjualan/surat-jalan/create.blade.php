@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Buat Surat Jalan Keluar</h4>

    <form action="{{ route('penjualan.sj.store') }}" method="POST">
        @csrf

        <input type="hidden" name="type" value="keluar">

        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="tgl" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Customer</label>
            <select name="customer_id" class="form-control" required>
                <option value="">-- Pilih Customer --</option>
                @foreach($customers as $cust)
                <option value="{{ $cust->id }}">{{ $cust->nama }}</option>
                @endforeach
            </select>
        </div>

        <hr>

        <h5>Barang</h5>

        <div class="row mb-2">
            <div class="col">
                <select name="barang_id[]" class="form-control">
                    @foreach($barangs as $barang)
                    <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <input type="number" name="qty[]" class="form-control" placeholder="Qty">
            </div>
        </div>

        <button class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
