@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="row justify-content-between">
                <h2>Delivery Note Pembelian</h2>
                <a href="{{ route('pembelian.delivery-note.create') }}" class="btn btn-primary mb-3">+ Surat Jalan</a>
            </div>
        </div>

        <div class="card-body">
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>No. Surat Jalan</th>
                <th>No. PO</th>
                <th>Supplier</th>
                {{-- <th>Total Barang</th> --}}
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            {{-- @forelse($deliveryNotes as $dn) --}}
            @forelse($deliveryNotes as $dn)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($dn->tgl)->format('d-m-Y') }}</td>
                <td>{{ $dn->no }}</td>
                <td>{{ $dn->order?->no ?? '-' }}</td> {{-- tampil No. PO --}}
                <td>{{ $dn->order?->supplier?->nama_supplier ?? '-' }}</td>
                {{-- <td>{{ $dn->details->sum(fn($d) => $d->orderDetail?->qty ?? 0) }}</td> --}}
                <td>
                    <a href="{{ route('pembelian.delivery-note.edit', $dn->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('pembelian.delivery-note.destroy', $dn->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus delivery note ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada delivery note</td>
            </tr>
            @endforelse
        </tbody>
    </table>
        </div>
</div>
</div>
@endsection
