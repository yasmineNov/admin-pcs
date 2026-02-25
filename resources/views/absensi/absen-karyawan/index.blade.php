@extends('layouts.admin')

@section('content')
    <div class="container-fluid">

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-edit"></i> Input Absensi Baru</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('absensi.absen-karyawan.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}"
                                required>
                        </div>
                        <div class="col-md-4">
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}"
                                required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Buka Form Checkbox</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($period))
            <form action="{{ route('absensi.absen-karyawan.store') }}" method="POST">
                @csrf
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">

                <div class="card">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-bordered text-nowrap">
                            <thead class="bg-dark">
                                <tr>
                                    <th class="align-middle">Nama Karyawan</th>
                                    @foreach($period as $date)
                                        <th class="text-center">{{ $date->format('d/m') }}</th>
                                    @endforeach
                                    <th class="text-center align-middle bg-primary">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        @foreach($period as $date)
                                            <td class="text-center">
                                                <input type="checkbox" name="kehadiran[{{ $user->id }}][]"
                                                    value="{{ $date->format('Y-m-d') }}" class="cb-hadir" data-userid="{{ $user->id }}">
                                            </td>
                                        @endforeach
                                        <td class="text-center"><span id="count-{{ $user->id }}" class="font-weight-bold">0</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-right">Simpan Absensi & Hitung Premi</button>
                    </div>
                </div>
            </form>
        @endif

        <div class="container-fluid">
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Sesi Absensi</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $h)
                                <tr>
                                    <td>{{ $h->tanggal_mulai }} s/d {{ $h->tanggal_akhir }}</td>
                                    <td>{{ $h->keterangan }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm btn-detail" data-id="{{ $h->id }}">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada data absensi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info">
                            <h5 class="modal-title text-white">Detail Absensi & Premi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div id="modalBody" class="modal-body">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-2">Memuat data...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

    @section('scripts')
        <script>
            // Gunakan ini untuk memastikan jQuery sudah ke-load
            $(document).ready(function () {
                console.log("JS Absensi Ready!"); // Cek di Console F12

                // Gunakan $(document).on supaya elemen dinamis tetap bisa diklik
                $(document).on('click', '.btn-detail', function (e) {
                    e.preventDefault();

                    let id = $(this).data('id');
                    console.log("Tombol Detail diklik! ID: " + id); // Log 1

                    // 1. Munculkan Modal
                    $('#modalDetail').modal('show');

                    // 2. Reset isi modal ke loading state
                    $('#modalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Memuat data...</p></div>');

                    // 3. Eksekusi AJAX
                    console.log("Mengirim permintaan AJAX ke: {{ url('absensi/absen-karyawan/detail') }}/" + id); // Log 2

                    $.ajax({
                        url: "{{ url('absensi/absen-karyawan/detail') }}/" + id,
                        type: "GET",
                        success: function (response) {
                            console.log("AJAX Berhasil!"); // Log 3
                            $('#modalBody').html(response);
                        },
                        error: function (xhr, status, error) {
                            console.error("AJAX Error: " + status + " - " + error); // Log Error
                            console.log(xhr.responseText);
                            $('#modalBody').html('<div class="alert alert-danger">Gagal mengambil data. Cek Console F12!</div>');
                        }
                    });
                });
            });
        </script>
    @endsection