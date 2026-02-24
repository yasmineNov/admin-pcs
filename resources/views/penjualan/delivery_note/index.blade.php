@extends('layouts.admin')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Delivery Note Penjualan</h1>
        <a href="{{ route('penjualan.delivery-note.create') }}"
           class="btn btn-primary">
           Buat Delivery Note
        </a>
    </div>

    {{-- SEARCH --}}
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari No DN / No SO / Customer..."
                   class="form-control">
            <button class="btn btn-secondary">Cari</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th style="width:70px;">No.</th>
                    <th>No. Delivery Note</th>
                    <th>No. SO</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Total Barang</th>
                    <th style="width:200px;">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($deliveryNotes as $dn)
                <tr>
                    <td>
                        {{ ($deliveryNotes->currentPage() - 1) * $deliveryNotes->perPage() + $loop->iteration }}
                    </td>

                    <td>{{ $dn->no }}</td>

                    <td>{{ $dn->order?->no ?? '-' }}</td>

                    <td>{{ \Carbon\Carbon::parse($dn->tgl)->format('d-m-Y') }}</td>

                    <td>{{ $dn->order?->customer?->nama_customer ?? '-' }}</td>

                    <td>{{ $dn->details->sum('qty') }}</td>

                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-primary btn-detail"
                                    data-id="{{ $dn->id }}">
                                Detail
                            </button>

                            <a href="{{ route('penjualan.delivery-note.edit', $dn->id) }}"
                               class="btn btn-sm btn-warning">
                               Edit
                            </a>

                            <form action="{{ route('penjualan.delivery-note.destroy', $dn->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus delivery note ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="7" class="text-center">
                        Belum ada delivery note
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- INFO + PAGINATION --}}
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            Menampilkan {{ $deliveryNotes->firstItem() ?? 0 }}
            –
            {{ $deliveryNotes->lastItem() ?? 0 }}
            dari {{ $deliveryNotes->total() }} data
        </div>

        <div>
            {{ $deliveryNotes->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>
@endsection
