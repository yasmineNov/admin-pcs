@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header bg-dark text-white">
        <h4>Buat Invoice Pembelian</h4>
    </div>

    <form action="{{ route('pembelian.invoice.store') }}" method="POST" id="invoiceForm">
        @csrf
        <div class="card-body">

            <div class="row mb-2">
                <div class="col-md-2">
                    <label>No. Invoice</label>
                    <input type="text" name="no" class="form-control" value="{{ generateDocumentNumber('invoices','INV') }}" readonly style="background-color: #e9ecef;">
                </div>

                <div class="col-md-2">
                    <label>Tanggal Invoice</label>
                    <input type="date" name="tgl" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-md-3">
                    <label>Supplier</label>
                    <input type="text" name="supplier_name" class="form-control" id="supplier_name" readonly style="background-color:#e9ecef;">
                </div>

                <div class="col-md-3">
                    <label>Delivery Note</label>
                    <select id="delivery_note_id" name="delivery_note_id" class="form-control" required>
                        <option value="">-- Pilih Delivery Note --</option>
                        @foreach($deliveryNotes as $dn)
                            <option value="{{ $dn->id }}">{{ $dn->no }} | {{ $dn->tgl->format('d-m-Y') }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Tgl Jatuh Tempo</label>
                    <input type="date" name="jatuh_tempo" class="form-control" value="{{ date('Y-m-d') }}" required>
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
                    <!-- Row akan diisi otomatis via JS -->
                </tbody>
            </table>

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
                    <input type="text" name="status" class="form-control" value="unpaid" readonly style="background-color:#e9ecef;">
                </div>
            </div>

        </div>

        <div class="card-footer">
            <button class="btn btn-success">Simpan Invoice</button>
            <a href="{{ route('pembelian.invoice.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const deliverySelect = document.getElementById('delivery_note_id');
    const itemsTable = document.querySelector('#items-table tbody');
    const supplierInput = document.getElementById('supplier_name');
    let rowIndex = 0; // index untuk details[]

    function calculateRow(row){
        let qty = parseFloat(row.querySelector('.qty').value) || 0;
        let harga = parseFloat(row.querySelector('.harga').value) || 0;
        let subtotalInput = row.querySelector('.subtotal-detail');
        subtotalInput.value = (qty * harga).toFixed(2);
        calculateTotal();
    }

    function calculateTotal(){
        let dpp = 0;
        document.querySelectorAll('.subtotal-detail').forEach(input => {
            dpp += parseFloat(input.value) || 0;
        });
        let pajak = dpp * 0.11;
        let total = dpp + pajak;
        document.getElementById('dpp').value = dpp.toFixed(2);
        document.getElementById('pajak').value = pajak.toFixed(2);
        document.getElementById('total').value = total.toFixed(2);
    }

    // Ambil data delivery note via AJAX
    deliverySelect.addEventListener('change', function(){
        const dnId = this.value;
        if(!dnId) return;

        fetch(`/pembelian/delivery-note/${dnId}/details`)
            .then(res => res.json())
            .then(data => {
                itemsTable.innerHTML = '';
                rowIndex = 0;
                if(data.length == 0) return;

                // Set supplier dari DN
                supplierInput.value = data[0].supplier_name;

                data.forEach(item => {
                    let row = document.createElement('tr');
                    row.innerHTML = `
                        <td>
                            ${item.nama_barang}
                            <input type="hidden" name="details[${rowIndex}][barang_id]" value="${item.barang_id}">
                            <input type="hidden" name="details[${rowIndex}][order_detail_id]" value="${item.order_detail_id}">
                        </td>
                        <td><input type="number" name="details[${rowIndex}][qty]" class="form-control qty" value="${item.qty}" readonly></td>
                        <td><input type="number" name="details[${rowIndex}][harga]" class="form-control harga" min="0" step="0.01" required></td>
                        <td><input type="number" name="details[${rowIndex}][subtotal]" class="form-control subtotal-detail" readonly style="background-color:#e9ecef;"></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-row">-</button></td>
                    `;
                    itemsTable.appendChild(row);
                    rowIndex++;
                });
            });
    });

    // Hapus row
    itemsTable.addEventListener('click', function(e){
        if(e.target.classList.contains('remove-row')){
            e.target.closest('tr').remove();
            calculateTotal();
        }
    });

    // Hitung subtotal saat input harga
    itemsTable.addEventListener('input', function(e){
        if(e.target.classList.contains('harga')){
            calculateRow(e.target.closest('tr'));
        }
    });
});
</script>
@endsection
