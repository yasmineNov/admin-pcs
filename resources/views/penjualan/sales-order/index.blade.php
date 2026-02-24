@extends('layouts.admin')

@section('content')
<div class="card">

    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h4>Sales Orders</h4>
        <a href="{{ route('penjualan.sales-order.create') }}"
           class="btn btn-success btn-sm">
            + Buat SO
        </a>
    </div>

    <div class="card-body">

        {{-- SEARCH --}}
        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Cari No SO / Customer..."
                       value="{{ request('search') }}">
                <button class="btn btn-secondary">Cari</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th style="width:70px;">No</th>
                        <th>No. SO</th>
                        <th>Tanggal SO</th>
                        <th>Customer</th>
                        <th class="text-end">Nominal</th>
                        <th>Keterangan</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $so)
                    <tr>
                        <td>
                            {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}
                        </td>

                        <td>{{ $so->no }}</td>

                        <td>{{ $so->tgl->format('d-m-Y') }}</td>

                        <td>{{ $so->customer?->nama_customer ?? '-' }}</td>

                        <td class="text-end">
                            {{ number_format($so->dpp, 0, ',', '.') }}
                        </td>

                        <td>{{ $so->keterangan ?? '-' }}</td>

                        <td>
                            <button class="btn btn-sm btn-primary"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#detail-{{ $so->id }}">
                                Detail
                            </button>
                        </td>
                    </tr>

                    {{-- Collapse Detail --}}
                    <tr class="collapse" id="detail-{{ $so->id }}">
                        <td colspan="7">
                            <table class="table table-sm table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th>Qty</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($so->details as $detail)
                                    <tr>
                                        <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                                        <td>{{ $detail->qty }}</td>
                                        <td>{{ number_format($detail->harga, 0, ',', '.') }}</td>
                                        <td>{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            Data SO belum tersedia
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- INFO + PAGINATION --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Menampilkan {{ $orders->firstItem() ?? 0 }}
                –
                {{ $orders->lastItem() ?? 0 }}
                dari {{ $orders->total() }} data
            </div>

            <div>
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        </div>

    </div>
</div>
@endsection
