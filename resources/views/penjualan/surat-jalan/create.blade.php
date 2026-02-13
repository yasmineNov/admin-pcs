@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Surat Jalan Baru</h3>
    </div>

    <div class="card-body">

        {{-- ERROR VALIDATION --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('surat-jalan.store') }}" method="POST">
            @csrf

            {{-- TANGGAL & CUSTOMER --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Tanggal Surat Jalan</label>
                    <input type="date"
                           name="tgl"
                           class="form-control"
                           value="{{ old('tgl', date('Y-m-d')) }}"
                           required>
                </div>

                <div class="col-md-4">
                    <label>Customer</label>
                    <select name="customer_id"
                            id="customerSelect"
                            class="form-control"
                            required>
                        <option value="">-- Pilih Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}"
                                    data-alamat="{{ $customer->alamat }}">
                                {{ $customer->nama_customer }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Alamat Kirim</label>
                    <input type="text"
                           name="alamat_kirim"
                           id="alamatKirim"
                           class="form-control"
                           readonly>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label>No. PO</label>
                    <input type="text"
                           name="po"
                           class="form-control"
                           value="{{ old('po') }}"
                           required>
                </div>
            </div>

            <hr>

            <h5>Barang Dikirim</h5>

            <div class="table-responsive">
                <table class="table table-bordered" id="table-barang">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Barang</th>
                            <th width="150">Qty Kirim</th>
                            <th width="60"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="barang_id[]"
                                        class="form-control"
                                        required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach ($barangs as $b)
                                        <option value="{{ $b->id }}">
                                            {{ $b->kode_barang }} -
                                            {{ $b->nama_barang }}
                                            (Stok: {{ $b->stok }})
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

            <button type="button"
                    id="add-row"
                    class="btn btn-secondary btn-sm mt-2">
                + Tambah Barang
            </button>

            <hr>

            <button type="submit" class="btn btn-primary">
                Simpan Surat Jalan
            </button>

        </form>
    </div>
</div>

{{-- SCRIPT --}}
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

document.getElementById('customerSelect')
    .addEventListener('change', function() {

    let selected = this.options[this.selectedIndex];
    let alamat = selected.getAttribute('data-alamat');

    document.getElementById('alamatKirim').value = alamat ?? '';
});
</script>

@endsection
