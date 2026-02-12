@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Surat Jalan Baru</h3>
    </div>

    <div class="card-body">
        <form action="{{ route('surat-jalan.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Tanggal Surat Jalan</label>
                    <input type="date" name="tgl_sj" class="form-control" required>
                </div>

                <div class="col-md-5">
                    <label>Customer</label>
                    <select name="customer_id" class="form-control" required>
                        <option value="">-- pilih customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->nama_customer }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label>No. PO</label>
                    <input type="text" name="po" class="form-control" required>
                </div>
            </div>

            <hr>

            <h5>Barang Dikirim</h5>

            <div class="table-responsive">
                <table class="table table-bordered" id="table-barang">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Barang</th>
                            <th width="120">Qty</th>
                            <th width="60"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="barang_id[]" class="form-control" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach ($barangs as $b)
                                        <option value="{{ $b->id }}">
                                            {{ $b->kode_barang }} - {{ $b->nama_barang }} (Stok: {{ $b->stok }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number"
                                       name="qty[]"
                                       class="form-control"
                                       min="1"
                                       required>
                            </td>
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-danger btn-sm remove">
                                    ×
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" id="add-row" class="btn btn-secondary btn-sm">
                + Tambah Barang
            </button>

            <hr>

            <button type="submit" class="btn btn-primary">
                Simpan Surat Jalan
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('add-row').addEventListener('click', function () {
    let tableBody = document.querySelector('#table-barang tbody');
    let firstRow = tableBody.querySelector('tr');
    let newRow = firstRow.cloneNode(true);

    newRow.querySelectorAll('input').forEach(input => input.value = '');
    newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

    tableBody.appendChild(newRow);
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove')) {
        let rows = document.querySelectorAll('#table-barang tbody tr');
        if (rows.length > 1) {
            e.target.closest('tr').remove();
        }
    }
});
</script>

@endsection
