@extends('layouts.admin')

@section('content')
    <div class="container">
        <h4>Surat Jalan Keluar</h4>

        <a href="{{ route('penjualan.sj.create') }}" class="btn btn-primary mb-3">
            + Buat Surat Jalan Keluar
        </a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Type</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                    <tr>
                        <td>{{ $item->no }}</td>
                        <td>{{ $item->tgl }}</td>
                        <td>{{ $item->customer->nama ?? '-' }}</td>
                        <td>{{ $item->type }}</td>
                        <td><button class="btn btn-sm btn-primary btn-detail" data-id="{{ $item->id }}">
                                Detail
                            </button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Surat Jalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                Loading...
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-detail').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id;

                fetch('/dnpo/' + id)
                    .then(res => res.text())
                    .then(data => {
                        document.getElementById('detailContent').innerHTML = data;
                        new bootstrap.Modal(document.getElementById('detailModal')).show();
                    });
            });
        });
    });
</script>