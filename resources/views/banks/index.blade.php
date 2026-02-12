@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between">
            <h2>Master Bank</h2>
            <a href="{{ route('banks.create') }}" class="btn btn-primary mb-2">+ Tambah Bank</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Kode</th>
                    <th>Nama Bank</th>
                    <th>Nama Rekening</th>
                    <th>No Rekening</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($banks as $b)
                <tr>
                    <td>{{ $b->kode_bank }}</td>
                    <td>{{ $b->nama_bank }}</td>
                    <td>{{ $b->nama_rekening ?? '-' }}</td>
                    <td>{{ $b->no_rekening ?? '-' }}</td>
                    <td>
                        <a href="{{ route('banks.edit', $b->id) }}" class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('banks.destroy', $b->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus bank?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
