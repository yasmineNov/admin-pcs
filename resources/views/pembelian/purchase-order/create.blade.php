@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header bg-dark text-white">
        <h4>Buat Purchase Order</h4>
    </div>

    <form action="{{ route('pembelian.purchase-order.store') }}" method="POST" id="poForm">
        @csrf
        <div class="card-body">

            <div class="row mb-2">
                <div class="col-md-3">
                    <label>No. PO</label>
                    <input type="text" name="no" class="form-control" value="{{ generateDocumentNumber('orders','PCS-PO') }}" readonly style="background-color: #e9ecef;">
                </div>

                <div class="col-md-2">
                    <label>Tanggal PO</label>
                    <input type="date" name="tgl" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-md-3">
                    <label>Supplier</label>
                    <select name="supplier_id" class="form-control" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label>TOP (Hari)</label>
                    <input type="number" name="top" class="form-control" value="0">
                </div>
                <div class="col-md-2">
                    <label>Tanggal Kirim</label>
                    <input type="date" name="tgl_kirim" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <hr>
            <h5>Detail Barang</h5>
            <table class="table table-bordered" id="items-table">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th>Barang</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select name="barang_id[]" class="form-control barang-select" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach(\App\Models\Barang::all() as $b)
                                    <option value="{{ $b->id }}">{{ $b->nama_barang }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="qty[]" class="form-control qty" min="0" step="0.01" required></td>
                        <td><input type="number" name="harga[]" class="form-control harga" min="0" step="0.01" required></td>
                        <td><input type="number" name="subtotal_detail[]" class="form-control subtotal-detail" readonly style="background-color:#e9ecef;"></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-row">-</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-primary btn-sm" id="add-row">+ Tambah Barang</button>

            <hr>
            <div class="row">
                <div class="col-md-3">
                    <label>Subtotal (DPP)</label>
                    <input type="number" name="dpp" id="dpp" class="form-control" readonly style="background-color:#e9ecef;">
                </div>
                <div class="col-md-3">
                    <label>Pajak 11%</label>
                    <input type="number" name="pajak" id="pajak" class="form-control" readonly style="background-color:#e9ecef;">
                </div>
                <div class="col-md-3">
                    <label>Total</label>
                    <input type="number" name="total" id="total" class="form-control" readonly style="background-color:#e9ecef;">
                </div>
                <div class="col-md-3">
                    <label>Status</label>
                    <input type="text" name="status" class="form-control" value="Belum Lunas" readonly style="background-color:#e9ecef;">
                </div>
            </div>

        </div>

        <div class="card-footer">
            <button class="btn btn-success">Simpan PO</button>
            <a href="{{ route('pembelian.purchase-order.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){

    function calculateRow(row){
        let qtyInput = row.querySelector('.qty');
        let hargaInput = row.querySelector('.harga');
        let subtotalInput = row.querySelector('.subtotal-detail');

        let qty = parseFloat(qtyInput.value);
        let harga = parseFloat(hargaInput.value);

        if (isNaN(qty)) qty = 0;
        if (isNaN(harga)) harga = 0;

        let subtotal = qty * harga;
        subtotalInput.value = subtotal.toFixed(2);

        calculateTotal();
    }
    document.getElementById('add-row').addEventListener('click', function(){

    let tbody = document.querySelector('#items-table tbody');
    let firstRow = tbody.querySelector('tr');
    let newRow = firstRow.cloneNode(true);

    // Reset semua input
    newRow.querySelectorAll('input').forEach(function(input){
        input.value = '';
    });

    // Reset select
    newRow.querySelectorAll('select').forEach(function(select){
        select.selectedIndex = 0;
    });

    tbody.appendChild(newRow);

});

document.getElementById('items-table').addEventListener('click', function(e){
    if(e.target.classList.contains('remove-row')){
        let tbody = document.querySelector('#items-table tbody');
        if(tbody.rows.length > 1){
            e.target.closest('tr').remove();
            calculateTotal();
        }
    }
});

    function calculateTotal(){
        let dpp = 0;

        document.querySelectorAll('.subtotal-detail').forEach(function(input){
            let value = parseFloat(input.value);
            if (!isNaN(value)) {
                dpp += value;
            }
        });

        let pajak = dpp * 0.11;
        let total = dpp + pajak;

        document.getElementById('dpp').value = dpp.toFixed(2);
        document.getElementById('pajak').value = pajak.toFixed(2);
        document.getElementById('total').value = total.toFixed(2);
    }

    // Event delegation (lebih aman untuk row dinamis)
    document.getElementById('items-table').addEventListener('input', function(e){
        if(e.target.classList.contains('qty') || e.target.classList.contains('harga')){
            let row = e.target.closest('tr');
            calculateRow(row);
        }
    });

});

</script>
@endsection
