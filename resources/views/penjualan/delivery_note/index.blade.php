@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Delivery Note Penjualan</h1>
    <a href="{{ route('penjualan.delivery-note.create') }}" class="btn btn-primary mb-3">Buat Delivery Note</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No.</th>
                <th>No. Delivery Note</th>
                <th>No. SO</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Total Barang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deliveryNotes as $dn)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $dn->no }}</td>
                <td>{{ $dn->order?->no ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($dn->tgl)->format('d-m-Y') }}</td>
                {{-- <td>{{ $dn->order?->customer?->nama ?? '-' }}</td> --}}
                <td>{{ $dn->order?->customer?->nama_customer ?? '-' }}</td>

                <td>{{ $dn->details->sum(fn($d) => $d->orderDetail->qty) }}</td>
                <td>
                    <a href="{{ route('penjualan.delivery-note.show', $dn->id) }}" class="btn btn-sm btn-info">Lihat</a>
                    <a href="{{ route('penjualan.delivery-note.edit', $dn->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('penjualan.delivery-note.destroy', $dn->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus delivery note ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Belum ada delivery note</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
