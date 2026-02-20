@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between">
            <h2>Master Barang</h2>
            <a href="{{ route('barang.create') }}" class="btn btn-primary mb-2">
                 + Tambah Barang
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Supplier</th>
                    <th>Stok</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barangs as $b)
                <tr>
                    <td>{{ $b->kode_barang }}</td>
                    <td>{{ $b->nama_barang }}</td>
                    <td>{{ $b->supplier->nama_supplier ?? '-' }}</td>
                    <td>{{ $b->stok }} roll</td>
                    <td>
                        <a href="{{ route('barang.edit',$b->id) }}"
                           class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('barang.destroy',$b->id) }}"
                              method="POST"
                              style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Hapus barang?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection



