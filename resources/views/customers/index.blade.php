@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between">
            <h2>Master Customer</h2>
            <a href="{{ route('customers.create') }}" class="btn btn-primary mb-2"> + Tambah Customer</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $c)
                <tr>
                    <td>{{ $c->kode_customer }}</td>
                    <td>{{ $c->nama_customer }}</td>
                    <td>{{ $c->email ?? '-' }}</td>
                    <td>{{ $c->telepon ?? '-' }}</td>
                    <td>
                        <a href="{{ route('customers.edit',$c->id) }}"
                           class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('customers.destroy',$c->id) }}"
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Hapus customer?')">
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
