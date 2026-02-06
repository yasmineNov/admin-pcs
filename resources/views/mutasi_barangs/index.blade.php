@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Kartu Stok Barang</h1>

    {{-- Filter Barang --}}
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <select name="barang_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->id }}"
                            {{ $barangId == $barang->id ? 'selected' : '' }}>
                            {{ $barang->nama_barang }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    {{-- Tabel Kartu Stok --}}
    @if($barangId)
    <div class="card">
        <div class="card-header bg-dark text-white">
            Kartu Stok :
            <strong>
                {{ $barangs->where('id', $barangId)->first()->nama_barang ?? '' }}
            </strong>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Tanggal</th>
                            <th width="10%" class="text-end">Masuk</th>
                            <th width="10%" class="text-end">Keluar</th>
                            <th width="10%" class="text-end">Saldo</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mutasis as $index => $mutasi)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($mutasi->tgl_mutasi)->format('d-m-Y') }}
                                </td>
                                <td class="text-end text-success">
                                    {{ $mutasi->masuk }}
                                </td>
                                <td class="text-end text-danger">
                                    {{ $mutasi->keluar }}
                                </td>
                                <td class="text-end fw-bold">
                                    {{ $mutasi->saldo }}
                                </td>
                                <td>
                                    {{ $mutasi->keterangan ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3">
                                    Belum ada mutasi untuk barang ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
        <div class="alert alert-info">
            Silakan pilih barang untuk melihat kartu stok
        </div>
    @endif
</div>
@endsection
