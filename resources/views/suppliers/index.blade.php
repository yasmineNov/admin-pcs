@extends('layouts.admin')

@section('content')

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between">
            <h2>Master Supplier</h2>
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary mb-2">
                + Tambah Supplier</a>
        </div>    
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $s)
                <tr>
                    <td>{{ $s->nama_supplier }}</td>
                    <td>{{ $s->email }}</td>
                    <td>{{ $s->telepon }}</td>
                    <td>
                        <a href="{{ route('suppliers.edit',$s->id) }}"
                           class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('suppliers.destroy',$s->id) }}"
                              method="POST"
                              style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Hapus supplier?')">
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
