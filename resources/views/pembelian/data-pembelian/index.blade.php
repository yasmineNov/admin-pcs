@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header bg-dark text-white">
        <div class="d-flex justify-content-between">
        <h4 class="mb-2">Data Pembelian</h4>
        <div>
        <a href="{{ route('pembelian.data-pembelian.export', request()->query()) }}"
   class="btn btn-success">
   Export Excel
</a>
<a href="{{ route('pembelian.data-pembelian.print', request()->query()) }}"
   target="_blank"
   class="btn btn-primary">
   Print
</a>
        </div>

</div>
    </div>

    <div class="card-body">
        {{-- FILTER --}}
        <form method="GET" class="mb-3">
    <div class="row align-items-end">

        <div class="col-md-3">
            <label>Dari Tanggal</label>
            <input type="date" name="from"
                   value="{{ request('from') }}"
                   class="form-control">
        </div>

        <div class="col-md-3">
            <label>Sampai Tanggal</label>
            <input type="date" name="to"
                   value="{{ request('to') }}"
                   class="form-control">
        </div>

        <div class="col-md-3">
            <label>Supplier</label>
            <select name="supplier_id" class="form-control">
                <option value="">-- Semua Supplier --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}"
                        {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->nama_supplier }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <button class="btn btn-primary">Filter</button>
            <a href="{{ route('pembelian.data-pembelian.index') }}"
               class="btn btn-secondary">
               Reset
            </a>
        </div>

    </div>
</form>
{{-- END FILTER --}}
        <table class="table table-bordered table-striped">
            <thead class="bg-secondary text-white">
                <tr>
                    <th>No</th>
                    <th>No. Inv</th>
                    <th>Tgl Invoice</th>
                    <th>Nama Supplier</th>
                    <th>DPP</th>
                    <th>PPN</th>
                    <th>Total</th>
                    <th>Qty</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $index => $inv)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $inv->no }}</td>
                        <td>{{ $inv->tgl->format('d-m-Y') }}</td>
                        <td>{{ $inv->supplier->nama_supplier ?? '-' }}</td>
                        <td>{{ number_format($inv->dpp,0,',','.') }}</td>
                        <td>{{ number_format($inv->ppn,0,',','.') }}</td>
                        <td>{{ number_format($inv->grand_total,0,',','.') }}</td>
                        <td>
                            {{ 
                                $inv->details->sum(function($d){
                                    return $d->orderDetail->qty ?? 0;
                                })
                            }}
                        </td>
                        <td>{{ $inv->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
