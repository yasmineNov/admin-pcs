@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Kalkulasi Premi & Sewa Kendaraan</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Karyawan</th>
                        <th>Periode</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-right">Premi</th>
                        <th class="text-right">Sewa</th>
                        <th class="text-right">Total Keseluruhan</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($premis as $p)
                        <tr>
                            <td>{{ $p->user->name }}</td>
                            <td><small>{{ $p->absensi->tanggal_mulai }} - {{ $p->absensi->tanggal_akhir }}</small></td>
                            <td class="text-center">{{ $p->total_hadir }} hari</td>
                            <td class="text-right">Rp {{ number_format($p->subtotal_premi) }}</td>
                            <td class="text-right">Rp {{ number_format($p->subtotal_sewa) }}</td>
                            <td class="text-right font-weight-bold text-primary">Rp {{ number_format($p->total_keseluruhan) }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $p->status == 'pending' ? 'badge-warning' : 'badge-success' }}">
                                    {{ strtoupper($p->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data premi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $premis->links() }}
            </div>
        </div>
    </div>
@endsection