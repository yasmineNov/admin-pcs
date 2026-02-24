@extends('layouts.admin')

@section('content')
<div class="card">

    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h4>Purchase Orders</h4>
        <a href="{{ route('pembelian.purchase-order.create') }}"
           class="btn btn-success btn-sm">
            + Buat PO
        </a>
    </div>

    <div class="card-body">

        {{-- SEARCH --}}
        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Cari No PO / Supplier..."
                       value="{{ request('search') }}">
                <button class="btn btn-secondary">Cari</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th style="width:70px;">No</th>
                        <th>No. PO</th>
                        <th>Tanggal PO</th>
                        <th>Supplier</th>
                        <th class="text-end">Nominal</th>
                        <th>Keterangan</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $po)
                    <tr>
                        <td>
                            {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}
                        </td>

                        <td>{{ $po->no }}</td>

                        <td>{{ $po->tgl->format('d-m-Y') }}</td>

                        <td>{{ $po->supplier->nama_supplier ?? '-' }}</td>

                        <td class="text-end">
                            {{ number_format($po->dpp, 0, ',', '.') }}
                        </td>

                        <td>{{ $po->keterangan ?? '-' }}</td>

                        <td>
                            <button class="btn btn-sm btn-primary"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#detail-{{ $po->id }}">
                                Detail
                            </button>
                        </td>
                    </tr>

                    {{-- Detail Collapse --}}
                    <tr class="collapse" id="detail-{{ $po->id }}">
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
                                    @foreach($po->details as $detail)
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
                            Data PO belum tersedia
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
