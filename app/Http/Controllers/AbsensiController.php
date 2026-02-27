<?php
namespace App\Http\Controllers;

use App\Models\PremiUser;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use App\Models\AbsensiUser;
use App\Models\SewaKendaraan;
use App\Models\PremiHadir;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil riwayat absensi untuk ditampilkan di tabel utama
        $history = Absensi::orderBy('tanggal_mulai', 'desc')->get();

        // 2. Logic untuk Generate Form Checkbox
        $period = null;
        $users = [];
        if ($request->has(['start_date', 'end_date'])) {
            $period = CarbonPeriod::create($request->start_date, $request->end_date);
            $users = User::all();
        }

        return view('absensi.absen-karyawan.index', compact('history', 'period', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'kehadiran' => 'required|array',
        ]);

        // Cek user yang nominal premi/sewa kosong
        $warningUsers = [];
        foreach ($request->kehadiran as $userId => $dates) {
            $user = User::find($userId); // ambil data user dari DB
            $masterPremi = PremiUser::where('user_id', $userId)->first();
            $nominalPremi = $masterPremi ? $masterPremi->nominal : null;

            $masterSewa = SewaKendaraan::where('user_id', $userId)->first();
            $nominalSewa = $masterSewa ? $masterSewa->nominal : null;

            if (is_null($nominalPremi) || is_null($nominalSewa)) {
                $warningUsers[] = [
                    'user_id' => $userId,
                    'name' => $user ? $user->name : 'User ' . $userId, // ambil nama dari tabel users
                    'nominal_premi' => $nominalPremi,
                    'nominal_sewa' => $nominalSewa,
                ];
            }
        }

        // Kalau ada warning, return JSON (buat JS handle)
        if (!empty($warningUsers)) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Beberapa user memiliki nominal premi/sewa kosong.',
                'users' => $warningUsers,
            ]);
        }

        // Kalau aman, lanjut simpan
        return $this->commitAbsensi($request);
    }

    // Fungsi pisah buat simpan data
    protected function commitAbsensi($request)
    {
        DB::beginTransaction();
        try {
            $absensi = Absensi::create([
                'tanggal_mulai' => $request->start_date,
                'tanggal_akhir' => $request->end_date,
                'keterangan' => "Absensi Periode {$request->start_date} s/d {$request->end_date}",
            ]);

            foreach ($request->kehadiran as $userId => $dates) {
                foreach ($dates as $tanggal) {
                    AbsensiUser::create([
                        'absensi_id' => $absensi->id,
                        'user_id' => $userId,
                        'tanggal' => $tanggal,
                    ]);
                }

                $totalHadir = count($dates);

                $masterPremi = PremiUser::where('user_id', $userId)->first();
                $nominalPremi = $masterPremi ? $masterPremi->nominal : 0;

                $masterSewa = SewaKendaraan::where('user_id', $userId)->first();
                $nominalSewa = $masterSewa ? $masterSewa->nominal : 0;

                PremiHadir::create([
                    'user_id' => $userId,
                    'absensi_id' => $absensi->id,
                    'total_hadir' => $totalHadir,
                    'nominal_premi_harian' => $nominalPremi,
                    'nominal_sewa_harian' => $nominalSewa,
                    'subtotal_premi' => $totalHadir * $nominalPremi,
                    'subtotal_sewa' => $totalHadir * $nominalSewa,
                    'total_keseluruhan' => $totalHadir * ($nominalPremi + $nominalSewa),
                    'status' => 'pending',
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Absensi dan Premi berhasil dihitung!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }
    public function premiIndex()
    {
        $absensis = Absensi::with('premiHadirs')
            ->latest()
            ->paginate(10);

        return view('absensi.premi-hadir.index', compact('absensis'));
    }
    public function detailPremi($id)
    {
        $absensi = Absensi::with(['premiHadirs.user'])
            ->findOrFail($id);

        return view('absensi.premi-hadir.detail', compact('absensi'));
    }

    public function getDetail($id)
    {
        $absensi = Absensi::findOrFail($id);

        $period = CarbonPeriod::create(
            $absensi->tanggal_mulai,
            $absensi->tanggal_akhir
        );

        $users = User::all();

        $absenDetails = AbsensiUser::where('absensi_id', $id)->get();


        // Kita return sebagai HTML partial agar mudah dimasukkan ke dalam modal
        return view('absensi.absen-karyawan.detail_partial', compact(
            'absensi',
            'period',
            'users',
            'absenDetails'
        ))->render();
    }
}