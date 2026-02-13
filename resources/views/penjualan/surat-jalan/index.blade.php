@extends('layouts.admin')

@section('content')
       <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
            <h2>Data Surat Jalan</h2>
            <a href="{{ route('surat-jalan.create') }}" class="btn btn-primary mb-2">
                + Tambah Surat Jalan
            </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>No SJ</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>No PO</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $key => $sj)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $sj->no }}</td>
                            <td>{{ \Carbon\Carbon::parse($sj->tgl)->format('d-m-Y') }}</td>
                            <td>{{ $sj->order->customer->nama_customer ?? '-' }}</td>
                            <td>{{ $sj->order->no ?? '-' }}</td>
                            <td>
                                <button class="btn btn-sm btn-info btn-detail"
                                        data-id="{{ $sj->id }}">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                Belum ada data Surat Jalan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
