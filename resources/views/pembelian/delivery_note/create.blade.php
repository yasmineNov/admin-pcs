@extends('layouts.admin')

@section('content')
<h1>Surat Jalan Pembelian</h1>

<form action="{{ route('pembelian.delivery-note.store') }}" method="POST">
    @csrf
    <div class="card-body">
    <div class="row mb-3">
        <div class="col-md-3">
        <label>No Surat Jalan</label>
        <input type="text" name="no" class="form-control" required>
    </div>

    <div class="col-md-3">
        <label>Tanggal</label>
        <input type="date" name="tgl" class="form-control" value="{{ date('Y-m-d') }}" required>
    </div>

    {{-- <div class="col-md-3">
        <label>Type</label>
        <select name="type" class="form-control" required>
            <option value="masuk">Masuk</option>
            <option value="keluar">Keluar</option>
        </select>
    </div> --}}

    <div class="col-md-3">
        <label>Purchase Order</label>
<select name="order_id" class="form-control">
    <option value="">-- Pilih Order --</option>
    @foreach($orders as $order)
        <option value="{{ $order->id }}">
            {{ $order->no }} - {{ $order->supplier->nama_supplier ?? '-' }}
        </option>
    @endforeach
</select>

    </div>
    <div class="col-md-3">
        <label>Alamat Kirim</label>
        <input type="text" name="alamat_kirim" class="form-control" required>
    </div>
    </div>

    <hr>
    <h4>Detail Barang</h4>
    <table class="table" id="detailTable">
        <thead>
            <tr>
                <th>Barang</th>
                <th>Keterangan</th>
                <th><button type="button" class="btn btn-sm btn-success" onclick="addRow()">+</button></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <select name="details[0][order_detail_id]" class="form-control">
    <option value="">-- Pilih Barang --</option>
    @foreach($orders as $order)
        @foreach($order->details as $detail)
            <option value="{{ $detail->id }}">
                {{ $detail->barang->nama_barang }} (Qty PO: {{ $detail->qty }})
            </option>
        @endforeach
    @endforeach
</select>

                </td>
                <td>
                    <input type="text" name="details[0][keterangan]" class="form-control">
                </td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">-</button></td>
            </tr>
        </tbody>
    </table>
    </div>

    <div class="card-footer">
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('pembelian.delivery-note.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</form>

<script>
let rowIndex = 1;

function addRow() {
    let table = document.getElementById('detailTable').getElementsByTagName('tbody')[0];
    let newRow = table.rows[0].cloneNode(true);

    // Update name index
    newRow.querySelectorAll('select, input').forEach(el => {
        let name = el.getAttribute('name');
        el.setAttribute('name', name.replace(/\d+/, rowIndex));
        if(el.tagName == 'SELECT') el.selectedIndex = 0;
        else el.value = '';
    });

    table.appendChild(newRow);
    rowIndex++;
}

function removeRow(btn) {
    let table = document.getElementById('detailTable').getElementsByTagName('tbody')[0];
    if(table.rows.length > 1) {
        btn.closest('tr').remove();
    } else {
        alert('Minimal 1 barang harus ada.');
    }
}
</script>
@endsection
